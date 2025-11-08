# Cargo Tracking System - Setup Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web Server (Apache/Nginx) or PHP built-in server

## Installation Steps

### 1. Install Dependencies (macOS)

```bash
# Install PHP
brew install php

# Install MySQL
brew install mysql

# Start MySQL service
brew services start mysql
```

### 2. Database Setup

```bash
# Login to MySQL (default root with no password)
mysql -u root

# Or if password is set
mysql -u root -p

# Run the database.sql file
mysql -u root < database.sql
```

Or manually:
```bash
# Create database and import schema
mysql -u root -e "CREATE DATABASE IF NOT EXISTS cargo;"
mysql -u root cargo < database.sql
```

### 3. Configuration

The `.env` file has been created with default settings:
```
DB_SERVER=localhost
DB_USER=root
DB_PASS=
DB_NAME=cargo
SERVER_PORT=8000
SERVER_HOST=localhost
```

Update these values if your database configuration differs.

### 4. Run the Development Server

From the project root directory:
```bash
cd public
php -S localhost:8000
```

Or from root with the startup script:
```bash
./start-server.sh
```

## Default Credentials

### Admin Login
- URL: http://localhost:8000/admin-login.php
- Username: `admin`
- Password: `admin`

### Test Customer
- URL: http://localhost:8000/signin.php
- Username: `testcustomer`
- Password: `password`

### Test Driver
- URL: http://localhost:8000/driver-login.php
- Username: `testdriver`
- Password: `password`

## Application Structure

- `/public/` - Main application files
- `/includes/` - Database connection, functions, headers
- `/public/home/` - Landing page
- `/public/assets/` - CSS, JS, fonts
- `/public/bin/` - Additional pages

## Main Features

1. **Customer Portal**: Register, place orders, track cargo
2. **Driver Portal**: View assigned deliveries, update status
3. **Admin Portal**: Manage customers, drivers, orders, payments
4. **Tracking System**: Real-time cargo tracking
5. **Payment Integration**: M-Pesa payment tracking

## Troubleshooting

### Cannot connect to database
- Ensure MySQL is running: `brew services list`
- Check database credentials in `.env`
- Verify database exists: `mysql -u root -e "SHOW DATABASES;"`

### Port 8000 already in use
- Change SERVER_PORT in `.env`
- Or kill the process: `lsof -ti:8000 | xargs kill -9`

### PHP not found
- Install PHP: `brew install php`
- Check version: `php --version`

## Project URLs

- Home: http://localhost:8000/home/home.php
- Admin Login: http://localhost:8000/admin-login.php
- Customer Sign In: http://localhost:8000/signin.php
- Driver Login: http://localhost:8000/driver-login.php
- Track Cargo: http://localhost:8000/track.php

## Database Tables

- `admin` - Admin users
- `customer` - Customer accounts
- `driver` - Driver accounts
- `cargo` - Cargo/orders
- `mpesa` - Payment transactions
- `location_updates` - Real-time tracking data
