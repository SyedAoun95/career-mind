# Bulk-import CSV formats (Careers, Jobs, Skills)

These CSVs feed the **Career & Job Recommendations** catalog (the `careers` and
`jobs` DB tables). The recommender ranks the *entire* catalog against each user's
skills and shows the top 5 — so the more rows you add, the richer and more
relevant the recommendations. No model retraining is required.

## How to upload

1. Log in as admin → **Datasets** page (`/admin/datasets`).
2. Pick the dataset type (Careers / Jobs / Skills), choose your CSV, upload.
3. **Order matters for jobs:** import **careers first**, then **jobs** — jobs link
   to careers by `career_title`, which must already exist.

Column names are matched case-insensitively by header. Extra columns are ignored.

---

## 1. Careers CSV  (dataset type: `careers`)

| Column            | Required | Notes                                  |
|-------------------|----------|----------------------------------------|
| `title`           | ✅ yes   | Career name, e.g. `Shopify Developer`  |
| `description`     | optional | One-line description                   |
| `required_skills` | optional | Comma-separated skills (quote the cell)|

```csv
title,description,required_skills
Shopify Developer,Builds and customises Shopify stores,"Shopify, Liquid, JavaScript, HTML, CSS"
Django Developer,Builds backend web apps with Django,"Python, Django, PostgreSQL, Git"
```

## 2. Jobs CSV  (dataset type: `jobs`)

| Column            | Required | Notes                                                        |
|-------------------|----------|--------------------------------------------------------------|
| `title`           | ✅ yes   | Job role, e.g. `Junior Shopify Developer`                    |
| `level`           | optional | `Intern` / `Entry` / `Junior` / `Mid` / `Senior`             |
| `location`        | optional | e.g. `Remote`, `Lahore`                                      |
| `required_skills` | optional | Comma-separated skills (quote the cell)                      |
| `career_title`    | optional | Links the job to a career by name (must already exist)       |
| `career_id`       | optional | Alternative to `career_title` — link by numeric career id    |

```csv
title,level,location,required_skills,career_title
Junior Shopify Developer,Entry,Remote,"Shopify, Liquid, HTML, CSS",Shopify Developer
Backend Django Developer,Junior,Remote,"Python, Django, PostgreSQL",Django Developer
```

## 3. Skills CSV  (dataset type: `skills`)

| Column                | Required | Notes                          |
|-----------------------|----------|--------------------------------|
| `skill_name` (or `name`) | ✅ yes | One skill per row              |

```csv
skill_name
Shopify
WordPress
React
```

---

## Notes / gotchas

- **`required_skills` should overlap with the skills catalog** — recommendations
  are matched on skill text, so use the same wording as the skills you seeded.
- **Imports append; they do not de-duplicate.** Uploading the same file twice
  creates duplicate rows. To start clean, clear the tables first
  (`DELETE FROM jobs; DELETE FROM careers;`) before a fresh import.
- The **ML "Career Prediction"** card is a *separate* fixed 6-class model trained
  on `career-mind-ai/data/careers_extended.csv`. Growing this catalog does **not**
  change those predictions — it only enriches the recommendations panel.
