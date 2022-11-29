<!DOCTYPE html>
<html>
<head>
<title>M.E.O.W.</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="styles.css">
<link rel="stylesheet" href="styles2.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
body,h1,h2,h3,h4,h5,h6 {font-family: "Lato", sans-serif;}
body, html {
  height: 100%;
  color: #777;
  line-height: 1.8;
}

/* Create a Parallax Effect */
.bgimg-1, .bgimg-2 {
  background-attachment: fixed;
  background-position: center;
  background-repeat: no-repeat;
  background-size: cover;
}

/* First image (Logo. Full height) */
.bgimg-1 {
  background-image: url('images/kockaapes.jpg');
  min-height: 100%;
}

/* Third image (Contact) */
.bgimg-2 {
  background-image: url('images/kockaapes.jpg');
  min-height: 400px;
}

.w3-wide {letter-spacing: 10px;}
.w3-hover-opacity {cursor: pointer;}

/* Turn off parallax scrolling for tablets and phones */
@media only screen and (max-device-width: 1600px) {
  .bgimg-1, .bgimg-2 {
    background-attachment: scroll;
    min-height: 400px;
  }
}
</style>
</head>
<body>

<header class="sticky" style="padding: 10px 0px 0px 0px; z-index: 9999;">
    <div class="logo"><img onclick="window.location.href='index.php'" src='images/icon.png' style="height:50px; margin:0;"> M.E.O.W.</div>
        <div class="header-right">
            <a class="active" href="index.php">Home</a>
            <a href="animals.php">Animals</a>
            <a href="login.php">Log in</a>
            <a href="register.php">Register</a>
        </div>
</header>

<!-- First Parallax Image with Logo Text -->
<div class="bgimg-1 w3-display-container w3-opacity-min" id="home">
  <div class="w3-display-middle" style="white-space:nowrap;">
    <span class="w3-center w3-padding-large w3-black w3-xlarge w3-wide w3-animate-opacity">MIKE ECHO <span class="w3-hide-small">OSCAR WHISKEY</span> SHELTER </span>
  </div>
</div>

<!-- Container (About Section) -->
<div class="w3-content w3-container w3-padding-64" id="about">
  <h3 class="w3-center">ABOUT US</h3>
  <p class="w3-center"><em>We love animals!</em></p>
  <p>At our M.E.O.W. animal shelter, we believe it is our moral responsibility to protect and improve the lives of abused, abandoned, and homeless cats, dogs and other animals, and to place them in loving permanent homes.
    We provide spay and neuter services for M.E.O.W. feral cats and any necessary medical care for pets. We work diligently to educate the public, especially children, on the importance of pet sterilization, responsible pet
    ownership and the humane treatment of animals.

    If you are looking to adopt a pet, please view the animals in our M.E.O.W. adoption shelter. If you want to help us taking care about our pets, create account on our pages or give us a call.</p>
  <div class="w3-row">

<!-- Second Parallax Image with Portfolio Text -->
<div class="bgimg-2 w3-display-container w3-opacity-min">
  <div class="w3-display-middle">
     <span class="w3-xxlarge w3-text-white w3-wide">CONTACT</span>
  </div>
</div>

<!-- Container (Contact Section) -->
<div class="w3-content w3-container w3-padding-64" id="contact">
  <h3 class="w3-center">WHERE WE WORK</h3>
  <p class="w3-center"><em>Don't hesitate to contact us!</em></p>

  <div class="w3-row w3-padding-32 w3-section">
    <div class="w3-col m4 w3-container">
      <img src="images/icon.png" class="w3-image w3-round" style="width:100%">
    </div>
    <div class="w3-col m8 w3-panel">
      <div class="w3-large w3-margin-bottom">
        <i class="fa fa-map-marker fa-fw w3-hover-text-black w3-xlarge w3-margin-right"></i> Brno, Czech Republic<br>
        <i class="fa fa-phone fa-fw w3-hover-text-black w3-xlarge w3-margin-right"></i> Phone: +420 740 896 301<br>
        <i class="fa fa-envelope fa-fw w3-hover-text-black w3-xlarge w3-margin-right"></i> Email: info@meow.com<br>
      </div>
    </div>
  </div>
</div>

<!-- Footer -->
<footer class="w3-center w3-black w3-padding-64 w3-opacity w3-hover-opacity-off">
  <a href="#home" class="w3-button w3-light-grey"><i class="fa fa-arrow-up w3-margin-right"></i>To the top</a>
  <div class="w3-xlarge w3-section">
    <a class="fa fa-facebook-official w3-hover-opacity" href="https://www.youtube.com/watch?v=ZZ5LpwO-An4"></a>
    <i class="fa fa-instagram w3-hover-opacity"></i>
    <i class="fa fa-snapchat w3-hover-opacity"></i>
    <i class="fa fa-pinterest-p w3-hover-opacity"></i>
    <i class="fa fa-twitter w3-hover-opacity"></i>
    <i class="fa fa-linkedin w3-hover-opacity"></i>
  </div>
</footer>
</body>
</html>
