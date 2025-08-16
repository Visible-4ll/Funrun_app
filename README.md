FunRun Registration System
Developed by Installers PH

Overview
A web-based application for managing participant registrations for running events. Features include multi-step registration, payment tracking, and QR code generation.

Features
✅ Multi-step registration form

✅ Distance selection (3Km, 6Km, 12Km) with pricing

✅ Participant information collection (name, contact, etc.)

✅ Payment method selection

✅ QR code generation for event access

✅ Countdown timer to event date

✅ Responsive design (works on mobile & desktop)

✅ Admin dashboard for managing registrations

System Requirements
PHP 7.4 or higher

MySQL 5.7 or higher

Apache Web Server

Composer (for dependency management)

Installation Guide
1. Clone the Repository
bash
git clone https://github.com/Visible-4II/Funrun_app.git
cd funrun_app
2. Install Dependencies
bash
composer install
3. Database Setup
Option A: Import Schema
bash
mysql -u username -p funrun_db < database/schema.sql
Option B: Manual Setup
sql
CREATE DATABASE funrun_db;
USE funrun_db;
-- (Run the full schema from database/schema.sql)
4. Configuration
Copy the sample config:

bash
cp config/config.php.sample config/config.php
Edit config/config.php with your database details:

php
define('DB_HOST', 'localhost');
define('DB_NAME', 'funrun_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
5. Set Directory Permissions
bash
chmod -R 755 assets/qrcodes uploads
6. Web Server Setup (Apache)
Point the virtual host to the public directory.

Ensure mod_rewrite is enabled.

7. Install QR Code Library
bash
mkdir -p phpqrcode
wget https://raw.githubusercontent.com/t0k4rt/phpqrcode/master/qrlib.php -O phpqrcode/qrlib.php
Usage
Registration Process
Select distance (3Km, 6Km, 12Km)

Enter personal details

Choose payment method

Review & confirm

Get QR code (for event access)

Admin Panel
Access: /admin (Default: admin/admin)

Features:

View & filter registrations

Export participant data

Manage payments

Troubleshooting
Common Issues
🔹 QR codes not generating?

Ensure assets/qrcodes is writable.

Check if PHP GD Library is installed.

🔹 Database connection errors?

Verify credentials in config/config.php.

Ensure MySQL is running.

🔹 Session errors?

Confirm session_start() is called before output.

Check PHP session directory permissions.

Security Recommendations
<<<<<<< HEAD

🔒 Change default admin credentials

=======
🔒 Change default admin credentials

>>>>>>> 69636ec01af932a64b79809a5e041b754c29d22a
🔒 Set proper file permissions:

bash
chmod 644 config/config.php

🔒 Use HTTPS in production

🔒 Regularly backup the database

License

⚠️ Proprietary Software © JohnDev404. All rights reserved.

Support

📧 Email: installersph@gmail.com

📞 Phone/Viber: +639618856615
<<<<<<< HEAD
=======

🌐 Facebook: InstallersPH

🔗 Website: https://installersph.com

Notes
For large events, optimize MySQL settings.

Test payment gateways thoroughly before production.
>>>>>>> 69636ec01af932a64b79809a5e041b754c29d22a

🌐 Facebook: InstallersPH

🔗 Website: https://installersph.com

Notes
For large events, optimize MySQL settings.

Test payment gateways thoroughly before production.