"""Career/job recommendation helpers."""

from __future__ import annotations

from typing import Any, Dict, List, Set, Tuple
import re

import mysql.connector
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity


SCHEMA_VERSION = 1
MODEL_VERSION = "tfidf_v1"


def get_mock_recommendations() -> Dict[str, Any]:
    return {
        "schema_version": SCHEMA_VERSION,
        "model_version": "mock",
        "career_recommendations": [
            {"title": "Software Developer", "reason": "Mock Phase 1 output", "score": 0.0}
        ],
        "job_recommendations": [
            {"title": "Junior Backend Developer", "reason": "Mock Phase 1 output", "score": 0.0}
        ],
        "warnings": ["AI model unavailable. Using mock recommendations."],
    }


def _split_skills(value: str | None) -> List[str]:
    if not value:
        return []
    return [token.strip() for token in re.split(r"[,;/]", value) if token.strip()]


def _normalize_tokens(items: List[str]) -> Set[str]:
    return {item.strip().lower() for item in items if item.strip()}


def _extract_user_skills(payload: Dict[str, Any]) -> Set[str]:
    skills = payload.get("skills") or []
    interests = payload.get("interests") or []

    if isinstance(skills, str):
        skills = _split_skills(skills)
    if isinstance(interests, str):
        interests = _split_skills(interests)

    combined = [str(item) for item in skills + interests]
    return _normalize_tokens(combined)


def _get_profile(payload: Dict[str, Any]) -> Dict[str, Any]:
    return payload.get("profile") or {}


def _eligible_job(job: Dict[str, Any], profile: Dict[str, Any]) -> bool:
    age_value = profile.get("age")
    education_level = str(profile.get("education_level") or "").lower()
    level = str(job.get("level") or "").lower()

    entry_keywords = ["entry", "intern", "junior", "trainee"]
    senior_keywords = ["senior", "lead", "manager", "principal", "mid"]

    if age_value:
        try:
            age = int(age_value)
        except (TypeError, ValueError):
            age = None
        if age is not None and age < 18:
            return any(keyword in level for keyword in entry_keywords) or level == ""

    if "matric" in education_level or "intermediate" in education_level:
        if any(keyword in level for keyword in senior_keywords):
            return False

    return True


def _apply_job_filters(jobs: List[Dict[str, Any]], filters: Dict[str, Any]) -> List[Dict[str, Any]]:
    if not filters:
        return jobs

    job_level = str(filters.get("job_level") or "").lower().strip()
    job_type = str(filters.get("job_type") or "").lower().strip()

    if not job_level and not job_type:
        return jobs

    filtered = []
    for job in jobs:
        level = str(job.get("level") or "").lower()
        location = str(job.get("location") or "").lower()

        if job_level and job_level not in level:
            continue

        if job_type:
            if job_type == "remote" and "remote" not in location:
                continue
            if job_type == "onsite" and ("remote" in location or "hybrid" in location):
                continue
            if job_type == "hybrid" and "hybrid" not in location:
                continue

        filtered.append(job)

    return filtered


def _build_user_text(payload: Dict[str, Any], user_skills: Set[str]) -> str:
    profile = payload.get("profile") or {}
    profile_parts = [
        str(profile.get("education_level") or ""),
        str(profile.get("institution") or ""),
        str(profile.get("age") or ""),
        str(profile.get("graduation_year") or ""),
    ]
    skill_text = " ".join(sorted(user_skills))
    return " ".join(profile_parts + [skill_text]).strip()


def _score_item(required_skills: Set[str], user_skills: Set[str]) -> int:
    if not required_skills or not user_skills:
        return 0
    return len(required_skills.intersection(user_skills))


def _build_reason(required_skills: Set[str], user_skills: Set[str]) -> str:
    matches = list(required_skills.intersection(user_skills))
    if matches:
        sample = ", ".join(sorted(matches)[:4])
        return f"Matched skills: {sample}"
    return "Based on your profile and interests."


def _fetch_careers(conn) -> List[Dict[str, Any]]:
    cursor = conn.cursor(dictionary=True)
    cursor.execute("SELECT id, title, description, required_skills FROM careers")
    return cursor.fetchall() or []


def _fetch_jobs(conn) -> List[Dict[str, Any]]:
    cursor = conn.cursor(dictionary=True)
    cursor.execute(
        "SELECT jobs.id, jobs.title, jobs.level, jobs.location, jobs.required_skills, "
        "jobs.career_id, careers.title AS career_title "
        "FROM jobs LEFT JOIN careers ON jobs.career_id = careers.id"
    )
    return cursor.fetchall() or []


def _career_text(career: Dict[str, Any]) -> str:
    return " ".join(
        [
            str(career.get("title") or ""),
            str(career.get("description") or ""),
            str(career.get("required_skills") or ""),
        ]
    ).strip()


def _job_text(job: Dict[str, Any]) -> str:
    return " ".join(
        [
            str(job.get("title") or ""),
            str(job.get("level") or ""),
            str(job.get("location") or ""),
            str(job.get("required_skills") or ""),
            str(job.get("career_title") or ""),
        ]
    ).strip()


def _rank_items(items: List[Dict[str, Any]], text_builder, user_text: str) -> List[Tuple[float, Dict[str, Any]]]:
    if not items or not user_text:
        return []

    corpus = [text_builder(item) for item in items]
    if not any(corpus):
        return []

    vectorizer = TfidfVectorizer(stop_words="english", ngram_range=(1, 2), max_features=2000)

    try:
        matrix = vectorizer.fit_transform(corpus + [user_text])
    except ValueError:
        return []

    item_vectors = matrix[:-1]
    user_vector = matrix[-1]
    similarities = cosine_similarity(item_vectors, user_vector)

    ranked = []
    for idx, score in enumerate(similarities.flatten().tolist()):
        ranked.append((float(score), items[idx]))

    ranked.sort(key=lambda item: item[0], reverse=True)
    return ranked


def get_recommendations(payload: Dict[str, Any], db_config: Dict[str, Any]) -> Dict[str, Any]:
    user_skills = _extract_user_skills(payload)
    profile = _get_profile(payload)
    user_text = _build_user_text(payload, user_skills)
    filters = payload.get("filters") or {}

    try:
        conn = mysql.connector.connect(
            host=db_config.get("host"),
            port=db_config.get("port"),
            user=db_config.get("user"),
            password=db_config.get("password"),
            database=db_config.get("database"),
        )
    except mysql.connector.Error:
        return get_mock_recommendations()

    careers = _fetch_careers(conn)
    jobs = _fetch_jobs(conn)
    if profile:
        jobs = [job for job in jobs if _eligible_job(job, profile)]
    jobs = _apply_job_filters(jobs, filters)
    conn.close()

    if not careers and not jobs:
        return get_mock_recommendations()

    scored_careers = _rank_items(careers, _career_text, user_text)
    if not scored_careers:
        scored_careers = []
        for career in careers:
            required = _normalize_tokens(_split_skills(career.get("required_skills")))
            score = _score_item(required, user_skills)
            scored_careers.append((float(score), career))
        scored_careers.sort(key=lambda item: item[0], reverse=True)

    top_careers = scored_careers[:5] if scored_careers else []
    top_career_ids = {item[1]["id"] for item in top_careers if item[1].get("id")}

    career_recommendations = []
    for score, career in top_careers:
        required = _normalize_tokens(_split_skills(career.get("required_skills")))
        title = career.get("title") or "Career Path"
        if title:
            career_recommendations.append(
                {
                    "title": title,
                    "reason": _build_reason(required, user_skills),
                    "score": round(score, 4),
                }
            )

    if not career_recommendations:
        career_recommendations = get_mock_recommendations()["career_recommendations"]

    scored_jobs = _rank_items(jobs, _job_text, user_text)
    if not scored_jobs:
        scored_jobs = []
        for job in jobs:
            required = _normalize_tokens(_split_skills(job.get("required_skills")))
            score = _score_item(required, user_skills)
            if job.get("career_id") in top_career_ids:
                score += 2
            scored_jobs.append((float(score), job))
        scored_jobs.sort(key=lambda item: item[0], reverse=True)

    top_jobs = scored_jobs[:5] if scored_jobs else []

    job_recommendations = []
    for score, job in top_jobs:
        required = _normalize_tokens(_split_skills(job.get("required_skills")))
        title = job.get("title") or "Job Role"
        if title:
            job_recommendations.append(
                {
                    "title": title,
                    "reason": _build_reason(required, user_skills),
                    "score": round(score, 4),
                }
            )

    if not job_recommendations:
        job_recommendations = get_mock_recommendations()["job_recommendations"]

    return {
        "schema_version": SCHEMA_VERSION,
        "model_version": MODEL_VERSION,
        "career_recommendations": career_recommendations,
        "job_recommendations": job_recommendations,
        "warnings": [],
    }
