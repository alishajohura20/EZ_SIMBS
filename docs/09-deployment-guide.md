# EZ_SIMBS - Deployment Guide

## Requirements

| Requirement | Version |
|-------------|---------|
| PHP | 8.0+ |
| MySQL | 8.0+ |
| Web Server | Apache 2.4+ or Nginx (or PHP built-in dev server) |
| Node.js | 16+ (for running Jest tests only) |

### Required PHP Extensions

- pdo_mysql
- mbstring
- json
- gd (for image uploads)
- openssl

## Installation

### 1. Clone or Download

```bash
git clone https://github.com/yourusername/ez_simbs.git
cd ez_simbs
```

### 2. Create Database

```bash
mysql -u root -p -e "CREATE DATABASE ez_simbs"
```

### 3. Import Schema

```bash
mysql -u root -p ez_simbs < ez_simbs.sql
```

### 4. Configure Database Connection

Edit `includes/config.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'ez_simbs');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### 5. Set Directory Permissions

```bash
chmod -R 777 uploads/
chmod -R 777 exports/
chmod -R 777 backup/
```

### 6. Start the Application

**Development (PHP built-in server):**

```bash
php -S localhost:8000
```

Open `http://localhost:8000` in your browser.

**Production (Apache):**

See Apache Virtual Host configuration below.

### 7. Default Login

| Field | Value |
|-------|-------|
| Email | admin@ezsimbs.local |
| Password | admin123 |

**Change this password immediately in production!**

---

## Apache Virtual Host

```apache
<VirtualHost *:80>
    ServerName ezsimbs.local
    DocumentRoot /var/www/html/ez_simbs
    
    <Directory /var/www/html/ez_simbs>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/ez_simbs_error.log
    CustomLog ${APACHE_LOG_DIR}/ez_simbs_access.log combined
</VirtualHost>
```

Add to `/etc/hosts`:
```
127.0.0.1 ezsimbs.local
```

## Nginx Configuration

```nginx
server {
    listen 80;
    server_name ezsimbs.local;
    root /var/www/html/ez_simbs;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Production Checklist

- [ ] Set `display_errors` to `0` in `config.php`
- [ ] Set `log_errors` to `1` in `config.php`
- [ ] Set proper file permissions (755 for dirs, 644 for files)
- [ ] Remove `ez_simbs.sql` from web root
- [ ] Enable HTTPS with SSL certificate
- [ ] Configure session cookie settings (secure, httponly, samesite)
- [ ] Set up automated database backups (cron job)
- [ ] **Change default admin password** after first login
- [ ] Configure `php.ini` for production:
  - `upload_max_filesize = 10M`
  - `post_max_size = 12M`
  - `memory_limit = 256M`
  - `max_execution_time = 30`
- [ ] Remove error_reporting from config.php
- [ ] Set proper MySQL user permissions (don't use root)
- [ ] Enable gzip compression on web server
- [ ] Set browser caching headers for static assets

---

## Cron Jobs (Production)

```bash
# Auto-reorder check (every hour)
0 * * * * curl -s http://localhost:8000/api/notifications/check.php

# Daily backup at 2 AM
0 2 * * * cd /var/www/html/ez_simbs && php -r "
require_once 'includes/config.php';
require_once 'includes/functions.php';
\$s = new Settings(); \$s->backup();
"

# Weekly activity log cleanup (keep 90 days)
0 3 * * 0 mysql -u root -p ez_simbs -e "DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)"
```

---

## Running Tests

```bash
# Install dependencies
npm install

# Run all tests
npm test

# Run specific test suite
npm run test:auth
npm run test:products
npm run test:sales
npm run test:dashboard

# Run with custom API URL
API_URL=http://localhost:8000/api npm test
```

### Importing Postman Collection

1. Open Postman
2. Click **Import**
3. Select `tests/postman/ez_simbs.postman_collection.json`
4. Set the `base_url` variable to your server URL
5. Run the Login request first to establish a session

---

## Backup & Restore

### Manual Backup

```bash
mysqldump -u root -p ez_simbs > backup/ez_simbs_$(date +%Y%m%d).sql
```

### Manual Restore

```bash
mysql -u root -p ez_simbs < backup/ez_simbs_20260907.sql
```

### Via Application

1. Go to **Settings → Backup** tab
2. Click **Create Backup**
3. To restore, click **Restore** next to a backup file

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Blank page | Check `display_errors` in config.php, check PHP error log |
| Database connection failed | Verify DB credentials in config.php, ensure MySQL is running |
| 403 Forbidden | Check file permissions and Apache directory config |
| Images not uploading | Check `uploads/` permissions, verify `upload_max_filesize` in php.ini |
| Session not persisting | Check session.save_path, ensure `session_start()` is called |
