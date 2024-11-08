# Admin Management System: Teacher and Subject Assignment

This project is part of my PHP internship, focusing on creating a management system for handling the administrative tasks related to teachers, subjects, classes, and divisions. It allows efficient management of teachers, subject assignments, and division assignments through an intuitive and simple interface.

## Overview
This system enables the admin to:
- Add, edit, and delete teacher records.
- Assign teachers to subjects and divisions.
- Manage subjects and divisions through dynamic form handling.

It is designed to be efficient, secure, and easy to use, with basic form validation and secure password handling.

## Key Features

1. **Admin Login**: Secure login system for the admin, ensuring only authorized users can manage records.
2. **Teacher Management**:
   - **Add** new teachers with necessary details such as name, username, password, subject, and division.
   - **Edit** teacher information, including changing subjects and divisions.
   - **Delete** teachers from the system.
3. **Subject and Division Management**:
   - Dynamic form to select and assign subjects and divisions to teachers based on the available records.
4. **Responsive Interface**: A clean, responsive layout that works seamlessly across devices, allowing easy access to teacher and subject management features.
5. **Validation**: Ensures that all fields are filled out before submission and provides error messages where necessary. Passwords are hashed before being stored for added security.
6. **Database-Driven**: All records (teachers, subjects, divisions) are managed through a MySQL database.

## Database Configuration
The database is configured with the following details:

- `$host = 'localhost';`
- `$db   = 'school_management';`
- `$user = 'root';`
- `$pass = '';`

## Database Table Structure

### Table: `admin`
- **Columns**:
  - `id` (INT) - Primary Key, auto-incremented
  - `username` (VARCHAR) - Admin username
  - `password` (VARCHAR) - Encrypted password for secure login

### Table: `teachers`
- **Columns**:
  - `id` (INT) - Primary Key, auto-incremented
  - `name` (VARCHAR) - Teacher's full name
  - `username` (VARCHAR) - Teacher's username
  - `password` (VARCHAR) - Encrypted password for secure login
  - `subject_id` (INT) - Foreign Key referencing `subjects.id`
  - `division_id` (INT) - Foreign Key referencing `divisions.id`

### Table: `subjects`
- **Columns**:
  - `id` (INT) - Primary Key, auto-incremented
  - `subject_name` (VARCHAR) - Name of the subject
  - `class_id` (INT) - Foreign Key referencing `classes.id`
  - `division_id` (INT) - Foreign Key referencing `divisions.id`

### Table: `divisions`
- **Columns**:
  - `id` (INT) - Primary Key, auto-incremented
  - `division_name` (VARCHAR) - Division name (e.g., "A", "B")
  - `class_id` (INT) - Foreign Key referencing `classes.id`

### Table: `classes`
- **Columns**:
  - `id` (INT) - Primary Key, auto-incremented
  - `class_name` (VARCHAR) - Name of the class (e.g., "Grade 1")
  - `description` (VARCHAR) - Description of the class

## Requirements

- PHP 7.0 or higher
- MySQL database
- Admin privileges for accessing the system

## Installation Instructions

1. **Set up the Database**:
- Create the database using the provided SQL schema.
- Import the database tables into your MySQL server.

2. **Configure Database Connection**:
- Modify the database credentials in `db.php` to match your local environment.

3. **Run the System**:
- Use `one.php` to create an admin user, with `user_name = 'admin'` and `password = 'securepassword'`
- Access the system by opening `index.php` in your web browser to log in as an admin.
- Once logged in, you can manage teachers, subjects, and divisions through the provided interface.

## How to Use

- **Login**: Access the system via `index.php` using valid admin credentials.
- **Add Teachers**: Use the teacher management section to add new teachers by providing their name, username, password, assigned subject, and division.
- **Edit Teachers**: Update existing teacher information through the edit interface.
- **Delete Teachers**: Remove a teacher from the system by clicking the delete button next to their record.
- **Manage Subjects and Divisions**: Assign teachers to appropriate subjects and divisions using dynamic dropdowns that adjust based on the selected class.
- **Logout**: The admin can log out from the system using the provided logout button.

  
