<?php
session_start();
if(!isset($_SESSION['username'])) {
    echo "<script>alert('You are not allowed to view this page'); window.location.href = '../Index/index.html';</script>";
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <a href="http://"></a>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="table.css">
    <link rel="stylesheet" href="measurement.css">
    <link rel="stylesheet" href="../logout.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <div class="sidebar">
        <div class="logo-details">
            <i class="ri-scissors-line"></i>
            <span class="logo_name">TAYLUR</span>
        </div>
        <ul class="nav-links">
            <li>
                <div class="iocn-link">
                    <a href="../Customer/customers.php">
                        <i class='bx bx-collection' ></i>
                        <span class="link_name">Customers</span>
                    </a>
                </div>
            </li>
            <li>
                <a href="../Measurement/measurements.php">
                    <i class='bx bx-pie-chart-alt-2' ></i>
                    <span class="link_name">Measurements</span>
                </a>
            </li>
            <li>
                <a href="../Appointment/appointments.php">
                    <i class='bx bx-line-chart' ></i>
                    <span class="link_name">Appointment</span>
                </a>
            </li>
            <li>
                <a href="../Project/project.php">
                    <i class='bx bx-history'></i>
                    <span class="link_name">Projects</span>
                </a>
            </li>
            <li>
                <div class="profile-details">
                    <div class="profile-content"></div>
                    <div class="name-job">
                        <?php
                        if (isset($_SESSION["username"])) {
                            $user = $_SESSION["username"];
                            echo "<div class='profile_name'>" . $user . "</div>";
                        }
                        ?>
                        <div class="job"> Fashion Designer </div>
                    </div>
                    <form action="" method="post">
                        <button type="submit" name="logout" class="logout-button"><i class='bx bx-log-out'></i></button>
                    </form>
                    <?php
                    include '../../BackEnd/src/controllers/logoutController.php';
                    if (isset($_POST['logout'])) {
                        logout();
                    }
                    ?>
                </div>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <div class="home-content">
            <i class='bx bx-menu' ></i>
            <span class="text">Welcome <i class="ri-user-6-line"></i></span>
        </div>
        <div class="container-above">
            <button id="individualButton">Individual</button>
            <button id="familyButton">Family</button>
        </div>

        <div id="measurementModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick = "closeModal()">&times;</span>
                <div id="measurements">
                    <!-- Measurements details will be filled in by JavaScript -->
                </div>
            </div>
        </div>

        <button id="addNewButton2" onclick="window.location.href = '../Customer/create-customer.php#individualx';">
            Add New Individual or Family
        </button>
        <div class='individual' id='individuals'>
            <h1 class='heading'>All Client</h1>
            <table border ="1" class ='table'>
              <?php
              include '../../BackEnd/src/config/db_config.php';
              if(isset($_SESSION['username'])) {
                  $username = $_SESSION['username'];
                  $sql = "SELECT * FROM clients WHERE username = '$username'";
                  $result = mysqli_query($conn, $sql);
                  if (mysqli_num_rows($result) > 0) {
                      // echo "<table border='1' class ='table'>";
                      echo "<tr><th>S/N</th><th>Full Name</th><th>Gender</th><th>Phone Number</th><th>Address</th><th>Show Measurement</th></tr>";
                      $serialNumber = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $serialNumber++ . "</td>";
                        echo "<td>" . $row['fullname'] . "</td>";
                        echo "<td>" . $row['gender'] . "</td>";
                        echo "<td>" . $row['phonenumber'] . "</td>";
                        echo "<td>" . $row['address'] . "</td>";
                        echo "<td><i class='ri-more-line' style='cursor: pointer;' onclick='toggleMeasurements(" . $row['clientid'] . ")'</i></td>";
                        echo "</tr>";
                      }
                  } else {
                    echo "<p class='error-message'>No clients has been registered</p>";

                  }
              }
              mysqli_close($conn);
              ?>
            </table>
        </div>
        <div class='family' id='family'>
          <h1 class='heading'>All Family</h1>
            <!-- <table border ="1" class ='table'> -->
              <?php
                include '../../BackEnd/src/config/db_config.php';
                if(isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];
                    $sql = "SELECT * FROM families WHERE username = '$username'";
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0) {
                        echo "<table border='1' class='table'>";
                        echo "<tr><th>S/N</th><th>Family Name</th><th>Phone Number</th><th>Number of clients</th></tr>";
                        $serialNumber = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $serialNumber++ . "</td>";
                            echo "<td>" . $row['familyname'] . "</td>";
                            echo "<td>" . $row['phonenumber'] . "</td>";
                            echo "<td>" . $row['num_clients'] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                      echo "<p class='error-message'>No Family has been registered </p>";

                    }
                } 
                mysqli_close($conn);
              ?>
            </table>
        </div>
    </section>
    <script>
      
      let sidebar = document.querySelector(".sidebar");
      let sidebarBtn = document.querySelector(".bx-menu");
      console.log(sidebarBtn);
      sidebarBtn.addEventListener("click", ()=>{
        sidebar.classList.toggle("close");
      });

      function toggleMeasurements(clientid) {
        var fullname = event.target.dataset.fullname;
        var modal = document.getElementById("measurementModal");

        if (modal.style.display === "none" || modal.style.display === "") {
            modal.style.display = "block";
        } else {
            modal.style.display = "none";
            return;
        }
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "../../BackEnd/src/controllers/fetch_measurement.php?clientid=" + clientid, true);
        
        xhr.onload = function() {
            if (this.status == 200) {
                var response = JSON.parse(this.responseText);

                // Clear previous content
                // measurementsDiv.innerHTML = '';

                // Check if the response has an 'error' property
                if (response.error) {
                    // Display the error message with class for styling
                    document.getElementById("measurements").innerHTML = "<p class ='error-message'>" + response.error + "</p>";
                    
                } else {
                    // Proceed with assuming the response is an array of measurements
                    
                    var content = "<div class='measurements-container'><p class='measurement-header'>Measurements for Client with id: " + clientid + "</p>";

                    response.forEach(function(measurement) {
                        content += "<div class='measurement-set'>";
                        for (var key in measurement) {
                            if (measurement.hasOwnProperty(key)) {
                                var niceKey = key.charAt(0).toUpperCase() + key.slice(1).replace(/_/g, ' ');
                                content += "<div class='measurement-detail'>" + niceKey + ": " + measurement[key] + "</div>";
                            }
                        }
                        content += "</div>"; // Close measurement-set div
                    });

                    content += "</div>"; // Close measurements-container div
                    document.getElementById("measurements").innerHTML = content;
                }
            } else {
                console.error("Server returned status:", this.status);
                // Handle non-200 status by displaying an error message in the div
                document.getElementById("measurements").innerHTML = "<p>Error loading measurement. Please try again</p>";
            }
        };




        xhr.onerror = function() {
            console.error("Error fetching measurements. Network issue.");
            // Display an error message
            document.getElementById("measurements").innerHTML = "<p>Error festching measurement due to network issues.</p>";
        };
        xhr.send();
    }

        function closeModal(){
            var modal = document.getElementById("measurementModal");
            modal.style.display = "none";
        }
      document.addEventListener("DOMContentLoaded", function() {
          // Selecting DOM elements
          let individualButton = document.getElementById("individualButton");
          let familyButton = document.getElementById("familyButton");
          let individuals = document.getElementById("individuals");
          let family = document.getElementById("family");

          // Initially hide the family table
          family.style.display = "none";

          // Event listener for the individual button
          individualButton.addEventListener("click", function() {
              individuals.style.display = "block";
              family.style.display = "none";
          });

          // Event listener for the family button
          familyButton.addEventListener("click", function() {
              individuals.style.display = "none";
              family.style.display = "block";
          });
      });
    </script>
</body>
</html>
