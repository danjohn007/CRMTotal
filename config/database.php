<?php
/**
 * CRM Total - Database Configuration
 * Cámara de Comercio de Querétaro
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'crm_ccq');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// PDO Options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
]);
