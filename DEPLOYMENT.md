# คู่มือการติดตั้งระบบบน Production Server

คู่มือฉบับนี้จะแนะนำการติดตั้ง NBU Knowledge Base System บนเซิร์ฟเวอร์จริง (Production) ด้วย Ubuntu Server, Nginx, PostgreSQL และ SSL Certificate

---

## 📋 สารบัญ

- [ความต้องการของเซิร์ฟเวอร์](#ความต้องการของเซิร์ฟเวอร์)
- [การเตรียม Server](#การเตรียม-server)
- [การติดตั้ง Dependencies](#การติดตั้ง-dependencies)
- [การตั้งค่า PostgreSQL](#การตั้งค่า-postgresql)
- [การติดตั้งแอปพลิเคชัน](#การติดตั้งแอปพลิเคชัน)
- [การตั้งค่า Nginx](#การตั้งค่า-nginx)
- [การติดตั้ง SSL Certificate](#การติดตั้ง-ssl-certificate)
- [การตั้งค่า Supervisor สำหรับ Queue](#การตั้งค่า-supervisor-สำหรับ-queue)
- [การตั้งค่า Cron Jobs](#การตั้งค่า-cron-jobs)
- [Optimization และ Performance](#optimization-และ-performance)
- [การ Backup](#การ-backup)
- [Monitoring และ Maintenance](#monitoring-และ-maintenance)

---

## ความต้องการของเซิร์ฟเวอร์

### ขั้นต่ำ (รองรับผู้ใช้ ~100 คน)

- **CPU**: 2 cores
- **RAM**: 2 GB
- **Storage**: 20 GB SSD
- **Bandwidth**: 100 Mbps

### แนะนำ (รองรับผู้ใช้ ~500 คน)

- **CPU**: 4 cores
- **RAM**: 4 GB
- **Storage**: 50 GB SSD
- **Bandwidth**: 500 Mbps

### Operating System

- Ubuntu Server 22.04 LTS (แนะนำ)
- Ubuntu Server 20.04 LTS
- Debian 11+

---

## การเตรียม Server

### 1. Update System

```bash
sudo apt update
sudo apt upgrade -y
```

### 2. สร้าง User สำหรับ Deploy (Optional แต่แนะนำ)

```bash
# สร้าง user ใหม่
sudo adduser deploy

# เพิ่มสิทธิ์ sudo
sudo usermod -aG sudo deploy

# สลับไปใช้ user deploy
su - deploy
```

### 3. ติดตั้ง Essential Packages

```bash
sudo apt install -y curl git unzip software-properties-common
```

---

## การติดตั้ง Dependencies

### 1. ติดตั้ง PHP 8.2

```bash
# เพิ่ม PPA repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# ติดตั้ง PHP และ extensions
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-pgsql php8.2-mbstring php8.2-xml php8.2-bcmath \
    php8.2-curl php8.2-zip php8.2-gd php8.2-intl php8.2-redis

# ตรวจสอบเวอร์ชัน
php -v
```

### 2. ติดตั้ง Composer

```bash
# ดาวน์โหลด Composer
curl -sS https://getcomposer.org/installer -o composer-setup.php

# ติดตั้ง
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# ตรวจสอบเวอร์ชัน
composer --version

# ลบไฟล์ setup
rm composer-setup.php
```

### 3. ติดตั้ง Node.js และ NPM

```bash
# ติดตั้ง Node.js 20.x LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# ตรวจสอบเวอร์ชัน
node -v
npm -v
```

### 4. ติดตั้ง Nginx

```bash
sudo apt install -y nginx

# เริ่มและเปิดใช้งาน Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# ตรวจสอบสถานะ
sudo systemctl status nginx
```

---

## การตั้งค่า PostgreSQL

### 1. ติดตั้ง PostgreSQL

```bash
# ติดตั้ง PostgreSQL 15
sudo apt install -y postgresql postgresql-contrib

# เริ่มและเปิดใช้งาน PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql
```

### 2. สร้าง Database และ User

```bash
# สลับไป postgres user
sudo -u postgres psql

# ในโหมด PostgreSQL, รันคำสั่งเหล่านี้:
```

```sql
-- สร้าง database
CREATE DATABASE kmsystem;

-- สร้าง user
CREATE USER kmsuser WITH PASSWORD 'your-secure-password-here';

-- ให้สิทธิ์
GRANT ALL PRIVILEGES ON DATABASE kmsystem TO kmsuser;

-- สำหรับ PostgreSQL 15+ ต้องให้สิทธิ์เพิ่มเติม
\c kmsystem
GRANT ALL ON SCHEMA public TO kmsuser;
GRANT CREATE ON SCHEMA public TO kmsuser;

-- ออกจาก PostgreSQL
\q
```

### 3. ตั้งค่า PostgreSQL สำหรับ Remote Access (ถ้าจำเป็น)

```bash
# แก้ไข postgresql.conf
sudo nano /etc/postgresql/15/main/postgresql.conf

# เปิด listen_addresses (ลบ # ออก)
# listen_addresses = 'localhost'  # เปลี่ยนเป็น '*' ถ้าต้องการเปิดให้ทุก IP

# แก้ไข pg_hba.conf
sudo nano /etc/postgresql/15/main/pg_hba.conf

# เพิ่มบรรทัดนี้ (แก้ไข IP range ตามต้องการ)
# host    all             all             0.0.0.0/0               md5

# Restart PostgreSQL
sudo systemctl restart postgresql
```

### 4. ทดสอบการเชื่อมต่อ

```bash
psql -h localhost -U kmsuser -d kmsystem -W
```

---

## การติดตั้งแอปพลิเคชัน

### 1. Clone Repository

```bash
# สร้างโฟลเดอร์สำหรับเว็บไซต์
sudo mkdir -p /var/www
cd /var/www

# Clone repository
sudo git clone https://github.com/your-username/kmsystem.git kmsystem

# เปลี่ยน ownership
sudo chown -R deploy:www-data /var/www/kmsystem
```

### 2. ติดตั้ง Dependencies

```bash
cd /var/www/kmsystem

# ติดตั้ง PHP dependencies
composer install --optimize-autoloader --no-dev

# ติดตั้ง Node dependencies
npm ci

# Build assets
npm run build
```

### 3. ตั้งค่า Environment

```bash
# คัดลอกไฟล์ .env
cp .env.example .env

# แก้ไขไฟล์ .env
nano .env
```

แก้ไขค่าต่อไปนี้:

```env
APP_NAME="NBU Knowledge Base"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kmsystem
DB_USERNAME=kmsuser
DB_PASSWORD=your-secure-password-here

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. สร้าง Application Key

```bash
php artisan key:generate
```

### 5. ตั้งค่า Storage และ Permissions

```bash
# สร้างโฟลเดอร์ที่จำเป็น
mkdir -p public/uploads/attachments
mkdir -p public/uploads/settings

# สร้าง symbolic link
php artisan storage:link

# ตั้งค่า permissions
sudo chown -R deploy:www-data /var/www/kmsystem
sudo chmod -R 755 /var/www/kmsystem
sudo chmod -R 775 /var/www/kmsystem/storage
sudo chmod -R 775 /var/www/kmsystem/bootstrap/cache
sudo chmod -R 775 /var/www/kmsystem/public/uploads
```

### 6. รัน Migrations และ Seeders

```bash
# รัน migrations
php artisan migrate --force

# รัน seeders
php artisan db:seed --force
```

### 7. Optimize Application

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

---

## การตั้งค่า Nginx

### 1. สร้าง Server Block Configuration

```bash
sudo nano /etc/nginx/sites-available/kmsystem
```

เพิ่มเนื้อหานี้:

```nginx
server {
    listen 80;
    listen [::]:80;

    server_name yourdomain.com www.yourdomain.com;
    root /var/www/kmsystem/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    # Increase upload size limit
    client_max_body_size 20M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css text/xml text/javascript application/json application/javascript application/xml+rss application/rss+xml font/truetype font/opentype application/vnd.ms-fontobject image/svg+xml;

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

### 2. เปิดใช้งาน Site

```bash
# สร้าง symbolic link
sudo ln -s /etc/nginx/sites-available/kmsystem /etc/nginx/sites-enabled/

# ลบ default site (ถ้ามี)
sudo rm /etc/nginx/sites-enabled/default

# ทดสอบ configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### 3. ตั้งค่า PHP-FPM

```bash
# แก้ไขไฟล์ PHP-FPM pool configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

ค้นหาและแก้ไขค่าเหล่านี้:

```ini
user = www-data
group = www-data

pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 500
```

แก้ไข PHP configuration:

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

แก้ไขค่าเหล่านี้:

```ini
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

Restart PHP-FPM:

```bash
sudo systemctl restart php8.2-fpm
```

---

## การติดตั้ง SSL Certificate

### วิธีที่ 1: ใช้ Let's Encrypt (ฟรี - แนะนำ)

```bash
# ติดตั้ง Certbot
sudo apt install -y certbot python3-certbot-nginx

# ขอ SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# ทดสอบ auto-renewal
sudo certbot renew --dry-run
```

Certbot จะแก้ไข Nginx configuration อัตโนมัติและเพิ่ม HTTPS redirect

### วิธีที่ 2: ใช้ SSL Certificate ที่ซื้อมา

```bash
# สร้างโฟลเดอร์สำหรับ SSL certificates
sudo mkdir -p /etc/nginx/ssl

# คัดลอกไฟล์ certificate และ private key
sudo cp your-certificate.crt /etc/nginx/ssl/
sudo cp your-private-key.key /etc/nginx/ssl/

# ตั้งค่า permissions
sudo chmod 600 /etc/nginx/ssl/your-private-key.key
```

แก้ไข Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/kmsystem
```

เพิ่มเนื้อหานี้:

```nginx
# Redirect HTTP to HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS server block
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;

    server_name yourdomain.com www.yourdomain.com;
    root /var/www/kmsystem/public;

    # SSL Configuration
    ssl_certificate /etc/nginx/ssl/your-certificate.crt;
    ssl_certificate_key /etc/nginx/ssl/your-private-key.key;

    # SSL Security Settings
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # ... (ส่วนอื่นๆ เหมือนกับ server block ปกติ)
}
```

Restart Nginx:

```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## การตั้งค่า Supervisor สำหรับ Queue

ถ้าคุณใช้งาน Laravel Queue (แนะนำสำหรับการส่งอีเมลและงานที่ใช้เวลานาน)

### 1. ติดตั้ง Supervisor

```bash
sudo apt install -y supervisor
```

### 2. สร้าง Configuration

```bash
sudo nano /etc/supervisor/conf.d/kmsystem-worker.conf
```

เพิ่มเนื้อหานี้:

```ini
[program:kmsystem-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/kmsystem/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/kmsystem/storage/logs/worker.log
stopwaitsecs=3600
```

### 3. เริ่มใช้งาน Supervisor

```bash
# อ่าน configuration ใหม่
sudo supervisorctl reread

# อัปเดต configuration
sudo supervisorctl update

# เริ่ม queue workers
sudo supervisorctl start kmsystem-worker:*

# ตรวจสอบสถานะ
sudo supervisorctl status
```

### คำสั่งที่ใช้บ่อยสำหรับ Supervisor

```bash
# Restart workers
sudo supervisorctl restart kmsystem-worker:*

# Stop workers
sudo supervisorctl stop kmsystem-worker:*

# ดู logs
sudo tail -f /var/www/kmsystem/storage/logs/worker.log
```

---

## การตั้งค่า Cron Jobs

### 1. แก้ไข Crontab

```bash
# แก้ไข crontab สำหรับ deploy user
crontab -e
```

### 2. เพิ่ม Laravel Scheduler

เพิ่มบรรทัดนี้:

```cron
* * * * * cd /var/www/kmsystem && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Cron Jobs เพิ่มเติม (Optional)

```cron
# Backup database ทุกวันเวลา 02:00
0 2 * * * cd /var/www/kmsystem && php artisan backup:run >> /dev/null 2>&1

# Clear expired cache ทุก 6 ชั่วโมง
0 */6 * * * cd /var/www/kmsystem && php artisan cache:prune-stale-tags >> /dev/null 2>&1

# Optimize images ทุกวันเวลา 03:00
0 3 * * * cd /var/www/kmsystem && php artisan optimize:images >> /dev/null 2>&1
```

---

## Optimization และ Performance

### 1. OPcache Configuration

```bash
sudo nano /etc/php/8.2/fpm/conf.d/10-opcache.ini
```

เพิ่มหรือแก้ไข:

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=10000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

### 2. ติดตั้ง Redis (สำหรับ Cache และ Session)

```bash
# ติดตั้ง Redis
sudo apt install -y redis-server

# เริ่มและเปิดใช้งาน Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# ทดสอบ Redis
redis-cli ping
# ควรได้รับ: PONG
```

แก้ไขไฟล์ `.env`:

```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Clear และ rebuild cache:

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
```

### 3. Database Optimization

```bash
# เข้าสู่ PostgreSQL
sudo -u postgres psql -d kmsystem
```

```sql
-- Analyze database
ANALYZE;

-- Vacuum database
VACUUM ANALYZE;

-- สร้าง indexes สำหรับ performance (ถ้ายังไม่มี)
CREATE INDEX IF NOT EXISTS idx_articles_published ON articles(published_at) WHERE status = 'published';
CREATE INDEX IF NOT EXISTS idx_articles_category ON articles(category_id);
CREATE INDEX IF NOT EXISTS idx_articles_user ON articles(user_id);
CREATE INDEX IF NOT EXISTS idx_articles_visibility ON articles(visibility);

\q
```

### 4. Nginx Performance Tuning

```bash
sudo nano /etc/nginx/nginx.conf
```

แก้ไขค่าเหล่านี้:

```nginx
user www-data;
worker_processes auto;
worker_rlimit_nofile 65535;

events {
    worker_connections 2048;
    multi_accept on;
    use epoll;
}

http {
    # Basic Settings
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    server_tokens off;

    # Buffer Settings
    client_body_buffer_size 128k;
    client_max_body_size 20M;
    client_header_buffer_size 1k;
    large_client_header_buffers 4 16k;

    # ... (ส่วนอื่นๆ)
}
```

Restart Nginx:

```bash
sudo systemctl restart nginx
```

---

## การ Backup

### 1. สร้าง Script สำหรับ Backup Database

```bash
sudo mkdir -p /var/backups/kmsystem
sudo nano /usr/local/bin/backup-kmsystem-db.sh
```

เพิ่มเนื้อหานี้:

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/kmsystem"
DB_NAME="kmsystem"
DB_USER="kmsuser"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Backup database
PGPASSWORD="your-db-password" pg_dump -U $DB_USER -h localhost $DB_NAME | gzip > $BACKUP_DIR/db_backup_$DATE.sql.gz

# Remove backups older than retention period
find $BACKUP_DIR -name "db_backup_*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "Database backup completed: db_backup_$DATE.sql.gz"
```

ตั้งค่า permissions:

```bash
sudo chmod +x /usr/local/bin/backup-kmsystem-db.sh
```

### 2. สร้าง Script สำหรับ Backup Files

```bash
sudo nano /usr/local/bin/backup-kmsystem-files.sh
```

เพิ่มเนื้อหานี้:

```bash
#!/bin/bash

# Configuration
BACKUP_DIR="/var/backups/kmsystem"
APP_DIR="/var/www/kmsystem"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

# Backup uploads folder
tar -czf $BACKUP_DIR/uploads_backup_$DATE.tar.gz -C $APP_DIR/public uploads

# Backup .env file
cp $APP_DIR/.env $BACKUP_DIR/env_backup_$DATE

# Remove backups older than retention period
find $BACKUP_DIR -name "uploads_backup_*.tar.gz" -mtime +$RETENTION_DAYS -delete
find $BACKUP_DIR -name "env_backup_*" -mtime +$RETENTION_DAYS -delete

echo "Files backup completed: uploads_backup_$DATE.tar.gz"
```

ตั้งค่า permissions:

```bash
sudo chmod +x /usr/local/bin/backup-kmsystem-files.sh
```

### 3. ตั้งค่า Automated Backup ด้วย Cron

```bash
sudo crontab -e
```

เพิ่มบรรทัดเหล่านี้:

```cron
# Backup database ทุกวันเวลา 02:00
0 2 * * * /usr/local/bin/backup-kmsystem-db.sh >> /var/log/kmsystem-backup.log 2>&1

# Backup files ทุกวันเวลา 03:00
0 3 * * * /usr/local/bin/backup-kmsystem-files.sh >> /var/log/kmsystem-backup.log 2>&1
```

### 4. ทดสอบ Backup Scripts

```bash
# ทดสอบ backup database
sudo /usr/local/bin/backup-kmsystem-db.sh

# ทดสอบ backup files
sudo /usr/local/bin/backup-kmsystem-files.sh

# ตรวจสอบไฟล์ backup
ls -lh /var/backups/kmsystem/
```

### 5. Restore จาก Backup

**Restore Database:**

```bash
# Decompress backup
gunzip /var/backups/kmsystem/db_backup_YYYYMMDD_HHMMSS.sql.gz

# Restore
PGPASSWORD="your-db-password" psql -U kmsuser -h localhost kmsystem < /var/backups/kmsystem/db_backup_YYYYMMDD_HHMMSS.sql
```

**Restore Files:**

```bash
# Restore uploads
cd /var/www/kmsystem/public
sudo rm -rf uploads
sudo tar -xzf /var/backups/kmsystem/uploads_backup_YYYYMMDD_HHMMSS.tar.gz

# ตั้งค่า permissions
sudo chown -R deploy:www-data uploads
sudo chmod -R 775 uploads
```

---

## Monitoring และ Maintenance

### 1. ติดตั้ง Log Rotation

```bash
sudo nano /etc/logrotate.d/kmsystem
```

เพิ่มเนื้อหานี้:

```
/var/www/kmsystem/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0644 deploy www-data
    sharedscripts
    postrotate
        php /var/www/kmsystem/artisan cache:clear > /dev/null 2>&1
    endscript
}
```

### 2. Basic Monitoring Commands

```bash
# ตรวจสอบ disk space
df -h

# ตรวจสอบ memory usage
free -h

# ตรวจสอบ CPU usage
top

# ตรวจสอบ Nginx status
sudo systemctl status nginx

# ตรวจสอบ PHP-FPM status
sudo systemctl status php8.2-fpm

# ตรวจสอบ PostgreSQL status
sudo systemctl status postgresql

# ดู error logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/www/kmsystem/storage/logs/laravel.log
```

### 3. Application Health Check

สร้างไฟล์สำหรับ health check:

```bash
cd /var/www/kmsystem
php artisan make:controller HealthController
```

แก้ไขไฟล์ `app/Http/Controllers/HealthController.php`:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    public function check()
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $allHealthy = !in_array(false, $checks, true);

        return response()->json([
            'status' => $allHealthy ? 'healthy' : 'unhealthy',
            'checks' => $checks,
            'timestamp' => now(),
        ], $allHealthy ? 200 : 503);
    }

    private function checkDatabase()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkCache()
    {
        try {
            Cache::put('health_check', true, 10);
            return Cache::get('health_check') === true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function checkStorage()
    {
        return is_writable(storage_path());
    }
}
```

เพิ่ม route ใน `routes/web.php`:

```php
Route::get('/health', [App\Http\Controllers\HealthController::class, 'check']);
```

ทดสอบ:

```bash
curl https://yourdomain.com/health
```

### 4. การอัปเดตแอปพลิเคชัน

```bash
cd /var/www/kmsystem

# เปิด maintenance mode
php artisan down

# Pull code ใหม่
git pull origin main

# ติดตั้ง dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# รัน migrations
php artisan migrate --force

# Clear และ rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers (ถ้าใช้)
sudo supervisorctl restart kmsystem-worker:*

# ปิด maintenance mode
php artisan up
```

---

## Security Checklist

- [ ] เปลี่ยนรหัสผ่าน default admin
- [ ] ตั้งค่า strong passwords สำหรับ database user
- [ ] เปิดใช้ SSL/TLS Certificate
- [ ] ตั้งค่า firewall (UFW)
- [ ] ปิด debug mode (`APP_DEBUG=false`)
- [ ] ซ่อน server tokens (`server_tokens off` ใน Nginx)
- [ ] ตั้งค่า rate limiting
- [ ] Update system และ packages เป็นประจำ
- [ ] ตั้งค่า backup อัตโนมัติ
- [ ] Monitor logs เป็นประจำ

### ตั้งค่า UFW Firewall

```bash
# ติดตั้ง UFW
sudo apt install -y ufw

# อนุญาต SSH
sudo ufw allow 22/tcp

# อนุญาต HTTP และ HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# เปิดใช้งาน UFW
sudo ufw enable

# ตรวจสอบสถานะ
sudo ufw status
```

---

## Performance Benchmarking

### ทดสอบด้วย Apache Bench

```bash
# ติดตั้ง Apache Bench
sudo apt install -y apache2-utils

# ทดสอบ homepage (1000 requests, 10 concurrent)
ab -n 1000 -c 10 https://yourdomain.com/

# ทดสอบด้วย keep-alive
ab -n 1000 -c 10 -k https://yourdomain.com/
```

### เป้าหมาย Performance

- **Response Time**: < 200ms สำหรับหน้าหลัก
- **Time to First Byte (TTFB)**: < 100ms
- **Page Load Time**: < 2 วินาที
- **Server Response Time**: < 500ms

---

## Troubleshooting

### 502 Bad Gateway

```bash
# ตรวจสอบ PHP-FPM
sudo systemctl status php8.2-fpm

# ตรวจสอบ logs
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/php8.2-fpm.log

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

### 500 Internal Server Error

```bash
# ตรวจสอบ Laravel logs
tail -f /var/www/kmsystem/storage/logs/laravel.log

# ตรวจสอบ permissions
sudo chown -R deploy:www-data /var/www/kmsystem
sudo chmod -R 755 /var/www/kmsystem
sudo chmod -R 775 /var/www/kmsystem/storage
sudo chmod -R 775 /var/www/kmsystem/bootstrap/cache
```

### Database Connection Error

```bash
# ตรวจสอบ PostgreSQL
sudo systemctl status postgresql

# ทดสอบการเชื่อมต่อ
psql -h localhost -U kmsuser -d kmsystem -W

# ตรวจสอบ .env
cat /var/www/kmsystem/.env | grep DB_
```

---

## Support และ Resources

- **Laravel Documentation**: https://laravel.com/docs
- **Nginx Documentation**: https://nginx.org/en/docs/
- **PostgreSQL Documentation**: https://www.postgresql.org/docs/

---

**© 2025 North Bangkok University. All rights reserved.**
