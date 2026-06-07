# Hospital Management System

Starter project structure for a modular Hospital Management System built with plain PHP, MySQL, JavaScript, and Tailwind CSS.

## Stack

- PHP
- MySQL
- JavaScript
- Tailwind CSS

## Project Layout

- `public/` contains public entry points and static assets.
- `app/config/` contains application and database configuration.
- `app/includes/` contains reusable layout partials.
- `app/helpers/` contains shared utility functions, auth helpers, and validation helpers.
- `app/handlers/` contains request and form processing logic.
- `app/views/` contains page views for each module.
- `database/` contains migrations, seeders, and the SQL starter file.
- `uploads/` stores user-uploaded files outside the public web root.
- `storage/` stores logs and backup files.

## Core Design Notes

- `public/index.php` is the main entry point.
- Patients should be stored once in a `patients` table.
- Every encounter should be tracked as a `visit`.
- Outpatient and inpatient flows should connect to shared patient and visit records.
- Inpatient admissions should extend a visit through an `admissions` table.

## Getting Started

1. Point your local web server document root to the `public/` directory when possible.
2. Update `app/config/app.php` values for your environment.
3. Update database credentials in `app/config/database.php`.
4. Install frontend dependencies:

```bash
npm install
```

5. Build Tailwind CSS:

```bash
npm run build:css
```

6. For active frontend work, run:

```bash
npm run watch:css
```

## Initial Entry Points

- `public/index.php` loads the dashboard layout.
- `public/login.php` is the login page stub.
- `public/logout.php` clears the current session and redirects to login.

## Next Suggested Modules

- Authentication and roles
- Patient registration
- Visit management
- Admission workflow
- Billing and reporting
