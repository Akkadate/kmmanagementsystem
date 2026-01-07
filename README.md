# NBU Knowledge Base System

ระบบจัดการความรู้สำหรับมหาวิทยาลัยนอร์ทกรุงเทพ (North Bangkok University Knowledge Management System)

## 📋 สารบัญ

- [ภาพรวมระบบ](#ภาพรวมระบบ)
- [คุณสมบัติหลัก](#คุณสมบัติหลัก)
- [ความต้องการของระบบ](#ความต้องการของระบบ)
- [การติดตั้ง](#การติดตั้ง)
- [การตั้งค่าระบบ](#การตั้งค่าระบบ)
- [สถาปัตยกรรมระบบ](#สถาปัตยกรรมระบบ)
- [คู่มือการพัฒนา](#คู่มือการพัฒนา)
- [API และ Helper Functions](#api-และ-helper-functions)

---

## ภาพรวมระบบ

ระบบจัดการความรู้ NBU เป็นแพลตฟอร์มสำหรับจัดเก็บ แบ่งปัน และจัดการความรู้ภายในองค์กร ออกแบบมาสำหรับมหาวิทยาลัยนอร์ทกรุงเทพ เพื่อให้บุคลากรและนักศึกษาสามารถเข้าถึงข้อมูลและทรัพยากรที่สำคัญได้อย่างมีประสิทธิภาพ

### เทคโนโลยีที่ใช้

- **Framework**: Laravel 11
- **Database**: PostgreSQL
- **Frontend**: Blade Templates, TailwindCSS, Alpine.js
- **Email**: Gmail SMTP
- **Authentication**: Laravel Breeze

---

## คุณสมบัติหลัก

### 1. ระบบจัดการบทความ (Article Management)

- สร้าง แก้ไข และลบบทความ
- รองรับไฟล์แนบหลายไฟล์
- ระบบแท็ก (Tags) สำหรับจัดหมวดหมู่
- ระบบการค้นหาขั้นสูง
- การดูจำนวนครั้ง (View Count)

### 2. ระดับการมองเห็นขั้นสูง (Advanced Visibility Levels)

ระบบรองรับ 5 ระดับการมองเห็น:

| Visibility Level | คำอธิบาย | ผู้ที่สามารถเข้าถึง |
|-----------------|---------|-------------------|
| **Public** | เปิดเผยต่อสาธารณะ | ทุกคน (ไม่ต้อง login) |
| **Members Only** | สำหรับสมาชิกที่ล็อกอิน | ผู้ใช้ที่ล็อกอินทุกคน (รวมบุคคลภายนอก) |
| **Staff Only** | สำหรับบุคลากรภายใน | ผู้ใช้ที่มี Department เท่านั้น |
| **Internal** | สำหรับแผนกเฉพาะ | ผู้ใช้ในแผนกเดียวกันกับผู้เขียน |
| **Private** | ส่วนตัว | เจ้าของบทความเท่านั้น |

#### การทำงานของ Staff Only

```php
// ArticlePolicy.php - view method
case 'staff_only':
    // ต้อง login และมี department_id
    if (!$user) {
        return false;
    }
    return $user->department_id !== null;
```

### 3. ระบบยืนยันอีเมล (Email Verification)

- ส่งอีเมลยืนยันอัตโนมัติเมื่อลงทะเบียน
- ใช้ Gmail SMTP สำหรับส่งอีเมล (รองรับ < 100 อีเมล/วัน)
- UI สำหรับยืนยันอีเมลเป็นภาษาไทย
- Admin สามารถยืนยันอีเมลด้วยตนเองได้
- ป้องกันการเข้าถึงฟีเจอร์สำคัญด้วย `verified` middleware

#### การใช้งาน Middleware

```php
// routes/web.php
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/articles', [ArticleController::class, 'store']);
    Route::post('/bookmarks', [BookmarkController::class, 'store']);
});
```

### 4. ระบบจัดการการตั้งค่า (Settings Management)

ระบบ CMS-like ที่ให้ Admin แก้ไขค่าต่างๆ ผ่านหน้าเว็บ โดยไม่ต้องแก้ไขโค้ด

#### กลุ่มการตั้งค่า

- **General**: ชื่อเว็บไซต์, คำอธิบาย, โลโก้, ข้อความหน้าแรก
- **Contact**: อีเมล, เบอร์โทร, ที่อยู่
- **Footer**: ข้อความลิขสิทธิ์, ข้อมูลเกี่ยวกับ
- **Social**: ลิงก์โซเชียลมีเดีย (Facebook, Twitter, YouTube, LINE)

#### รองรับ Input Types

- `text` - ข้อความธรรมดา
- `textarea` - ข้อความหลายบรรทัด
- `number` - ตัวเลข
- `email` - อีเมล
- `url` - URL
- `image` - รูปภาพ
- `boolean` - Checkbox

#### การใช้งาน Helper Function

```blade
{{-- ในไฟล์ Blade --}}
{{ setting('site_name', 'ค่าเริ่มต้น') }}
{{ setting('contact_email') }}

@if(setting('site_logo'))
    <img src="{{ asset(setting('site_logo')) }}" alt="Logo">
@endif
```

### 5. ระบบจัดการผู้ใช้และบทบาท

#### บทบาท (Roles)

- **Admin**: จัดการทุกอย่างในระบบ
- **Editor**: สร้างและแก้ไขบทความทั้งหมด
- **Contributor**: สร้างและแก้ไขบทความของตนเอง
- **Viewer**: อ่านบทความเท่านั้น

#### ระบบแผนก (Department)

- ผู้ใช้สามารถมี Department ได้ 1 แผนก
- ใช้ในการควบคุมการเข้าถึงแบบ `staff_only` และ `internal`
- Admin จัดการแผนกได้ผ่าน Admin Dashboard

### 6. คุณสมบัติเพิ่มเติม

- **ระบบหมวดหมู่**: จัดกลุ่มบทความตามหมวดหมู่
- **ระบบบุ๊กมาร์ก**: บันทึกบทความที่สนใจ
- **ระบบการค้นหา**: ค้นหาบทความด้วยชื่อ, เนื้อหา, แท็ก
- **ไฟล์แนบ**: แนบไฟล์กับบทความได้หลายไฟล์
- **Responsive Design**: รองรับทุกขนาดหน้าจอ

---

## ความต้องการของระบบ

### Server Requirements

- PHP >= 8.2
- PostgreSQL >= 13
- Composer
- Node.js และ NPM
- Web Server (Apache/Nginx)

### PHP Extensions

```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PCRE
- PDO
- PostgreSQL PDO Driver (pdo_pgsql)
- Tokenizer
- XML
```

---

## การติดตั้ง

### ตัวเลือก A: การติดตั้งแบบครบวงจรบน Ubuntu Server + Nginx

คู่มือนี้สำหรับการติดตั้งบนเซิร์ฟเวอร์ Ubuntu ใหม่ตั้งแต่ต้น เหมาะสำหรับ Production Server

#### ข้อกำหนดเบื้องต้น
- Ubuntu 22.04 LTS หรือสูงกว่า (แนะนำ)
- สิทธิ์ root หรือ sudo access
- อย่างน้อย 2GB RAM และ 20GB ดิสก์

---

#### 1. อัปเดตระบบและติดตั้ง Dependencies พื้นฐาน

```bash
# อัปเดต package list
sudo apt update && sudo apt upgrade -y

# ติดตั้ง dependencies พื้นฐาน
sudo apt install -y software-properties-common curl wget git unzip
```

---

#### 2. ติดตั้ง PHP 8.2 และ Extensions

```bash
# เพิ่ม PPA repository สำหรับ PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# ติดตั้ง PHP 8.2 และ extensions ที่จำเป็น
sudo apt install -y php8.2 php8.2-cli php8.2-fpm php8.2-common \
    php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl \
    php8.2-gd php8.2-zip php8.2-pgsql php8.2-intl \
    php8.2-dom php8.2-fileinfo php8.2-tokenizer

# ตรวจสอบเวอร์ชัน PHP
php --version
```

**คาดหวังผลลัพธ์**: PHP 8.2.x

---

#### 3. ติดตั้ง Composer

```bash
# ดาวน์โหลด Composer installer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php

# ติดตั้ง Composer
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# ลบไฟล์ installer
rm composer-setup.php

# ตรวจสอบการติดตั้ง
composer --version
```

**คาดหวังผลลัพธ์**: Composer version 2.x

---

#### 4. ติดตั้ง PostgreSQL

```bash
# ติดตั้ง PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# เริ่มต้นและเปิดใช้งาน PostgreSQL
sudo systemctl start postgresql
sudo systemctl enable postgresql

# ตรวจสอบสถานะ
sudo systemctl status postgresql
```

**ตั้งค่า Database และ User**:

```bash
# เข้าสู่ PostgreSQL shell
sudo -u postgres psql

# สร้าง database และ user (รันคำสั่งด้านล่างใน PostgreSQL prompt)
```

```sql
-- สร้าง database
CREATE DATABASE kmsystem;

-- สร้าง user และกำหนดรหัสผ่าน (เปลี่ยน 'your_password' เป็นรหัสผ่านที่ต้องการ)
CREATE USER kmsystem_user WITH ENCRYPTED PASSWORD 'your_password';

-- ให้สิทธิ์ทั้งหมดแก่ user
GRANT ALL PRIVILEGES ON DATABASE kmsystem TO kmsystem_user;

-- (สำหรับ PostgreSQL 15+) ให้สิทธิ์ใน public schema
\c kmsystem
GRANT ALL ON SCHEMA public TO kmsystem_user;

-- ออกจาก PostgreSQL
\q
```

**ทดสอบการเชื่อมต่อ**:

```bash
psql -U kmsystem_user -d kmsystem -h localhost -W
# กรอกรหัสผ่านที่ตั้งไว้ ถ้าเชื่อมต่อได้แสดงว่าสำเร็จ
# พิมพ์ \q เพื่อออก
```

---

#### 5. ติดตั้ง Node.js และ npm

```bash
# ติดตั้ง Node.js 20.x LTS (แนะนำ)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# ตรวจสอบเวอร์ชัน
node --version
npm --version
```

**คาดหวังผลลัพธ์**:
- Node: v20.x
- npm: v10.x

---

#### 6. ติดตั้งและตั้งค่า Nginx

```bash
# ติดตั้ง Nginx
sudo apt install -y nginx

# เริ่มต้นและเปิดใช้งาน Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# ตรวจสอบสถานะ
sudo systemctl status nginx
```

**ตั้งค่า Firewall (ถ้าใช้ UFW)**:

```bash
# อนุญาต HTTP และ HTTPS
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable
```

---

#### 7. ติดตั้งโปรเจค Laravel

**สร้างโฟลเดอร์สำหรับโปรเจค**:

```bash
# สร้างโฟลเดอร์สำหรับ web application
sudo mkdir -p /var/www/kmsystem

# เปลี่ยนเจ้าของโฟลเดอร์เป็น user ปัจจุบัน
sudo chown -R $USER:$USER /var/www/kmsystem

# เข้าสู่โฟลเดอร์
cd /var/www/kmsystem
```

**Clone Repository** (หรือ upload โค้ด):

```bash
# Clone จาก Git (เปลี่ยน <repository-url> เป็น URL จริง)
git clone <repository-url> .

# หรือถ้า upload ไฟล์มา ให้แตกไฟล์ที่นี่
```

**ติดตั้ง Dependencies**:

```bash
# ติดตั้ง PHP dependencies
composer install --optimize-autoloader --no-dev

# ติดตั้ง Node dependencies
npm install
```

---

#### 8. ตั้งค่า Environment และ Application

**สร้างไฟล์ .env**:

```bash
# คัดลอกจาก .env.example
cp .env.example .env

# แก้ไขไฟล์ .env
nano .env
```

**แก้ไขค่าต่อไปนี้ใน .env**:

```env
APP_NAME="NBU KM System"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kmsystem
DB_USERNAME=kmsystem_user
DB_PASSWORD=your_password

# สำหรับการส่งอีเมล (ดูคู่มือการตั้งค่า Gmail SMTP ด้านล่าง)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**สร้าง Application Key**:

```bash
php artisan key:generate
```

---

#### 9. ตั้งค่า Storage และ Permissions

```bash
# สร้างโฟลเดอร์สำหรับอัปโหลดไฟล์
mkdir -p public/uploads/attachments
mkdir -p public/uploads/settings

# สร้าง Symbolic Link
php artisan storage:link

# ตั้งค่า permissions
sudo chown -R www-data:www-data /var/www/kmsystem
sudo chmod -R 755 /var/www/kmsystem
sudo chmod -R 775 /var/www/kmsystem/storage
sudo chmod -R 775 /var/www/kmsystem/bootstrap/cache
sudo chmod -R 775 /var/www/kmsystem/public/uploads
```

---

#### 10. รัน Migrations และ Seeders

```bash
# รัน migrations
php artisan migrate --force

# รัน seeders (สร้างข้อมูลเริ่มต้น)
php artisan db:seed --force
```

---

#### 11. Build Frontend Assets

```bash
# Build production assets
npm run build

# ลบ node_modules เพื่อประหยัดพื้นที่ (ถ้าต้องการ)
rm -rf node_modules
```

---

#### 12. ตั้งค่า Nginx Server Block

**สร้างไฟล์ configuration**:

```bash
sudo nano /etc/nginx/sites-available/kmsystem
```

**เพิ่มเนื้อหาต่อไปนี้** (เปลี่ยน `yourdomain.com` เป็น domain จริง):

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

    # กำหนดขนาดไฟล์อัปโหลดสูงสุด
    client_max_body_size 100M;

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
}
```

**เปิดใช้งาน site**:

```bash
# สร้าง symbolic link
sudo ln -s /etc/nginx/sites-available/kmsystem /etc/nginx/sites-enabled/

# ลบ default site (ถ้าไม่ต้องการ)
sudo rm /etc/nginx/sites-enabled/default

# ทดสอบ configuration
sudo nginx -t

# Reload Nginx
sudo systemctl reload nginx
```

---

#### 13. ตั้งค่า PHP-FPM

**แก้ไขไฟล์ php.ini**:

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

**แก้ไขค่าเหล่านี้**:

```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 256M
max_execution_time = 300
```

**Restart PHP-FPM**:

```bash
sudo systemctl restart php8.2-fpm
```

---

#### 14. ตั้งค่า Laravel Optimizations

```bash
cd /var/www/kmsystem

# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

---

#### 15. ตั้งค่า Cron Job (สำหรับ Laravel Scheduler)

```bash
# แก้ไข crontab
sudo crontab -e -u www-data
```

**เพิ่มบรรทัดนี้**:

```cron
* * * * * cd /var/www/kmsystem && php artisan schedule:run >> /dev/null 2>&1
```

---

#### 16. (ทางเลือก) ติดตั้ง SSL Certificate ด้วย Let's Encrypt

```bash
# ติดตั้ง Certbot
sudo apt install -y certbot python3-certbot-nginx

# รับ SSL certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# ทดสอบ auto-renewal
sudo certbot renew --dry-run
```

Certbot จะแก้ไข Nginx configuration อัตโนมัติเพื่อใช้ HTTPS

---

#### 17. ทดสอบการทำงาน

เปิดเบราว์เซอร์ไปที่:
- **HTTP**: http://yourdomain.com
- **HTTPS**: https://yourdomain.com (ถ้าติดตั้ง SSL แล้ว)

**ข้อมูล Login เริ่มต้น**:
```
Email: admin@northbkk.ac.th
Password: password
```

**⚠️ สำคัญ**: เปลี่ยนรหัสผ่าน Admin ทันทีหลังเข้าสู่ระบบครั้งแรก!

---

#### 18. การจัดการและ Monitoring

**ดู Logs**:

```bash
# Laravel logs
tail -f /var/www/kmsystem/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/access.log

# Nginx error logs
sudo tail -f /var/log/nginx/error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

**Restart Services**:

```bash
# Restart Nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Restart PostgreSQL
sudo systemctl restart postgresql
```

**อัปเดตโปรเจค**:

```bash
cd /var/www/kmsystem

# Pull latest code
git pull origin main

# ติดตั้ง dependencies ใหม่
composer install --optimize-autoloader --no-dev
npm install && npm run build

# รัน migrations ใหม่
php artisan migrate --force

# Clear และ cache ใหม่
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ตั้งค่า permissions
sudo chown -R www-data:www-data /var/www/kmsystem
```

---

#### Security Checklist สำหรับ Production

- [ ] ตั้งค่า `APP_DEBUG=false` ใน `.env`
- [ ] ตั้งค่า `APP_ENV=production` ใน `.env`
- [ ] ติดตั้ง SSL Certificate (Let's Encrypt)
- [ ] เปลี่ยนรหัสผ่าน PostgreSQL และ Admin user
- [ ] ตั้งค่า Firewall (UFW) อนุญาตเฉพาะ port ที่จำเป็น
- [ ] ตั้งค่า Fail2ban สำหรับป้องกัน brute force
- [ ] Backup ฐานข้อมูลสม่ำเสมอ
- [ ] ตรวจสอบ file permissions
- [ ] ปิด directory listing ใน Nginx
- [ ] อัปเดต system และ packages สม่ำเสมอ

---

### ตัวเลือก B: การติดตั้งแบบพื้นฐาน (Development)

สำหรับการพัฒนาบนเครื่อง local

#### 1. Clone Repository

```bash
git clone <repository-url>
cd kmsystem
```

#### 2. ติดตั้ง Dependencies

```bash
# ติดตั้ง PHP dependencies
composer install

# ติดตั้ง Node dependencies
npm install
```

### 3. สร้างไฟล์ Environment

```bash
cp .env.example .env
```

### 4. สร้าง Application Key

```bash
php artisan key:generate
```

### 5. ตั้งค่า Database

สร้าง PostgreSQL database:

```sql
CREATE DATABASE kmsystem;
```

แก้ไขไฟล์ `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kmsystem
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

### 6. รัน Migrations และ Seeders

```bash
# รัน migrations
php artisan migrate

# รัน seeders เพื่อสร้างข้อมูลเริ่มต้น
php artisan db:seed
```

Seeders ที่สำคัญ:
- `AdminUserSeeder` - สร้าง admin user เริ่มต้น
- `DepartmentSeeder` - สร้างแผนกตัวอย่าง
- `CategorySeeder` - สร้างหมวดหมู่ตัวอย่าง
- `SettingsSeeder` - สร้างการตั้งค่าเริ่มต้น

### 7. สร้างโฟลเดอร์สำหรับอัปโหลดไฟล์

```bash
mkdir -p public/uploads/attachments
mkdir -p public/uploads/settings
chmod -R 775 public/uploads
```

### 8. สร้าง Symbolic Link สำหรับ Storage

```bash
php artisan storage:link
```

### 9. Build Assets

```bash
npm run build

# หรือสำหรับ development
npm run dev
```

### 10. รันเซิร์ฟเวอร์

```bash

```

เข้าถึงระบบได้ที่: `http://localhost:8000`

---

## การตั้งค่าระบบ

### 1. การตั้งค่าอีเมล (Gmail SMTP)

#### ขั้นตอนการตั้งค่า Gmail SMTP

1. **สร้าง App Password ใน Gmail**:
   - ไปที่ Google Account Settings → Security
   - เปิด 2-Step Verification
   - ไปที่ "App passwords"
   - สร้าง App password ใหม่ (เลือก "Mail" และ "Other")
   - คัดลอก password 16 หลัก

2. **แก้ไขไฟล์ `.env`**:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

3. **Clear Config Cache**:

```bash
php artisan config:clear
php artisan cache:clear
```

#### ทดสอบการส่งอีเมล

```bash
php artisan tinker
```

```php
Mail::raw('Test email from KMS', function($message) {
    $message->to('test@example.com')->subject('Test Email');
});
```

#### ข้อจำกัดของ Gmail SMTP

- **ฟรี**: ส่งได้ไม่เกิน 100 อีเมล/วัน
- เหมาะสำหรับการใช้งานภายในองค์กรขนาดเล็ก-กลาง
- สำหรับการใช้งานจำนวนมาก แนะนำ: Amazon SES, SendGrid, Mailgun

### 2. การตั้งค่าผ่านหน้าเว็บ (Database Settings)

Admin สามารถเข้าไปแก้ไขการตั้งค่าได้ที่:

```
Admin Dashboard → System Settings
```

#### Best Practice: การแบ่งประเภทการตั้งค่า

| ประเภทข้อมูล | เก็บที่ | เหตุผล | ตัวอย่าง |
|-------------|---------|--------|---------|
| **Sensitive Data** | `.env` | ความปลอดภัย | Database password, API keys, SMTP password |
| **Non-Sensitive Data** | Database | ความยืดหยุ่น | Site name, Contact info, Footer text |

#### การเพิ่มการตั้งค่าใหม่

1. **เพิ่มใน SettingsSeeder**:

```php
// database/seeders/SettingsSeeder.php
[
    'key' => 'my_new_setting',
    'value' => 'ค่าเริ่มต้น',
    'type' => 'text',
    'group' => 'general',
    'label' => 'การตั้งค่าใหม่',
    'description' => 'คำอธิบายการตั้งค่า',
    'order' => 10,
]
```

2. **รัน Seeder**:

```bash
php artisan db:seed --class=SettingsSeeder
```

3. **ใช้งานในโค้ด**:

```blade
{{ setting('my_new_setting', 'ค่าเริ่มต้น') }}
```

### 3. การตั้งค่า Admin User เริ่มต้น

User เริ่มต้นจาก `AdminUserSeeder`:

```
Email: admin@northbkk.ac.th
Password: password
Role: admin
```

**⚠️ สำคัญ**: เปลี่ยนรหัสผ่านทันทีหลังติดตั้งเสร็จ!

---

## สถาปัตยกรรมระบบ

### Database Schema

#### ตารางหลัก

**users**
- `id`, `name`, `email`, `password`
- `role` (admin, editor, contributor, viewer)
- `department_id` (FK → departments)
- `email_verified_at`
- `is_active`

**articles**
- `id`, `title`, `slug`, `summary`, `content`
- `category_id` (FK → categories)
- `user_id` (FK → users)
- `visibility` (public, members_only, staff_only, internal, private)
- `status` (draft, published, archived)
- `views_count`
- `published_at`

**categories**
- `id`, `name`, `slug`, `description`, `icon`
- `parent_id` (self-referencing FK)
- `order`

**departments**
- `id`, `name`, `code`, `description`

**settings**
- `id`, `key`, `value`, `type`, `group`, `label`, `description`, `order`

**bookmarks**
- `id`, `user_id` (FK → users)
- `article_id` (FK → articles)

**attachments**
- `id`, `article_id` (FK → articles)
- `filename`, `original_name`, `mime_type`, `size`, `path`

**tags** และ **article_tag** (Many-to-Many)

### Models และ Relationships

#### User Model

```php
class User extends Authenticatable implements MustVerifyEmail
{
    // Relationships
    public function department(): BelongsTo
    public function articles(): HasMany
    public function bookmarks(): HasMany

    // Helper Methods
    public function isAdmin(): bool
    public function isEditor(): bool
    public function isContributor(): bool
    public function canManageArticles(): bool
    public function canEditArticle(Article $article): bool
}
```

#### Article Model

```php
class Article extends Model
{
    // Relationships
    public function user(): BelongsTo
    public function category(): BelongsTo
    public function tags(): BelongsToMany
    public function attachments(): HasMany
    public function bookmarkedBy(): BelongsToMany

    // Scopes
    public function scopePublished($query)
    public function scopeVisible($query, ?User $user)

    // Helper Methods
    public function isPublic(): bool
    public function isMembersOnly(): bool
    public function isStaffOnly(): bool
    public function isInternal(): bool
    public function isPrivate(): bool
}
```

#### Setting Model

```php
class Setting extends Model
{
    // Static Methods
    public static function get(string $key, $default = null)
    public static function set(string $key, $value): void
    public static function getByGroup(string $group): array
    public static function clearCache(): void
}
```

### Policies (Authorization)

#### ArticlePolicy

```php
class ArticlePolicy
{
    public function view(?User $user, Article $article): bool
    {
        // ตรวจสอบตาม visibility level
        switch ($article->visibility) {
            case 'public':
                return true; // ทุกคนดูได้

            case 'members_only':
                return $user !== null; // ต้อง login

            case 'staff_only':
                return $user && $user->department_id !== null; // ต้องมี department

            case 'internal':
                return $user && $user->department_id === $article->user->department_id;

            case 'private':
                return $user && $user->id === $article->user_id;
        }
    }

    public function create(User $user): bool
    public function update(User $user, Article $article): bool
    public function delete(User $user, Article $article): bool
}
```

### Helper Functions

**SettingsHelper.php**

```php
// รับค่าการตั้งค่าเดียว
setting(string $key, $default = null): mixed

// รับค่าการตั้งค่าทั้งกลุ่ม
settings(string $group): array
```

**ตัวอย่างการใช้งาน**:

```blade
{{-- Navbar --}}
<span>{{ setting('site_name', 'Knowledge Base') }}</span>

{{-- Footer --}}
<p>{{ setting('footer_text', '© 2025 All rights reserved') }}</p>

{{-- Contact --}}
<a href="mailto:{{ setting('contact_email') }}">
    {{ setting('contact_email') }}
</a>

{{-- Homepage Hero --}}
<h1>{{ setting('home_hero_title') }}</h1>
<p>{{ setting('home_hero_subtitle') }}</p>
```

### Middleware

**verified** - ตรวจสอบการยืนยันอีเมล

```php
Route::middleware(['auth', 'verified'])->group(function () {
    // Routes ที่ต้องยืนยันอีเมล
});
```

### Caching Strategy

#### Settings Cache

- Cache TTL: 1 ชั่วโมง (3600 วินาที)
- Cache Key Format: `setting_{key}`
- Auto-clear เมื่อมีการอัปเดต

```php
// การทำงานของ Setting::get()
Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
    $setting = static::where('key', $key)->first();
    return $setting ? $setting->value : $default;
});

// Clear cache เมื่ออัปเดต
Setting::set($key, $value); // จะ clear cache อัตโนมัติ
Setting::clearCache(); // clear ทั้งหมด
```

---

## คู่มือการพัฒนา

### 1. การเพิ่มระดับ Visibility ใหม่

#### ขั้นตอน:

**1. อัปเดต Database Enum**

สร้าง Migration:

```php
// database/migrations/xxxx_add_new_visibility_level.php
public function up(): void
{
    DB::statement("ALTER TABLE articles ALTER COLUMN visibility DROP DEFAULT;");
    DB::statement("CREATE TYPE visibility_new AS ENUM ('public', 'members_only', 'staff_only', 'internal', 'private', 'new_level');");
    DB::statement("ALTER TABLE articles ALTER COLUMN visibility TYPE visibility_new USING visibility::text::visibility_new;");
    DB::statement("DROP TYPE IF EXISTS visibility;");
    DB::statement("ALTER TYPE visibility_new RENAME TO visibility;");
    DB::statement("ALTER TABLE articles ALTER COLUMN visibility SET DEFAULT 'public'::visibility;");
}
```

**2. อัปเดต Check Constraint**

```php
DB::statement('ALTER TABLE articles DROP CONSTRAINT IF EXISTS articles_visibility_check;');
DB::statement("ALTER TABLE articles ADD CONSTRAINT articles_visibility_check CHECK (visibility IN ('public', 'members_only', 'staff_only', 'internal', 'private', 'new_level'));");
```

**3. อัปเดต Article Model**

```php
// app/Models/Article.php
public function isNewLevel(): bool
{
    return $this->visibility === 'new_level';
}
```

**4. อัปเดต ArticlePolicy**

```php
// app/Policies/ArticlePolicy.php
public function view(?User $user, Article $article): bool
{
    switch ($article->visibility) {
        // ... existing cases
        case 'new_level':
            // กำหนดเงื่อนไขการเข้าถึง
            return $user && $user->someCondition;
    }
}
```

**5. อัปเดต Controller Validation**

```php
// app/Http/Controllers/ArticleController.php
'visibility' => 'required|in:public,members_only,staff_only,internal,private,new_level',
```

**6. อัปเดต Form View**

```blade
{{-- resources/views/articles/form.blade.php --}}
<option value="new_level">New Level - Description</option>
```

### 2. การเพิ่ม Setting Group ใหม่

**1. อัปเดต SettingsSeeder**

```php
// database/seeders/SettingsSeeder.php
[
    'key' => 'new_group_setting',
    'value' => 'default value',
    'type' => 'text',
    'group' => 'new_group',
    'label' => 'Label',
    'description' => 'Description',
    'order' => 1,
]
```

**2. อัปเดต SettingsController**

```php
// app/Http/Controllers/Admin/SettingsController.php
public function index()
{
    $groups = ['general', 'contact', 'footer', 'social', 'new_group'];
    // ...
}
```

**3. รัน Seeder**

```bash
php artisan db:seed --class=SettingsSeeder
```

### 3. Best Practices

#### Security

- ✅ ใช้ Policy สำหรับ Authorization
- ✅ Validate input ทุกครั้ง
- ✅ ใช้ Mass Assignment Protection (`$fillable`)
- ✅ Hash รหัสผ่านด้วย `Hash::make()`
- ✅ ใช้ CSRF Protection
- ✅ Sanitize user input ก่อนแสดงผล

#### Performance

- ✅ ใช้ Eager Loading เพื่อป้องกัน N+1 Problem
- ✅ ใช้ Cache สำหรับข้อมูลที่ไม่เปลี่ยนบ่อย
- ✅ ใช้ Pagination สำหรับรายการยาว
- ✅ Optimize Database Queries

```php
// ❌ Bad - N+1 Problem
$articles = Article::all();
foreach ($articles as $article) {
    echo $article->user->name; // Query ใหม่ทุกรอบ
}

// ✅ Good - Eager Loading
$articles = Article::with('user')->get();
foreach ($articles as $article) {
    echo $article->user->name; // ใช้ข้อมูลที่โหลดไว้แล้ว
}
```

#### Code Organization

- ✅ ใช้ Form Request สำหรับ validation ที่ซับซ้อน
- ✅ สร้าง Service Class สำหรับ business logic ที่ซับซ้อน
- ✅ ใช้ Events และ Listeners สำหรับ side effects
- ✅ ใช้ Jobs สำหรับงานที่ใช้เวลานาน

### 4. Common Tasks

#### เพิ่ม Admin User ใหม่

```bash
php artisan tinker
```

```php
$user = new App\Models\User();
$user->name = 'Admin Name';
$user->email = 'admin@example.com';
$user->password = Hash::make('password');
$user->role = 'admin';
$user->email_verified_at = now();
$user->save();
```

#### Clear All Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

#### Reset Database

```bash
php artisan migrate:fresh --seed
```

**⚠️ คำเตือน**: คำสั่งนี้จะลบข้อมูลทั้งหมด!

---

## API และ Helper Functions

### Helper Functions

#### setting()

รับค่าการตั้งค่าจากฐานข้อมูล

```php
setting(string $key, mixed $default = null): mixed
```

**ตัวอย่าง**:

```blade
{{ setting('site_name') }}
{{ setting('site_name', 'Default Name') }}
{{ setting('contact_email', 'info@example.com') }}
```

#### settings()

รับค่าการตั้งค่าทั้งกลุ่ม

```php
settings(string $group): array
```

**ตัวอย่าง**:

```php
$generalSettings = settings('general');
// [
//     'site_name' => 'NBU Knowledge Base',
//     'site_description' => '...',
//     'items_per_page' => '12',
// ]

$socialSettings = settings('social');
// [
//     'social_facebook' => 'https://...',
//     'social_twitter' => 'https://...',
// ]
```

### Model Methods

#### User Model

```php
// Check roles
$user->isAdmin(): bool
$user->isEditor(): bool
$user->isContributor(): bool
$user->isViewer(): bool

// Permissions
$user->canManageArticles(): bool
$user->canEditArticle(Article $article): bool

// Relationships
$user->department(): BelongsTo
$user->articles(): HasMany
$user->bookmarks(): HasMany
```

#### Article Model

```php
// Visibility checks
$article->isPublic(): bool
$article->isMembersOnly(): bool
$article->isStaffOnly(): bool
$article->isInternal(): bool
$article->isPrivate(): bool

// Status checks
$article->isDraft(): bool
$article->isPublished(): bool
$article->isArchived(): bool

// Relationships
$article->user(): BelongsTo
$article->category(): BelongsTo
$article->tags(): BelongsToMany
$article->attachments(): HasMany
$article->bookmarkedBy(): BelongsToMany

// Scopes
Article::published()->get()
Article::visible($user)->get()
```

#### Setting Model

```php
// Get setting value
Setting::get(string $key, mixed $default = null): mixed

// Set setting value
Setting::set(string $key, mixed $value): void

// Get all settings in a group
Setting::getByGroup(string $group): array

// Clear all settings cache
Setting::clearCache(): void
```

---

## การ Deploy

### Production Checklist

- [ ] เปลี่ยน `APP_ENV` เป็น `production`
- [ ] ตั้งค่า `APP_DEBUG` เป็น `false`
- [ ] สร้าง `APP_KEY` ใหม่
- [ ] ตั้งค่า Database credentials ที่ปลอดภัย
- [ ] ตั้งค่า MAIL credentials ที่ปลอดภัย
- [ ] เปลี่ยนรหัสผ่าน Admin เริ่มต้น
- [ ] ตั้งค่า permissions สำหรับ `storage/` และ `bootstrap/cache/`
- [ ] รัน `php artisan config:cache`
- [ ] รัน `php artisan route:cache`
- [ ] รัน `php artisan view:cache`
- [ ] ตั้งค่า Queue Worker (ถ้าใช้)
- [ ] ตั้งค่า SSL/TLS Certificate
- [ ] ตั้งค่า Backup อัตโนมัติ

### Environment Variables สำหรับ Production

```env
APP_NAME="NBU Knowledge Base"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=pgsql
DB_HOST=your-db-host
DB_PORT=5432
DB_DATABASE=your-db-name
DB_USERNAME=your-db-user
DB_PASSWORD=your-secure-password

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your-email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## การแก้ปัญหา (Troubleshooting)

### อีเมลยืนยันไม่ส่ง

1. ตรวจสอบ `.env` มี `MAIL_ENCRYPTION=tls`
2. Clear config cache: `php artisan config:clear`
3. ทดสอบส่งอีเมลด้วย tinker:

```php
Mail::raw('Test', function($msg) {
    $msg->to('test@example.com')->subject('Test');
});
```

### การตั้งค่าไม่บันทึก

1. ตรวจสอบ `$fillable` ใน Model
2. Clear cache: `Setting::clearCache()`
3. ตรวจสอบ permissions โฟลเดอร์ `storage/`

### Visibility Level ใหม่ไม่ทำงาน

1. ตรวจสอบ Database Enum ถูกอัปเดตแล้ว
2. ตรวจสอบ Check Constraint
3. ตรวจสอบ Policy logic
4. ตรวจสอบ Controller validation

---

## License

This project is open-sourced software licensed under the MIT license.

---

## Support

สำหรับการสนับสนุนหรือคำถาม กรุณาติดต่อ:
- Email: kms@northbkk.ac.th
- มหาวิทยาลัยนอร์ทกรุงเทพ

---

**Developed for North Bangkok University**

**© 2025 North Bangkok University. All rights reserved.**
