import os
import re
import tempfile
from pathlib import Path
from typing import Any, Dict, List, Optional, Set

from flask import Flask, jsonify, request

from services.cv_analyzer import analyze_cv as analyze_cv_file
from services.recommender import MODEL_VERSION, get_mock_recommendations, get_recommendations
from train import DEFAULT_DATASET, DEFAULT_MODEL_PATH, load_rows, run_training
import joblib

app = Flask(__name__)
_MODEL_CACHE = None
_CAREER_METADATA: Optional[Dict[str, Any]] = None


@app.get("/")
def index():
    return jsonify({
        "service": "Career Mind AI",
        "status": "ready",
        "endpoints": ["/health", "/recommendations/mock", "/recommendations", "/cv/analyze"],
    })


@app.get("/health")
def health():
    model_ok = _load_model() is not None
    return jsonify({
        "status": "ok" if model_ok else "degraded",
        "model_loaded": model_ok,
    })


@app.get("/recommendations/mock")
def recommendations_mock():
    return jsonify(get_mock_recommendations())


@app.post("/cv/analyze")
def analyze_cv_endpoint():
    file = request.files.get("cv_file")
    filename = file.filename if file else None

    if not file:
        return jsonify({
            "schema_version": 1,
            "model_version": "nlp_v1",
            "summary": "No CV file uploaded.",
            "missing_skills": [],
            "feedback": ["Please upload a PDF or DOCX file."],
            "score": 0,
            "file": filename or "unknown",
            "analysis_method": "pdf_docx_nlp",
            "extracted_skills": [],
        }), 400

    extension = os.path.splitext(filename or "")[1].lower()
    suffix = extension if extension in {".pdf", ".doc", ".docx"} else ""

    temp_file = tempfile.NamedTemporaryFile(delete=False, suffix=suffix)
    temp_path = temp_file.name
    temp_file.close()

    try:
        file.save(temp_path)
        analysis = analyze_cv_file(temp_path, filename, _db_config())
        return jsonify(analysis)
    finally:
        if os.path.exists(temp_path):
            os.remove(temp_path)


@app.post("/recommendations")
def recommendations():
    payload = request.get_json(silent=True) or {}
    return jsonify(get_recommendations(payload, _db_config()))


@app.post("/train")
def train_model():
    payload = request.get_json(silent=True) or {}
    dataset_path = Path(payload.get("dataset_path") or DEFAULT_DATASET)
    model_path = Path(payload.get("model_path") or DEFAULT_MODEL_PATH)

    results = run_training(dataset_path, model_path)
    global _MODEL_CACHE
    _MODEL_CACHE = None
    return jsonify({
        "status": "trained",
        "model_path": results["model_path"],
        "accuracy": results["accuracy"],
        "train_size": results["train_size"],
        "test_size": results["test_size"],
    })


@app.post("/predict")
def predict():
    payload = request.get_json(silent=True) or {}
    text = payload.get("text") or _build_text(payload)

    if not text:
        return jsonify({"error": "Missing input text or profile."}), 400

    pipeline = _load_model()
    if pipeline is None:
        return jsonify({"error": "Model not found. Train the model first."}), 404

    probabilities = None
    labels = None
    if hasattr(pipeline, "predict_proba"):
        probabilities = pipeline.predict_proba([text])[0].tolist()
        labels = pipeline.classes_.tolist()

    prediction = pipeline.predict([text])[0]
    user_tokens = _extract_user_tokens(payload)

    ranked = []
    if probabilities and labels:
        ranked = sorted(zip(labels, probabilities), key=lambda item: item[1], reverse=True)
    if not ranked:
        ranked = [(prediction, 1.0)]

    top_predictions = [
        _build_prediction_entry(label, score, user_tokens)
        for label, score in ranked[:3]
    ]

    response = {
        "schema_version": 1,
        "model_version": MODEL_VERSION,
        "prediction": prediction,
        "top_predictions": top_predictions,
    }

    return jsonify(response)


def _db_config():
    return {
        "host": os.getenv("DB_HOST", "127.0.0.1"),
        "port": int(os.getenv("DB_PORT", "3306")),
        "database": os.getenv("DB_NAME", "career_mind"),
        "user": os.getenv("DB_USER", "root"),
        "password": os.getenv("DB_PASS", "password"),
    }


def _load_model():
    global _MODEL_CACHE
    if _MODEL_CACHE is not None:
        return _MODEL_CACHE

    model_path = Path(DEFAULT_MODEL_PATH)
    if not model_path.exists():
        return None

    _MODEL_CACHE = joblib.load(model_path)
    return _MODEL_CACHE


def _build_text(payload: dict) -> str:
    profile = payload.get("profile") or {}
    skills = payload.get("skills") or []
    interests = payload.get("interests") or []

    if isinstance(skills, str):
        skills = [skills]
    if isinstance(interests, str):
        interests = [interests]

    parts = [
        str(profile.get("education_level") or ""),
        str(profile.get("institution") or ""),
        str(profile.get("age") or ""),
        str(profile.get("graduation_year") or ""),
        " ".join([str(item) for item in skills]),
        " ".join([str(item) for item in interests]),
    ]
    return " ".join([part for part in parts if part]).strip()


def _build_token_map(items: List[str]) -> Dict[str, str]:
    result: Dict[str, str] = {}
    for item in items:
        normalized = item.strip().lower()
        if not normalized:
            continue
        if normalized not in result:
            result[normalized] = item.strip()
    return result


def _split_tokens(value: str | None) -> List[str]:
    if not value:
        return []
    return [token.strip() for token in re.split(r"[,;/]", value) if token.strip()]


def _to_token_list(value: Any) -> List[str]:
    if isinstance(value, list):
        return [str(item).strip() for item in value if str(item).strip()]
    if isinstance(value, str):
        return _split_tokens(value)
    return []


def _extract_user_tokens(payload: Dict[str, Any]) -> Dict[str, Any]:
    skills = _to_token_list(payload.get("skills"))
    interests = _to_token_list(payload.get("interests"))
    return {
        "skill_map": _build_token_map(skills),
        "interest_map": _build_token_map(interests),
    }


def _match_user_tokens(user_map: Dict[str, str], career_tokens: Set[str]) -> List[str]:
    if not user_map or not career_tokens:
        return []
    matches: List[str] = []
    for normalized, raw in user_map.items():
        if normalized in career_tokens:
            matches.append(raw)
    return matches


def _build_prediction_summary(matched_skills: List[str], education_context: List[str]) -> str:
    parts: List[str] = []
    if matched_skills:
        parts.append(f"Matched skills: {', '.join(matched_skills[:4])}")
    if education_context:
        parts.append(f"Education context: {', '.join(education_context[:2])}")
    if not parts:
        return "Recommendation derived from your profile data."
    return " | ".join(parts)


def _build_prediction_details(label: Any, user_tokens: Dict[str, Any]) -> Dict[str, Any]:
    metadata = _load_career_metadata().get(str(label), {})
    matched_skills = _match_user_tokens(user_tokens.get("skill_map", {}), metadata.get("skills", set()))
    matched_interests = _match_user_tokens(user_tokens.get("interest_map", {}), metadata.get("interests", set()))
    education_context = list(metadata.get("education_display", []))
    summary = _build_prediction_summary(matched_skills, education_context)
    return {
        "matched_skills": matched_skills,
        "matched_interests": matched_interests,
        "education_match": education_context,
        "summary": summary,
    }


def _build_prediction_entry(label: Any, score: float, user_tokens: Dict[str, Any]) -> Dict[str, Any]:
    details = _build_prediction_details(label, user_tokens)
    return {
        "label": str(label),
        "score": round(float(score), 4),
        **details,
    }


def _load_career_metadata() -> Dict[str, Any]:
    global _CAREER_METADATA
    if _CAREER_METADATA is not None:
        return _CAREER_METADATA

    metadata: Dict[str, Any] = {}
    try:
        rows = load_rows(DEFAULT_DATASET)
    except (FileNotFoundError, ValueError):
        _CAREER_METADATA = metadata
        return _CAREER_METADATA

    for row in rows:
        label = str(row.get("label") or row.get("career") or row.get("career_title") or "").strip()
        if not label:
            continue

        entry = metadata.setdefault(label, {
            "skills": set(),
            "skills_display": set(),
            "interests": set(),
            "interests_display": set(),
            "education_display": set(),
        })

        for token in _split_tokens(row.get("skills")):
            normalized = token.lower()
            if not normalized:
                continue
            entry["skills"].add(normalized)
            entry["skills_display"].add(token)

        for token in _split_tokens(row.get("interests")):
            normalized = token.lower()
            if not normalized:
                continue
            entry["interests"].add(normalized)
            entry["interests_display"].add(token)

        education = (row.get("education") or "").strip()
        if education:
            entry["education_display"].add(education)

    for values in metadata.values():
        values["skills_display"] = sorted(values["skills_display"], key=str.lower)
        values["interests_display"] = sorted(values["interests_display"], key=str.lower)
        values["education_display"] = sorted(values["education_display"], key=str.lower)

    _CAREER_METADATA = metadata
    return _CAREER_METADATA


if __name__ == "__main__":
    # threaded=True so health + predict (and overlapping page loads) don't block
    # each other; debug=False avoids the reloader dropping in-flight requests.
    # Host/port come from env so Docker can bind 0.0.0.0 (reachable across the
    # container network); locally they default to 127.0.0.1:5001 as before.
    app.run(
        host=os.getenv("FLASK_RUN_HOST", "127.0.0.1"),
        port=int(os.getenv("FLASK_RUN_PORT", "5001")),
        threaded=True,
        debug=False,
    )
