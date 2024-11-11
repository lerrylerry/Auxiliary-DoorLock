<?php
require('../dbcred/db.php'); // Include your database connection file

$sqldeleteitem = "DELETE FROM `tbpendingadd`";
mysqli_query($db, $sqldeleteitem);

// Close the database connection (optional)
mysqli_close($db);
?>

