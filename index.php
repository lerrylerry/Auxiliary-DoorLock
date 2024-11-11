<?php

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: index.html");
} else {
    header("location: admin/homepage.php");
}
?>