<?php

require_once "../config.php";

if(isset($_POST['date']) || isset($_POST['animalName']) || isset($_POST['page'])){
    $date = trim($_POST['date']);
    $animalName = ucfirst(trim($_POST['animalName']));

    $variables = array();
    $paramTypes = "";
    

    if(isset($_POST['vetID'])){
        $vetID = $_POST['vetID'];
        $sql = "SELECT T.ID, T.Date, T.Name, A.Name, A.Type, A.ID, T.VeterinarianID FROM TreatmentRequests AS T INNER JOIN Animal AS A WHERE T.AnimalID = A.ID AND T.VeterinarianID = ? ";
        array_push($variables, $vetID);
        $paramTypes .= "i";
    }
    else{
        //only show untaken, pending requests
        $sql = "SELECT T.ID, T.Date, T.Name, A.Name, A.Type, A.ID, T.VeterinarianID FROM TreatmentRequests AS T INNER JOIN Animal AS A WHERE T.AnimalID = A.ID AND T.VeterinarianID IS NULL ";
    }

    if(isset($_POST['page']))
        $page = $_POST['page'];
    else
        $page = 1;

    $olddate = $date;
    $date = join('-', array_reverse(explode('.', $olddate)));
  
    $sql .= "AND A.Name LIKE ? AND T.Date LIKE ?";
    array_push($variables, "%$animalName%","%$date%");
    $paramTypes .= "ss";

    $page_limit = 8;
    $offset = ($page-1) * $page_limit;

    $sql .= " ORDER BY T.Date DESC";

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
            mysqli_stmt_bind_result($stmt, $ID, $Date, $Name, $animalName, $animalType, $animalID, $SavedVetID);
            
        }  
    }
    
?>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Animal Name</th>
            <th>Animal Type</th>
            <th>Name</th>
            <th></th>
        <tr>
    </thead>

    <tbody>
        <?php 
            while(mysqli_stmt_fetch($stmt)){
                $formattedDate = join('.', array_reverse(explode('-', $Date)));
                $Status = (isset($SavedVetID)?"Accepted":"Pending");
                if(isset($vetID)){
                    echo $animalID;
                    echo "<tr>
                            <td>$formattedDate</td>
                            <td>$animalName</td>
                            <td>$animalType</td>
                            <td>$Name</td>
                            <td><button class='returnTreatmentRequest btn btn-info btn-s' id='$ID'>RETURN</button></td>
                            <td><button class='viewTreatmentRequest btn btn-info btn-s' id='$ID'>VIEW</button></td>
                            <td><a href='treatment.php?treatmentRequestID=$ID' class='createTreatment btn btn-info btn-s' id='$ID'>CREATE TREATMENT</a></td>
                        </tr>";
                }
                elseif($Status == "Pending"){
                    echo "<tr>
                            <td>$formattedDate</td>
                            <td>$animalName</td>
                            <td>$animalType</td>
                            <td>$Name</td>
                            <td><a href='#' class='viewTreatmentRequest btn btn-info btn-s' id='".$ID."'>VIEW</button></td>
                            <td><a href='#' class='acceptTreatmentRequest btn btn-info btn-s' id='".$ID."'>ACCEPT</button></td>
                        </tr>";
                }
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
                mysqli_stmt_bind_result($stmt, $ID, $Date, $Name, $animalName, $animalType, $animalID, $SavedVetID);
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
