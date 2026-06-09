"""
CV parsing and analysis using lightweight NLP techniques.
Extracts text from PDF/DOCX and matches against a curated skills catalog.

Matching is done against data/skill_catalog.json — a clean, curated list of
in-demand skills derived from the careers dataset — NOT the `skills` database
table (which is polluted with raw, user-entered profile text). The DB catalog
is only used as an optional supplement after being cleaned.
"""

from __future__ import annotations

from typing import Any, Dict, List
import json
import os
import re

import mysql.connector
import pdfplumber
from docx import Document


SCHEMA_VERSION = 1
MODEL_VERSION = "nlp_v1"

_CATALOG_PATH = os.path.join(os.path.dirname(__file__), "..", "data", "skill_catalog.json")

# Strong, résumé-specific markers. Business proposals / contracts / essays rarely
# contain these, so any one of them is enough to treat the document as a CV.
_CV_STRONG_MARKERS = (
    "education",
    "work experience",
    "professional experience",
    "employment history",
    "work history",
    "curriculum vitae",
    "resume",
)

# Generic section words that appear in CVs but also in other documents, so they
# only count as a CV signal alongside contact details.
_CV_WEAK_MARKERS = (
    "experience",
    "skills",
    "projects",
    "summary",
    "objective",
    "certification",
    "references",
)

_EMAIL_RE = re.compile(r"[\w.+-]+@[\w-]+\.[\w.-]+")
_PHONE_RE = re.compile(r"\+?\d[\d\s().-]{7,}\d")


def analyze_cv(file_path: str, filename: str | None, db_config: Dict[str, Any]) -> Dict[str, Any]:
    text = _extract_text(file_path)
    if not text.strip():
        return _empty_analysis(filename, "No readable text extracted from the CV.")

    # Reject documents that don't look like a CV at all.
    if not _looks_like_cv(text):
        result = _empty_analysis(
            filename,
            "This file does not look like a CV/résumé. Please upload an actual resume "
            "with sections like Experience, Education, and Skills.",
        )
        result["score"] = 0
        result["is_cv"] = False
        return result

    skills_catalog = _load_catalog(db_config)
    extracted_skills = _match_skills(text, skills_catalog)
    missing_skills = _suggest_missing(skills_catalog, extracted_skills)

    feedback = _build_feedback(text, extracted_skills)
    score, breakdown = _score_cv(text, extracted_skills)

    return {
        "schema_version": SCHEMA_VERSION,
        "model_version": MODEL_VERSION,
        "summary": "CV parsed successfully.",
        "missing_skills": missing_skills,
        "feedback": feedback,
        "score": score,
        "score_breakdown": breakdown,
        "file": filename or os.path.basename(file_path),
        "analysis_method": "pdf_docx_nlp",
        "extracted_skills": extracted_skills,
        "is_cv": True,
    }


def _empty_analysis(filename: str | None, message: str) -> Dict[str, Any]:
    return {
        "schema_version": SCHEMA_VERSION,
        "model_version": MODEL_VERSION,
        "summary": message,
        "missing_skills": [],
        "feedback": [
            "Ensure the CV is text-based (not scanned images).",
            "Use standard headings like Skills, Experience, Education.",
        ],
        "score": 0,
        "file": filename or "unknown",
        "analysis_method": "pdf_docx_nlp",
        "extracted_skills": [],
    }


def _looks_like_cv(text: str) -> bool:
    """
    Treat the document as a CV if it has a strong résumé-specific marker
    (e.g. an Education section), or contact details paired with at least two
    generic résumé sections. This rejects proposals/contracts/essays that merely
    mention words like "experience" or "summary".
    """
    lowered = text.lower()

    if any(marker in lowered for marker in _CV_STRONG_MARKERS):
        return True

    has_contact = bool(_EMAIL_RE.search(text)) or bool(_PHONE_RE.search(text))
    weak_hits = sum(1 for word in _CV_WEAK_MARKERS if word in lowered)
    return has_contact and weak_hits >= 2


def _extract_text(file_path: str) -> str:
    extension = os.path.splitext(file_path)[1].lower()
    if extension == ".pdf":
        return _extract_pdf_text(file_path)
    if extension in {".doc", ".docx"}:
        return _extract_docx_text(file_path)
    return ""


def _extract_pdf_text(file_path: str) -> str:
    text_parts: List[str] = []
    try:
        with pdfplumber.open(file_path) as pdf:
            for page in pdf.pages:
                page_text = page.extract_text() or ""
                if page_text:
                    text_parts.append(page_text)
    except Exception:
        return ""
    return "\n".join(text_parts)


def _extract_docx_text(file_path: str) -> str:
    try:
        doc = Document(file_path)
    except Exception:
        return ""
    return "\n".join([paragraph.text for paragraph in doc.paragraphs if paragraph.text])


def _load_catalog(db_config: Dict[str, Any]) -> List[str]:
    """
    Curated skill catalog (from data/skill_catalog.json), optionally supplemented
    with *cleaned* skills from the DB. Ordering is preserved: catalog skills come
    first (most in-demand first), so "missing skill" suggestions stay relevant.
    """
    catalog = _load_catalog_file()
    seen = {s.lower() for s in catalog}

    for skill in _fetch_skills(db_config):
        cleaned = _clean_skill(skill)
        if cleaned and cleaned.lower() not in seen:
            catalog.append(cleaned)
            seen.add(cleaned.lower())

    return catalog


def _load_catalog_file() -> List[str]:
    try:
        with open(_CATALOG_PATH, encoding="utf-8") as f:
            data = json.load(f)
    except (OSError, ValueError):
        return []
    return [str(s) for s in data if str(s).strip()]


def _clean_skill(value: str) -> str:
    """Strip stray quotes/whitespace from a DB skill so junk rows don't pollute matching."""
    cleaned = value.strip().strip('"').strip("'").strip()
    # Drop obvious non-skills (empty, single chars, sentinel values).
    if len(cleaned) < 2 or cleaned.lower() in {"noskills", "none", "n/a"}:
        return ""
    return cleaned


def _fetch_skills(db_config: Dict[str, Any]) -> List[str]:
    try:
        conn = mysql.connector.connect(
            host=db_config.get("host"),
            port=db_config.get("port"),
            user=db_config.get("user"),
            password=db_config.get("password"),
            database=db_config.get("database"),
        )
    except mysql.connector.Error:
        return []

    cursor = conn.cursor()
    cursor.execute("SELECT skill_name FROM skills")
    rows = cursor.fetchall() or []
    conn.close()
    return [row[0] for row in rows if row and row[0]]


def _match_skills(text: str, skills_catalog: List[str]) -> List[str]:
    if not skills_catalog:
        return []

    normalized_text = _normalize_text(text)
    matches: List[str] = []

    for skill in skills_catalog:
        normalized_skill = _normalize_text(skill)
        if not normalized_skill:
            continue
        # Boundary on alphanumerics so symbols like c++, c#, .net, ci/cd match
        # correctly without \b (which breaks on non-word characters).
        pattern = r"(?<![a-z0-9])" + re.escape(normalized_skill) + r"(?![a-z0-9])"
        if re.search(pattern, normalized_text):
            matches.append(skill)

    # Preserve catalog order (in-demand first) while de-duplicating.
    seen = set()
    ordered: List[str] = []
    for skill in matches:
        key = skill.lower()
        if key not in seen:
            seen.add(key)
            ordered.append(skill)
    return ordered


def _suggest_missing(skills_catalog: List[str], extracted_skills: List[str], limit: int = 5) -> List[str]:
    """
    Suggest the most in-demand catalog skills the CV is missing. The catalog is
    ordered by demand, so this returns popular, relevant skills — not arbitrary
    leftover rows.
    """
    if not skills_catalog:
        return []
    extracted_set = {skill.lower() for skill in extracted_skills}
    missing = [skill for skill in skills_catalog if skill.lower() not in extracted_set]
    return missing[:limit]


def _build_feedback(text: str, extracted_skills: List[str]) -> List[str]:
    feedback = []
    word_count = len(text.split())

    if word_count < 150:
        feedback.append("Add more detail about projects and responsibilities.")
    if not extracted_skills:
        feedback.append("Include a dedicated Skills section for better matching.")
    if "experience" not in text.lower():
        feedback.append("Add an Experience section with measurable achievements.")

    if not feedback:
        feedback.append("Your CV structure looks strong. Keep refining with clear metrics.")

    return feedback


# Section headings grouped by the résumé area they represent. A CV gets credit
# once per area, so synonyms don't double-count.
_SECTION_GROUPS = {
    "summary": ("summary", "objective", "profile", "about me"),
    "experience": ("experience", "employment", "work history", "professional background"),
    "education": ("education", "academic", "qualification"),
    "skills": ("skills", "technical skills", "competencies", "expertise"),
    "projects": ("projects", "portfolio"),
    "certifications": ("certification", "certificate", "awards", "achievements", "accomplishments"),
}

_URL_RE = re.compile(r"(linkedin\.com|github\.com|gitlab\.com|behance\.net|dribbble\.com|https?://)", re.I)
# Quantified impact: percentages, currency amounts, or numbers tied to outcomes.
_METRIC_RES = (
    re.compile(r"\b\d+(?:\.\d+)?\s?%"),
    re.compile(r"[$£€]\s?\d"),
    re.compile(r"\b\d[\d,]*\+?\s*(?:users|customers|clients|projects|people|hours|days|x)\b", re.I),
    re.compile(r"\b(?:increased|reduced|improved|grew|saved|boosted|cut|raised|decreased)\b[^.\n]{0,40}\b\d", re.I),
)


def _score_cv(text: str, extracted_skills: List[str]) -> tuple[int, Dict[str, int]]:
    """
    Realistic, multi-dimensional CV score out of 100. Each dimension is partial,
    so a typical CV lands in the 55–85 range and only a complete, quantified,
    well-rounded CV approaches 100. Returns (score, per-dimension breakdown).
    """
    lowered = text.lower()
    word_count = len(text.split())

    # 1. Section completeness (max 30) — 5 points per résumé area present.
    sections_present = sum(
        1 for keywords in _SECTION_GROUPS.values() if any(k in lowered for k in keywords)
    )
    section_score = min(30, sections_present * 5)

    # 2. Skills depth (max 25) — distinct in-demand skills matched.
    skills_score = min(25, round(len(extracted_skills) * 2.5))

    # 3. Contact & links (max 15) — email, phone, professional URL.
    contact_score = 0
    if _EMAIL_RE.search(text):
        contact_score += 6
    if _PHONE_RE.search(text):
        contact_score += 5
    if _URL_RE.search(text):
        contact_score += 4

    # 4. Quantified impact (max 15) — metrics signal strong, results-driven CVs.
    metric_hits = sum(len(rgx.findall(text)) for rgx in _METRIC_RES)
    impact_score = min(15, metric_hits * 3)

    # 5. Length fit (max 15) — too short under-sells; too long is unfocused.
    if word_count < 150:
        length_score = 3
    elif word_count < 250:
        length_score = 7
    elif word_count < 350:
        length_score = 11
    elif word_count <= 900:
        length_score = 15
    elif word_count <= 1500:
        length_score = 12
    else:
        length_score = 8

    breakdown = {
        "sections": section_score,
        "skills": int(skills_score),
        "contact": contact_score,
        "impact": impact_score,
        "length": length_score,
    }
    total = min(100, sum(breakdown.values()))
    return total, breakdown


def _normalize_text(value: str) -> str:
    return re.sub(r"\s+", " ", value.strip().lower())
