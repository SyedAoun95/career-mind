"""
Generate the Career Mind catalog + ML training dataset.

Emits three CSVs in this directory:
  - careers_catalog.csv      -> title, description, required_skills        (for DB `careers`)
  - jobs_catalog.csv         -> title, level, location, required_skills, career_title  (for DB `jobs`)
  - careers_extended.csv     -> skills, interests, education, tools, experience_level, career  (ML training)

~150 careers across domains, each with a curated skill set. Re-run any time:
    python generate_catalog.py
Deterministic (fixed seed) so output is stable.
"""

from __future__ import annotations
import csv
import os
import random

HERE = os.path.dirname(__file__)
RNG = random.Random(42)

# ── Per-domain context (interests / education / tools / job levels) ──
DOMAINS = {
    "frontend":   dict(desc="modern, responsive web user interfaces",
                       interests=["web development", "user interfaces", "design systems"],
                       education=["BS Computer Science", "BS Software Engineering"],
                       tools=["VS Code", "Git", "Figma", "Webpack", "npm"]),
    "backend":    dict(desc="server-side application logic and APIs",
                       interests=["backend systems", "scalability", "api design"],
                       education=["BS Computer Science", "BS Software Engineering"],
                       tools=["Git", "Docker", "Postman", "PostgreSQL", "Redis"]),
    "fullstack":  dict(desc="full end-to-end web applications",
                       interests=["web development", "product building", "automation"],
                       education=["BS Computer Science", "BS Software Engineering"],
                       tools=["Git", "Docker", "VS Code", "npm", "Postman"]),
    "mobile":     dict(desc="native and cross-platform mobile apps",
                       interests=["mobile apps", "user experience", "product building"],
                       education=["BS Computer Science", "BS Software Engineering"],
                       tools=["Git", "Android Studio", "Xcode", "Firebase"]),
    "data":       dict(desc="data pipelines, analysis and insights",
                       interests=["data analysis", "statistics", "business insights"],
                       education=["BS Data Science", "BS Statistics", "BS Computer Science"],
                       tools=["Jupyter", "SQL", "Excel", "Power BI", "Tableau"]),
    "ai":         dict(desc="machine-learning and AI systems",
                       interests=["machine learning", "research", "automation"],
                       education=["BS Data Science", "BS Computer Science", "MS Artificial Intelligence"],
                       tools=["Jupyter", "PyTorch", "TensorFlow", "sklearn", "Git"]),
    "cloud":      dict(desc="cloud infrastructure and deployment automation",
                       interests=["cloud", "automation", "reliability"],
                       education=["BS Computer Science", "BS Software Engineering"],
                       tools=["AWS", "Docker", "Kubernetes", "Terraform", "Git"]),
    "security":   dict(desc="securing systems, networks and applications",
                       interests=["security", "networking", "threat analysis"],
                       education=["BS Cyber Security", "BS Information Security", "BS Computer Science"],
                       tools=["Splunk", "Burp Suite", "Wireshark", "Kali Linux"]),
    "design":     dict(desc="user-centred product and visual design",
                       interests=["design", "user experience", "creativity"],
                       education=["BS Design", "BFA", "BS Human Computer Interaction"],
                       tools=["Figma", "Adobe XD", "Photoshop", "Illustrator"]),
    "marketing":  dict(desc="digital marketing and growth",
                       interests=["marketing", "growth", "content", "branding"],
                       education=["BS Marketing", "BBA", "BS Business"],
                       tools=["Google Analytics", "HubSpot", "Meta Ads", "Mailchimp", "Canva"]),
    "product":    dict(desc="product strategy, delivery and operations",
                       interests=["product management", "strategy", "collaboration"],
                       education=["BBA", "BS Business", "BS Computer Science"],
                       tools=["Jira", "Notion", "Miro", "Confluence"]),
    "qa":         dict(desc="software quality assurance and test automation",
                       interests=["quality", "automation", "problem solving"],
                       education=["BS Computer Science", "BS Software Engineering"],
                       tools=["Selenium", "Jira", "Postman", "Git"]),
    "infra":      dict(desc="databases, networks and systems",
                       interests=["systems", "networking", "reliability"],
                       education=["BS Computer Science", "BS Information Technology"],
                       tools=["Linux", "SQL", "Bash", "Git"]),
    "emerging":   dict(desc="emerging and specialised technology",
                       interests=["research", "innovation", "automation"],
                       education=["BS Computer Science", "MS Artificial Intelligence"],
                       tools=["Git", "Python", "Docker", "Jupyter"]),
}

LOCATIONS = ["Remote", "Hybrid", "Lahore", "Karachi", "Islamabad", "On-site"]
LEVELS = ["Intern", "Entry", "Junior", "Mid", "Senior"]

# ── Careers: (title, domain, [skills]) ──
ROLES = [
    # frontend
    ("React Developer", "frontend", ["React", "JavaScript", "TypeScript", "HTML", "CSS", "REST API"]),
    ("Angular Developer", "frontend", ["Angular", "TypeScript", "RxJS", "HTML", "CSS"]),
    ("Vue.js Developer", "frontend", ["Vue.js", "JavaScript", "HTML", "CSS", "REST API"]),
    ("Frontend Developer", "frontend", ["HTML", "CSS", "JavaScript", "React", "Bootstrap"]),
    ("JavaScript Developer", "frontend", ["JavaScript", "TypeScript", "HTML", "CSS", "Node.js"]),
    ("Next.js Developer", "frontend", ["Next.js", "React", "TypeScript", "Tailwind CSS"]),
    ("UI Developer", "frontend", ["HTML", "CSS", "JavaScript", "Tailwind CSS", "Figma"]),
    ("Tailwind CSS Developer", "frontend", ["Tailwind CSS", "HTML", "CSS", "JavaScript"]),
    ("Web Accessibility Specialist", "frontend", ["accessibility", "HTML", "CSS", "JavaScript"]),
    ("jQuery Developer", "frontend", ["jQuery", "JavaScript", "HTML", "CSS"]),
    # backend
    ("Python Developer", "backend", ["Python", "Flask", "FastAPI", "SQL", "Git"]),
    ("Django Developer", "backend", ["Python", "Django", "REST API", "PostgreSQL", "Git"]),
    ("Flask Developer", "backend", ["Python", "Flask", "REST API", "SQL"]),
    ("FastAPI Developer", "backend", ["Python", "FastAPI", "REST API", "PostgreSQL"]),
    ("Node.js Developer", "backend", ["Node.js", "Express.js", "JavaScript", "MongoDB", "REST API"]),
    ("Express.js Developer", "backend", ["Express.js", "Node.js", "JavaScript", "MongoDB"]),
    ("PHP Developer", "backend", ["PHP", "MySQL", "REST API", "Git"]),
    ("Laravel Developer", "backend", ["Laravel", "PHP", "MySQL", "REST API"]),
    ("Ruby on Rails Developer", "backend", ["Ruby", "REST API", "PostgreSQL", "Git"]),
    ("Java Backend Developer", "backend", ["Java", "Spring Boot", "SQL", "REST API"]),
    ("Spring Boot Developer", "backend", ["Spring Boot", "Java", "REST API", "PostgreSQL"]),
    ("Go Developer", "backend", ["Go", "REST API", "Docker", "PostgreSQL"]),
    ("C# Developer", "backend", ["C#", ".NET", "SQL", "REST API"]),
    (".NET Developer", "backend", [".NET", "C#", "Microsoft SQL Server", "REST API"]),
    # fullstack / cms / ecommerce
    ("Full Stack Developer", "fullstack", ["JavaScript", "React", "Node.js", "SQL", "Git"]),
    ("MERN Stack Developer", "fullstack", ["MongoDB", "Express.js", "React", "Node.js"]),
    ("MEAN Stack Developer", "fullstack", ["MongoDB", "Express.js", "Angular", "Node.js"]),
    ("WordPress Developer", "fullstack", ["WordPress", "PHP", "MySQL", "HTML", "CSS"]),
    ("Shopify Developer", "fullstack", ["Shopify", "Liquid", "JavaScript", "HTML", "CSS"]),
    ("Magento Developer", "fullstack", ["Magento", "PHP", "MySQL", "JavaScript"]),
    ("Drupal Developer", "fullstack", ["Drupal", "PHP", "MySQL", "HTML"]),
    ("WooCommerce Developer", "fullstack", ["WordPress", "WooCommerce", "PHP", "CSS"]),
    ("Webflow Developer", "fullstack", ["Webflow", "HTML", "CSS", "JavaScript"]),
    ("Headless CMS Developer", "fullstack", ["Next.js", "React", "GraphQL", "REST API"]),
    ("Jamstack Developer", "fullstack", ["Next.js", "React", "GraphQL", "Tailwind CSS"]),
    # mobile
    ("Android Developer", "mobile", ["Kotlin", "Java", "Android", "REST API", "Firebase"]),
    ("iOS Developer", "mobile", ["Swift", "iOS", "REST API", "Firebase"]),
    ("Flutter Developer", "mobile", ["Flutter", "Dart", "REST API", "Firebase"]),
    ("React Native Developer", "mobile", ["React Native", "React", "JavaScript", "REST API"]),
    ("Kotlin Developer", "mobile", ["Kotlin", "Android", "REST API"]),
    ("Swift Developer", "mobile", ["Swift", "iOS", "REST API"]),
    ("Mobile App Developer", "mobile", ["Flutter", "React Native", "REST API", "Firebase"]),
    ("Ionic Developer", "mobile", ["Ionic", "Angular", "TypeScript", "REST API"]),
    ("Game Developer", "mobile", ["C#", "Unity", "C++", "problem solving"]),
    ("AR/VR Developer", "mobile", ["Unity", "C#", "computer vision basics", "3D"]),
    # data
    ("Data Analyst", "data", ["SQL", "Excel", "Power BI", "Tableau", "Statistics"]),
    ("Data Scientist", "data", ["Python", "Pandas", "Machine Learning", "SQL", "Statistics"]),
    ("Data Engineer", "data", ["Python", "SQL", "Apache Spark", "Airflow", "dbt"]),
    ("Business Intelligence Analyst", "data", ["Power BI", "SQL", "Tableau", "Excel"]),
    ("BI Developer", "data", ["Power BI", "SQL", "data modeling", "dbt"]),
    ("Analytics Engineer", "data", ["dbt", "SQL", "Python", "data modeling"]),
    ("Big Data Engineer", "data", ["Apache Spark", "Hadoop", "Python", "SQL"]),
    ("Data Architect", "data", ["data modeling", "SQL", "data warehousing", "ETL"]),
    ("Statistician", "data", ["Statistics", "R", "Python", "data visualization"]),
    ("Marketing Analyst", "data", ["Google Analytics", "SQL", "Excel", "data visualization"]),
    ("Quantitative Analyst", "data", ["Python", "Statistics", "R", "data modeling"]),
    ("Bioinformatics Analyst", "data", ["Python", "R", "Statistics", "data analysis"]),
    # ai
    ("AI Engineer", "ai", ["Python", "Machine Learning", "Deep Learning", "TensorFlow", "PyTorch"]),
    ("Machine Learning Engineer", "ai", ["Python", "Scikit-learn", "Machine Learning", "PyTorch", "MLOps"]),
    ("Deep Learning Engineer", "ai", ["Python", "Deep Learning", "TensorFlow", "PyTorch"]),
    ("NLP Engineer", "ai", ["Python", "NLP", "PyTorch", "Hugging Face"]),
    ("Computer Vision Engineer", "ai", ["Python", "OpenCV", "Deep Learning", "PyTorch"]),
    ("Generative AI Engineer", "ai", ["Python", "LLMs", "Prompt Engineering", "Hugging Face"]),
    ("AI Prompt Engineer", "ai", ["Prompt Engineering", "LLMs", "Python"]),
    ("MLOps Engineer", "ai", ["MLOps", "Docker", "Kubernetes", "Python", "CI/CD"]),
    ("Robotics Engineer", "ai", ["Python", "C++", "computer vision basics", "control systems"]),
    # cloud / devops
    ("DevOps Engineer", "cloud", ["Docker", "Kubernetes", "AWS", "CI/CD", "Linux"]),
    ("Cloud Engineer", "cloud", ["AWS", "Docker", "Terraform", "Linux"]),
    ("AWS Solutions Architect", "cloud", ["AWS", "Terraform", "Docker", "networking"]),
    ("Azure Engineer", "cloud", ["Azure", "Docker", "CI/CD", "Terraform"]),
    ("GCP Engineer", "cloud", ["Google Cloud", "Docker", "Kubernetes", "Terraform"]),
    ("Site Reliability Engineer", "cloud", ["Kubernetes", "Prometheus", "Linux", "CI/CD"]),
    ("Platform Engineer", "cloud", ["Kubernetes", "Terraform", "Docker", "CI/CD"]),
    ("Kubernetes Administrator", "cloud", ["Kubernetes", "Docker", "Linux", "Helm"]),
    ("Infrastructure Engineer", "cloud", ["Terraform", "AWS", "Linux", "Ansible"]),
    ("Release Engineer", "cloud", ["CI/CD", "Jenkins", "Git", "Docker"]),
    ("Cloud Security Engineer", "cloud", ["cloud security", "AWS", "Network Security", "compliance"]),
    # security
    ("Cybersecurity Analyst", "security", ["Network Security", "Splunk", "Linux", "Incident Response"]),
    ("Security Engineer", "security", ["Network Security", "Cryptography", "Linux", "cloud security"]),
    ("Penetration Tester", "security", ["Penetration Testing", "Burp Suite", "Kali Linux", "Ethical Hacking"]),
    ("SOC Analyst", "security", ["SIEM", "Splunk", "Network Security", "Incident Response"]),
    ("Information Security Analyst", "security", ["Network Security", "compliance", "risk assessment"]),
    ("Network Security Engineer", "security", ["Network Security", "Firewalls", "networking", "Linux"]),
    ("Application Security Engineer", "security", ["Penetration Testing", "Burp Suite", "API security"]),
    ("Security Architect", "security", ["Network Security", "Cryptography", "cloud security", "compliance"]),
    ("Incident Responder", "security", ["Incident Response", "SIEM", "Splunk", "forensics"]),
    ("Malware Analyst", "security", ["malware analysis", "reverse engineering", "Kali Linux"]),
    ("Ethical Hacker", "security", ["Ethical Hacking", "Penetration Testing", "Kali Linux", "Burp Suite"]),
    # design
    ("UI/UX Designer", "design", ["Figma", "Adobe XD", "Wireframing", "Prototyping", "User Research"]),
    ("Product Designer", "design", ["Figma", "Prototyping", "User Research", "Design Systems"]),
    ("UX Researcher", "design", ["User Research", "usability testing", "Figma"]),
    ("Graphic Designer", "design", ["Photoshop", "Illustrator", "Typography", "Canva"]),
    ("Visual Designer", "design", ["Figma", "Photoshop", "Typography", "color theory"]),
    ("Interaction Designer", "design", ["Figma", "Prototyping", "Interaction Design"]),
    ("Motion Designer", "design", ["Adobe After Effects", "motion", "animation"]),
    ("Brand Designer", "design", ["Illustrator", "brand guidelines", "Typography"]),
    ("Web Designer", "design", ["Figma", "HTML", "CSS", "Webflow"]),
    ("Design Systems Engineer", "design", ["Figma", "Design Systems", "HTML", "CSS"]),
    # marketing
    ("Digital Marketer", "marketing", ["SEO", "Google Ads", "Google Analytics", "content marketing"]),
    ("SEO Specialist", "marketing", ["SEO", "Google Analytics", "content strategy", "keyword research"]),
    ("Content Marketer", "marketing", ["content marketing", "copywriting", "SEO", "content strategy"]),
    ("Social Media Manager", "marketing", ["Social Media Marketing", "Canva", "content strategy"]),
    ("Performance Marketer", "marketing", ["Meta Ads", "Google Ads", "conversion optimization"]),
    ("Email Marketing Specialist", "marketing", ["Email Marketing", "Mailchimp", "HubSpot", "copywriting"]),
    ("Growth Marketer", "marketing", ["Growth Marketing", "Google Analytics", "conversion optimization"]),
    ("PPC Specialist", "marketing", ["Google Ads", "Meta Ads", "conversion optimization"]),
    ("Affiliate Marketing Manager", "marketing", ["Affiliate Marketing", "SEO", "Google Analytics"]),
    ("Brand Manager", "marketing", ["Brand Strategy", "content strategy", "Communication"]),
    ("Copywriter", "marketing", ["copywriting", "content strategy", "SEO"]),
    ("Content Strategist", "marketing", ["content strategy", "SEO", "content marketing"]),
    ("Influencer Marketing Manager", "marketing", ["Social Media Marketing", "Brand Strategy", "Communication"]),
    # product / management
    ("Product Manager", "product", ["Project Management", "Agile", "Communication", "Stakeholder Management"]),
    ("Project Manager", "product", ["Project Management", "Agile", "Jira", "Communication"]),
    ("Scrum Master", "product", ["Scrum", "Agile", "Jira", "Communication"]),
    ("Business Analyst", "product", ["Business Analysis", "SQL", "Communication", "Problem Solving"]),
    ("Product Owner", "product", ["Agile", "Scrum", "Stakeholder Management"]),
    ("Program Manager", "product", ["Project Management", "Stakeholder Management", "Communication"]),
    ("Technical Program Manager", "product", ["Project Management", "Agile", "Communication"]),
    ("Agile Coach", "product", ["Agile", "Scrum", "Communication", "Leadership"]),
    ("Operations Manager", "product", ["Operations", "Leadership", "Communication"]),
    ("Strategy Analyst", "product", ["Business Analysis", "Excel", "Communication"]),
    # qa
    ("QA Engineer", "qa", ["Selenium", "Test Automation", "Jira", "Problem Solving"]),
    ("Test Automation Engineer", "qa", ["Selenium", "Test Automation", "Python", "CI/CD"]),
    ("Manual QA Tester", "qa", ["manual testing", "Jira", "test cases"]),
    ("SDET", "qa", ["Test Automation", "Selenium", "Java", "CI/CD"]),
    ("Performance Test Engineer", "qa", ["JMeter", "performance testing", "Test Automation"]),
    ("Quality Analyst", "qa", ["manual testing", "test cases", "Jira"]),
    # infra / database / network
    ("Database Administrator", "infra", ["SQL", "PostgreSQL", "MySQL", "Linux"]),
    ("Database Developer", "infra", ["SQL", "PostgreSQL", "data modeling"]),
    ("ETL Developer", "infra", ["ETL", "SQL", "Python", "data warehousing"]),
    ("Systems Administrator", "infra", ["Linux", "Bash", "networking", "Git"]),
    ("Network Engineer", "infra", ["networking", "Linux", "Firewalls"]),
    ("Embedded Systems Engineer", "infra", ["C++", "C", "embedded", "Linux"]),
    ("IoT Developer", "infra", ["Python", "C++", "embedded", "REST API"]),
    ("Blockchain Developer", "infra", ["Solidity", "blockchain", "JavaScript", "REST API"]),
    # emerging / specialised
    ("Technical Writer", "emerging", ["technical writing", "Communication", "Markdown"]),
    ("Developer Advocate", "emerging", ["Communication", "Python", "public speaking"]),
    ("Computer Graphics Engineer", "emerging", ["C++", "OpenGL", "3D", "mathematics"]),
    ("Solutions Engineer", "emerging", ["Communication", "REST API", "SQL", "Problem Solving"]),
    ("Sales Engineer", "emerging", ["Communication", "REST API", "Problem Solving"]),
    ("IT Support Specialist", "emerging", ["Linux", "networking", "troubleshooting", "Communication"]),
]

SAMPLES_PER_CAREER = 10


def _csv(items):
    return ", ".join(items)


def _sample(pool, lo, hi):
    n = min(len(pool), RNG.randint(lo, hi))
    return RNG.sample(pool, n)


def main():
    careers_rows, jobs_rows, train_rows = [], [], []

    for title, domain, skills in ROLES:
        d = DOMAINS[domain]
        careers_rows.append({
            "title": title,
            "description": f"{title} — works on {d['desc']}.",
            "required_skills": _csv(skills),
        })

        # one representative job per career (varied level/location)
        level = RNG.choice(LEVELS)
        jobs_rows.append({
            "title": f"{level} {title}".replace("Intern ", "").strip() if level == "Intern" else f"{level} {title}",
            "level": level,
            "location": RNG.choice(LOCATIONS),
            "required_skills": _csv(skills),
            "career_title": title,
        })

        # ML training samples — vary skills/interests/tools/level per row
        for _ in range(SAMPLES_PER_CAREER):
            row_skills = _sample(skills, max(2, len(skills) - 2), len(skills))
            train_rows.append({
                "skills": _csv(row_skills),
                "interests": _csv(_sample(d["interests"], 1, len(d["interests"]))),
                "education": RNG.choice(d["education"]),
                "tools": _csv(_sample(d["tools"], 1, 3)),
                "experience_level": RNG.choice(["entry", "junior", "mid", "senior"]),
                "career": title,
            })

    _write(os.path.join(HERE, "careers_catalog.csv"),
           ["title", "description", "required_skills"], careers_rows)
    _write(os.path.join(HERE, "jobs_catalog.csv"),
           ["title", "level", "location", "required_skills", "career_title"], jobs_rows)
    _write(os.path.join(HERE, "careers_extended.csv"),
           ["skills", "interests", "education", "tools", "experience_level", "career"], train_rows)

    print(f"careers : {len(careers_rows)}")
    print(f"jobs    : {len(jobs_rows)}")
    print(f"training: {len(train_rows)} rows across {len(ROLES)} career classes")


def _write(path, fields, rows):
    with open(path, "w", newline="", encoding="utf-8") as f:
        w = csv.DictWriter(f, fieldnames=fields)
        w.writeheader()
        w.writerows(rows)


if __name__ == "__main__":
    main()
