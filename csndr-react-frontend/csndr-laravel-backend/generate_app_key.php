<?php
/**
 * Script to generate a proper Laravel APP_KEY
 * This fixes the "Unsupported cipher" error
 */

// Generate a random 32-byte key for AES-256-CBC
$key = random_bytes(32);
$base64Key = 'base64:' . base64_encode($key);

echo "Generated APP_KEY: " . $base64Key . "\n";
echo "\nPlease update your .env file with this APP_KEY\n";
echo "Replace the current APP_KEY line with:\n";
echo "APP_KEY=" . $base64Key . "\n";

// Also generate JWT secret if needed
$jwtSecret = bin2hex(random_bytes(32));
echo "\nGenerated JWT_SECRET: " . $jwtSecret . "\n";
echo "Replace the current JWT_SECRET line with:\n";
echo "JWT_SECRET=" . $jwtSecret . "\n";
?>
