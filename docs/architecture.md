# Career Mind Architecture (Phase 1)

## Overview
Three-tier architecture with PHP MVC for the web layer and Flask as a separate AI microservice. Phase 1 implements only the foundation.

## Presentation Layer (PHP)
- Bootstrap-based UI
- Login/Register
- Profile management (education, skills, interests)

## Application Layer
- PHP controllers handle auth, profile updates, and future REST calls to Flask
- Flask exposes placeholder endpoints for AI

## Data Layer
- MySQL schema defined in database/schema.sql

## Why Microservice?
The AI layer is isolated so ML models can be updated without changing the PHP web app. REST endpoints form a stable contract for later phases.
