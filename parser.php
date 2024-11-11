<?php

require('dbcred/db.php');

if (isset($_GET['pincode'])) {
    $sqlgetpin = "SELECT name,status from tbup WHERE pincode = '" . $_GET['pincode'] . "' AND dooraccess ='Approved'";
    $sqlnullpin = "UPDATE `tbup` SET `dooraccess` = NULL WHERE pincode = '" .  $_GET['pincode'] . "'";
    $result = mysqli_query($db, $sqlgetpin);
    $nullpin = mysqli_query($db, $sqlnullpin);
    $data = mysqli_fetch_array($result);

    if (!empty($data)) {
        echo "startname;" . $data [0] . ";endname;startstatus;" . $data [1] . ";endstatus;";

    } else {
	if (($_GET['pincode']) === "0000") {
	    echo "startname;Master;endname;startstatus;active;endstatus;";
	} else {
            echo "nothing;noresult";
 	 }
	}
}



?>
