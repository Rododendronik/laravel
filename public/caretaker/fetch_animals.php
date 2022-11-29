<?php

require_once "../config.php";

if(isset($_POST['type']) || isset($_POST['name']) || isset($_POST['page'])){
    $type = $_POST['type'];
    $name = ucfirst(trim($_POST['name']));
    $variables = array();
    $paramTypes = "";

    $sql = "SELECT ID, Name, Type, Age, Image FROM Animal";

    if($type != "Any"){
        $sql .= " WHERE Type = ?"; //type
        array_push($variables, $type);
        $paramTypes .= "s";
    }
        

    if($type == "Any")
        $sql .= " WHERE Name LIKE ?";  //name
    else
        $sql .= " AND Name LIKE ?"; //name
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
            mysqli_stmt_bind_result($stmt, $ID, $Name, $Type, $Age, $Image);
        }  
    }
?>

<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Age</th>
            <th></th>
        <tr>
    </thead>

    <tbody>
        <?php 

            while(mysqli_stmt_fetch($stmt)){
                $Age = date("Y") - $Age;
                echo "<tr>
                        <td>$Name</td>
                        <td>$Type</td>
                        <td>$Age</td>
                        <td><img src='../$Image' style='width:100px; height:100px; object-fit: contain;'><td>
                        <td><a href='animal_view.php?animal=$ID' class='viewAnimal btn btn-info btn-s' id='$ID'>VIEW</a></td>
                        <td><a href='animal_edit.php?animalID=$ID' class='viewAnimal btn btn-info btn-s' id='$ID'>EDIT</a></td>
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
