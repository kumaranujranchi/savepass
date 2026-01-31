# SecureVault

A modern, secure, and minimal Password Manager web application built with PHP and MySQL.

## Features

- **Secure Storage**: AES-256 encryption for passwords and notes.
- **Dashboard**: Overview of your vault statistics.
- **Passwords**: Add, edit, and filter your login credentials.
- **API Keys**: Manage developer API keys with environment tagging.
- **Secure Notes**: Encrypted notepad for sensitive information.
- **Security**: Master password protection and encryption.

## Setup

1. Import `db_schema.sql` into your MySQL database.
2. Configure `config/db.php` with your database credentials.
3. Serve the application using Apache/Nginx or PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
4. Register a new account and start using the vault.

## Tech Stack

- PHP 8.x
- MySQL
- Vanilla HTML/CSS/JS
