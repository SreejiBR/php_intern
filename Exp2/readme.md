Exercise 2: User Authentication and Profile Management

This project is part of my PHP internship, focusing on developing a secure user authentication system with robust data handling and session management.

Overview: This exercise features a user signup and authentication system that provides a seamless user experience with secure access to a dashboard.

Key Features:

1. Created a signup form that allows users to register by providing their first name, last name, email, and password.
2. Stored user data securely in a MySQL database, with passwords encrypted using hashing for enhanced security.
3. Implemented a sign-in process where users can log in, and sessions are used to manage authentication. Upon successful sign-in, users are redirected to a dashboard that lists all registered users.
4. Provided options for users to log out and edit their profiles, ensuring that session management is utilized for user-specific features.
5. Added
validation for both the signup and sign-in pages, including:

•Checking for existing email addresses to prevent duplicates.
•Ensuring no fields are left empty.
•Implementing a password strength check to promote secure password practices.

Database Configuration: The database is configured with the following details:

$host = 'localhost';
$db   = 'php_internship';
$user = 'root';
$pass = '';

Database Table Structure:

Table Name: users
Columns:
id (INT) - Primary Key, auto-incremented
first_name (VARCHAR) - User’s first name
last_name (VARCHAR) - User’s last name
email (VARCHAR) - User’s email, unique constraint
password (VARCHAR) - Encrypted password
created_at (TIMESTAMP) - Date and time of registration
