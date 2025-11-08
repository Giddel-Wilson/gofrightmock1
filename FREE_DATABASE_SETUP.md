# FREE MySQL Database Setup - Step by Step

## 🆓 100% FREE Options That Actually Work

### Option 1: Aiven (RECOMMENDED - Always Free)

**Why Aiven?**
- ✅ Always free tier (no credit card required)
- ✅ 1 CPU, 1GB RAM, 5GB storage
- ✅ Reliable and fast
- ✅ Easy setup (5 minutes)

#### Step-by-Step Setup:

**1. Create Account**
```
URL: https://aiven.io/mysql
1. Click "Sign up free"
2. Use your email or GitHub
3. No credit card needed!
```

**2. Create MySQL Service**
```
1. Click "Create service"
2. Select "MySQL"
3. Choose cloud: AWS or Google Cloud
4. Region: Choose closest to you (e.g., us-east-1)
5. Plan: Select "Hobbyist" (FREE forever)
6. Service name: gofreight-mysql
7. Click "Create service"
8. Wait 3-5 minutes for deployment
```

**3. Get Connection Details**
```
Once running, click your service and you'll see:

Host: gofreight-mysql-yourproject.aivencloud.com
Port: 12345
Username: avnadmin
Password: [shown in dashboard]
Database: defaultdb
SSL: Required (download CA certificate)
```

**4. Update Your .env File**
```env
DB_SERVER=gofreight-mysql-yourproject.aivencloud.com
DB_USER=avnadmin
DB_PASS=your_password_from_aiven
DB_NAME=defaultdb
```

**5. Download SSL Certificate**
```
1. In Aiven dashboard, click "Download CA cert"
2. Save as: /Users/maintenance/Documents/Cargo-Tracking-System-master/ca-certificate.crt
```

**6. Update db_connection.php for SSL**
See instructions below in "SSL Configuration" section.

**7. Import Database**
```bash
# Connect to Aiven
mysql --host=gofreight-mysql-yourproject.aivencloud.com \
      --port=12345 \
      --user=avnadmin \
      --password=your_password \
      --ssl-ca=ca-certificate.crt \
      defaultdb < database.sql
```

---

### Option 2: FreeSQLDatabase.com (No Registration)

**Instant Setup - No Account Needed!**

**1. Get Instant Database**
```
URL: https://www.freesqldatabase.com/
1. Visit website
2. Click "Create Database"
3. Instant credentials appear on screen!
```

**2. Example Credentials You'll Get**
```
Host: sql12.freesqldatabase.com
Database Name: sql12123456
Username: sql12123456
Password: TgK9xYz3Rt
Port: 3306
```

**3. Update Your .env**
```env
DB_SERVER=sql12.freesqldatabase.com
DB_USER=sql12123456
DB_PASS=TgK9xYz3Rt
DB_NAME=sql12123456
```

**4. Import Database**
```bash
mysql -h sql12.freesqldatabase.com -u sql12123456 -pTgK9xYz3Rt sql12123456 < database.sql
```

**Limitations:**
- 5MB storage (good for testing)
- May have ads on their management interface
- Less reliable for production

---

### Option 3: db4free.net (Free MySQL)

**1. Register**
```
URL: https://www.db4free.net/signup.php
1. Fill in the form
2. Database name: gofreight (or your choice)
3. Username: your_username
4. Password: your_password
5. Email: your_email
6. Submit and wait for confirmation email
```

**2. Update Your .env**
```env
DB_SERVER=db4free.net
DB_USER=your_username
DB_PASS=your_password
DB_NAME=gofreight
```

**3. Import Database**
```bash
mysql -h db4free.net -u your_username -p gofreight < database.sql
```

**Note:** This is for testing only, not recommended for production.

---

### Option 4: Railway (Has FREE $5 Credit)

**Correction:** Railway gives $5 FREE credit per month (no card needed initially)

**1. Sign Up**
```
URL: https://railway.app/
1. Sign up with GitHub (free)
2. No credit card required initially
3. Get $5 monthly credit
```

**2. Create MySQL**
```
1. Click "New Project"
2. Select "Provision MySQL"
3. Wait 2 minutes
```

**3. Get Connection Details**
```
Click MySQL service → Variables tab:

MYSQLHOST=containers-us-west-123.railway.app
MYSQLPORT=1234
MYSQLUSER=root
MYSQLPASSWORD=abc123xyz
MYSQLDATABASE=railway
```

**4. Update .env**
```env
DB_SERVER=containers-us-west-123.railway.app
DB_USER=root
DB_PASS=abc123xyz
DB_NAME=railway
```

**5. Import Database**
```bash
mysql -h containers-us-west-123.railway.app -u root -pabc123xyz railway < database.sql
```

**Cost:** $5/month credit covers small projects for free!

---

## 🔧 SSL Configuration for Cloud Databases

Update `/Users/maintenance/Documents/Cargo-Tracking-System-master/includes/db_connection.php`:

```php
<?php 
// Load environment variables
$envFile = dirname(__DIR__) . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

define('DB_SERVER', $_ENV['DB_SERVER'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'cargo');

// Try SSL connection first (for cloud databases)
$con = mysqli_init();

if (!$con) {
    die("mysqli_init failed");
}

// Set SSL certificate if exists (for Aiven)
$ssl_cert = dirname(__DIR__) . '/ca-certificate.crt';
if (file_exists($ssl_cert)) {
    mysqli_ssl_set($con, NULL, NULL, $ssl_cert, NULL, NULL);
}

// Attempt connection with SSL
$connected = @mysqli_real_connect(
    $con,
    DB_SERVER,
    DB_USER,
    DB_PASS,
    DB_NAME,
    3306,
    NULL,
    MYSQLI_CLIENT_SSL
);

// If SSL fails, try regular connection
if (!$connected) {
    $con = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
}

if (!$con || mysqli_connect_errno()) {
    die("Could not connect to the server: " . mysqli_connect_error());
}

mysqli_set_charset($con, 'utf8mb4');
?>
```

---

## 📋 Testing Your Database Connection

After updating .env, test the connection:

```bash
cd /Users/maintenance/Documents/Cargo-Tracking-System-master
php -r "include 'includes/db_connection.php'; echo 'Connected successfully!' . PHP_EOL;"
```

If successful, import the database:

```bash
# For Aiven (with SSL)
mysql --host=your-host \
      --user=avnadmin \
      --password=your_password \
      --ssl-ca=ca-certificate.crt \
      defaultdb < database.sql

# For others (no SSL)
mysql -h your-host -u your_user -pyour_password your_database < database.sql
```

---

## 🎯 My Recommendation

**For Production (Free & Reliable):**
👉 **Aiven** - Always free, reliable, professional

**For Quick Testing:**
👉 **FreeSQLDatabase.com** - Instant, no signup

**For Development:**
👉 **Railway** - $5/month credit covers small projects

---

## ⚠️ Important Notes

1. **Aiven takes 3-5 minutes to provision** - be patient
2. **FreeSQLDatabase is instant** but has 5MB limit
3. **Railway requires GitHub** signup
4. **All of these are FREE** for small projects

Choose Aiven for the best balance of free + reliable + professional!
