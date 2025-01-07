<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password Hash for 'admin123': " . $hash;
?> 