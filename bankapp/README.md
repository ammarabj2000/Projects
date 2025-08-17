# Bank Management App (PHP + MySQL)

A minimal bank management web app using PHP, MySQL, and vanilla HTML/CSS/JS. Includes authentication, CSRF protection, and CRUD for customers, accounts, and transactions.

## Requirements
- XAMPP (Apache + MySQL) or PHP 8+ with MySQL
- MySQL 8 (or 5.7+)

## Setup
1. Create database and tables:
   - Import `db/schema.sql` into MySQL.
2. Configure DB credentials:
   - Update `src/config.php` to match your MySQL settings (XAMPP defaults to user `root` with empty password).
3. Start the server:
   - With XAMPP: place the `bankapp/public` folder in your Apache docroot or map a virtual host.
   - Or use PHP built-in server (dev only):
     ```bash
     php -S 127.0.0.1:8000 -t public
     ```
4. Login with the seeded admin credentials:
   - Email: `admin@bank.local`
   - Password: `Admin@12345`

## Features
- Secure login/logout with password hashing (bcrypt) and session hardening
- CSRF tokens for all state-changing requests
- Customers: create, read, update, delete
- Accounts: create, read, update (status/type), delete
- Transactions: deposit, withdraw (balance updates and audit log)

## Project Structure
```
bankapp/
  public/            # Web root
  src/               # PHP source (db, auth, csrf, utils)
  db/schema.sql      # Database schema + seed data
```

## Notes
- This is a teaching scaffold, not production-hardened banking software.
- For production, add HTTPS, secure cookies, proper CORS, rate limiting, and strict roles.