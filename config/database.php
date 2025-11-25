<?php
/**
 * CRM Total - Database Configuration
 * Cámara de Comercio de Querétaro
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'enlaceca_total');
define('DB_USER', 'enlaceca_total');
define('DB_PASS', '~0=2,&gdgS%s');
define('DB_CHARSET', 'utf8mb4');

// PDO Options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 30,  // Connection timeout in seconds
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="TRADITIONAL"',
]);
