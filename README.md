# LinkedIn Clone Full Stack Web Application

Core PHP LinkedIn-style platform with three panels:

- Admin panel: existing admin functionality preserved, dark mode removed.
- Normal user panel: feed, profile, network, jobs, messaging, notifications.
- Recruiter/company panel: company profile, job posting, analytics dashboard, ATS pipeline.

## Stack

- Core PHP 8+
- MySQL / MariaDB
- Bootstrap 5
- Vanilla JavaScript + AJAX
- Composer
- PHPMailer only

No Laravel, CodeIgniter, React, Vue, Angular, Node.js or Express are required.

## Main URLs

Update `APP_URL` in `config/app.php`, then open:

- Admin: `APP_URL/login`
- User/recruiter auth: `APP_URL/signin`
- User home: `APP_URL/home`
- Recruiter dashboard: `APP_URL/recruiter-dashboard`

Demo portal accounts after importing `database/schema.sql`:

- User: `user@demo.com` / `Password@123`
- Recruiter: `recruiter@demo.com` / `Password@123`

## Installation

1. Copy the `LinkedIn` folder into your XAMPP/WAMP web root.
2. Import `database/schema.sql` in phpMyAdmin.
3. Edit `config/database.php` with your MySQL credentials.
4. Edit `config/app.php`:

```php
define('APP_URL', 'http://localhost/LinkedIn/public');
```

5. Install PHPMailer with Composer:

```bash
composer install
```

The project also includes the original bundled PHPMailer files under `app/libraries/PHPMailer`, so it can run in simple XAMPP setups even before Composer is installed.

## SMTP Setup

Configure SMTP in `config/app.php` or the admin settings screen:

```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your@gmail.com');
define('SMTP_PASS', 'your_app_password');
define('SMTP_FROM_EMAIL', 'noreply@anjali.com');
define('SMTP_FROM_NAME', 'LinkedIn Admin');
define('SMTP_REPLY_TO_EMAIL', SMTP_USER);
define('SMTP_REPLY_TO_NAME', SMTP_FROM_NAME);
```

PHPMailer is used for password reset and can be reused for verification, recruiter notifications, application confirmations and support emails.

## Features Added

- Responsive LinkedIn-style layout with sticky navigation, sidebars, cards, dropdown search, modals, toasts and mobile navigation.
- Role-based auth for normal users and companies.
- User profile sections: photo, cover, headline, about, experience, education, skills, certifications, languages, resume, social links and visibility.
- Feed with text/media/document posts, likes, comments, saves and AJAX interactions.
- Networking with suggested connections, requests, accept/reject and notifications.
- Jobs board with filters, saved jobs, Easy Apply and application tracking.
- Recruiter dashboard with stats, Chart.js analytics, job management and recent applications.
- ATS stages: applied, reviewing, shortlisted, interview, hired, rejected.
- Messaging with one-to-one conversations, attachments, seen status and AJAX polling.
- Global live search for people, companies, jobs and posts.
- Secure upload validation for images, PDFs and video.
- Additive database extensions for messages, likes, saved posts/jobs, notifications, search history, profile fields and demo data.

## Admin Panel Note

The existing admin panel remains on the original MVC routes. Only the dark theme option was removed from:

- `app/views/layouts/admin.php`
- `public/assets/js/admin.js`
- `public/assets/css/admin.css`

## Security

The project uses PDO prepared statements, password hashing, session regeneration on login, role checks, CSRF tokens for AJAX/form actions, output escaping, upload MIME/size validation and account status checks.

For production, use HTTPS, rotate SMTP credentials, disable display errors, tighten upload permissions and remove demo accounts.
