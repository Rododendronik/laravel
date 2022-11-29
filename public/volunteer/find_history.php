<?php

require_once "../config.php";
session_start();

if(isset($_POST['date']) || isset($_POST['page'])){
    $date = $_POST['date'];
    $olddate = $date;
    $date = join('-', array_reverse(explode('.', $olddate)));
    $date = "%$date%";
    $IDD = $_POST['UserID'];
    $sql = "SELECT Reservations.ID, Timeslot, Date, Status, AnimalID, VolunteerID, CaretakerID, Name FROM Reservations INNER JOIN Animal ON Reservations.AnimalID = Animal.ID WHERE VolunteerID = ? AND Date LIKE ? ORDER BY Date DESC";
    
    if(isset($_POST['page']))
        $page = $_POST['page'];
    else
        $page = 1;

    $page_limit = 12;
    $offset = ($page-1) * $page_limit;

    $sql_for_pagination = $sql;
    $sql .= " LIMIT $offset, $page_limit";

    if($stmt = mysqli_prepare($link, $sql)){

        mysqli_stmt_bind_param($stmt, "is", $IDD, $date);
        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $ID, $Timeslot, $Date, $Status, $AnimalID, $VolunteerID, $CaretakerID, $Name);
        }  
    }
?>

<table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Timeslot</th>
            <th>Animal</th>
            <th></th>
        <tr>
    </thead>

    <tbody>
        <?php 

            while(mysqli_stmt_fetch($stmt)){
                
                $formattedDate = join('.', array_reverse(explode('-', $Date)));

                echo "<tr>
                        <td>$formattedDate</td>
                        <td>$Timeslot</td>
                        <td><a href='animal.php?animal=".$AnimalID."' class='btn btn-info'>$Name</a></td>
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
            mysqli_stmt_bind_param($stmt, "is", $IDD, $date );
    
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $ID, $Timeslot, $Date, $Status, $AnimalID, $VolunteerID, $CaretakerID, $Name);
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
