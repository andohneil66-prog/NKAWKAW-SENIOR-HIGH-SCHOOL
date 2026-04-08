# Nkawkaw Senior High School Website

A complete school management website with student portal, admin panel, and MySQL backend.

## Quick Start

### Option 1: Use Without Backend (LocalStorage)
Simply upload all HTML files to any web host - it will work with localStorage fallback.

### Option 2: Full Backend (MySQL)
1. **Setup Database:**
   - Upload the `api/` folder to your server
   - Visit `api/setup.php` in browser to create database and tables
   - Update `api/index.php` with your database credentials

2. **Database Credentials** (edit in api/index.php):
```php
$host = 'localhost';      // Database host
$db   = 'nkawkaw_shs';   // Database name
$user = 'root';          // Database username
$pass = '';              // Database password
```

3. **Upload Files:**
   - Upload all HTML files to your web server
   - Upload the `api/` folder
   - Create an `uploads/` folder for gallery images

## Default Login

**Admin Panel:** `admin.html`
- Password: `neil2010/11/7`

**Student Demo:**
- Username: `student`
- Password: `1234`

## Features

- Student registration with auto-generated ID
- Student portal with results, exams, profile
- Admin panel for managing students, news, gallery, results
- AI chatbot assistant
- Gallery with image upload
- News publishing
- Responsive design

## Hosting Requirements

- PHP 7.0+
- MySQL 5.6+
- Apache/Nginx web server

## Folder Structure

```
nkawkaw/
├── index.html          # Home page
├── login.html          # Student login
├── register.html       # Student registration
├── dashboard.html     # Student portal
├── admin.html         # Admin panel
├── news.html          # News page
├── gallery.html       # Photo gallery
├── results.html       # Student results
├── exams.html         # Exam schedule
├── admissions.html    # Admissions page
├── security.html      # Security info
├── settings.html      # Admin settings
├── api/
│   ├── index.php      # API endpoints
│   └── setup.php      # Database setup
└── uploads/           # Gallery images (create this folder)
```