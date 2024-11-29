<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$pythonScriptPath = '/Auxiliary/motion-detection.py';
$command = "/usr/bin/python " . escapeshellarg($pythonScriptPath);
exec($command, $output, $return_var);

if ($return_var !== 0) {
    echo "Error: Unable to execute the script.";
    echo "<br>Command executed: $command";
    echo "<br>Return value: $return_var";
    echo "<pre>" . implode("\n", $output) . "</pre>";
} else {
    echo "<pre>" . implode("\n", $output) . "</pre>";
}
?>
