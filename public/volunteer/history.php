<?php
require_once "../session_expiration.php";
require_once "volunteer_auth.php";
require_once "../config.php";
$UserID = $_SESSION['id'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>History of reservations</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../styles.css">
    <link rel="stylesheet" href="../styles2.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <style>
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<header>
    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img  onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a href="welcome.php">Animals</a>
            <a href="../calendar/calendar.php">Reservation System</a>
            <a class="active" href="history.php">History of reservations</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
</header>
<body>
    <h2>Your history of reservations</h2>
    <div >
            <div class="filters" style="display: inline;">
                
                <label>Filter by date:</label>
                <input type="text" class="filterDate" id="filterDate"/>
            </div>
        <div>
        <div class="container history_listing"> <!-- content from fetch -->
            <table class="table"></table>
            <div class="pages"></div>
        </div>
    
    <script type="text/javascript">
    $(document).ready(function(){

        function updateTable(curPage) {
            var dateValue = $("#filterDate").val();
            var id = "<?php echo $UserID ?>";
            
            $.ajax({
                url:"find_history.php",
                type:"POST",
                data: {date: dateValue, UserID: id, page: curPage},
                
                success:function(data){ 
                    $(".history_listing").html(data);
                }
            });
        }

        var page = 1;
        updateTable(page);

        $(document).on('input', '.filters input', function(){
            page = 1;
            updateTable(page);
        });

        $(document).on('click', '.paginationLink', function(){  
            page = $(this).attr("id");  
            updateTable(page);  
        }); 
    });
</script>
</body>
</html>