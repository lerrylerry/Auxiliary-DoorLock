<?php

require('dbcred/db.php');

if (isset($_GET['pincode'])) {
    // Prepare SQL to get the pincode details from tbup
    $sqlgetpin = "SELECT name, status FROM tbup WHERE pincode = '" . $_GET['pincode'] . "' AND dooraccess = 'Approved'";
    $sqlnullpin = "UPDATE tbup SET dooraccess = NULL WHERE pincode = '" . $_GET['pincode'] . "'";

    // Query the tbup table
    $result = mysqli_query($db, $sqlgetpin);
    $nullpin = mysqli_query($db, $sqlnullpin);
    $data = mysqli_fetch_array($result);

    // If no result is found with the given pincode in tbup
    if (empty($data)) {
        // Query the tbparser table to get the dynamic 'master' value for id = 1
        $sqlMaster = "SELECT pincodes FROM tbparser WHERE id = 1";
        $resultMaster = mysqli_query($db, $sqlMaster);

        // If a result is found for the 'master' value in tbpars er
        if (($_GET['pincode']) === $resultMaster ) {
            $row = mysqli_fetch_assoc($resultMaster);
            $masterName = "Master" ;// Get the 'master' value from tbparser

            // Output the dynamic values
            echo "startname;{$masterName};endname;startstatus;active;endstatus;";
        } else {
            // In case there's no 'master' in tbparser, return a no result message
            echo "nothing;noresult";
        }
    } else {
        // If data is found for the given pincode, return the name and status from tbup
        echo "startname;" . $data[0] . ";endname;startstatus;" . $data[1] . ";endstatus;";
    }
}

?>
