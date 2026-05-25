# LinkedIn Clone — Full Stack Web Application

A feature-rich LinkedIn-style professional networking platform built with **Core PHP 8**, **MySQL**, **Bootstrap 5**, and **Vanilla JavaScript**. No frameworks required.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Database Setup](#database-setup)
- [SMTP / Email Setup](#smtp--email-setup)
- [Demo Accounts](#demo-accounts)
- [Application URLs](#application-urls)
- [Security](#security)
- [Contributing](#contributing)
- [License](#license)

---

## Overview

This project is a full-featured LinkedIn-style platform with three distinct panels:

| Panel | Description |
|-------|-------------|
| **Admin** | User management, agent approvals, reports, content moderation |
| **User** | Feed, profile, network, jobs, messaging, notifications |
| **Recruiter / Company** | Company profile, job postings, ATS pipeline, analytics dashboard |

---

## Features

### User Portal
- Responsive LinkedIn-style layout — sticky nav, sidebars, cards, modals, toasts, mobile nav
- Role-based authentication for regular users and companies
- Rich user profiles: photo, cover, headline, about, experience, education, skills, certifications, languages, resume, social links, visibility controls
- Feed with text/media/document posts, likes, comments, saves and AJAX interactions
- Networking — suggested connections, connection requests, accept/reject, notifications
- Jobs board with filters, saved jobs, Easy Apply and application tracking
- One-to-one messaging with attachments, seen status and AJAX polling
- Global live search across people, companies, jobs and posts

### Recruiter / Company Panel
- Company profile management
- Job posting and management
- ATS pipeline stages: `applied → reviewing → shortlisted → interview → hired → rejected`
- Recruiter dashboard with Chart.js analytics and recent applications

### Admin Panel
- Full user and company management
- Agent approval workflow
- Content moderation and reporting
- Settings and support management

### Technical Highlights
- Custom MVC architecture (no framework)
- PDO prepared statements throughout
- AJAX-driven interactions (no page reloads for feeds, messaging, search)
- Secure file uploads — MIME/size validation for images, PDFs and video
- PHPMailer integration for transactional email
- CSRF tokens on all AJAX and form actions

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Language | PHP 8.0+ |
| Database | MySQL / MariaDB |
| Frontend | Bootstrap 5, Vanilla JS, AJAX |
| Email | PHPMailer 7.x |
| Dependency Manager | Composer |
| Local Dev | XAMPP / WAMP |

---

## Project Structure

```
Linkedin/
├── app/
│   ├── controllers/        # Request handlers (Auth, Portal, Admin, etc.)
│   ├── core/               # MVC core: Controller, Model, Database, Mailer
│   ├── helpers/            # Utility helpers (captcha, portal)
│   ├── models/             # Database models
│   └── views/              # PHP view templates
│       ├── auth/           # Login, password reset
│       ├── portal/         # User-facing pages (home, jobs, messages, etc.)
│       ├── layouts/        # Shared layout wrappers (admin, portal)
│       └── ...             # admin sub-views (users, companies, agents, etc.)
├── config/
│   ├── app.php             # App constants, SMTP config (reads from .env)
│   └── database.php        # DB constants (reads from .env)
├── database/
│   ├── schema.sql          # Clean schema — import this for a fresh install
│   └── database.sql        # Full database dump with demo data
├── public/
│   ├── index.php           # Front controller / router
│   ├── .htaccess           # URL rewriting
│   ├── assets/
│   │   ├── css/            # admin.css, portal.css, auth.css, captcha.css
│   │   └── js/             # admin.js, portal.js, auth.js
│   └── uploads/            # Runtime uploads (gitignored)
│       ├── avatars/
│       ├── covers/
│       ├── companies/
│       ├── posts/
│       └── resumes/
├── vendor/                 # Composer packages (gitignored)
├── .env                    # Secret credentials — NEVER commit this
├── .env.example            # Template — copy to .env and fill in
├── .gitignore
├── composer.json
└── README.md
```

---

## Prerequisites

- PHP **8.0** or higher (with `pdo_mysql`, `mbstring`, `openssl` extensions)
- MySQL **5.7+** or MariaDB **10.3+**
- Apache with `mod_rewrite` enabled (XAMPP / WAMP / Laragon)
- [Composer](https://getcomposer.org/) (for PHPMailer)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/your-username/linkedin-clone.git
cd linkedin-clone
```

### 2. Copy into your web root

Place the `Linkedin/` folder inside your XAMPP/WAMP `htdocs` (or configure a virtual host pointing to `public/`).

```
C:/xampp/htdocs/Linkedin/
```

### 3. Install dependencies

```bash
composer install
```

### 4. Set up environment variables

```bash
cp .env.example .env
```

Edit `.env` with your local values (see [Configuration](#configuration) below).

### 5. Import the database

```bash
mysql -u root -p < database/schema.sql
```

Or import via phpMyAdmin. Use `database/database.sql` if you also want demo data.

### 6. Open in browser

```
http://localhost/Linkedin/public
```

---

## Configuration

Copy `.env.example` to `.env` and fill in your values. **Never commit `.env` to Git.**

```env
# Application
APP_URL=http://localhost/Linkedin/public

# Database
DB_HOST=localhost
DB_NAME=linkedin_admin
DB_USER=root
DB_PASS=your_db_password
DB_CHARSET=utf8mb4

# SMTP
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your@gmail.com
SMTP_PASS=your_app_password
SMTP_FROM_EMAIL=noreply@yoursite.com
SMTP_FROM_NAME=LinkedIn Clone
SMTP_REPLY_TO_EMAIL=
SMTP_REPLY_TO_NAME=LinkedIn Clone
```

> **Gmail users:** Generate an [App Password](https://myaccount.google.com/apppasswords) (requires 2FA) and use it as `SMTP_PASS`.

---

## Database Setup

| File | Purpose |
|------|---------|
| `database/schema.sql` | Clean schema — use this for a fresh install |
| `database/database.sql` | Full dump including demo users, companies and posts |

To create an admin account manually after a fresh schema import:

```bash
php -r "echo password_hash('Admin@1234', PASSWORD_DEFAULT);"
```

Then insert the result into the `admins` table via phpMyAdmin or MySQL CLI.

---

## SMTP / Email Setup

PHPMailer is used for:
- Password reset emails
- Email verification
- Recruiter notifications
- Application confirmation emails

Configuration is read from `.env` via `config/app.php`. You can also update SMTP settings from the admin settings screen after logging in.

---

## Demo Accounts

After importing `database/database.sql`:

| Role | Email | Password |
|------|-------|----------|
| User | `user@demo.com` | `Password@123` |
| Recruiter | `recruiter@demo.com` | `Password@123` |

> Remove demo accounts before deploying to production.

---

## Application URLs

| URL | Description |
|-----|-------------|
| `/login` | Admin login |
| `/signin` | User / recruiter login & registration |
| `/home` | User feed (after login) |
| `/recruiter-dashboard` | Recruiter analytics dashboard |
| `/jobs` | Jobs board |
| `/network` | Connections and suggestions |
| `/messages` | Messaging inbox |
| `/notifications` | Notification centre |

> All URLs are relative to `APP_URL` defined in your `.env`.

---

## Security

The following security measures are implemented:

- **PDO prepared statements** — no raw SQL interpolation
- **Password hashing** — `password_hash()` / `password_verify()` (bcrypt)
- **Session regeneration** on login
- **Role-based access control** on every controller
- **CSRF tokens** on all AJAX and form actions
- **Output escaping** — `htmlspecialchars()` throughout views
- **Upload validation** — MIME type and file size checks for images, PDFs and video
- **Account status checks** — blocked/suspended users cannot log in

### Production Checklist

- [ ] Enable HTTPS (SSL certificate)
- [ ] Set `display_errors = Off` in `php.ini`
- [ ] Rotate SMTP credentials
- [ ] Tighten upload directory permissions
- [ ] Remove demo accounts
- [ ] Set strong `DB_PASS` in `.env`
- [ ] Disable directory listing in Apache/Nginx

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit your changes: `git commit -m "Add my feature"`
4. Push to the branch: `git push origin feature/my-feature`
5. Open a Pull Request

Please make sure your code follows the existing MVC conventions and that credentials are never hardcoded.

---

## License

This project is released for educational purposes. Feel free to use, modify and distribute it with attribution.