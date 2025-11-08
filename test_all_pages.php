#!/usr/bin/env php
<?php
/**
 * Comprehensive Testing Script for GoFreight Cargo Tracking System
 * Tests all pages, functionality, and reports errors
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "🚀 GoFreight Testing Script\n";
echo "====================================\n\n";

// Include database connection
require_once 'includes/db_connection.php';
require_once 'includes/functions.php';

$test_results = [];
$error_count = 0;
$success_count = 0;

/**
 * Test database connection
 */
function testDatabaseConnection($con) {
    global $test_results, $error_count, $success_count;
    
    echo "📊 Testing Database Connection...\n";
    
    if ($con && mysqli_ping($con)) {
        echo "  ✅ Database connection successful\n";
        $test_results[] = ['test' => 'Database Connection', 'status' => 'PASS'];
        $success_count++;
        return true;
    } else {
        echo "  ❌ Database connection failed: " . mysqli_connect_error() . "\n";
        $test_results[] = ['test' => 'Database Connection', 'status' => 'FAIL', 'error' => mysqli_connect_error()];
        $error_count++;
        return false;
    }
}

/**
 * Test if all required tables exist
 */
function testTablesExist($con) {
    global $test_results, $error_count, $success_count;
    
    echo "\n📋 Testing Database Tables...\n";
    
    $required_tables = ['admin', 'customer', 'driver', 'cargo', 'mpesa', 'location_updates'];
    $missing_tables = [];
    
    foreach ($required_tables as $table) {
        $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
        if (mysqli_num_rows($result) == 0) {
            $missing_tables[] = $table;
            echo "  ❌ Table '$table' is missing\n";
        } else {
            echo "  ✅ Table '$table' exists\n";
        }
    }
    
    if (empty($missing_tables)) {
        $test_results[] = ['test' => 'Database Tables', 'status' => 'PASS'];
        $success_count++;
        return true;
    } else {
        $test_results[] = ['test' => 'Database Tables', 'status' => 'FAIL', 'error' => 'Missing tables: ' . implode(', ', $missing_tables)];
        $error_count++;
        return false;
    }
}

/**
 * Test table data
 */
function testTableData($con) {
    global $test_results, $error_count, $success_count;
    
    echo "\n📊 Testing Table Data...\n";
    
    $tables = [
        'admin' => 'SELECT COUNT(*) as count FROM admin',
        'customer' => 'SELECT COUNT(*) as count FROM customer',
        'driver' => 'SELECT COUNT(*) as count FROM driver',
        'cargo' => 'SELECT COUNT(*) as count FROM cargo',
        'mpesa' => 'SELECT COUNT(*) as count FROM mpesa'
    ];
    
    foreach ($tables as $table => $query) {
        $result = mysqli_query($con, $query);
        if ($result) {
            $data = mysqli_fetch_assoc($result);
            $count = $data['count'];
            echo "  ✅ Table '$table' has $count records\n";
        } else {
            echo "  ❌ Error querying '$table': " . mysqli_error($con) . "\n";
            $error_count++;
        }
    }
    
    $success_count++;
}

/**
 * Test PHP files for syntax errors
 */
function testPHPFiles() {
    global $test_results, $error_count, $success_count;
    
    echo "\n📝 Testing PHP Files for Syntax Errors...\n";
    
    $php_files = glob('public/*.php');
    $syntax_errors = [];
    
    foreach ($php_files as $file) {
        $output = [];
        $return_var = 0;
        exec("php -l " . escapeshellarg($file) . " 2>&1", $output, $return_var);
        
        if ($return_var !== 0) {
            $syntax_errors[] = ['file' => $file, 'error' => implode("\n", $output)];
            echo "  ❌ Syntax error in $file\n";
            $error_count++;
        } else {
            echo "  ✅ $file - No syntax errors\n";
        }
    }
    
    if (empty($syntax_errors)) {
        $test_results[] = ['test' => 'PHP Syntax', 'status' => 'PASS'];
        $success_count++;
        return true;
    } else {
        $test_results[] = ['test' => 'PHP Syntax', 'status' => 'FAIL', 'errors' => $syntax_errors];
        return false;
    }
}

/**
 * Test authentication files exist
 */
function testAuthenticationFiles() {
    global $test_results, $error_count, $success_count;
    
    echo "\n🔐 Testing Authentication Files...\n";
    
    $auth_files = [
        'public/admin-login.php',
        'public/signin.php',
        'public/driver-login.php',
        'public/auth_admin.php',
        'public/auth_cus.php',
        'public/logout.php',
        'includes/session.php'
    ];
    
    $missing_files = [];
    
    foreach ($auth_files as $file) {
        if (file_exists($file)) {
            echo "  ✅ $file exists\n";
        } else {
            echo "  ❌ $file is missing\n";
            $missing_files[] = $file;
            $error_count++;
        }
    }
    
    if (empty($missing_files)) {
        $test_results[] = ['test' => 'Authentication Files', 'status' => 'PASS'];
        $success_count++;
        return true;
    } else {
        $test_results[] = ['test' => 'Authentication Files', 'status' => 'FAIL', 'error' => 'Missing files: ' . implode(', ', $missing_files)];
        return false;
    }
}

/**
 * Test critical functions exist
 */
function testFunctionsExist() {
    global $test_results, $error_count, $success_count;
    
    echo "\n🔧 Testing Critical Functions...\n";
    
    $required_functions = ['confirm_query', 'test_input', 'rad', 'cost', 'driver'];
    $missing_functions = [];
    
    foreach ($required_functions as $func) {
        if (function_exists($func)) {
            echo "  ✅ Function '$func' exists\n";
        } else {
            echo "  ❌ Function '$func' is missing\n";
            $missing_functions[] = $func;
            $error_count++;
        }
    }
    
    if (empty($missing_functions)) {
        $test_results[] = ['test' => 'Required Functions', 'status' => 'PASS'];
        $success_count++;
        return true;
    } else {
        $test_results[] = ['test' => 'Required Functions', 'status' => 'FAIL', 'error' => 'Missing functions: ' . implode(', ', $missing_functions)];
        return false;
    }
}

/**
 * Test for common vulnerabilities
 */
function testSecurityIssues() {
    global $test_results, $error_count, $success_count;
    
    echo "\n🔒 Testing for Security Issues...\n";
    
    $php_files = glob('public/*.php');
    $security_issues = [];
    
    foreach ($php_files as $file) {
        $content = file_get_contents($file);
        
        // Check for SQL injection vulnerabilities
        if (preg_match('/\$_(?:GET|POST|REQUEST)\[.*?\].*?(?:SELECT|INSERT|UPDATE|DELETE)/i', $content)) {
            $security_issues[] = "$file: Potential SQL injection (unsanitized input in query)";
            echo "  ⚠️  $file: Potential SQL injection risk\n";
        }
        
        // Check for missing mysqli_real_escape_string
        if (preg_match('/\$_(?:GET|POST|REQUEST).*?mysqli_query/i', $content) && 
            !preg_match('/mysqli_real_escape_string/', $content)) {
            // This is already flagged above
        }
    }
    
    if (empty($security_issues)) {
        echo "  ✅ No obvious security issues found\n";
        $success_count++;
    } else {
        echo "  ⚠️  Found " . count($security_issues) . " potential security issues\n";
    }
}

/**
 * Generate summary report
 */
function generateSummary() {
    global $test_results, $error_count, $success_count;
    
    echo "\n\n";
    echo "====================================\n";
    echo "📊 TEST SUMMARY\n";
    echo "====================================\n";
    echo "Total Tests: " . ($success_count + $error_count) . "\n";
    echo "✅ Passed: $success_count\n";
    echo "❌ Failed: $error_count\n";
    echo "\n";
    
    if ($error_count > 0) {
        echo "⚠️  ISSUES FOUND - See details above\n";
        return false;
    } else {
        echo "🎉 ALL TESTS PASSED!\n";
        return true;
    }
}

// Run all tests
echo "Starting comprehensive tests...\n\n";

$db_ok = testDatabaseConnection($con);

if ($db_ok) {
    testTablesExist($con);
    testTableData($con);
}

testPHPFiles();
testAuthenticationFiles();
testFunctionsExist();
testSecurityIssues();

$all_passed = generateSummary();

// Save report to file
$report = "GoFreight Test Report - " . date('Y-m-d H:i:s') . "\n";
$report .= "====================================\n\n";
$report .= json_encode($test_results, JSON_PRETTY_PRINT);
file_put_contents('test_report.txt', $report);

echo "\n📄 Detailed report saved to: test_report.txt\n";

exit($all_passed ? 0 : 1);
