<?php

require_once "../config.php";

session_start();

if(isset($_POST['type']) || isset($_POST['name']) || isset($_POST['page'])){
    $type = $_POST['type'];
    $name = ucfirst(trim($_POST['name']));
    $variables = array();
    $paramTypes = "";

    $sql = "SELECT ID, Name, Type, Age, Image FROM Animal";

    if($type != "Any"){
        $sql .= " WHERE Type = ?";
        array_push($variables, $type);
        $paramTypes .= "s";
    }
        

    if($type == "Any")
        $sql .= " WHERE Name LIKE ?";
    else
        $sql .= " AND Name LIKE ?";
    array_push($variables, "%$name%");
    $paramTypes .= "s";


    if(isset($_POST['page']))
        $page = $_POST['page'];
    else
        $page = 1;

    $page_limit = 8;
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
            mysqli_stmt_bind_result($stmt, $ID, $Name, $Type, $Age, $Image);
        }  
    }
?>

<div class="container">
    <?php 

        while(mysqli_stmt_fetch($stmt)){
            $Age = date("Y") - $Age;
            echo "
            <div class='animalBox'>
                    <h2 class='text-primary'>$Name</h2>
                    $Type, $Age years old
                    <img src='../$Image' style='width:100%; height:300px; object-fit: cover;'>
                    <a href='animal.php?animal=".$ID."' class='viewAnimal btn btn-info btn-block' id='".$ID."'>VIEW</a>
            </div>";
        }
        mysqli_stmt_close($stmt);
    ?>
</div>

<div name="pages" id="pages">
    <?php 
        if($stmt = mysqli_prepare($link, $sql_for_pagination))
        {
            if(!empty($paramTypes))
                mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $ID, $Name, $Type, $Age, $Image);
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