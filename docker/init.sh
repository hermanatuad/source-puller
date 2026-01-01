#!/bin/bash

# Script to initialize the Yii2 application in Docker

echo "Initializing Yii2 application..."

# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
while ! mysqladmin ping -h"mysql" --silent; do
    sleep 1
done

echo "MySQL is ready!"

# Run Yii2 migrations
echo "Running database migrations..."
php yii migrate --interactive=0

# Initialize RBAC
echo "Initializing RBAC..."
php yii migrate --migrationPath=@yii/rbac/migrations --interactive=0

echo "Application initialized successfully!"
