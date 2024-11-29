<?php
$pythonScriptPath = '../motion-detection.py';
$output = shell_exec("python" . escapeshellarg($pythonScriptPath));
echo "<pre>$output</pre>";
?>
