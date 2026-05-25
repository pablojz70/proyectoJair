<?php
header('Content-Type: text/plain');
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "\n";
echo "ORIG_SCRIPT_NAME: " . ($_SERVER['ORIG_SCRIPT_NAME'] ?? 'NOT SET') . "\n";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "\n";
echo "REDIRECT_URL: " . ($_SERVER['REDIRECT_URL'] ?? 'NOT SET') . "\n";
echo "REDIRECT_SCRIPT_NAME: " . ($_SERVER['REDIRECT_SCRIPT_NAME'] ?? 'NOT SET') . "\n";
?>
