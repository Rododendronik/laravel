<?php

require_once "../config.php";

if(isset($_POST['name']) || isset($_POST['date']) || isset($_POST['page'])){
    $name = trim($_POST['name']);
    $date = trim($_POST['date']);
    $animalID = $_POST['id'];
    $variables = array($animalID, "%$name%");
    $paramTypes = "is";

    $sql = "SELECT ID, Date, Name, Description, VeterinarianID FROM TreatmentRequests WHERE AnimalID = ? AND Name LIKE ?";

    if(isset($_POST['page']))
        $page = $_POST['page'];
    else
        $page = 1;

    $olddate = $date;
    $date = join('-', array_reverse(explode('.', $olddate)));
   
    $sql .= " AND Date LIKE ?";
    array_push($variables, "%$date%");
    $paramTypes .= "s";

    $sql .= " ORDER BY Date DESC";

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
            mysqli_stmt_bind_result($stmt, $ID, $Date, $Name, $Description, $VetID);
            
        }  
    }
?>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Status</th>
            <th>Name</th>
            <th></th>
        <tr>
    </thead>

    <tbody>
        <?php 
            
            while(mysqli_stmt_fetch($stmt)){
                $formattedDate = join('.', array_reverse(explode('-', $Date)));
                $Status = (isset($VetID)?"Accepted":"Pending");
                echo "<tr>
                        <td class='rowDate'>$formattedDate</td>
                        <td class='rowStatus'>$Status</td>
                        <td class='rowName'>$Name</td>
                        <td><button class='viewTreatmentRequest btn btn-info btn-s' id='$ID'>VIEW</button></td>
                        <td><button class='deleteTreatmentRequest btn btn-info btn-s' id='$ID'>DELETE</button></td>
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
                mysqli_stmt_bind_result($stmt, $ID, $Date, $Name, $Description, $VetID);
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
