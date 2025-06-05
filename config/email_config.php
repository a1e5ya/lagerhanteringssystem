<?php
/**
 * Gmail SMTP Configuration - SSL Method (Port 465)
 * This works around the STARTTLS issue
 */

return [
    'use_smtp' => true,
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 465,                    // Changed to SSL port
    'smtp_secure' => 'ssl',                // Changed to SSL
    'smtp_auth' => true,
    'smtp_username' => '...',        // Replace with your Gmail
    'smtp_password' => '...',   // Replace with App Password
    'from_email' => 'noreply@karisantikvariat.fi',
    'from_name' => 'Karis Antikvariat',
    'charset' => 'UTF-8',
    'debug_level' => 0,  // Keep debug on for testing
];
?>