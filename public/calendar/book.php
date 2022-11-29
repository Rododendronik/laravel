<?php
    require_once "../config.php";
    require_once "../session_expiration.php";
    require_once "../volunteer/volunteer_auth.php";
    
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

        $sql = "SELECT Timeslot, Status, VolunteerID FROM Reservations WHERE Date = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "si", $date, $animal_id);
        $reservations = array();

        $res = array();

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $Timeslot, $Status, $VolunteerID);

            if(mysqli_stmt_num_rows($stmt) > 0){
                while(mysqli_stmt_fetch($stmt)){
                    $reservations[] =  $Timeslot;
                    $res[] =  [$Timeslot, $VolunteerID, $Status];
                }
            }
            mysqli_stmt_close($stmt);
        }
    }

    if(isset($_POST['submit']) && !isset($_POST['status'])){

        $volunteerID = $_SESSION['id'];
        $timeslot = $_POST['timeslot'];

        $sql = "SELECT * FROM Reservations WHERE Date = ? AND Timeslot = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $date, $timeslot);
        
        if(mysqli_stmt_execute($stmt)){
            
            mysqli_stmt_store_result($stmt);

            if(mysqli_stmt_num_rows($stmt) > 0){
                $msg = "<div class='alert alert-danger alert-dismissible'>Timeslot is Already Booked <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
                mysqli_stmt_close($stmt);
            } else {

                mysqli_stmt_close($stmt);

                $sql = "INSERT INTO Reservations (Timeslot, Date, Status, AnimalID, VolunteerID) VALUES (?,?,?,?,?)";
                $stmt = mysqli_prepare($link, $sql);
                $status = "Reserved";
                $animalID = $animal_id;
                mysqli_stmt_bind_param($stmt, "sssii", $timeslot, $date, $status, $animalID, $_SESSION["id"]);
                if(mysqli_stmt_execute($stmt)){
                    $msg = "<div class='alert alert-success alert-dismissible'>Booking Successfull <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
                    mysqli_stmt_close($stmt);
                }
                $reservations[] = $timeslot;
                $res[] = [$timeslot, $_SESSION['id'], $status];
            }
        }
    }

    if(isset($_POST['submit']) && isset($_POST['status']) && isset($_POST['unb_timeslot'])){
        $volunteerID = $_SESSION['id'];
        $timeslot = $_POST['unb_timeslot'];
        $status = $_POST['status'];

        $sql = "DELETE FROM Reservations WHERE Date = ? AND Timeslot = ? AND AnimalID = ? AND VolunteerID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "ssii", $date, $timeslot, $animal_id, $volunteerID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (($key = array_search($timeslot, $reservations)) !== false) {
            unset($reservations[$key]);
        }

        $k = 0;
        foreach ($res as &$tmp){
            if(empty(array_diff($tmp, [$timeslot, $volunteerID, $status]))){
                unset($res[$k]);
                break;
            }
            $k++;
        }
        $msg = "<div class='alert alert-warning alert-dismissible'>Sorry to hear that you canceled your reservation<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a></div>";
    }

    $duration = 30;
    $start = "07:00";
    $end = "19:00";

    function timeslots($duration, $start, $end){
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

            $insert = true;

            for($i = 0; $i < count($not_available); $i++){
                if($not_available[$i][0] <= $intStart->format("H:i") && $not_available[$i][1] >= $endPeriod->format("H:i")){
                    $insert = false;
                    break;
                }
            }     
            if($insert){     
                $slots[] = $intStart->format("H:i")." - ".$endPeriod->format("H:i");
            }
        }

        return $slots;
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Book Animal</title>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title></title>
    <style>
    .btn{ 
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
            <?php $timeslots = timeslots($duration, $start, $end);

                foreach($timeslots as $ts){
            ?>
                    <div class="col-md-2">
                        <div class="form-group">
                            <?php if(in_array([$ts, $_SESSION['id'], "Reserved"], $res)){ ?>
                                <button class="btn btn-warning unbook" status="Reserved" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else if(in_array([$ts, $_SESSION['id'], "Approved"], $res)){ ?>
                                <button class="btn btn-info unbook" status="Approved" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php } else if(in_array($ts, $reservations)){ ?>
                                <button class="btn btn-danger"><?php echo $ts; ?> </button>
                            <?php } else { ?>
                                <button class="btn btn-success book" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?> </button>
                            <?php }?>
                        </div>
                    </div>
            <?php } ?>
        </div>
        <a href="calendar.php" class="btn btn-primary"> Back </a>
    </div>
    
    <div id="bookModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Booking: <span id="slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input required type="text" readonly name="timeslot" id="timeslot" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' name='submit'>Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="unbookModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Cancel Reservation: <span id="unb_slot"> </span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <div class="form-group">
                                    <label for="">Timeslot</label>
                                    <input required type="text" readonly name="unb_timeslot" id="unb_timeslot" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="">Status of your reservation</label>
                                    <input required type="text" readonly name="status" id="status" class="form-control">
                                </div>
                                <div class="form-group pull-right">
                                    <button class="btn btn-primary" type='submit' name='submit'>Cancel Reservation</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
    $(".book").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        $("#slot").html(timeslot);
        $("#timeslot").val(timeslot);
        $("#bookModal").modal("show");
    })

    $(".unbook").click(function(){
        var timeslot = $(this).attr('data-timeslot');
        var status = $(this).attr('status');
        $("#unb_slot").html(timeslot);
        $("#unb_timeslot").val(timeslot);
        $("#status").val(status);
        $("#unbookModal").modal("show");
    })
</script>
</body>
</html>