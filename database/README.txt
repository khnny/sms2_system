SMS 2 – Database & Security Adoption
====================================

1. Start Apache + MySQL (XAMPP)
2. Copy the project folder into htdocs. Any folder name is OK.
3. Install schema (once), from the project folder:
     C:\xampp\php\php.exe database\install.php
4. Open setup and create YOUR Super Admin (no demo accounts):
     http://localhost/<your-folder-name>/setup/
5. After setup, add staff/student users in User Management.

If the other computer has a MySQL password or different database names, copy:
     config\local.example.php
to:
     config\local.php
then edit the values there.

What install creates:
  - roles, role_permissions, system_settings, empty users table

What install does NOT create:
  - demo logins / sample users

Deployment migration
--------------------
For web/database deployment, run both SQL dumps with:

     php database/migrate.php

This runner calls:
  - database/sms2_db.sql
  - modules/crad/database/crad_db.sql

HostForge (hostforgeplatform.cloud)
----------------------------------
Two separate MariaDB databases are supported (sms2_db + crad_db).

1. Provision two MySQL/MariaDB databases in the workspace (not PostgreSQL).
2. Import database/sms2_db.sql into sms2_db and modules/crad/database/crad_db.sql
   into crad_db (HostForge Import button or php database/migrate.php in app terminal).
3. Do NOT upload config/local.php with XAMPP localhost settings. When DB_* env
   vars are set, the app skips local.php automatically.
4. Workspace → Environment Variables — set ALL of the following from each
   database's connection details panel:

   SMS2 main (login/users):
     DB_HOST=mariadb-xxxx.internal
     DB_PORT=31467
     DB_DATABASE=sms2_db
     DB_USERNAME=<sms2_db user>
     DB_PASSWORD=<sms2_db password>
     DB_CONNECTION=mysql
     DB_CHARSET=utf8mb4

   CRAD module (separate instance):
     CRAD_DB_HOST=mariadb-yyyy.internal
     CRAD_DB_PORT=33632
     CRAD_DB_NAME=crad_db
     CRAD_DB_USER=<crad_db user>
     CRAD_DB_PASS=<crad_db password>
     CRAD_DB_CHARSET=utf8mb4

   Optional:
     SMS2_DEPLOY_TOKEN=<random secret for setup/health.php and deploy-db.php>
     SMS2_BASE_URL=<public path if auto-detect is wrong>

   See config/local.hostforge.example.php for a full reference.

5. Redeploy the application after saving env vars.
6. Open: https://YOUR-SITE/setup/health.php?token=YOUR_TOKEN
   Confirm users count > 0 and both DB connections are OK.
7. Log in with your imported account email (e.g. superadmin@bestlink.edu.ph)
   using the SAME password as on local XAMPP (import does not reset passwords).

If login says "invalid credentials" but health check shows users > 0:
  - Wrong password — reset in sms2_db console or app terminal (see below).
  - Clear lockouts in sms2_db console:
      DELETE FROM login_throttles;
      UPDATE users SET failed_login_attempts = 0, locked_until = NULL;
  - Complete the "Verify you are human" CAPTCHA before Sign In.

Password reset (app terminal on HostForge):
     php -r "echo password_hash('YourNewPassword123!', PASSWORD_DEFAULT);"
  Copy the hash, then in sms2_db SQL console:
     UPDATE users SET password_hash = 'PASTE_HASH_HERE',
       failed_login_attempts = 0, locked_until = NULL, must_change_password = 0
     WHERE email = 'superadmin@bestlink.edu.ph';

CLI migrate (alternative to web import):
     php database/migrate.php

Useful options:
  - --fresh  Drop and rebuild both databases. DESTROYS DATA.
  - --force  Apply SQL even when tables already exist.

Docker startup option:
  Set SMS2_RUN_MIGRATIONS=1 to run database/migrate.php before Apache starts.

InfinityFree (free hosting)
---------------------------
InfinityFree has no SSH, so use the web deploy helper instead of CLI migrate.

1. Sign up at https://infinityfree.net and create a hosting account.
2. vPanel → MySQL Databases: create one database. Copy hostname, db name, user, password.
3. Copy config/local.infinityfree.example.php to config/local.php on the server.
   Fill in MySQL values. Use the SAME db name for all module defines (free plan = 1 database).
4. Upload the project to htdocs via FTP (FileZilla). Put files in htdocs root if possible.
5. Replace .htaccess with .htaccess.infinityfree if the site shows HTTP 500
   (InfinityFree often blocks php_value in .htaccess).
6. Open: https://YOUR-SITE.infinityfreeapp.com/setup/deploy-db.php?token=YOUR_TOKEN
7. Open: https://YOUR-SITE.infinityfreeapp.com/setup/ and create the Super Admin.
8. Remove SMS2_DEPLOY_TOKEN from config/local.php after migration succeeds.

Alternative: import database/sms2_db.sql and modules/crad/database/crad_db.sql
via phpMyAdmin instead of step 6.
