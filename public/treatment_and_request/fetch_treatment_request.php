<?php
    if(isset($_POST['treatment_request_id'])){
        require_once "../config.php";

        $vetExists = "";
        $ID = $_POST['treatment_request_id'];

        $sql = "SELECT VeterinarianID FROM TreatmentRequests WHERE ID = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "i", $ID);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $vetExists);
                mysqli_stmt_fetch($stmt);
            }
        }
        if(empty($vetExists)){
            $sql = "SELECT T.Name, T.Date, T.Description, A.Name, A.Type, A.Age, A.ID, ".
            "C.Name, C.Surname, C.Email, C.Phone FROM TreatmentRequests AS T ". 
            "INNER JOIN Animal AS A ON T.AnimalID=A.ID ".
            "INNER JOIN Users AS C ON T.CaretakerID=C.UserID ".
            "WHERE T.ID = ?";
        }
        else{
            $sql = "SELECT T.Name, T.Date, T.Description, A.Name, A.Type, A.Age, A.ID, ".
            "V.Name, V.Surname, V.Email, V.Phone, C.Name, C.Surname, C.Email, C.Phone FROM TreatmentRequests AS T ". 
            "INNER JOIN Animal AS A ON T.AnimalID=A.ID ".
            "INNER JOIN Users AS V ON T.VeterinarianID=V.UserID ".
            "INNER JOIN Users AS C ON T.CaretakerID=C.UserID ".
            "WHERE T.ID = ?";
        }


        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "i", $ID);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                $V_Name=$V_Surname=$V_Email=$V_Phone="";
                if(empty($vetExists))
                    mysqli_stmt_bind_result($stmt, $T_Name, $T_Date, $T_Description, $A_Name, $A_Type, $A_Age, $A_ID, $C_Name, $C_Surname, $C_Email, $C_Phone );
                else
                    mysqli_stmt_bind_result($stmt, $T_Name, $T_Date, $T_Description, $A_Name, $A_Type, $A_Age, $A_ID, $V_Name, $V_Surname, $V_Email, $V_Phone, $C_Name, $C_Surname, $C_Email, $C_Phone );
                mysqli_stmt_fetch($stmt);
                $A_Age = date("Y") - $A_Age;
                $T_Status = (isset($T_VeterinarianID)?"Accepted":"Pending");
                $formattedDate = join('.', array_reverse(explode('-', $T_Date)));

                echo " <h2>$T_Name</h2> 
                       <p><label>Created: </label>$formattedDate</p>
                       <p><label>Status: </label>$T_Status</p>
                       <label>Description: </label><p>$T_Description</p>
                       <h3>Animal</h3>
                       <p><label>ID: </label>$A_ID</p>
                       <p><label>Name: </label>$A_Name</p>
                       <p><label>Type: </label>$A_Type</p>
                       <p><label>Age: </label>$A_Age</p>
                       <p><label>Type: </label>$A_Type</p>
                       <h3>Created by</h3>
                       <p><label>Full Name: </label>$C_Name $C_Surname</p>
                       <p><label>Contact: </label>$C_Email $C_Phone</p>
                       <h3>Accepted by</h3>
                       <p><label>Full Name: </label>$V_Name $V_Surname</p>
                       <p><label>Contact: </label>$V_Email $V_Phone</p>
                ";
            }  
            mysqli_stmt_close($stmt);
        }
    }
?>