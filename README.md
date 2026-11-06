# BRAC Migration & Reintegration Management Information System

A robust Migration & Reintegration Management Information System powered by **Laravel 13**, built for BRAC to track the complete lifecycle of migrant workers. Every person is modelled with one of two profile types — **Reintegrated Profile** or **Potential Customer Profile** — with skills, migration history, documents, support records, and follow-ups attached to a single user profile. Features role-based access control, a full audit trail, CSV/Excel/PDF exports, a public job board that only publishes potential customers, and a Sanctum-secured REST API.

## Tech Badges

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Blade](https://img.shields.io/badge/Blade-Templates-F05138?style=for-the-badge&logo=laravel&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4-06B6D4?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-8-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-7.4-DC382D?style=for-the-badge&logo=redis&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![Sanctum](https://img.shields.io/badge/Laravel%20Sanctum-Auth-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-yellow?style=for-the-badge&logo=open-source-initiative&logoColor=white)

---

## Features

### Core Functionality

| Feature                    | Description                                                                                 |
| -------------------------- | ------------------------------------------------------------------------------------------- |
| **User Profiles**          | One profile per person, typed as either a Reintegrated Profile or a Potential Customer Profile |
| **Skill Access**           | Add and remove skills, then mark a profile as a potential customer with a checkbox           |
| **Migration History**      | Record destination country, employer, job title, visa type, dates, and current status        |
| **Reintegration Module**   | Follow-ups can mark a potential customer as reintegrated, updating the profile type          |
| **Branch Hierarchy**       | Head Office → District → Upazila structure with parent validation and child-branch guards   |
| **HRM Module**             | Staff profiles, designation management, staff incentives, and headcount insights             |
| **Finance Ledger**         | Basic income/expense entries with category, branch tagging, filters, totals, and audit logging |
| **Role-Based Access**      | spatie/laravel-permission with route-level permission middleware per resource                |
| **Audit Trail**            | Every create/update/delete logged with before/after values and the acting user               |
| **Administrator Menu**     | Central settings editor and a System Activity Logger built on the audit trail                |
| **Reports & Exports**      | CSV, Excel, and PDF downloads for every module; every export is logged                       |
| **Public Job Board**       | Public-facing page that only surfaces Potential Customer Profiles to employers                |

> **Note:** The Finance module is currently a basic income/expense ledger. Its final business scope is still to be defined.

### Data Integrity & Reporting

| Feature                          | Description                                                          |
| -------------------------------- | -------------------------------------------------------------------- |
| **Dashboard KPIs**               | At-a-glance metrics split by Reintegrated and Potential Customer profiles |
| **Searchable List Pages**        | GET filter forms on every index (name, profile type, status, branch, date ranges) |
| **Follow-up Reminders**          | Queued job + notification reminding case officers of due follow-ups   |
| **Document Management**          | Profiles can store NID scans, passports, and contracts                |
| **REST API**                     | Full Sanctum-protected API for all resources plus report summaries    |

### System Architecture

- **Admin Portal** — Full CRUD for user profiles, skills, migration history, documents, branches, staff, designations, staff incentives, and finance entries with RBAC, plus an Administrator menu for settings and system activity
- **Public Website** — Job board that surfaces eligible program participants for livelihood matching
- **API Layer** — Sanctum token auth with resource endpoints for future field/mobile apps

---

## Screenshots

### Admin Login

![Admin Login](screenshots/Admin_Login.png)

### Admin Dashboard

![Admin Dashboard](screenshots/Admin_Dashboard.png)

### BRAC Job Board

![BRAC Job Board](screenshots/BRAC_Job_Board.png)

### Branches

![Branches](screenshots/Branches.png)

### Staff

![Staff](screenshots/Staff.png)

### Reports

![Reports](screenshots/Reports.png)

### Audit Logs

![Audit Logs](screenshots/Audit_Logs.png)

### Roles & Permissions

![Roles](screenshots/Roles.png)

### Edit Role

![Edit Role](screenshots/Edit-Role.png)

### Permissions

![Permissions](screenshots/Permissions.png)

### Assign Roles

![Assign Roles](screenshots/Assign-Roles.png)

---

## Tech Stack

| Layer            | Technology                      |
| ---------------- | ------------------------------- |
| Backend          | Laravel 13 (PHP 8.3+)           |
| Frontend         | Blade Templates, Tailwind CSS 4 |
| Build Tool       | Vite 8                          |
| Database         | MySQL 8.0 (Docker)              |
| Cache & Sessions | Redis 7.4                       |
| Authentication   | Laravel sessions + Sanctum      |
| RBAC             | spatie/laravel-permission       |
| Exports          | maatwebsite/excel, dompdf       |
| Containerization | Docker + Docker Compose         |

---

## Project Structure

```
brac-mis/
├── docker-compose.yml            # Full local stack (app, nginx, mysql, redis, phpMyAdmin)
├── docker/
│   ├── php/                      # PHP 8.3-FPM Dockerfile + entrypoint
│   └── nginx/                    # nginx site config
├── screenshots/                  # UI screenshots
└── src/                          # Laravel 13 application
    ├── app/
    │   ├── Http/Controllers/     # 21 web controllers + 4 Api/ controllers
    │   ├── Models/               # 20 Eloquent models
    │   ├── Services/             # Business logic (Audit, Branch, Dashboard, Report)
    │   ├── Jobs/                 # GenerateReport
    │   └── Notifications/        # FollowUpReminder
    ├── database/
    │   ├── migrations/           # 31 migrations
    │   └── seeders/              # Admin user, branches, staff, designations, incentives, demo data, RBAC
    ├── resources/views/          # 69 Blade templates
    └── routes/                   # web.php (68 routes) + api.php (12 routes)
```

---

## Quick Start

### Prerequisites

- Docker & Docker Compose

### Installation

```bash
# 1. Start all containers
docker compose up -d --build

# 2. Install dependencies (inside the app container)
docker exec -it brac_mis_app composer install

# 3. Generate key & run migrations
docker exec -it brac_mis_app php artisan key:generate
docker exec -it brac_mis_app php artisan migrate --force

# 4. Seed demo data & roles
docker exec -it brac_mis_app php artisan db:seed
```

### Access Points

| Service      | URL               |
| ------------ | ----------------- |
| Laravel App  | http://localhost:8087 |
| phpMyAdmin   | http://localhost:8089 |
| MySQL (host) | localhost:3309    |
| Redis (host) | localhost:6383    |

### Default Credentials

| Role  | Email                  | Password |
| ----- | ---------------------- | -------- |
| Admin | admin@bracmis.org      | password |

---

## Database Schema

| Table                           | Purpose                                       |
| ------------------------------- | --------------------------------------------- |
| `users`                         | User profiles with `profile_type` (`reintegrated` or `potential_customer`) |
| `user_skills`                   | Skills, proficiency, experience, and certification per profile |
| `user_migration_histories`      | Destination, employer, job title, visa, dates, and migration status |
| `user_documents`                | Uploaded NID scans, passports, and contracts  |
| `immediate_supports`            | Support records linked to a user profile or unregistered recipient |
| `support_follow_ups`            | Follow-ups with reintegration status          |
| `courses` / `course_enrollments` / `certificates` | Training and certification records |
| `finances`                      | Organisation income/expense ledger entries    |
| `branches` / `staff` / `designations` / `staff_incentives` | Organisation structure and HRM data |
| `audit_logs`                    | Full audit trail of all changes               |
| `reports`                       | Export & report generation log                |

---

## API Routes

### Public Routes

- `GET /` — Homepage
- `GET /login` — Login form
- `GET /job-board` — Public job board
- `GET /job-board/{user}` — Potential customer profile detail (404 for any other profile type)

### Admin Routes (requires session + permission)

- `GET|POST /users/*` — User profile management (CRUD)
- `GET /skill-access` — Skills and profile-type management
- `POST /skill-access/{user}/profile-type` — Mark as potential customer / revert to reintegrated
- `GET|POST /support/*` — Support records and follow-ups
- `GET|POST /branches/*` — Branch hierarchy management (CRUD)
- `GET /hrm` — HRM overview (staff, designations, branch distribution)
- `GET|POST /staff/*` — Staff management (CRUD)
- `GET|POST /designations/*` — Designation management (CRUD)
- `GET|POST /staff-incentives/*` — Staff incentive management (CRUD)
- `GET|POST /finances/*` — Finance ledger management (CRUD)
- `GET /reports` — Reports dashboard
- `GET /reports/export/{type}/{format}` — CSV / Excel / PDF exports
- `GET /audit-logs` — System Activity Logger (audit trail)
- `GET|PUT /settings` — Administrator settings editor

### REST API (requires Sanctum token)

- `POST /api/login` — Obtain API token
- `GET /api/branches` — Branches CRUD
- `GET /api/staff` — Staff CRUD
- `GET /api/reports/*` — Profile, migration, and branch performance summaries

---

## Development

```bash
# Access Laravel container
docker exec -it brac_mis_app bash

# Run artisan commands
docker exec -it brac_mis_app php artisan [command]

# Run tests
docker exec -it brac_mis_app php artisan test

# Clear cache
docker exec -it brac_mis_app php artisan config:clear
docker exec -it brac_mis_app php artisan cache:clear
```

---

## License

MIT License

---

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

---

## Support

For support, please open an issue in the GitHub repository.
