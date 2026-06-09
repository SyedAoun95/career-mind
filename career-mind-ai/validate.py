"""K-Fold validation to estimate generalization."""

from __future__ import annotations

from pathlib import Path
from typing import List
import random

from sklearn.model_selection import StratifiedKFold
from sklearn import metrics

from train import (
    load_rows,
    build_text,
    drop_tokens,
    build_pipeline,
    DEFAULT_DATASET,
    DEFAULT_DROP_RANGE,
    RANDOM_SEED,
)


def main() -> None:
    rows = load_rows(Path(DEFAULT_DATASET))
    rng = random.Random(RANDOM_SEED)

    texts: List[str] = []
    labels: List[str] = []
    for row in rows:
        label = row.get("label") or row.get("career") or row.get("career_title")
        if not label:
            raise ValueError("Dataset must include a label/career column.")
        text = build_text(row, shuffle_fields=False, rng=rng)
        texts.append(text)
        labels.append(label)

    skf = StratifiedKFold(n_splits=5, shuffle=True, random_state=RANDOM_SEED)
    accuracies = []
    f1_scores = []

    for fold, (train_idx, test_idx) in enumerate(skf.split(texts, labels), start=1):
        train_texts = []
        train_labels = []
        fold_rng = random.Random(RANDOM_SEED + fold)

        for idx in train_idx:
            text = build_text(rows[idx], shuffle_fields=True, rng=fold_rng)
            drop_rate = fold_rng.uniform(*DEFAULT_DROP_RANGE)
            text = drop_tokens(text, drop_rate, fold_rng)
            train_texts.append(text)
            train_labels.append(labels[idx])

        test_texts = [texts[idx] for idx in test_idx]
        test_labels = [labels[idx] for idx in test_idx]

        pipeline = build_pipeline()
        pipeline.fit(train_texts, train_labels)
        preds = pipeline.predict(test_texts)

        accuracy = metrics.accuracy_score(test_labels, preds)
        macro_f1 = metrics.f1_score(test_labels, preds, average="macro", zero_division=0)

        accuracies.append(accuracy)
        f1_scores.append(macro_f1)

        print(f"Fold {fold} -> Accuracy: {accuracy:.4f}, Macro F1: {macro_f1:.4f}")

    mean_acc = sum(accuracies) / len(accuracies)
    mean_f1 = sum(f1_scores) / len(f1_scores)

    print("\nK-Fold Summary")
    print(f"Mean Accuracy: {mean_acc:.4f}")
    print(f"Mean Macro F1: {mean_f1:.4f}")


if __name__ == "__main__":
    main()
