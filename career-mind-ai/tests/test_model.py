"""Basic model training and prediction test."""

from __future__ import annotations

import tempfile
from pathlib import Path
import unittest

import joblib

from train import run_training, load_dataset


class ModelTrainingTest(unittest.TestCase):
    def test_model_training_and_prediction(self) -> None:
        dataset_path = Path(__file__).resolve().parents[1] / "data" / "sample_careers.csv"
        with tempfile.TemporaryDirectory() as temp_dir:
            model_path = Path(temp_dir) / "career_model.pkl"
            results = run_training(dataset_path, model_path)

            self.assertTrue(model_path.exists())
            self.assertGreaterEqual(results["accuracy"], 0.0)

            pipeline = joblib.load(model_path)
            texts, _ = load_dataset(dataset_path)
            prediction = pipeline.predict([texts[0]])
            self.assertEqual(len(prediction), 1)


if __name__ == "__main__":
    unittest.main()
