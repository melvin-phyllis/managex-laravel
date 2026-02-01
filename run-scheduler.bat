@echo off
REM ManageX Laravel Scheduler
REM This script runs the Laravel scheduler every minute
REM Set up Windows Task Scheduler to run this script

cd /d D:\ManageX
php artisan schedule:run >> storage\logs\scheduler.log 2>&1
