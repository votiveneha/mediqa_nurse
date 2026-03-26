# Queue Worker Setup Guide for Production Server
## MediQa Real-Time Chat - Production Deployment

---

## ⚠️ Important: Why Queue Worker is Needed

Your chat system uses **Laravel Queues** to broadcast real-time messages via Pusher. Without a queue worker running, messages won't be sent in real-time.

**Current Configuration:**
```env
QUEUE_CONNECTION=database
BROADCAST_DRIVER=pusher
```

---

## 📋 Table of Contents

1. [Option 1: Supervisor (Recommended for Production)](#option-1-supervisor-recommended)
2. [Option 2: Systemd Service](#option-2-systemd-service-linux)
3. [Option 3: Cron Job (Alternative)](#option-3-cron-job-alternative)
4. [Monitoring Queue Workers](#monitoring-queue-workers)
5. [Troubleshooting](#troubleshooting)

---

## Option 1: Supervisor (Recommended) ⭐

**Supervisor** is a process control system that keeps your queue workers running automatically, even after server reboots.

### Step 1: Install Supervisor

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install supervisor
```

**CentOS/RHEL:**
```bash
sudo yum install supervisor
# OR with dnf
sudo dnf install supervisor
```

### Step 2: Create Supervisor Configuration

Create a new configuration file:

```bash
sudo nano /etc/supervisor/conf.d/mediqa-worker.conf
```

Add the following configuration:

```ini
[program:mediqa-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mediqa_nurse/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasuser=false
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/mediqa_nurse/storage/logs/worker.log
stopwaitsecs=3600
```

**Important:** Replace these paths:
- `/path/to/mediqa_nurse` → Your actual project path (e.g., `/var/www/mediqa_nurse` or `/home/mediqa/public_html`)
- `user=www-data` → Your web server user (could be `apache`, `nginx`, or your username)

### Step 3: Update the Configuration

If you have multiple queue connections (jobs, emails, broadcasts), you can create separate workers:

```ini
# For broadcast events (real-time chat)
[program:mediqa-broadcast]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mediqa_nurse/artisan queue:work --queue=broadcast --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/mediqa_nurse/storage/logs/broadcast-worker.log

# For other jobs (emails, etc.)
[program:mediqa-default]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/mediqa_nurse/artisan queue:work --queue=default --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/mediqa_nurse/storage/logs/default-worker.log
```

### Step 4: Start Supervisor

```bash
# Reload supervisor configuration
sudo supervisorctl reread

# Update supervisor with new configuration
sudo supervisorctl update

# Start the workers
sudo supervisorctl start mediqa-worker:*

# Check status
sudo supervisorctl status
```

### Step 5: Enable Supervisor on Boot

```bash
# Enable supervisor service
sudo systemctl enable supervisor
sudo systemctl start supervisor
```

---

## Option 2: Systemd Service (Linux)

Create a systemd service file:

```bash
sudo nano /etc/systemd/system/mediqa-queue.service
```

Add the following:

```ini
[Unit]
Description=MediQa Laravel Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
Group=www-data
WorkingDirectory=/path/to/mediqa_nurse
ExecStart=/usr/bin/php /path/to/mediqa_nurse/artisan queue:work database --sleep=3 --tries=3
Restart=always
RestartSec=5s

# Log output
StandardOutput=append:/path/to/mediqa_nurse/storage/logs/queue-worker.log
StandardError=append:/path/to/mediqa_nurse/storage/logs/queue-worker-error.log

[Install]
WantedBy=multi-user.target
```

Enable and start the service:

```bash
# Reload systemd
sudo systemctl daemon-reload

# Enable service
sudo systemctl enable mediqa-queue

# Start service
sudo systemctl start mediqa-queue

# Check status
sudo systemctl status mediqa-queue
```

---

## Option 3: Cron Job (Alternative)

If you can't use Supervisor or Systemd, use cron with `queue:work` timeout:

### Step 1: Create Cron Job

```bash
crontab -e
```

Add this line:

```bash
* * * * * cd /path/to/mediqa_nurse && php artisan queue:work --sleep=3 --tries=3 --max-time=3600 >> /path/to/mediqa_nurse/storage/logs/cron-queue.log 2>&1
```

**⚠️ Warning:** This approach is less reliable than Supervisor because:
- Multiple cron jobs might run simultaneously
- There's a small delay between job executions
- Not recommended for high-traffic applications

---

## Monitoring Queue Workers

### Check Worker Status

**Supervisor:**
```bash
sudo supervisorctl status
```

**Systemd:**
```bash
sudo systemctl status mediqa-queue
```

### View Logs

```bash
# Worker logs
tail -f /path/to/mediqa_nurse/storage/logs/worker.log

# Laravel logs
tail -f /path/to/mediqa_nurse/storage/logs/laravel.log
```

### Check Queue Status

```bash
# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush

# Monitor queue size
php artisan queue:monitor database
```

---

## Deployment Best Practices

### 1. Deploy Script

Create a deployment script that restarts workers:

```bash
#!/bin/bash

# Go to project directory
cd /path/to/mediqa_nurse

# Pull latest changes
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Run migrations
php artisan migrate --force

# Restart queue workers
sudo supervisorctl restart mediqa-worker:*

echo "Deployment complete!"
```

Make it executable:
```bash
chmod +x deploy.sh
```

### 2. Environment-Specific Settings

**Production (.env):**
```env
QUEUE_CONNECTION=database
BROADCAST_DRIVER=pusher
CACHE_DRIVER=redis
SESSION_DRIVER=redis
```

**Development (.env.local):**
```env
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=pusher
```

### 3. Database Queue Table

Make sure you have the queue table:

```bash
# Create queue table (if not exists)
php artisan queue:table
php artisan migrate

# Create failed jobs table
php artisan queue:failed-table
php artisan migrate
```

---

## Troubleshooting

### Issue: Workers Not Starting

**Check permissions:**
```bash
sudo chown -R www-data:www-data /path/to/mediqa_nurse/storage
sudo chmod -R 775 /path/to/mediqa_nurse/storage
```

**Check PHP path:**
```bash
which php
# Update supervisor config with correct path if needed
```

### Issue: Workers Stop After Some Time

**Increase max-time in supervisor config:**
```ini
command=php artisan queue:work --max-time=86400
```

**Add memory limit:**
```ini
command=php artisan queue:work --memory=512
```

### Issue: High Memory Usage

**Restart workers periodically:**
```ini
command=php artisan queue:work --max-jobs=1000 --max-time=3600
```

### Issue: Messages Not Broadcasting

1. **Check if worker is running:**
   ```bash
   sudo supervisorctl status
   ```

2. **Check queue for pending jobs:**
   ```bash
   php artisan queue:monitor database
   ```

3. **Check failed jobs:**
   ```bash
   php artisan queue:failed
   ```

4. **Check logs:**
   ```bash
   tail -f storage/logs/laravel.log
   tail -f storage/logs/worker.log
   ```

### Issue: Multiple Workers Processing Same Job

This is normal and expected. Laravel ensures each job is processed only once.

---

## Quick Reference Commands

```bash
# Start all workers
sudo supervisorctl start mediqa-worker:*

# Stop all workers
sudo supervisorctl stop mediqa-worker:*

# Restart all workers
sudo supervisorctl restart mediqa-worker:*

# Check status
sudo supervisorctl status

# View logs
tail -f storage/logs/worker.log

# Check queue
php artisan queue:monitor database

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Flush failed jobs
php artisan queue:flush
```

---

## Server Requirements

- **PHP:** 8.2+
- **Database:** MySQL/MariaDB (for queue table)
- **Supervisor:** Latest version
- **Memory:** At least 512MB RAM for workers
- **Extensions:** php-mbstring, php-xml, php-curl, php-pdo

---

## Support

If you encounter issues:
1. Check worker logs: `storage/logs/worker.log`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Verify queue table exists in database
4. Ensure Supervisor is running: `sudo systemctl status supervisor`

---

**Last Updated:** March 26, 2026
**Version:** 1.0
