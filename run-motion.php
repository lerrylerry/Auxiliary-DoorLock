<?php
$pythonScriptPath = '/Auxiliary/motion-detection.py';
$command = "/c/Users/aryss/AppData/Local/Programs/Python/Python313/python " . escapeshellarg($pythonScriptPath);
exec($command, $output, $return_var);

if ($return_var !== 0) {
    echo "Error: Unable to execute the script.";
} else {
    echo "<pre>" . implode("\n", $output) . "</pre>";
}
?>
