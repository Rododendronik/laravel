<?php

require_once "../config.php";

if(isset($_POST['datetime']) || isset($_POST['animalName']) || isset($_POST['animalID']) || isset($_POST['vetID']) || isset($_POST['page'])){
    $datetime = trim($_POST['datetime']);
    $vetID = $_POST['vetID'];
    $animalName = ucfirst($_POST['animalName']);
    $animalID = $_POST['animalID'];
    $variables = array($vetID,"%$animalName%","%$animalID%");
    $paramTypes = "iss";
    
    $vetID = $_POST['vetID'];
    $sql = "SELECT T.ID, T.Name, T.Datetime, A.Name, A.Type, A.ID FROM Treatments AS T INNER JOIN Animal AS A WHERE VeterinarianID = ? AND T.AnimalID=A.ID ".
            "AND A.Name LIKE ? AND A.ID LIKE ? AND T.Datetime LIKE ?";

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
        
    array_push($variables, "%$datetime%");
    $paramTypes .= "s";

    $page_limit = 8;
    $offset = ($page-1) * $page_limit;

    $sql_for_pagination = $sql;
    $paginationParams = $variables;
    $paginationParamTypes = $paramTypes;
    
    $sql .= " ORDER BY Datetime DESC";
    $sql .= " LIMIT ?,?";
    array_push($variables, $offset, $page_limit);
    $paramTypes .= "ii";

    $params = $variables;
    
    if($stmt = mysqli_prepare($link, $sql)){

        if(!empty($paramTypes))
            mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);


        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $T_ID, $T_Name, $Datetime, $A_Name, $A_Type, $A_ID);
            
        }  
    }
    
?>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Animal ID</th>
            <th>Animal Name</th>
            <th>Treatment Name</th>
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
                        <td>$A_ID</td>
                        <td>$A_Name</td>
                        <td>$T_Name</td>
                        <td><button class='viewTreatment btn btn-info btn-s' id='$T_ID'>VIEW</button></td>
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
                mysqli_stmt_bind_result($stmt, $T_ID, $T_Name, $Datetime, $A_Name, $A_Type, $A_ID);
                $total_rows = mysqli_stmt_num_rows($stmt);
                $total_pages = ceil($total_rows/$page_limit);
                
                if(isset($_POST['vetID'])){
                    for($i=1; $i<=$total_pages; $i++){
                        echo "<span class='paginationLinkAccepted' style='cursor:pointer; margin:2px; padding:6px; border:1px solid #ccc;' id='".$i."'>".$i."</span>";
                    }
                }
                else{
                    for($i=1; $i<=$total_pages; $i++){
                        echo "<span class='paginationLinkAvailable' style='cursor:pointer; margin:2px; padding:6px; border:1px solid #ccc;' id='".$i."'>".$i."</span>";
                    }
                }


                mysqli_stmt_close($stmt);
            } 
        }
    ?>
</div>
<?php
}
?>
