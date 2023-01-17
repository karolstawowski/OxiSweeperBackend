# OxiSweeperBackend

![Language](https://img.shields.io/badge/language-PHP-3993fa)
![License](https://img.shields.io/github/license/karolstawowski/OxiSweeperBackend?color=3993fa)
![Version](https://img.shields.io/badge/version-0.0.1-3993fa) <br>

### <a href="https://github.com/karolstawowski/OxiSweeperFrontend">Link to OxiSweeperFrontend</a>

## Description

<b>Laravel</b> implementation of backend for popular game called 'Minesweeper' made by Robert Donner.</br>
_OxiSweeperBackend_ implements application user interface and database for user authentication, authorization and record tracking.</br>
_OxiSweeperFrontend_ implements the Minesweeper game itself and routing for users depending of theirs role.

## Installation and usage

### Prerequisite

To run the project you need to have followed software installed:

1. [Xampp](https://www.apachefriends.org/download.html)
1. [Composer](https://getcomposer.org/)
1. [Laravel Sanctum](https://laravel.com/docs/9.x/sanctum#installation)

### Installation

1. Clone the repository:

```bash
git clone https://github.com/karolstawowski/OxiSweeperBackend
```

2. Create `.env` file based on `.env.example`

```bash
copy .env.example .env
```

3. Run Apache and MySQL server using Xampp 

4. Create new database (default database name: `minesweeper`)

5. Migrate the database:

```bash
php artisan migrate
```

6. Seed the database with records:

```bash
php artisan db:seed
```

### Project run

To run the project use

```bash
php artisan serve
```

command in the root directory of the repositorium.

## Use case example

1. User enters log in/register page
2. User logs in/creates new accout
3. User is redirected to route based on role - `/game` or `/dashboard`
4. User can log out, which redirects to `/login` page

## Authorization/authentication model

When user logs in or registers, user token is being assigned. User token is stored in client's local storage.
Every time user enters any page, request for user role to backend is being sent. User role is stored in frontend application's context.
Unauthorized user is being redirected to allowed path. Unauthenticated user can access `/login` path only.

## Database structure

### Scores table

```sql
CREATE TABLE `scores` (
  `id` bigint(20) UNSIGNED NOT NULL PRIMARY KEY,
  `score` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL FOREIGN KEY,
  `difficulty_level` enum('1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Users table

```sql
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL PRIMARY KEY,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 2 FOREIGN KEY,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### User roles table

```sql
CREATE TABLE `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL PRIMARY KEY,
  `role_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## API Routes

### Public routes

-   **POST** /register
-   **POST** /login

### Protected routes

-   **GET** /users
-   **GET** /scores
-   **GET** /score/top/{user_token}/{difficulty_level}
-   **POST** /role
-   **POST** /user
-   **POST** /score
-   **POST** /logout

## Tools and technologies

PHP, Laravel, Sanctum, Eloquent ORM.
