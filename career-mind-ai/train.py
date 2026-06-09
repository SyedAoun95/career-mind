"""Train a TF-IDF + classifier model for career recommendations."""

from __future__ import annotations

import argparse
import csv
from pathlib import Path
from typing import List, Tuple, Dict, Any
from collections import Counter
import random

from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.linear_model import LogisticRegression
from sklearn.model_selection import train_test_split
from sklearn.pipeline import Pipeline
from sklearn import metrics
import joblib

DEFAULT_DATASET = Path(__file__).resolve().parent / "data" / "careers_extended.csv"
DEFAULT_MODEL_PATH = Path(__file__).resolve().parent / "models" / "career_model.pkl"

DEFAULT_DROP_RANGE = (0.1, 0.2)
RANDOM_SEED = 42


def load_rows(path: Path) -> List[Dict[str, str]]:
    if not path.exists():
        raise FileNotFoundError(f"Dataset not found: {path}")

    with path.open("r", encoding="utf-8") as handle:
        reader = csv.DictReader(handle)
        if reader.fieldnames is None:
            raise ValueError("Dataset must include a header row.")
        rows = [row for row in reader]

    if not rows:
        raise ValueError("Dataset is empty.")

    return rows


def build_text(row: Dict[str, str], shuffle_fields: bool, rng: random.Random) -> str:
    if row.get("text"):
        return row.get("text", "").strip()

    parts = [
        row.get("skills", ""),
        row.get("interests", ""),
        row.get("education", ""),
        row.get("tools", ""),
        row.get("experience_level", ""),
        row.get("title", ""),
        row.get("description", ""),
        row.get("required_skills", ""),
    ]
    parts = [part.strip() for part in parts if part and part.strip()]
    if shuffle_fields and parts:
        rng.shuffle(parts)
    return " ".join(parts)


def drop_tokens(text: str, drop_rate: float, rng: random.Random) -> str:
    tokens = text.split()
    if not tokens:
        return text

    keep = []
    for token in tokens:
        if rng.random() > drop_rate:
            keep.append(token)

    if not keep:
        keep = tokens[: max(1, len(tokens) // 2)]
    return " ".join(keep)


def load_dataset(path: Path) -> Tuple[List[str], List[str]]:
    rows = load_rows(path)
    texts: List[str] = []
    labels: List[str] = []
    rng = random.Random(RANDOM_SEED)

    for row in rows:
        label = row.get("label") or row.get("career") or row.get("career_title")
        if not label:
            raise ValueError("Dataset must include a label/career column.")

        text = build_text(row, shuffle_fields=False, rng=rng)
        texts.append(text)
        labels.append(label)

    return texts, labels


def build_pipeline() -> Pipeline:
    return Pipeline(
        [
            (
                "tfidf",
                TfidfVectorizer(
                    stop_words="english",
                    ngram_range=(1, 2),
                    min_df=2,
                    max_features=8000,
                ),
            ),
            (
                "clf",
                LogisticRegression(max_iter=1000, multi_class="auto"),
            ),
        ]
    )


def run_training(dataset_path: Path, model_path: Path = DEFAULT_MODEL_PATH) -> Dict[str, Any]:
    rows = load_rows(dataset_path)
    rng = random.Random(RANDOM_SEED)

    labels = []
    texts = []
    for row in rows:
        label = row.get("label") or row.get("career") or row.get("career_title")
        if not label:
            raise ValueError("Dataset must include a label/career column.")

        text = build_text(row, shuffle_fields=True, rng=rng)
        drop_rate = rng.uniform(*DEFAULT_DROP_RANGE)
        text = drop_tokens(text, drop_rate, rng)

        texts.append(text)
        labels.append(label)

    label_counts = Counter(labels)
    print(f"Dataset size: {len(labels)}")
    print("Class distribution:")
    for label, count in label_counts.items():
        print(f"  {label}: {count}")

    can_stratify = len(set(labels)) > 1 and min(label_counts.values()) >= 2

    X_train, X_test, y_train, y_test = train_test_split(
        texts,
        labels,
        test_size=0.25,
        random_state=42,
        stratify=labels if can_stratify else None,
    )

    pipeline = build_pipeline()
    pipeline.fit(X_train, y_train)

    predictions = pipeline.predict(X_test)
    accuracy = metrics.accuracy_score(y_test, predictions)
    report = metrics.classification_report(y_test, predictions, zero_division=0)

    model_path.parent.mkdir(parents=True, exist_ok=True)
    joblib.dump(pipeline, model_path)

    run_sanity_check(pipeline)

    return {
        "accuracy": accuracy,
        "report": report,
        "model_path": str(model_path),
        "train_size": len(X_train),
        "test_size": len(X_test),
    }


def run_sanity_check(pipeline: Pipeline) -> None:
    samples = [
        "built a small flask api, used mysql, and deployed on ubuntu server",
        "created seo content plans, ran ads, tracked conversions in google analytics",
        "monitored splunk alerts, investigated phishing, wrote incident reports",
        "built dashboards in power bi, cleaned data with sql and excel",
        "designed mobile onboarding flows in figma, ran usability interviews",
    ]

    print("Sanity check predictions:")
    for text in samples:
        prediction = pipeline.predict([text])[0]
        confidence = None
        if hasattr(pipeline, "predict_proba"):
            proba = pipeline.predict_proba([text])[0]
            confidence = max(proba)
        if confidence is not None:
            print(f"  {prediction} (confidence: {confidence:.2f}) :: {text}")
        else:
            print(f"  {prediction} :: {text}")


def main() -> None:
    parser = argparse.ArgumentParser(description="Train Career Mind AI model")
    parser.add_argument(
        "--dataset",
        type=Path,
        default=DEFAULT_DATASET,
        help="Path to labeled CSV dataset",
    )
    parser.add_argument(
        "--model",
        type=Path,
        default=DEFAULT_MODEL_PATH,
        help="Path to save model artifact",
    )
    args = parser.parse_args()

    results = run_training(args.dataset, args.model)
    print("Training complete")
    print(f"Model saved to: {results['model_path']}")
    print(f"Accuracy: {results['accuracy']:.4f}")
    print(results["report"])


if __name__ == "__main__":
    main()
