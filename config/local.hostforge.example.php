<?php
/**
 * HostForge Platform — environment variable reference
 *
 * Do NOT copy this file to config/local.php on HostForge.
 * Set these values in your workspace → Environment Variables instead.
 *
 * When DB_* env vars are present, config/local.php is skipped automatically
 * so XAMPP localhost settings cannot override the cloud database.
 *
 * See also: database/README.txt (HostForge section)
 *           setup/health.php?token=YOUR_SMS2_DEPLOY_TOKEN
 */

// Optional: protect setup/health.php and setup/deploy-db.php
// define('SMS2_DEPLOY_TOKEN', 'choose-a-long-random-secret');

// Optional: override auto-detected public URL path (usually leave unset)
// define('SMS2_BASE_URL', '');

/*
 * SMS2 main database (sms2_db) — login, users, roles, settings
 *
 * DB_HOST=mariadb-vnbeokwa.internal
 * DB_PORT=31467
 * DB_DATABASE=sms2_db
 * DB_USERNAME=<from HostForge sms2_db connection details>
 * DB_PASSWORD=<from HostForge sms2_db connection details>
 * DB_CONNECTION=mysql
 * DB_CHARSET=utf8mb4
 */

/*
 * CRAD module database (crad_db) — separate MariaDB instance
 *
 * CRAD_DB_HOST=mariadb-cdec97zl.internal
 * CRAD_DB_PORT=33632
 * CRAD_DB_NAME=crad_db
 * CRAD_DB_USER=<from HostForge crad_db connection details>
 * CRAD_DB_PASS=<from HostForge crad_db connection details>
 * CRAD_DB_CHARSET=utf8mb4
 */
