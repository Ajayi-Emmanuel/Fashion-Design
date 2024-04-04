<?php

session_start();
// Include the database configuration
include '../../config/db_config.php';
include '../../model/family.model.php';
include '../../model/client.model.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and update family model
    $family['familyname'] = $_POST["familyname"];
    $family['phonenumber'] = $_POST["phonenumber"];
    $family['clientid'] = $_POST["clientid"];

    // Check if the user is logged in
    if(isset($_SESSION['username'])) {
        $family['username'] = $_SESSION['username'];
    }

    // Check if family name and phone number are provided
    if(empty($family['familyname']) || empty($family['phonenumber'])) {  
        echo "<script>alert('Enter Complete Details'); window.location.href = '../../../../FrontEnd/Customer/create-customer.php';</script>";
        // return; // Exit the function
    }
   
    $sql = "SELECT * FROM families WHERE familyname = '" . $family['familyname'] . "' AND username = '" . $_SESSION['username'] . "'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $rows = mysqli_fetch_array($result);
        $family_id = $rows["family_id"];
        addFamily($family_id, $conn);
        
        // If clients are to be added to the family
    }else{
        // Insert new family into the database
        $sql = "INSERT INTO families (familyname, phonenumber, num_clients, username) VALUES ('" . $family['familyname'] . "', '" . $family['phonenumber'] . "', '" . $family['num_clients'] . "', '". $family['username']."')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $family_id = mysqli_insert_id($conn); // Get the inserted family ID
            // If clients are to be added to the family
            addFamily($family_id, $conn);
           
        } else {
            echo "<script>alert('Error registering FAMILY'); window.location.href = '../../../../FrontEnd/Customer/create-customer.php';</script>";
        }
    }
}


function addFamily($family_id, $conn){
    if (!empty($_POST["clientid"]) && is_array($_POST["clientid"])) {
        foreach ($_POST["clientid"] as $clientid) {
            // Update client records with the family ID
            $sql = "SELECT * FROM clients  WHERE clientid = $clientid AND username = '" . $_SESSION['username'] . "'";
            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $rows = mysqli_fetch_array($result);
                $currentValue = $rows["family_id"];
                if(empty($currentValue) || is_null($currentValue)) {
                    $update_client = "UPDATE clients SET family_id = $family_id WHERE clientid = $clientid AND username = '" . $_SESSION['username'] . "'";
                    $update_result_client = mysqli_query($conn, $update_client);
                    $sql = "SELECT * FROM families WHERE family_id = $family_id AND username = '" . $_SESSION['username'] . "'";
                    $result = mysqli_query($conn, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        $rows = mysqli_fetch_array($result);
                        $currentValue = $rows["num_clients"];
                        $newValue = $currentValue + 1;

                        $update_family = "UPDATE families SET num_clients = $newValue WHERE family_id = $family_id AND username = '" . $_SESSION['username'] . "'";
                        $update_result_family = mysqli_query($conn, $update_family);

                    }
                }else{
                     // Family already registered.
                    echo "<script>alert('Client is already registered under a family'); window.location.href = '../../../../FrontEnd/Customer/create-customer.php';</script>";
                    exit; // Exit the function
                    // Handle any errors if needed
                }   
            }
        }
    }
    // Registration successful
    echo "<script>alert('Family Registered successfully'); window.location.href = '../../../../FrontEnd/Customer/customers.php';</script>";
}
?>