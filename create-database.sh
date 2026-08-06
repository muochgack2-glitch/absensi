#!/bin/bash

# Script untuk membuat database MySQL untuk Absensi

echo "=== Create MySQL Database untuk Absensi ==="
echo ""

# Database credentials
DB_NAME="absensi_db"
DB_USER="absensi_db"
DB_PASS="password"

# Root MySQL password (akan diminta input)
echo "Masukkan password MySQL root:"
read -s MYSQL_ROOT_PASS

echo ""
echo "Creating database and user..."

# Create database dan user
mysql -u root -p"$MYSQL_ROOT_PASS" <<EOF
-- Create database
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';

-- Grant privileges
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Show databases
SHOW DATABASES;
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✓ Database created successfully!"
    echo ""
    echo "Database Details:"
    echo "  Name: ${DB_NAME}"
    echo "  User: ${DB_USER}"
    echo "  Pass: ${DB_PASS}"
    echo "  Host: localhost"
    echo ""
else
    echo ""
    echo "✗ Error creating database!"
    echo "Please create manually via aaPanel or phpMyAdmin"
    echo ""
fi
