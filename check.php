<?php

session_start();
if (!isset($_SESSION['loginid'])) {
    header("location: index.php");
} else {
    header("location: admin/homepage.php");
}
?>