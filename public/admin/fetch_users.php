<?php

require_once "../config.php";

if(isset($_POST['role']) ||  isset($_POST['name']) ||  isset($_POST['email']) ||  isset($_POST['page'])){
    $role = $_POST['role'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $variables = array();
    $paramTypes = "";

    $sql = "SELECT UserID, Name, Surname, Email FROM Users WHERE UserRole = ?"; 
    array_push($variables, $role);
    $paramTypes .= "i";

    if(strpos($name, " ")){
        $fullName = explode(' ', $name, 2);
        $name = $fullName[0];
        $surname = $fullName[1];
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
    $sql .= " LIMIT $offset, $page_limit";

    
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
            <th>Manage</th>
        <tr>
    </thead>

    <tbody>
        <?php 
            while(mysqli_stmt_fetch($stmt)){
                
                echo "<tr >
                        <td style='width:25%' class='name'>$Name</td>
                        <td style='width:25%'class='surname'>$Surname</td>
                        <td style='width:25%'>$Email</td>
                        <td style='width:15%'><a href='updateuser.php?id=$UserID' class='btn btn-success'>Update</a>  <button name=".$Name." surname=".$Surname." email=".$Email." systemrole=".$role." userid=".$UserID." class='btn delete btn-danger'>Delete</button></td>
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
                mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    
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
