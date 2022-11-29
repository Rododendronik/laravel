<?php
    if(isset($_POST['treatment_id'])){
        require_once "../config.php";

        $ID = $_POST['treatment_id'];

        $sql = "SELECT T.Name, T.Datetime, T.Description, A.Name, A.Type, A.Age, V.Name, V.Surname, V.Email, V.Phone ".
        "FROM Treatments AS T ".
        "INNER JOIN Animal AS A ON T.AnimalID=A.ID ".
        "INNER JOIN Users AS V ON T.VeterinarianID=V.UserID ".
        "WHERE T.ID = ?";

        if($stmt = mysqli_prepare($link, $sql)){
            
            mysqli_stmt_bind_param($stmt, "i", $ID);
            
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $T_Name, $T_Datetime, $T_Description, $A_Name, $A_Type, $A_Age, $V_Name, $V_Surname, $V_Email, $V_Phone);
                mysqli_stmt_fetch($stmt);

                $split = explode(' ', $T_Datetime);
                $date = join('.', array_reverse(explode('-', $split[0])));
                $time = $split[1];
                $formattedDatetime = join(' ', [$date, $time]);
                $A_Age = date("Y") - $A_Age;
                echo " <h2>$T_Name</h2>
                       <label>When: </label><p>$formattedDatetime</p>
                       <label>Animal Name: </label><p>$A_Name</p>
                       <label>Animal Species: </label><p>$A_Type</p>
                       <label>Animal Age: </label><p>$A_Age</p>
                       <label>Done by: </label><p>$V_Name $V_Surname</p>
                       <label>Contact: </label><p>$V_Email $V_Phone</p>
                       <label>Description: </label><p>$T_Description</p>
                ";
                
            }  
            mysqli_stmt_close($stmt);
        }
    }
?>