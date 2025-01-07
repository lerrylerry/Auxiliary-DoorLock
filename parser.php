<?php

require('dbcred/db.php');

if (isset($_GET['pincode'])) {
    // Prepare SQL to get the pincode details from tbup
    $sqlgetpin = "SELECT name, status FROM tbup WHERE pincode = '" . $_GET['pincode'] . "' AND dooraccess = 'Approved'";
    $sqlnullpin = "UPDATE tbup SET dooraccess = NULL WHERE pincode = '" . $_GET['pincode'] . "'";
    $sqlMaster = "SELECT pincodes FROM tbparser WHERE id = 1";
    
    // Query the tbup table
    $resultMaster = mysqli_query($db, $sqlMaster);
    $result = mysqli_query($db, $sqlgetpin);
    $nullpin = mysqli_query($db, $sqlnullpin);
    $data = mysqli_fetch_array($result);
    $data2 = mysqli_fetch_array($resultMaster);

    if (!empty($data2)) {
        echo "startname;" . $data [0] . ";endname;startstatus;" . $data [1] . ";endstatus;";

    } else {
	    if (($_GET['pincode']) === "7676") {
	    echo "startname;Master;endname;startstatus;active;endstatus;";
	} else {
            echo "nothing;noresult";
 	 }
	}
}

?>
