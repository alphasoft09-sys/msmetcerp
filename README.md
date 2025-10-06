# MSME Training Center ERP System

## About
This is the MSME Training Center ERP System for managing training centers, students, and exams.

## Features
- Student Management
- Exam Management
- Training Center Management
- LMS (Learning Management System)
- Document Management

## Installation
1. Clone the repository
2. Run `composer install`
3. Copy `.env.example` to `.env` and configure your database
4. Run `php artisan migrate`
5. Run `php artisan db:seed`
6. Run `php artisan storage:link`
7. Run `php artisan serve`

## Storage Setup
If you encounter issues with image uploads, run:
```
php artisan storage:fix-link
```

## Last Updated
Last updated: October 6, 2025