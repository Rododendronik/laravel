<?php
    require_once "../config.php";
    require_once "../session_expiration.php";
    require_once "caretaker_auth.php";  
    
    if(isset($_GET['date']) && isset($_GET['animal_id'])){
        
        $date = $_GET['date'];
        $animal_id = $_GET['animal_id'];

        $sql = "SELECT Name, Type FROM Animal WHERE ID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $animal_id);
        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $Name, $Type);

            if(mysqli_stmt_num_rows($stmt) > 0){
                mysqli_stmt_fetch($stmt);
                $animalName = $Name;
                $animalType = $Type;
            }
            mysqli_stmt_close($stmt);
        }

        $sql = "SELECT Timeslot, Status FROM Reservations WHERE Date = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "si", $date, $animal_id);

        $res = array();

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $Timeslot, $Status);

            if(mysqli_stmt_num_rows($stmt) > 0){
                while(mysqli_stmt_fetch($stmt)){
                    $res[] =  [$Timeslot, $Status];
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    if(isset($_POST['cancelbtn']) && isset($_POST['status']) && isset($_POST['timeslot'])){
        $timeslot = $_POST['timeslot'];
        $status = $_POST['status'];

        $sql = "DELETE FROM Reservations WHERE Date = ? AND Timeslot = ? AND Status = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $date, $timeslot, $status, $animal_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $k = 0;
        foreach ($res as &$tmp){
            if(empty(array_diff($tmp, [$timeslot, $status]))){
                unset($res[$k]);
                break;
            }
            $k++;
        }
        $msg = "<div class='alert alert-danger alert-dismissible'>Reservation for ".$timeslot." canceled<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    if(isset($_POST['cancelblckbtn']) && isset($_POST['blck_timeslot'])){
        $timeslotstart = explode(' - ', $_POST['blck_timeslot'])[0];
        $timeslotend = explode(' - ', $_POST['blck_timeslot'])[1];

        $sql = "DELETE FROM Availability WHERE TimeslotStart = ? AND TimeslotEnd = ? AND Date = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $timeslotstart, $timeslotend, $date, $animal_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        $msg = "<div class='alert alert-danger alert-dismissible'>Block Window for ".$timeslotstart." - ".$timeslotend." canceled<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    if(isset($_POST['approvebtn']) && isset($_POST['status']) && isset($_POST['timeslot'])){

        $timeslot = $_POST['timeslot'];
        $status_before = $_POST['status'];
        $caretakerID = $_SESSION['id'];

        $sql = "UPDATE Reservations SET Status = ?, CaretakerID = ? WHERE Date = ? AND Timeslot = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        $status_appr = "Approved";
        mysqli_stmt_bind_param($stmt, "sissi", $status_appr, $caretakerID, $date, $timeslot, $animal_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $k = 0;
        foreach ($res as &$tmp){
            if(empty(array_diff($tmp, [$timeslot, $status_before]))){
                $res[$k] = [$timeslot, $status_appr];
                break;
            }
            $k++;
        }
        $msg = "<div class='alert alert-success alert-dismissible'>Reservation for ".$timeslot." approved<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    if(isset($_POST['cancelapprovedbtn']) && isset($_POST['appr_status']) && isset($_POST['appr_timeslot'])){
        $timeslot = $_POST['appr_timeslot'];
        $status = $_POST['appr_status'];

        $sql = "DELETE FROM Reservations WHERE Date = ? AND Timeslot = ? AND Status = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "sssi", $date, $timeslot, $status, $animal_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $k = 0;
        foreach ($res as &$tmp){
            if(empty(array_diff($tmp, [$timeslot, $status]))){
                unset($res[$k]);
                break;
            }
            $k++;
        }
        $msg = "<div class='alert alert-danger alert-dismissible'>Reservation for ".$timeslot." canceled<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    if(isset($_POST['kidnapped']) && isset($_POST['appr_status']) && isset($_POST['appr_timeslot'])){
        $timeslot = $_POST['appr_timeslot'];
        $status = $_POST['appr_status'];

        $sql = "UPDATE Reservations SET Status = ? WHERE Date = ? AND Timeslot = ? AND Status = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        $pickedup = "Away";
        mysqli_stmt_bind_param($stmt, "ssssi", $pickedup, $date, $timeslot, $status, $animal_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $k = 0;
        foreach ($res as &$tmp){
            if(empty(array_diff($tmp, [$timeslot, $status]))){
                $res[$k] = [$timeslot, $pickedup];
                break;
            }
            $k++;
        }
        $msg = "<div class='alert alert-warning alert-dismissible'>Animal picked up<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    if(isset($_POST['returned']) && isset($_POST['away_status']) && isset($_POST['away_timeslot'])){
        $timeslot = $_POST['away_timeslot'];
        $status = $_POST['away_status'];

        $sql = "UPDATE Reservations SET Status = ? WHERE Date = ? AND Timeslot = ? AND Status = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        $returned = "Returned";
        mysqli_stmt_bind_param($stmt, "ssssi", $returned, $date, $timeslot, $status, $animal_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $k = 0;
        foreach ($res as &$tmp){
            if(empty(array_diff($tmp, [$timeslot, $status]))){
                $res[$k] = [$timeslot, $returned];
                break;
            }
            $k++;
        }
        $msg = "<div class='alert alert-success alert-dismissible'>Animal returned<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    $aval_problem = "";
    $availability = array();

    if(isset($_POST['availability'])){
        $from = $_POST['from'];
        $until = $_POST['until'];

        if($from >= $until){
            $aval_problem = "<div class='alert alert-danger alert-dismissible'>Start can not exceed end <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
        }
        else{
            $sql = "SELECT TimeslotStart, TimeslotEnd FROM Availability WHERE AnimalID = ? AND Date = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "is", $animal_id, $date);

            $not_available = array();

            if(mysqli_stmt_execute($stmt)){

                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $TimeslotStart, $TimeslotEnd);

                if(mysqli_stmt_num_rows($stmt) > 0){
                    $i = 0;
                    while(mysqli_stmt_fetch($stmt)){
                        $not_available[$i] = array($TimeslotStart, $TimeslotEnd);
                        $i++;
                    }
                }
                mysqli_stmt_close($stmt);
            }

            $can_create = true;
        
            for($i = 0; $i < count($not_available); $i++){
                if(($not_available[$i][0] <= $from && $not_available[$i][1] > $from) || ($not_available[$i][0] < $until && $not_available[$i][1] >= $until)){
                    $can_create = false;
                }
            }
            
            if($can_create){
                
                $sql = "DELETE FROM Reservations WHERE Date = ? AND Timeslot = ? AND AnimalID = ?";
                $stmt = mysqli_prepare($link, $sql);
                for($i = 0; $i < count($res); $i++){
                    $ts = explode(' - ', $res[$i][0]);
                    if($ts[0] >= $from && $ts[1] <= $until){
                        mysqli_stmt_bind_param($stmt, "ssi", $date, $res[$i][0], $animal_id);
                        mysqli_stmt_execute($stmt);
                        unset($res[$i]);
                    }
                }
                mysqli_stmt_close($stmt);

                $sql = "INSERT INTO Availability (TimeslotStart, TimeslotEnd, Date, AnimalID) VALUES (?,?,?,?)";
                $stmt = mysqli_prepare($link, $sql);
                $animalID = $animal_id;
                mysqli_stmt_bind_param($stmt, "sssi",  $from, $until, $date, $animalID);
                if(mysqli_stmt_execute($stmt)){
                    $aval_problem  = "<div class='alert alert-success alert-dismissible'>Block period added <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
                    mysqli_stmt_close($stmt);
                    $availability[]  = $from." - ".$until;
                }
            } else {
                $aval_problem  = "<div class='alert alert-danger alert-dismissible'>Time period interfere with another blocking window<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
            }
        }
    }

    $duration = 30;
    $start = "07:00";
    $end = "19:00";

    function timeslots($duration, $start, $end, &$availability, &$blockedslots = array()){
        global $link;
        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = new DateInterval("PT".$duration."M");
        $slots = array();

        $sql = "SELECT TimeslotStart, TimeslotEnd FROM Availability WHERE AnimalID = ? AND Date = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "is", $_GET['animal_id'], $_GET['date']);

        $not_available = array();

        if(mysqli_stmt_execute($stmt)){
            
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $TimeslotStart, $TimeslotEnd);

            if(mysqli_stmt_num_rows($stmt) > 0){
                $i = 0;
                while(mysqli_stmt_fetch($stmt)){
                    $not_available[$i] = array($TimeslotStart, $TimeslotEnd);
                    $i++;
                }
            }
            mysqli_stmt_close($stmt);
        }

        for($intStart = $start; $intStart < $end; $intStart->add($interval)){
            $endPeriod = clone $intStart;
            $endPeriod-> add($interval);

            if($endPeriod > $end){
                break;
            }

            for($i = 0; $i < count($not_available); $i++){
                if($not_available[$i][0] <= $intStart->format("H:i") && $not_available[$i][1] >= $endPeriod->format("H:i")){
                    $availability[] = $intStart->format("H:i")." - ".$endPeriod->format("H:i");
                    $blockedslots[] = [$intStart->format("H:i")." - ".$endPeriod->format("H:i"), $not_available[$i][0]." - ".$not_available[$i][1]];
                    break;
                }
            }     
            $slots[] = $intStart->format("H:i")." - ".$endPeriod->format("H:i");
        }
        return $slots;
    }

    function getVolunteerName($date, $timeslot, $animal_id){
        global $link;
        $VolunteerID = 0;
        $name = "";

        $sql = "SELECT VolunteerID FROM Reservations WHERE Date = ? AND Timeslot = ? AND AnimalID = ?";
       
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ssi", $date, $timeslot, $animal_id);

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $VolunteerID);

            if(mysqli_stmt_num_rows($stmt) > 0){
                mysqli_stmt_fetch($stmt);
            }
            mysqli_stmt_close($stmt);
        }

        $sql = "SELECT Name, Surname FROM Users WHERE UserID = ?";

        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "i", $VolunteerID);

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $Name, $Surname);

            if(mysqli_stmt_num_rows($stmt) > 0){
                mysqli_stmt_fetch($stmt);
                $name =  $Name . " "  .$Surname;
            }
            mysqli_stmt_close($stmt);
        }

        return $name;
    }

    function getTimeperiod($timeslot, $blockedslots){

        for($i = 0; $i < count($blockedslots); $i++){
            if($timeslot == $blockedslots[$i][0]){
                return $blockedslots[$i][1];
            }
        }
        return "";
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title></title>
    <style>
    .btn-success, .btn-warning, .btn-info, .btn-danger, .btn-secondary, .btn-default { 
        padding-left:40px; 
        padding-right:40px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">Booking for <?php echo "$animalType $animalName" ?>, Date: <?php echo date('d.m.Y', strtotime($date)); ?></h1><hr>
        <div class="row">
            <div class="col-md-12">
                <?php echo isset($msg) ? $msg : '' ?>
            </div>
            <?php 
                $blockedslots = array();
                $timeslots = timeslots($duration, $start, $end, $availability, $blockedslots);

                foreach($timeslots as $ts){
                    $name = getVolunteerName($date, $ts, $animal_id);
                    $blockedslot = getTimeperiod($ts, $blockedslots);
            ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?php if(in_array($ts, $availability)){ ?>
                                <button class="btn btn-secondary blocked" blocked-timeslot="<?php echo $blockedslot; ?>" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else if(in_array([$ts, "Reserved"], $res)){ ?>
                                <button class="btn btn-warning reserved" volunteerName="<?php echo $name; ?>" status="Reserved" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else if(in_array([$ts, "Approved"], $res)){ ?>
                                <button class="btn btn-info approved" volunteerName="<?php echo $name; ?>" status="Approved" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else if(in_array([$ts, "Away"], $res)){ ?>
                                <button class="btn btn-danger away" volunteerName="<?php echo $name; ?>" status="Away" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else if(in_array([$ts, "Returned"], $res)){ ?>
                                <button class="btn btn-success returned" volunteerName="<?php echo $name; ?>" status="Returned" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else { ?>
                                <button class="btn btn-default" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php }?>
                        </div>
                    </div>
            <?php } ?>
        </div>
        <a href="reservations.php" class="btn btn-primary pull-right"> Back </a>
    </div><br>

    <div class="container" style:="min-height:25; height=100%; ">
        <h2>Block reservation option:</h2>
        <?php echo isset($aval_problem) ? $aval_problem : '' ?>
        <form method="post">
            <input hidden readonly type="text" id="date" name="date" value="<?php echo $date ?>">
            <input hidden readonly type="text" id="animal_id" name="animal_id" value="<?php echo $animal_id ?>">
            <label>From</label>
            <select name="from" id="from">From
                <?php foreach($timeslots as $ts){ ?>
                    <option value="<?php echo explode(' - ', $ts)[0] ?>"><?php echo explode(' - ', $ts)[0] ?></option>
                <?php }?>
            </select>
            <label>Until</label>
            <select name="until" id="until">
                <?php foreach($timeslots as $ts){ ?>
                    <option value="<?php echo explode(' - ', $ts)[1] ?>"><?php echo explode(' - ', $ts)[1] ?></option>
                <?php }?>
            </select>
            <input type="submit" id="availability" name="availability" value="submit" class="btn btn-primary btn-sm"><br>
        </form>
    </div>
    
    <div id="reservedModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Manage Reservation: <span id="slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">Volunteer</label>
                                    <input required type="text" readonly name="volunteer" id="volunteer" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Status of your reservation</label>
                                    <input required type="text" readonly name="status" id="status" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' id ='approvebtn' name='approvebtn'>Approve</button>
                                    <button class="btn btn-primary" type='submit' id='cancelbtn' name='cancelbtn'>Cancel Reservation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="approvedModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Reservation: <span id="appr_slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">Volunteer</label>
                                    <input required type="text" readonly name="appr_volunteer" id="appr_volunteer" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input required type="text" readonly name="appr_timeslot" id="appr_timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Status of your reservation</label>
                                    <input required type="text" readonly name="appr_status" id="appr_status" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' id='kidnapped' name='kidnapped'>Picked up</button>
                                    <button class="btn btn-primary" type='submit' id='cancelapprovedbtn' name='cancelapprovedbtn'>Cancel Reservation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="blockModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Blocked Window: <span id="blck_slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">Blocked time</label>
                                    <input required type="text" readonly name="blck_timeslot" id="blck_timeslot" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' id='cancelblckbtn' name='cancelblckbtn'>Cancel Block</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="awayModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Blocked Window: <span id="away_slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                            <div class="form-group">
                                    <label for="">Volunteer</label>
                                    <input required type="text" readonly name="away_volunteer" id="away_volunteer" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input required type="text" readonly name="away_timeslot" id="away_timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Status of your reservation</label>
                                    <input required type="text" readonly name="away_status" id="away_status" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' id='returned' name='returned'>Animal Returned</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="returnedModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Blocked Window: <span id="ret_slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                            <div class="form-group">
                                    <label for="">Volunteer</label>
                                    <input required type="text" readonly name="ret_volunteer" id="ret_volunteer" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input required type="text" readonly name="ret_timeslot" id="ret_timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Status of your reservation</label>
                                    <input required type="text" readonly name="ret_status" id="ret_status" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(".reserved").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        var status = $(this).attr('status');
        var name = $(this).attr('volunteerName');
        $("#slot").html(timeslot);
        $("#timeslot").val(timeslot);
        $("#volunteer").val(name);
        $("#status").val(status);
        $("#reservedModal").modal("show");
    })

    $(".approved").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        var status = $(this).attr('status');
        var name = $(this).attr('volunteerName');
        $("#appr_slot").html(timeslot);
        $("#appr_timeslot").val(timeslot);
        $("#appr_volunteer").val(name);
        $("#appr_status").val(status);
        $("#approvedModal").modal("show");
    })

    $(".blocked").click(function(){
        var timeslot = $(this).attr('blocked-timeslot');
        $("#blck_slot").html(timeslot);
        $("#blck_timeslot").val(timeslot);
        $("#blockModal").modal("show");
    })

    $(".away").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        var status = $(this).attr('status');
        var name = $(this).attr('volunteerName');
        $("#away_slot").html(timeslot);
        $("#away_timeslot").val(timeslot);
        $("#away_volunteer").val(name);
        $("#away_status").val(status);
        $("#awayModal").modal("show");
    })

    $(".returned").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        var status = $(this).attr('status');
        var name = $(this).attr('volunteerName');
        $("#ret_slot").html(timeslot);
        $("#ret_timeslot").val(timeslot);
        $("#ret_volunteer").val(name);
        $("#ret_status").val(status);
        $("#returnedModal").modal("show");
    })

</script>
</body>
</html>