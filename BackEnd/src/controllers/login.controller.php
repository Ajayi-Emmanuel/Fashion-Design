<?php
    session_start();

    include '../config/db_config.php';
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get username and password from form
        $username = $_POST["username"];
        $password = $_POST["password"];
    
        $username = mysqli_real_escape_string($conn , $username);
        $password = mysqli_real_escape_string($conn , $password);
        if ($username == "" || $password == "") {
            echo "<script>alert('Enter Complete Details'); window.location.href = '../../../FrontEnd/Index/index.html';</script>";
        }
       
        // Prepare a SQL statement to fetch user from the database
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $sql);
    
        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_array($result);
            if(password_verify($password, $row["password"])) {
                
                $_SESSION['username'] = $username;
                echo "<script>alert('Login Successfull'); window.location.href = '../../../FrontEnd/Customer/customers.php';</script>";
                
                exit;
            }else {
                // Authentication failed, redirect back to login page with error message
                               
                echo "<script>alert('Invalid username or password'); window.location.href = '../../../FrontEnd/Index/index.html';</script>";

                exit;
            }
        }else {
            // User does not exist, redirect back to login page with error message
            echo "<script>alert('User not found); window.location.href = '../../../FrontEnd/Index/index.html';</script>";
            
        }
    } else {
        // Redirect back to login page if accessed directly
        echo "<script>alert('Method not allowed'); window.location.href = '../../../FrontEnd/Index/index.html';</script>";

    }
?>