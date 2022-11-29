<?php 
    session_start();
    require_once "../config.php";

    function build_calendar($month, $year, $animal){

        global $link;

        $daysOfWeek = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
        $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
        $numberOfDays = date("t", $firstDayOfMonth);
        $dateComponents = getdate($firstDayOfMonth);
        $monthName = $dateComponents['month'];
        $dayOfWeek = $dateComponents['wday'];
        if($dayOfWeek == 0){
            $dayOfWeek = 6;
        }
        else{
            $dayOfWeek = $dayOfWeek - 1;
        }

        $calendar ="<table class='table table-bordered'>";
        $calendar.= "<center><h2>$monthName $year</h2>";
        $calendar.="<button class='changemonth btn btn-primary btn-xs' data-month='".date('m', mktime(0,0,0, $month-1, 1, $year))."'data-year='".date('Y', mktime(0,0,0, $month-1, 1, $year))."'>Prev Month</button> ";
        $calendar.="<button class='changemonth btn btn-primary btn-xs' id='current_month' data-month='".date('m')."'data-year='".date('Y')."'>Current Month</button> ";
        $calendar.="<button class='changemonth btn btn-primary btn-xs' data-month='".date('m', mktime(0,0,0, $month+1, 1, $year))."'data-year='".date('Y', mktime(0,0,0, $month+1, 1, $year))."'>Next Month</button></center><br>";
        
        $sql = "SELECT ID, Type, Name, Age FROM Animal";
        $stmt = mysqli_prepare($link, $sql);

        $dogs = "";
        $cats = "";
        $others = "";

        $first_animal = 0;
        $i = 0;

        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $ID, $Type, $Name, $Year);

            if(mysqli_stmt_num_rows($stmt) > 0){
                while(mysqli_stmt_fetch($stmt)){

                    if($i == 0){
                        $first_animal = $ID;

                        if($animal != 0){
                            $first_animal = $animal;
                        }
                    }

                    $Age = date('Y') - $Year;

                    $selected = $first_animal == $ID ? 'selected' : '';

                    if($Type == "Dog"){
                        $dogs.= "<option $selected value='".$ID."'>".$Name." (".$Age.")</option>";
                    } else if ($Type == "Cat"){
                        $cats.= "<option $selected value='".$ID."'>".$Name." (".$Age.")</option>";
                    } else {
                        $others.= "<option $selected value='".$ID."'>".$Name." (".$Age.")</option>";
                    }

                    $i++;
                }
            }
            mysqli_stmt_close($stmt);
        }

        $calendar.="<label>Select Animal</label><select class='form-control' id='animal_select' name='animal'>
            <optgroup label='Dogs'>".$dogs."</optgroup>
            <optgroup label='Cats'>".$cats."</optgroup>
            <optgroup label='Others'>".$others."</optgroup>
        </select><br>";

        $calendar.="<tr>";

        foreach($daysOfWeek as $day){
            $calendar.="<th class='header'>$day</th>";
        }
        
        $calendar.="</tr><tr>";

        if($dayOfWeek > 0){
            for($i = 0; $i < $dayOfWeek; $i++){
                $calendar.="<td class='empty'></td>";
            }
        }

        //Initialising day counter
        $currentDay = 1;
        $month = str_pad($month, 2, "0", STR_PAD_LEFT);

        while($currentDay <= $numberOfDays){
            
            if($dayOfWeek == 7){
                $dayOfWeek = 0;
                $calendar.="</tr><tr>";
            }

            $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
            $date = "$year-$month-$currentDayRel";
            $today = $date == date('Y-m-d') ? 'today' : '';

            $maxDayReservations = maxSlots(24, 30, $first_animal, $date);

            if($date<date('Y-m-d')){
                $calendar.="<td class='$today'><h4>$currentDayRel</h4><a class='btn btn-danger btn-xs disabled'>N/A</a></td>";
            } else {
                $totalreservations = checkSlots($date, $first_animal);
                if($totalreservations == $maxDayReservations){
                    $calendar.="<td class='$today'><h4>$currentDayRel</h4><a href='#' class='btn btn-danger btn-xs'>All Booked</a></td>";
                }else {
                    $availableslots = $maxDayReservations - $totalreservations;
                    $calendar.="<td class='$today'><h4>$currentDayRel</h4><a href='book.php?date=".$date."&animal_id=".$first_animal."' class='btn btn-success btn-xs'>Book</a><small><i>$availableslots slots available</i></small></td>";
                }
            }
    
            $currentDay++;
            $dayOfWeek++;
        }

        if($dayOfWeek != 7){
            $remainingDays = 7-$dayOfWeek;
            for($j = 0; $j < $remainingDays; $j++){
                $calendar.="<td class='empty'></td>";
            }
        }
        $calendar.="</tr></table>";

        echo $calendar;
    }

    function maxSlots($max, $duration, $animal, $date){
        global $link;
        $maxDayReservations = $max;
        $start = new DateTime("7:00");
        $end = new DateTime("19:00");
        $interval = new DateInterval("PT".$duration."M");

        $sql = "SELECT TimeslotStart, TimeslotEnd FROM Availability WHERE AnimalID = ? AND Date = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "is", $animal, $date);

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $TimeslotStart, $TimeslotEnd);

            if(mysqli_stmt_num_rows($stmt) > 0){
                while(mysqli_stmt_fetch($stmt)){
                    $start = DateTime::createFromFormat('H:i', $TimeslotStart);
                    $end = DateTime::createFromFormat('H:i', $TimeslotEnd);
                    for($intStart = $start; $intStart < $end; $intStart->add($interval)){
                        $maxDayReservations--;
                    }
                }
            }
            mysqli_stmt_close($stmt);
        }

        return $maxDayReservations;
    }

    function checkSlots($date, $animal){
        global $link;

        $sql = "SELECT Timeslot FROM Reservations WHERE Date = ? AND AnimalID = ?";
        $stmt = mysqli_prepare($link, $sql);
        mysqli_stmt_bind_param($stmt, "si", $date, $animal);

        $totalreservations = 0;

        if(mysqli_stmt_execute($stmt)){

            mysqli_stmt_store_result($stmt);
            mysqli_stmt_bind_result($stmt, $Timeslot);

            if(mysqli_stmt_num_rows($stmt) > 0){
                while(mysqli_stmt_fetch($stmt)){
                    $totalreservations++;
                }
            }
            mysqli_stmt_close($stmt);
        }
        return $totalreservations;
    }

    if(isset($_POST['month']) && isset($_POST['year'])){
        $month = $_POST['month'];
        $year = $_POST['year'];
        $animal_id = $_POST['animal_id'];
    }
    else{
        $dateComponents = getdate();
        $month = $dateComponents['mon'];
        $year = $dateComponents['year'];
        $animal_id = 0;
    }

    echo build_calendar($month, $year, $animal_id);
?>
