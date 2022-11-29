<?php
require_once "../session_expiration.php";
require_once "../volunteer/volunteer_auth.php";

if(isset($_SESSION['verified']) && $_SESSION['verified'] == 0){
    header("location: ../volunteer/unverified.php");
}
?>

<html>
<head>
    <title>Animals Schedule</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <style>
        @media only screen and (max-width: 760px),
        (min-device-width:802px) and (max-device-width:1020px){
            table,
            thead,
            tbody,
            th,
            td,
            tr {
                display: block;
            }

            .empty {
                display: none;
            }

            th {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            tr {
                border: 1px solid #ccc;
            }

            td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }

            td:nth-of-type(1):before{
                content: "Monday";
            }

            td:nth-of-type(2):before{
                content: "Tuesday";
            }

            td:nth-of-type(3):before{
                content: "Wednesday";
            }

            td:nth-of-type(4):before{
                content: "Thursday";
            }

            td:nth-of-type(5):before{
                content: "Friday";
            }

            td:nth-of-type(6):before{
                content: "Saturday";
            }

            td:nth-of-type(7):before{
                content: "Sunday";
            }
        }

        @media (min-width: 641px){
            table {
                table-layout: fixed;
            }

            td{
                width: 33%;
            }
        }

        .row {
            margin-top: 20px;
        }
        
        .today{
            background: gold;
        }

        btn-success{
            background: greenyellow;
        }
    </style>
</head>
<header style="padding: 10px 0px 0px 0px">
    <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
    <div class="header-right">
        <a href="../volunteer/welcome.php">Animals</a>
        <a class="active" href="calendar.php">Reservation System</a>
        <a href="../volunteer/history.php">History of reservations</a>
        <a href="../logout.php">Logout</a>
    </div>
</header>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.js"></script>
    <script>
        $.ajax({
            url: "calendarbuilder.php",
            type: "POST",
            data: {'month': '<?php echo date('m'); ?>', 'year':'<?php  echo date('Y'); ?>', 'animal_id': 0},
            success: function(data){
                $("#calendar").html(data);
            }
        });
        
        $(document).on('click', '.changemonth', function(){
            var animal_id = $("#animal_select").val();
            $.ajax({
                url: "calendarbuilder.php",
                type: "POST",
                data: {'month': $(this).data('month'), 'year' : $(this).data('year'), 'animal_id': animal_id},
                success: function(data){
                    $("#calendar").html(data);
                }   
             });

        });

        $(document).on('change', '#animal_select', function(){
            var animal_id = $(this).val();
            $.ajax({
                url: "calendarbuilder.php",
                type: "POST",
                data: {'month': $('#current_month').data('month'), 'year' : $('#current_month').data('year'), 'animal_id': animal_id},
                success: function(data){
                    $("#calendar").html(data);
                }   
             });

        });
        
    </script>
</body>
</html>