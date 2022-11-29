<?php
    if(isset($_POST['ID'])){
        require_once "../config.php";

        $ID = $_POST['ID'];

        $sql = "SELECT Name, Surname, Email, Phone, Address FROM Users WHERE UserID = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "i", $ID);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $Name, $Surname, $Email, $Phone, $Address);
                mysqli_stmt_fetch($stmt);
                
                echo " 
                       <label>Name: </label><p>$Name</p>
                       <label>Surname: </label><p>$Surname</p>
                       <label>Email: </label><p>$Email</p>
                       <label>Phone: </label><p>$Phone</p>
                       <label>Address: </label><p>$Address</p>
                ";
                
            }  
            mysqli_stmt_close($stmt);
        }
    }
?>