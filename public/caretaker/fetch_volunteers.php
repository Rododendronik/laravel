<?php

require_once "../config.php";

if(isset($_POST['verification']) ||  isset($_POST['name']) || isset($_POST['email']) ||  isset($_POST['page'])){
    $verification = $_POST['verification'];
    $name = ucfirst(trim($_POST['name']));
    $email = trim($_POST['email']);
    $variables = array();
    $paramTypes = "";

    $sql = "SELECT UserID, Name, Surname, Email FROM Users JOIN
             VolunteersVerification WHERE Users.UserID = VolunteersVerification.VolunteerID
            AND UserRole = 3"; 

    if($verification == "Verified")
        $sql .= " AND Verified = 1";
    else
        $sql .= " AND Verified = 0";

    if(strpos($name, " ")){
        $fullName = explode(' ', $name, 2);
        $name = $fullName[0];
        $surname = ucfirst($fullName[1]);
        $sql .= " AND Name LIKE ? AND Surname LIKE ?";
        array_push($variables, "%$name%", "%$surname%");
        $paramTypes .= "ss";
    }     
    else{
        $sql .= " AND (Name LIKE ? OR Surname LIKE ?)"; 
        array_push($variables, "%$name%", "%$name%");
        $paramTypes .= "ss";
    }

    $sql .= " AND (Email LIKE ? )"; 
    array_push($variables, "%$email%");
    $paramTypes .= "s";

        
    if(isset($_POST['page']))
        $page = $_POST['page'];
    else
        $page = 1;

    $page_limit = 10;
    $offset = ($page-1) * $page_limit;
    
    $sql .= " ORDER BY Name ";

    $sql_for_pagination = $sql;

    $paginationParams = $variables;
    $paginationParamTypes = $paramTypes;
    
    $sql .= " LIMIT ?,?";
    array_push($variables, $offset, $page_limit);
    $paramTypes .= "ii";

    
    $params = $variables;
 
    if($stmt = mysqli_prepare($link, $sql)){

        if(!empty($paramTypes))
            mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);


        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $UserID, $Name, $Surname, $Email);
        }  
    }
?>

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Surname</th>
            <th>Email</th>
            <th>Verification</th>
        <tr>
    </thead>

    <tbody>
        <?php 
            
            if($verification == "Verified"){
                $verificationButtonText = "Unverify";
            }
            else{
                $verificationButtonText = "Verify";
            }

            while(mysqli_stmt_fetch($stmt)){
                
                echo "<tr >
                        <td class='name'>$Name</td>
                        <td class='surname'>$Surname</td>
                        <td>$Email</td>
                        <td><button id='$UserID' class='viewVolunteer btn btn-info btn-s' data-toggle=".'"modal"'." data-target='#myModal'>View</button></td>
                        <td><button id='$UserID' class='verifyVolunteer btn btn-info btn-s' data-toggle=".'"modal"'." data-target='#myModal'>$verificationButtonText</button></td>
                    </tr>";
            }
            mysqli_stmt_close($stmt);
        ?>
    </tbody>
</table>

<div name="pages" id="pages">
    <?php 
        if($stmt = mysqli_prepare($link, $sql_for_pagination))
        {
            if(!empty($paramTypes))
                mysqli_stmt_bind_param($stmt, $paginationParamTypes, ...$paginationParams);
    
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $UserID, $Name, $Surname, $Email);
                $total_rows = mysqli_stmt_num_rows($stmt);
                $total_pages = ceil($total_rows/$page_limit);

                for($i=1; $i<=$total_pages; $i++){
                    echo "<span class='paginationLink' style='cursor:pointer; margin:2px; padding:6px; border:1px solid #ccc;' id='".$i."'>".$i."</span>";
                }

                mysqli_stmt_close($stmt);
            } 
        }
    ?>
</div>
<?php
}
?>
