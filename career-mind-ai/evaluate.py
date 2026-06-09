"""Evaluate a trained Career Mind AI model."""

from __future__ import annotations

import argparse
from pathlib import Path

import joblib
from sklearn.model_selection import train_test_split
from sklearn import metrics

from train import load_dataset, DEFAULT_DATASET, DEFAULT_MODEL_PATH


def main() -> None:
    parser = argparse.ArgumentParser(description="Evaluate Career Mind AI model")
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
        help="Path to saved model artifact",
    )
    args = parser.parse_args()

    texts, labels = load_dataset(args.dataset)
    X_train, X_test, y_train, y_test = train_test_split(
        texts,
        labels,
        test_size=0.2,
        random_state=42,
        stratify=labels if len(set(labels)) > 1 else None,
    )

    pipeline = joblib.load(args.model)
    predictions = pipeline.predict(X_test)

    accuracy = metrics.accuracy_score(y_test, predictions)
    macro_f1 = metrics.f1_score(y_test, predictions, average="macro", zero_division=0)
    report = metrics.classification_report(y_test, predictions, zero_division=0)
    matrix = metrics.confusion_matrix(y_test, predictions)

    print("Evaluation complete")
    print(f"Accuracy: {accuracy:.4f}")
    print(f"Macro F1-score: {macro_f1:.4f}")
    print(report)
    print("Confusion Matrix:")
    print(matrix)


if __name__ == "__main__":
    main()
