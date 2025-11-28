<?php
// Temporary: Check if mysqli or pdo_mysql extensions are loaded
// Remove after debugging
header('Content-Type: text/plain');
$extensions = get_loaded_extensions();
$required = ['mysqli', 'pdo_mysql'];
$missing = [];
foreach ($required as $ext) {
    if (!extension_loaded($ext)) {
        $missing[] = $ext;
    }
}

echo "PHP Version: " . phpversion() . PHP_EOL;
echo "Loaded extensions: \n" . implode(', ', $extensions) . PHP_EOL . PHP_EOL;
if (empty($missing)) {
    echo "All required extensions are installed.\n";
} else {
    echo "Missing extensions: \n" . implode(', ', $missing) . PHP_EOL;
}

?>
