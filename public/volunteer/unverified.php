<?php 
require_once "../session_expiration.php";
require_once "volunteer_auth.php";

?>

<html>
    <head>
        <title>Animal Schedule</title>
        <link rel="stylesheet" href="../styles.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <header style="padding: 10px 0px 0px 0px">
        <div class="logo"><img onclick="window.location.href='../index.php'" src='../images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a href="welcome.php">Animals</a>
            <a class="active" href="unverified.php">Reservation System</a>
            <a href="history.php">History of reservations</a>
            <a href="../logout.php">Logout</a>
        </div>
    </header>
    <body>
    <h1 class="my-5" style="padding-left:15%; padding-right:15%;" >Hi, <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>. We are sorry, but reservation system is available only for verified users. &#128546;</h1>
    <br>
    <h3 style="padding-left:15%; padding-right:15%;">Please be patient.</h3>
    <h3 style="padding-left:15%; padding-right:15%;"> Our employees will contact you ASAP. </h3>
    </body>
</html>