<?php
    require_once "../config.php";

    function printRequestInfoForTreatment($treatmentID){
        global $link;
        $sql = "SELECT T.Name, T.Date, T.Description, A.Name, A.Type, A.Age, A.ID, ".
                "C.Name, C.Surname, C.Email, C.Phone FROM TreatmentRequests AS T ". 
                "INNER JOIN Animal AS A ON T.AnimalID=A.ID ".
                "INNER JOIN Users AS C ON T.CaretakerID=C.UserID ".
                "WHERE T.ID = ?";
  
        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "i", $treatmentID);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
 

                mysqli_stmt_bind_result($stmt, $T_Name, $T_Date, $T_Description, $A_Name, $A_Type, $A_Age, $A_ID, $C_Name, $C_Surname, $C_Email, $C_Phone );
                mysqli_stmt_fetch($stmt);

                $formattedDate = join('.', array_reverse(explode('-', $T_Date)));
                $A_Age = date("Y") - $A_Age;
                echo " <h3>$T_Name</h3> 
                       <p><label>Created: </label>$formattedDate</p>
                       <label>Description: </label><p>$T_Description</p>
                       <h3>Animal</h3>
                       <p><label>ID: </label>$A_ID <label>Name: </label>$A_Name <label>Type: </label>$A_Type
                       <label>Age: </label>$A_Age</p>
                       <h3>Created by</h3>
                       <p><label>Full Name: </label>$C_Name $C_Surname</p>
                       <p><label>Contact: </label>$C_Email $C_Phone</p>
                ";
            }  
            mysqli_stmt_close($stmt);
        }
    }
?>