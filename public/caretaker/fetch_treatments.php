<?php

require_once "../config.php";

if(isset($_POST['name']) || isset($_POST['date']) || isset($_POST['page'])){
    $name = trim($_POST['name']);
    $datetime = trim($_POST['datetime']);
    $animalID = $_POST['id'];
    $variables = array($animalID, "%$name%");
    $paramTypes = "is";

    $sql = "SELECT ID, Datetime, Name FROM Treatments WHERE AnimalID = ? AND Name LIKE ?";

    if(isset($_POST['page']))
        $page = $_POST['page'];
    else
        $page = 1;

    $olddatetime = $datetime;
    
    if(strpos($olddatetime, " ")){
        $split = explode(' ', $olddatetime, 2);
        $date = join('-', array_reverse(explode('.', $split[0])));
        $time = $split[1];
        $datetime = join(' ', [$date, $time]);
    }
    else{
        $datetime = join('-', array_reverse(explode('.', $olddatetime)));
    }
        
    $sql .= " AND Datetime LIKE ?";
    array_push($variables, "%$datetime%");
    $paramTypes .= "s";

    $sql .= " ORDER BY Datetime DESC";

    $page_limit = 8;
    $offset = ($page-1) * $page_limit;

    $sql_for_pagination = $sql;
    $sql .= " LIMIT $offset, $page_limit";

    $params = $variables;
    
    if($stmt = mysqli_prepare($link, $sql)){

        if(!empty($paramTypes))
            mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);


        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $ID, $Datetime, $Name);
            
        }  
    }
?>

<table class="table">
    <thead>
        <tr>
            <th>Datetime</th>
            <th>Name</th>
            <th></th>
        <tr>
    </thead>

    <tbody>
        <?php 
            
            while(mysqli_stmt_fetch($stmt)){
                $split = explode(' ', $Datetime);
                $date = join('.', array_reverse(explode('-', $split[0])));
                $time = $split[1];
                $formattedDatetime = join(' ', [$date, $time]);
                
                echo "<tr>
                        <td>$formattedDatetime</td>
                        <td>$Name</td>
                        <td><button class='viewTreatment btn btn-info btn-s' id='$ID'>VIEW</button></td>
                        <td></td>
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
                mysqli_stmt_bind_result($stmt, $ID, $Datetime, $Name);
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
