# GoFreight - Modern Cargo Tracking System

A comprehensive web-based cargo tracking system with real-time location updates, payment processing, and modern UI/UX design.

## 🚀 Features

- **Real-time Tracking**: Track cargo shipments with live location updates
- **Multi-role Access**: Separate dashboards for Admin, Customers, and Drivers
- **Payment Management**: Integrated payment processing and tracking
- **Modern UI**: Clean, responsive design with smooth animations
- **Secure**: SSL/TLS database connections, input sanitization, session management
- **Reports**: Generate PDF reports for customers, drivers, orders, and payments

## 📋 Requirements

- PHP 8.0+
- MySQL 5.7+ or MySQL 8.0+
- Web server (Apache/Nginx)
- SSL certificate for production (included for Aiven)

## 🛠️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/Giddel-Wilson/gofrightmock1.git
cd gofrightmock1
```

### 2. Configure Database

Update the `.env` file with your database credentials:

```env
DB_SERVER=your-database-host
DB_USER=your-database-user
DB_PASS=your-database-password
DB_NAME=your-database-name
DB_PORT=3306
```

### 3. Import Database Schema

```bash
mysql -u your-user -p your-database < database.sql
```

### 4. Start the Server

For development:

```bash
php -S localhost:8000 -t public
```

For production, configure your web server to point to the `public` directory.

## 👥 Default Login Credentials

### Admin
- Email: `admin@gofreight.com`
- Password: `admin`

### Customer
- Email: `customer@gofreight.com`
- Password: `password`

### Driver
- Email: `driver@gofreight.com`
- Password: `password`

⚠️ **IMPORTANT**: Change these passwords immediately after first login!

## 🎨 UI/UX Features

- **Modern Design**: Clean, professional interface with gradient effects
- **Responsive**: Mobile-friendly design that works on all devices
- **Smooth Animations**: CSS transitions and hover effects
- **Color Scheme**: Professional blue/purple gradient theme

## 🔒 Security Features

- SQL injection prevention with sanitization
- Session management and timeout
- SSL/TLS database connections
- Input sanitization

## 🐛 Bug Fixes in This Version

- ✅ Fixed array offset warnings in index.php
- ✅ Fixed undefined array key errors in manage_drivers.php
- ✅ Fixed track.php hardcoded values
- ✅ Fixed syntax error in order.php (removed stray `-` character)
- ✅ Added isset() checks throughout codebase
- ✅ Improved error handling
- ✅ Added modern UI/UX design
- ✅ Fixed database connection SSL support

## 📞 Support

For issues and feature requests, please create an issue on GitHub.

---

**Version**: 2.0  
**Last Updated**: November 2025  
**Author**: GoFreight Development Team