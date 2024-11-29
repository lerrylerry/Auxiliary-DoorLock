<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pythonScriptPath = 'Auxiliary/motion-detection.py';
$output = shell_exec("python " . escapeshellarg($pythonScriptPath));
echo "<pre>$output</pre>";
?>
