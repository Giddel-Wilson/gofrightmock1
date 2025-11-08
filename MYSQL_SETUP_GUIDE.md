# MySQL Database Setup Guide for GoFreight

This guide will walk you through creating your own MySQL database for production use.

## Option 1: Using PlanetScale (Recommended for Vercel)

PlanetScale is a serverless MySQL platform that works perfectly with Vercel.

### Step 1: Create PlanetScale Account
1. Go to https://planetscale.com/
2. Sign up for a free account
3. Click "Create a new database"
4. Enter database name: `gofreight-prod`
5. Select region closest to your users (e.g., US East, EU West, etc.)
6. Click "Create database"

### Step 2: Get Connection Details
1. In your database dashboard, click "Connect"
2. Select "Connect with: PHP"
3. You'll see connection details like:
   ```
   Host: aws.connect.psdb.cloud
   Username: xxxxxxxxxxxxxxxx
   Password: pscale_pw_xxxxxxxxxxxxxxxx
   Database: gofreight-prod
   ```
4. Copy these values - you'll need them for your .env file

### Step 3: Update Your .env File
Replace the current values in `.env` with PlanetScale credentials:

```env
DB_SERVER=aws.connect.psdb.cloud
DB_USER=your_planetscale_username
DB_PASS=your_planetscale_password
DB_NAME=gofreight-prod
```

**Important:** PlanetScale uses SSL by default. You may need to update `db_connection.php`

### Step 4: Import Database Schema
PlanetScale doesn't support traditional SQL import. Use their CLI:

```bash
# Install PlanetScale CLI
brew install planetscale/tap/pscale

# Login
pscale auth login

# Connect to your database
pscale shell gofreight-prod main

# Copy and paste the SQL from database.sql line by line
```

Or use MySQL Workbench with the connection string provided.

---

## Option 2: Using Railway

Railway offers easy MySQL hosting with simple setup.

### Step 1: Create Railway Account
1. Go to https://railway.app/
2. Sign up with GitHub
3. Click "New Project"
4. Select "Provision MySQL"

### Step 2: Get Connection Details
1. Click on your MySQL service
2. Go to "Variables" tab
3. You'll see:
   - `MYSQLHOST`: The host address
   - `MYSQLUSER`: Usually `root`
   - `MYSQLPASSWORD`: Auto-generated password
   - `MYSQLDATABASE`: Database name
   - `MYSQLPORT`: Usually `3306`

### Step 3: Update .env File
```env
DB_SERVER=containers-us-west-xxx.railway.app
DB_USER=root
DB_PASS=your_railway_password
DB_NAME=railway
```

### Step 4: Import Database
Use MySQL client to connect:
```bash
mysql -h containers-us-west-xxx.railway.app -u root -p'your_password' railway < database.sql
```

---

## Option 3: AWS RDS MySQL

Professional option for production applications.

### Step 1: Create RDS Instance
1. Log in to AWS Console
2. Go to RDS service
3. Click "Create database"
4. Choose MySQL
5. Select template: "Free tier" or "Production"
6. Settings:
   - DB instance identifier: `gofreight-db`
   - Master username: `admin`
   - Master password: Create a strong password
7. Instance configuration: db.t3.micro (free tier eligible)
8. Storage: 20 GB
9. Connectivity:
   - Public access: Yes (for initial setup)
   - VPC security group: Create new
10. Click "Create database"

### Step 2: Configure Security Group
1. Go to EC2 → Security Groups
2. Find the RDS security group
3. Add inbound rule:
   - Type: MYSQL/Aurora
   - Port: 3306
   - Source: Anywhere (0.0.0.0/0) or Your IP for security

### Step 3: Get Connection Details
1. Go to RDS → Databases
2. Click your database
3. Copy the endpoint (e.g., `gofreight-db.xxxxx.us-east-1.rds.amazonaws.com`)

### Step 4: Update .env File
```env
DB_SERVER=gofreight-db.xxxxx.us-east-1.rds.amazonaws.com
DB_USER=admin
DB_PASS=your_aws_password
DB_NAME=cargo
```

### Step 5: Import Database
```bash
mysql -h gofreight-db.xxxxx.us-east-1.rds.amazonaws.com -u admin -p < database.sql
```

---

## Option 4: DigitalOcean Managed Database

Good balance of price and features.

### Step 1: Create Managed Database
1. Log in to DigitalOcean
2. Click "Create" → "Databases"
3. Select MySQL
4. Choose datacenter region
5. Select plan (starts at $15/month)
6. Name: `gofreight-mysql`
7. Click "Create"

### Step 2: Get Connection Details
1. Click on your database cluster
2. Copy connection details:
   - Host
   - Port
   - Username (usually `doadmin`)
   - Password
   - Database name

### Step 3: Update .env File
```env
DB_SERVER=db-mysql-nyc3-xxxxx-do-user-xxxxx-0.b.db.ondigitalocean.com
DB_USER=doadmin
DB_PASS=your_do_password
DB_NAME=defaultdb
```

### Step 4: Import Database
Use their connection string or MySQL client.

---

## Testing Your Database Connection

After updating your .env file, test the connection:

```bash
cd /Users/maintenance/Documents/Cargo-Tracking-System-master
php -r "include 'includes/db_connection.php'; echo 'Connection successful!'; echo PHP_EOL;"
```

If successful, you should see: `Connection successful!`

---

## Important: SSL/TLS Connections

Many cloud databases require SSL. Update `includes/db_connection.php`:

```php
<?php 
	// Load .env file
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
	
	// For cloud databases that require SSL
	$con = mysqli_init();
	
	// Enable SSL if needed (uncomment for PlanetScale, Railway, etc.)
	// mysqli_ssl_set($con, NULL, NULL, NULL, NULL, NULL);
	
	mysqli_real_connect(
		$con,
		DB_SERVER,
		DB_USER,
		DB_PASS,
		DB_NAME,
		3306,
		NULL,
		MYSQLI_CLIENT_SSL
	);
	
	if (mysqli_connect_errno()) {
		die("Could not connect to the server: " . 
			mysqli_connect_errno() . " (" . mysqli_connect_error() . ")");
	}
?>
```

---

## Security Best Practices

1. **Never commit .env to Git**
   ```bash
   echo ".env" >> .gitignore
   ```

2. **Use environment variables in production**

3. **Restrict database access by IP**

4. **Use strong passwords**

5. **Enable SSL/TLS**

6. **Regular backups**
   - Most managed services auto-backup
   - For manual: `mysqldump -h HOST -u USER -p DATABASE > backup.sql`

---

## Cost Comparison

| Provider      | Free Tier | Paid Plans Start | Best For |
|---------------|-----------|------------------|----------|
| PlanetScale   | 5GB       | $29/month       | Serverless, Vercel |
| Railway       | $5 credit | Pay-as-you-go   | Easy setup |
| AWS RDS       | 12 months | $15/month       | Enterprise |
| DigitalOcean  | None      | $15/month       | Simplicity |
| Aiven         | $10/month | $10/month       | Multiple clouds |

---

## Next Steps

1. Choose your database provider
2. Create the database
3. Update your `.env` file
4. Test the connection
5. Import the schema using `database.sql`
6. Proceed to Vercel deployment (see VERCEL_DEPLOYMENT.md)
