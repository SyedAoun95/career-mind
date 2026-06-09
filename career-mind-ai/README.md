# Career Mind AI Service (Phase 3)

This is the Flask microservice layer. Phase 3 adds a trainable scikit-learn TF-IDF classifier pipeline for career predictions and keeps response schema versioning for existing endpoints.

## Endpoints
- GET /health
- GET /recommendations/mock
- POST /recommendations
- POST /cv/analyze
- POST /train
- POST /predict

## Scope (Phase 3)
- Trainable TF-IDF + Logistic Regression pipeline for career prediction
- Response schema versioning via `schema_version`
- CV parsing with skill extraction

## Response Schema Versioning
All AI responses include:
- `schema_version` (integer)
- `model_version` (string)

The PHP layer validates `schema_version` and shows an error if it differs from the expected version.

## Dataset Format
CSV columns (minimum):
- `career` or `label` (target class)
- `title`, `description`, `required_skills` (used as text features)

Extended schema (recommended):
- `skills`, `interests`, `education`, `tools`, `experience_level`, `career`

Example: see data/sample_careers.csv

## Training
1. Train a model:
	 - `python train.py --dataset data/sample_careers.csv`
2. Model artifact saved to models/career_model.pkl

## Evaluation
Run evaluation against a dataset:
- `python evaluate.py --dataset data/sample_careers.csv`

## Validation (K-Fold)
Run k-fold validation to estimate generalization:
- `python validate.py`

## Why 100% Accuracy Is Misleading
On small, clean datasets, TF-IDF classifiers can memorize patterns and show perfect scores. That does not mean the model generalizes. This project applies:
- K-Fold cross validation
- Token drop augmentation and shuffled field order
- A sanity check on messy real-world descriptions

These steps reduce overfitting and provide a more realistic estimate of performance.

## Prototype Limitations
- Dataset is synthetic and limited in size
- Labels are coarse-grained (career categories)
- No production-grade monitoring or drift handling

## Prediction API
POST /predict with JSON:
```json
{
	"text": "python flask api sql",
	"profile": {"education_level": "BSCS"},
	"skills": ["python", "flask"],
	"interests": ["backend"]
}
```

### Prediction response
```
HTTP/1.1 200 OK
{
	"schema_version": 1,
	"model_version": "tfidf_v1",
	"prediction": "Software Developer",
	"top_predictions": [
		{
			"label": "Software Developer",
			"score": 0.7236,
			"matched_skills": ["python", "sql"],
			"matched_interests": ["backend systems"],
			"education_match": ["BS Computer Science"],
			"summary": "Matched skills: python, sql | Education context: BS Computer Science"
		},
		{
			"label": "AI Engineer",
			"score": 0.1521,
			"matched_skills": ["python"],
			"matched_interests": [],
			"education_match": ["BS Data Science"],
			"summary": "Matched skills: python | Education context: BS Data Science"
		}
	]
}
```

Each item in `top_predictions` includes the normalized label, its confidence `score`, the subset of user skills/interests that matched the career bucket, the education context pulled from the dataset, and a short `summary` string that the dashboard can surface to explain the suggestion.

## Training API (dev only)
POST /train with JSON:
```json
{
	"dataset_path": "data/sample_careers.csv",
	"model_path": "models/career_model.pkl"
}
```
