<?php 
	// Load .env file if exists
	$envFile = dirname(__DIR__) . '/.env';
	if (file_exists($envFile)) {
		$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {
			if (strpos(trim($line), '#') === 0) {
				continue; // Skip comments
			}
			if (strpos($line, '=') !== false) {
				list($key, $value) = explode('=', $line, 2);
				$_ENV[trim($key)] = trim($value);
			}
		}
	}

	// Define database constants from .env or use defaults
	define('DB_SERVER', isset($_ENV['DB_SERVER']) ? $_ENV['DB_SERVER'] : 'localhost');
	define('DB_USER', isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root');
	define('DB_PASS', isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '');
	define('DB_NAME', isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'cargo');
	define('DB_PORT', isset($_ENV['DB_PORT']) ? $_ENV['DB_PORT'] : 3306);
    ?>
    <?php
	// Try SSL connection for cloud databases, fall back to regular connection
	$con = mysqli_init();
	
	if (!$con) {
		die("mysqli_init failed");
	}
	
	// Check if SSL certificate exists (for cloud databases like Aiven)
	$ssl_cert = dirname(__DIR__) . '/ca-certificate.crt';
	$use_ssl = file_exists($ssl_cert) && DB_SERVER !== 'localhost';
	
	if ($use_ssl) {
		// Set SSL certificate for secure connection
		mysqli_ssl_set($con, NULL, NULL, $ssl_cert, NULL, NULL);
		
		// Attempt SSL connection
		$connected = @mysqli_real_connect(
			$con,
			DB_SERVER,
			DB_USER,
			DB_PASS,
			DB_NAME,
			DB_PORT,
			NULL,
			MYSQLI_CLIENT_SSL
		);
	} else {
		// Regular connection for localhost or non-SSL databases
		$connected = @mysqli_real_connect(
			$con,
			DB_SERVER,
			DB_USER,
			DB_PASS,
			DB_NAME,
			DB_PORT
		);
	}
	
	// If connection failed, show error
	if (!$connected || mysqli_connect_errno()) {		
		die("Could not connect to the server: " .
			mysqli_connect_errno() . " (" . mysqli_connect_error() . ")");
	}
	
	// Set charset for proper character encoding
	mysqli_set_charset($con, 'utf8mb4');
	?>

	

	
