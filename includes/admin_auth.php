<?php
/**
 * Admin Authentication Helper
 * Include this at the top of all admin pages
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auth.php';

// Require admin access
requireAdmin('../login.php');
?>
