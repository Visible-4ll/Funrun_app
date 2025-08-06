FunRun Registration System - Installers PH
Overview
The FunRun Registration System is a web-based application designed to manage participant registrations for running events. This system provides a multi-step registration process, payment tracking, and QR code generation for event participants.

Features
Multi-step registration form

Distance selection (3Km, 6Km, 12Km) with Price

Participant information collection

Payment method selection

QR code generation for participants

Countdown timer to event date

Responsive design for mobile and desktop

System Requirements
PHP 7.4 or higher

MySQL 5.7 or higher

Web server (Apache)

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
Create a new MySQL database and import the SQL schema:

bash
mysql -u username -p funrun_db < database/schema.sql
Or run these commands in your MySQL client:

sql
CREATE DATABASE funrun_db;
USE funrun_db;

-- Create tables (see complete schema in database/schema.sql)
4. Configuration
Copy the sample configuration file and update with your details:

bash
cp config/config.php config/config.php
Edit config/config.php with your database credentials:

php
define('DB_HOST', 'localhost');
define('DB_NAME', 'funrun_db');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
5. Directory Permissions
Ensure these directories are writable by the web server:

bash
chmod -R 755 assets/qrcodes
chmod -R 755 uploads
6. Web Server Configuration
Apache
Create a virtual host pointing to the public directory.


7. QR Code Library
Download the PHP QR Code library:

bash
mkdir -p phpqrcode
wget https://raw.githubusercontent.com/t0k4rt/phpqrcode/master/qrlib.php -O phpqrcode/qrlib.php
Usage
Access the system through your web browser

The registration process consists of 5 steps:

Step 1: Select distance

Step 2: Enter personal information

Step 3: Choose payment method

Step 4: Review and confirm

Step 5: Registration complete with QR code

Admin Features
Access the admin panel at /admin (default credentials: admin/admin)

View all registrations

Filter by payment status

Export participant data

Manage payment methods

Troubleshooting
Common Issues
QR codes not generating:

Ensure assets/qrcodes directory exists and is writable

Verify PHP GD library is installed

Database connection errors:

Double-check credentials in config/config.php

Verify MySQL server is running

Session errors:

Ensure session_start() is called before any output

Check PHP session save path is writable

Security Recommendations
Change default admin credentials

Set proper file permissions:

bash
chmod 644 config/config.php
Implement HTTPS for production

Regularly backup the database

License
This project is proprietary software developed by JohnDev404. All rights reserved.

Support
For technical support, please contact:

Email: installersph@gmail.com

Phone/Viber: +639618856615

Facebook page: https://www.facebook.com/InstallersPH

URL: https://installersph.com




