# Career Mind Web (Phase 1)

This is the PHP MVC web layer for the Career Mind system. Phase 1 focuses on authentication, student profiles, and skills/interests management only.

## Scope (Phase 1 Only)
- User registration and login (session-based)
- Profile management (age, education, institution, graduation year)
- Skills and interests management (stored in MySQL)
- No AI logic, no CV parsing

## How It Fits Later Phases
The web app will call the Flask AI service using REST (JSON) once the AI layer is implemented. Controllers are structured so AI service calls can be added without refactoring core auth/profile logic.

## Local Setup (Summary)
1. Create MySQL database and import the schema from ../database/schema.sql
2. Update database credentials in config/config.php
3. Point your web server document root to public/

## Next Phase
Phase 2 will add role-based dashboards and admin tooling, and Phase 3 will add CV upload.
