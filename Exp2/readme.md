# Exercise 2: User Authentication and Profile Management

This project is part of my PHP internship, focusing on developing a secure user authentication system with robust data handling and session management.

## Overview
This exercise features a user signup and authentication system that provides a seamless user experience with secure access to a dashboard.

## Key Features

1. **Signup Form**: Allows users to register by providing their first name, last name, email, and password.
2. **Secure Data Storage**: Stores user data securely in a MySQL database, with passwords encrypted using hashing for enhanced security.
3. **Sign-In Process**: Authenticates users and uses sessions for secure access. After signing in, users are redirected to a dashboard listing all registered users.
4. **Profile Management**: Enables users to log out and edit their profiles, with session-based authentication for user-specific features.
5. **Form Validation**: Includes validations for both signup and sign-in:
   - Checking for existing email addresses to prevent duplicates.
   - Ensuring no fields are left empty.
   - Enforcing a password strength check for security.

## Database Configuration
The database is configured with the following details:

- `$host = 'localhost';`
- `$db   = 'php_internship';`
- `$user = 'root';`
- `$pass = '';`

## Database Table Structure
- **Table Name**: `users`
- **Columns**:
  - `id` (INT) - Primary Key, auto-incremented
  - `first_name` (VARCHAR) - User’s first name
  - `last_name` (VARCHAR) - User’s last name
  - `email` (VARCHAR) - User’s email, unique constraint
  - `password` (VARCHAR) - Encrypted password
  - `created_at` (TIMESTAMP) - Date and time of registration
