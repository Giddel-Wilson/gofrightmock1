# Vercel Deployment Guide for GoFreight

## ⚠️ Important Note About PHP on Vercel

**Vercel does NOT support traditional PHP applications.** Vercel is designed for:
- Next.js (React)
- Node.js
- Static sites
- Serverless functions

Your current application is built with PHP and MySQL, which requires a traditional server environment.

---

## Recommended Hosting Options for PHP Applications

### Option 1: Heroku (Recommended for PHP)

Heroku supports PHP applications with MySQL/PostgreSQL.

#### Step-by-Step Deployment:

**1. Install Heroku CLI**
```bash
brew install heroku/brew/heroku
```

**2. Prepare Your Application**

Create `composer.json` in project root:
```json
{
    "require": {
        "php": "^8.0.0",
        "ext-mysqli": "*"
    }
}
```

Create `Procfile` in project root:
```
web: vendor/bin/heroku-php-apache2 public/
```

Create `.htaccess` in public folder:
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]
</IfModule>
```

**3. Initialize Git (if not already)**
```bash
cd /Users/maintenance/Documents/Cargo-Tracking-System-master
git init
git add .
git commit -m "Initial commit for GoFreight"
```

**4. Create Heroku App**
```bash
heroku create gofreight-cargo
heroku addons:create jawsdb:kitefin  # Free MySQL addon
```

**5. Set Environment Variables**
```bash
# Get database URL from JawsDB
heroku config:get JAWSDB_URL

# It will look like:
# mysql://username:password@host:3306/database

# Update your db_connection.php to use DATABASE_URL
heroku config:set APP_ENV=production
```

**6. Deploy**
```bash
git push heroku main
```

**7. Import Database**
```bash
# Get connection details
heroku config:get JAWSDB_URL

# Connect and import
mysql -h host -u username -p database < database.sql
```

---

### Option 2: Railway (Easiest)

Railway supports PHP with zero configuration.

**1. Sign Up**
- Go to https://railway.app/
- Sign up with GitHub

**2. Deploy**
- Click "New Project"
- Select "Deploy from GitHub repo"
- Connect your repository
- Railway auto-detects PHP

**3. Add MySQL**
- Click "New"
- Select "Database" → "MySQL"
- Get connection details from Variables tab

**4. Set Environment Variables**
In Railway dashboard:
```
DB_SERVER=containers-us-west-xxx.railway.app
DB_USER=root
DB_PASS=xxxxx
DB_NAME=railway
```

**5. Deploy**
- Automatic on git push

---

### Option 3: DigitalOcean App Platform

**1. Create Account**
- Go to https://www.digitalocean.com/

**2. Create App**
- Click "Create" → "Apps"
- Connect GitHub repository
- Select branch
- Set root directory to `/public`

**3. Configure**
- Detected type: PHP
- Build command: `composer install`
- Run command: `heroku-php-apache2 public/`

**4. Add Database**
- Create Managed MySQL Database
- Link to your app

**5. Environment Variables**
Add in App Platform settings:
```
DB_SERVER=your-db-host
DB_USER=doadmin
DB_PASS=xxxxx
DB_NAME=defaultdb
```

---

### Option 4: Traditional cPanel Hosting

Cheapest option for PHP/MySQL apps.

**Recommended Providers:**
- Namecheap ($2.88/month)
- Bluehost ($2.95/month)
- SiteGround ($3.99/month)
- A2 Hosting ($2.99/month)

**Steps:**
1. Purchase hosting plan
2. Upload files via FTP or File Manager
3. Create MySQL database in cPanel
4. Import database.sql via phpMyAdmin
5. Update .env with cPanel database credentials
6. Point domain to hosting

---

## Converting to Work with Vercel (Advanced)

If you really want to use Vercel, you need to rebuild the app. Here are two approaches:

### Approach 1: Use Vercel Serverless Functions (PHP Runtime)

Vercel supports PHP through custom runtimes, but it's complex.

**1. Install Vercel PHP Runtime**
```bash
npm init -y
npm install @vercel/php
```

**2. Create `vercel.json`**
```json
{
  "functions": {
    "api/**/*.php": {
      "runtime": "@vercel/php@0.6.0"
    }
  },
  "routes": [
    { "src": "/(.*)", "dest": "/api/$1" }
  ],
  "env": {
    "DB_SERVER": "@db_server",
    "DB_USER": "@db_user",
    "DB_PASS": "@db_pass",
    "DB_NAME": "@db_name"
  }
}
```

**3. Restructure Files**
Move all PHP files to `/api` directory.

**4. Use Serverless MySQL**
PlanetScale is required (supports serverless connections).

**5. Deploy**
```bash
vercel
```

**Limitations:**
- Session handling is tricky
- File uploads need S3
- Complex to maintain

### Approach 2: Rebuild as Next.js (Recommended if using Vercel)

This requires completely rebuilding your application.

**Tech Stack:**
- Frontend: Next.js (React)
- Backend: Next.js API Routes
- Database: PlanetScale (MySQL)
- ORM: Prisma

**Estimated Time:** 2-4 weeks for full rebuild

---

## Configuration Files for Different Platforms

### For Heroku

**composer.json:**
```json
{
  "require": {
    "php": "^8.0",
    "ext-mysqli": "*",
    "ext-mbstring": "*",
    "ext-json": "*"
  },
  "require-dev": {
    "heroku/heroku-buildpack-php": "*"
  }
}
```

**Procfile:**
```
web: vendor/bin/heroku-php-apache2 public/
```

### For Railway

Create `railway.json`:
```json
{
  "build": {
    "builder": "NIXPACKS"
  },
  "deploy": {
    "startCommand": "php -S 0.0.0.0:$PORT -t public",
    "restartPolicyType": "ON_FAILURE",
    "restartPolicyMaxRetries": 10
  }
}
```

### For DigitalOcean

Create `.do/app.yaml`:
```yaml
name: gofreight
services:
- name: web
  environment_slug: php
  github:
    branch: main
    deploy_on_push: true
    repo: yourusername/gofreight
  http_port: 8080
  instance_count: 1
  instance_size_slug: basic-xxs
  routes:
  - path: /
  source_dir: /
  envs:
  - key: DB_SERVER
    value: ${db.HOSTNAME}
  - key: DB_USER
    value: ${db.USERNAME}
  - key: DB_PASS
    value: ${db.PASSWORD}
  - key: DB_NAME
    value: ${db.DATABASE}
databases:
- engine: MYSQL
  name: db
  production: false
  version: "8"
```

---

## Updated .env for Production

Update your `.env` file based on your chosen platform:

```env
# For Heroku with JawsDB
DB_SERVER=your-jawsdb-host.com
DB_USER=your-jawsdb-user
DB_PASS=your-jawsdb-password
DB_NAME=your-jawsdb-database

# For Railway
DB_SERVER=containers-us-west-xxx.railway.app
DB_USER=root
DB_PASS=railway-generated-password
DB_NAME=railway

# For DigitalOcean
DB_SERVER=db-mysql-nyc3-xxxxx-do-user-xxxxx-0.b.db.ondigitalocean.com
DB_USER=doadmin
DB_PASS=do-generated-password
DB_NAME=defaultdb

# For PlanetScale (if using Vercel)
DB_SERVER=aws.connect.psdb.cloud
DB_USER=your-planetscale-user
DB_PASS=pscale_pw_xxxxx
DB_NAME=gofreight-prod

# Application Settings
SERVER_PORT=8080
SERVER_HOST=0.0.0.0
APP_ENV=production
```

---

## Security for Production

**1. Update db_connection.php for SSL**

Replace the current connection code with:

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

// Use environment variables or Heroku DATABASE_URL
if (isset($_ENV['DATABASE_URL'])) {
    $url = parse_url($_ENV['DATABASE_URL']);
    define('DB_SERVER', $url['host']);
    define('DB_USER', $url['user']);
    define('DB_PASS', $url['pass']);
    define('DB_NAME', ltrim($url['path'], '/'));
} else {
    define('DB_SERVER', $_ENV['DB_SERVER'] ?? 'localhost');
    define('DB_USER', $_ENV['DB_USER'] ?? 'root');
    define('DB_PASS', $_ENV['DB_PASS'] ?? '');
    define('DB_NAME', $_ENV['DB_NAME'] ?? 'cargo');
}

// Connect with SSL support
$con = mysqli_init();

if (!$con) {
    die("mysqli_init failed");
}

// For cloud databases (PlanetScale, Railway, etc.)
mysqli_options($con, MYSQLI_OPT_CONNECT_TIMEOUT, 10);

// Attempt connection
$success = mysqli_real_connect(
    $con,
    DB_SERVER,
    DB_USER,
    DB_PASS,
    DB_NAME,
    3306
);

if (!$success) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($con, 'utf8mb4');
?>
```

**2. Create .gitignore**
```
.env
.env.local
vendor/
node_modules/
.DS_Store
*.log
```

**3. Secure Session Handling**

Add to `includes/session.php`:
```php
<?php
// Production session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // HTTPS only
ini_set('session.use_only_cookies', 1);
session_start();
```

---

## Deployment Checklist

- [ ] Choose hosting platform
- [ ] Set up MySQL database
- [ ] Update .env with production credentials
- [ ] Test database connection
- [ ] Import database schema
- [ ] Update db_connection.php for SSL
- [ ] Create .gitignore
- [ ] Commit code to Git
- [ ] Deploy to platform
- [ ] Set environment variables on platform
- [ ] Test all features
- [ ] Set up custom domain
- [ ] Enable HTTPS
- [ ] Configure backups

---

## Recommended Choice

For your PHP/MySQL application, I recommend:

**1. Railway** - Easiest, free tier available
**2. Heroku** - More mature, better documentation
**3. DigitalOcean App Platform** - Good balance of features and price

**Avoid Vercel** unless you want to rebuild the entire application in Next.js.

---

## Need Help?

If you choose to rebuild for Vercel using Next.js, I can help with:
1. Next.js project setup
2. API route creation
3. Prisma ORM configuration
4. Frontend migration
5. Authentication setup
6. Deployment configuration

Let me know which platform you'd like to use!
