#!/bin/bash

# Cargo Tracking System - Startup Script

# Colors for output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================${NC}"
echo -e "${BLUE}Cargo Tracking System${NC}"
echo -e "${BLUE}================================${NC}"
echo ""

# Load environment variables
if [ -f .env ]; then
    export $(cat .env | grep -v '^#' | xargs)
    echo -e "${GREEN}✓${NC} Environment variables loaded"
else
    echo -e "${RED}✗${NC} .env file not found"
    SERVER_PORT=8000
    SERVER_HOST=localhost
fi

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo -e "${RED}✗${NC} PHP is not installed"
    echo "Installing PHP..."
    brew install php
fi

PHP_VERSION=$(php --version | head -n 1)
echo -e "${GREEN}✓${NC} PHP: $PHP_VERSION"

# Check if MySQL is installed
if ! command -v mysql &> /dev/null; then
    echo -e "${RED}✗${NC} MySQL is not installed"
    echo "Please install MySQL: brew install mysql"
    exit 1
fi

MYSQL_VERSION=$(mysql --version)
echo -e "${GREEN}✓${NC} MySQL: $MYSQL_VERSION"

# Check if MySQL is running
if pgrep -x "mysqld" > /dev/null; then
    echo -e "${GREEN}✓${NC} MySQL service is running"
else
    echo -e "${RED}✗${NC} MySQL service is not running"
    echo "Starting MySQL..."
    brew services start mysql
    sleep 3
fi

# Check database connection
echo ""
echo "Checking database connection..."
if mysql -u${DB_USER:-root} ${DB_PASS:+-p$DB_PASS} -e "USE ${DB_NAME:-cargo};" 2>/dev/null; then
    echo -e "${GREEN}✓${NC} Database '${DB_NAME:-cargo}' exists and is accessible"
else
    echo -e "${RED}✗${NC} Database '${DB_NAME:-cargo}' does not exist"
    echo "Creating database and importing schema..."
    mysql -u${DB_USER:-root} ${DB_PASS:+-p$DB_PASS} < database.sql
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} Database created successfully"
    else
        echo -e "${RED}✗${NC} Failed to create database"
        exit 1
    fi
fi

echo ""
echo -e "${BLUE}================================${NC}"
echo -e "${GREEN}Starting Development Server${NC}"
echo -e "${BLUE}================================${NC}"
echo ""
echo -e "Server running at: ${GREEN}http://${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}${NC}"
echo ""
echo "Available URLs:"
echo -e "  • Home Page:      ${BLUE}http://${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}/home/home.php${NC}"
echo -e "  • Admin Login:    ${BLUE}http://${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}/admin-login.php${NC}"
echo -e "  • Customer Login: ${BLUE}http://${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}/signin.php${NC}"
echo -e "  • Driver Login:   ${BLUE}http://${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}/driver-login.php${NC}"
echo -e "  • Track Cargo:    ${BLUE}http://${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}/track.php${NC}"
echo ""
echo "Press Ctrl+C to stop the server"
echo ""

# Change to public directory and start server
cd public
php -S ${SERVER_HOST:-localhost}:${SERVER_PORT:-8000}
