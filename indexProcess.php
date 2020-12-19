<?php
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Duval</title>
  <meta charset="utf-8"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
     integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="main.css">
  <link rel = "stylesheet"
        type = "text/css"
        href = "jquery-ui-1.10.3.custom.css" />
  <style type = "text/css">
 
    h1 {
      text-align: center;
    }
    #selectable .ui-selecting {
      background-color: gray;
    }
    #selectable .ui-selected {
      background-color: black;
      color: white;
    }
	img {
	  float: left;
	}
  </style>
  <script type = "text/javascript"
          src = "jquery-1.9.1.js"></script>
  <script type = "text/javascript"
          src = "jquery-ui-1.10.3.custom.min.js"></script>
  <script type = "text/javascript">

    $(init);
    function init(){
      $("h1").addClass("ui-widget-header");
      $("#tabs").tabs();
      $("#datePicker").datepicker();
      $("#slider").slider()
      .bind("slide", reportSlider);
      $("#selectable").selectable();
      $("#sortable").sortable();
	  
      $("#dialog").dialog({
		  autoOpen : false,         		  
	  }); 
	  
    } // end init
	
	var text = " ";
	
    function reportSlider(){
      var sliderVal = $("#slider").slider("value");
      $("#slideOutput").html(sliderVal);
    } // end reportSlider
	
    function openDialog()
	{		
       $("#dialog").dialog("open");	   
    } // end openDialog
	
    function closeDialog(){
      $("#dialog").dialog("close");
    } // end closeDialog
	
	function showDialog(text)	
	{	
        HTMLdata = 	"<p>" + text + "</p>"; 
	   $("#dialog").html(HTMLdata);
       $("#dialog").dialog("open");	   
    } // end showDialog

    </script>
</head>
<body>

<?php
  function init() {
	global $mbrID;
	global $status; 
	global $firstName;
	
	global $isAdmin;
	global $isMbr;
	global $isActive;
	
	global $lowLeftPhoto;
	global $lowLeftText;
	global $lowRightPhoto;
	global $lowRightText;
	global $leaguePeriodPosition;
	global $eventPeriodPosition;
	
    if (filter_has_var(INPUT_POST, "mbrID")){
        $mbrID = filter_input(INPUT_POST, "mbrID");		
        $_SESSION["mbrID"] = $mbrID;	
	    $_SESSION["password"] = filter_input(INPUT_POST, "password");
	}
	
	$enteredPassword = $_SESSION["password"];

	if (isSet($_SESSION["mbrID"]) && $_SESSION["mbrID"] != NULL && $_SESSION["mbrID"] != "")		
	{
        $mbrID =   $_SESSION["mbrID"];
	    $isMbr =   $_SESSION["isMbr"];
	    $isAdmin = $_SESSION["isAdmin"];
		$status =  $_SESSION["status"];
	}
	else 
	{
		$mbrID = "Guest";
		$isMbr = false;
		$isAdmin = false;
		$status = "";
		
		$_SESSION["mbrID"] = "Guest";
		$_SESSION["password"] = "";
		$_SESSION["isAdmin"] = false; 
		$_SESSION["status"] = ""; 
		$_SESSION["isActive"] = false;
		$_SESSION["isMbr"] = false;
	}

	$status = "D";
                                                                          // retrieve SQL record
	try {                                                
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $result = $con->query("SELECT * FROM member WHERE mbrID = '$mbrID'");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {                              // populate values for kicks
		    if ($name == 'mbrID')  $_SESSION["isMbr"] = true;              // set isMbr
			if ($name == 'firstName')  $firstName = $value;
			if ($name == 'lastName')   $lastName  = $value;
			if ($name == 'phonePrimary')   $phonePrimary  = $value;
			if ($name == 'phoneSecondary')   $phoneSecondary  = $value;
			if ($name == 'village')   $village  = $value;
			if ($name == 'streetAddr')   $streetAddr  = $value;
			if ($name == 'cityAddr')   $cityAddr  = $value;
			if ($name == 'zipAddr')   $zipAddr  = $value;
			if ($name == 'emailAddr')   $_SESSION["myEmailAddr"] = $value;   // set my email address
			if ($name == 'status')   $status  = $value;
			if ($name == 'password')   $password  = $value;
			if ($name == 'tennisRanking')   $tennisRanking  = $value;
		  }		 		  
	    }        
		
		$result = $con->query("SELECT * FROM admin WHERE adminID = '$mbrID' ");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {            // test for isAdmin
			if ($name == 'adminID' && $value == $mbrID) {
				$isAdmin = true;
                $_SESSION["isAdmin"] = true;				
            }
			If ($name == 'masterKey')  $_SESSION["masterKey"] = $value;
		  }		 		  
	    }   		
    }		 //end of try
 	
    catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
//                                                                                   load page settings	
	try {                                                
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $result = $con->query("SELECT * FROM settings WHERE rcdNbr > 0 ");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {                         
		    if ($name == 'lowLeftPhoto')  $lowLeftPhoto = $value;          
			if ($name == 'lowLeftText')  $lowLeftText = $value;
			if ($name == 'lowRightPhoto')   $lowRightPhoto  = $value;
			if ($name == 'lowRightText')   $lowRightText  = $value;
		  }		 		  
	    }
		
		if ($lowRightPhoto != "") {
        $lowRightPhoto = trim($lowRightPhoto);                                          // copy event image
		$fileConnected = fopen("$lowRightPhoto.jpg", "r");
			if ($fileConnected == true) {				
				$shellCmd = "sudo " . "cp " . "$lowRightPhoto.jpg " . "event.jpg" ;
				shell_exec("$shellCmd");
				fclose($fileConnected);
			}
		}
		
		if ($lowLeftPhoto != "") {
        $lowLeftPhoto = trim($lowLeftPhoto);                                          // copy league image
		$fileConnected = fopen("$lowLeftPhoto.jpg", "r");
			if ($fileConnected == true) {								
				$shellCmd = "sudo " ."cp " . "$lowLeftPhoto.jpg " . "league.jpg" ;
				shell_exec("$shellCmd");
				fclose($fileConnected);
			}
		}
		
        $leaguePeriodPosition = (int) stripos( $lowLeftText, "." );
        $eventPeriodPosition = (int) stripos( $lowRightText, "." );		
//                                                                                   set isAdmin		
		$result = $con->query("SELECT * FROM admin WHERE adminID = '$mbrID' ");
		$result->setFetchMode(PDO::FETCH_ASSOC);
		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {            // test for isAdmin
			if ($name == 'adminID' && $value == $mbrID) {
				$isAdmin = true;
                $_SESSION["isAdmin"] = true;				
            }
			If ($name == 'masterKey')  $_SESSION["masterKey"] = $value;
		  }		 		  
	    }   		
    }		 //end of try
 	
    catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
	

	if ($status == "A") 
	{
		$_SESSION["status"] = $status;
		$_SESSION["isActive"] = true;		
	}
	else
	{
		$_SESSION["status"] = $status;
		$_SESSION["isActive"] = false;
	}
/*	                                // temporary during orientation period - security off
	if ($password != $enteredPassword && $enteredPassword != $_SESSION["masterKey"]) {
	  $_SESSION["mbrID"] = "Guest";
	  $_SESSION["isAdmin"] = false; 
   	  $_SESSION["status"] = ""; 
	  $_SESSION["isActive"] = false;
	  $_SESSION["isMbr"] = false;
	  $mbrID =   "Guest";
	  $isMbr =   false;
	  $isAdmin = false;
	  $isActive = false;
	  $status = "";
	}
*/	
  }    //end init function
  
  init();
  
  $_SESSION["isAdmin"] = true;    // temporary during orientation period - admin rights on
?>

<header class="container">
<div id="wrapper">

<nav id="nav">
	<ul id="navigation">
		<li><a href="index.html">Login</a></li>
		<li><a href="#">Member Info &raquo;</a>
			<ul>
				<li><a href="members.php">MemberRoster</a></li>
				<li><a href="mbrProfile.php">MemberProfile</a></li>
				<li><a href="availability.php">MemberAvailability</a></li>
				<li><a href="groupAvailability.html">GroupAvailability</a></li>
				<li><a href="mbrRanking.php">Tennis Rankings</a></li>
				<li><a href="sumResults.html">MemberStats</a></li>				
			</ul>
		</li>
		<li><a href="#">Court Info &raquo;</a>
			<ul>
				<li><a href="facilities.php">Facilities</a></li>
				<li><a href="facility.php">CourtAvailability</a></li>
				<li><a href="assignments.html">CourtAssignments</a></li>				
			</ul>				
		</li>
		<li><a href="#">Play Results &raquo;</a>
			<ul>
				<li><a href="setResults.html">ResultsEntry</a></li>
				<li><a href="sumResults.html">ResultsSummary</a></li>				
			</ul>				
		</li>
		<li><a href="changeMonth.php">Change Month</a></li>
		<li><a href="sendEmail.php">Format Email</a></li>
		<li><a href="#">Help &raquo;</a>
			<ul>
				<li><a href="settings.php">Settings</a></li>
				<li><a href="help.php">User Guide</a></li>				
			</ul>				
		</li>
	</ul>
</nav>

</div><!--end wrapper-->
</header>

<?php
switch ($status) {
case "A":	
print <<<HERE
<section class="jumbotron">
  <div class="container">
    <div class="row text-center">

      <h2>The Duval Group</h2><br>
      <h3>Moderated by James Lush and Dick Madler</h3><br>
	
      <form action="availability.php" method= "post">
	  <h4><p> logged in as</p></h4>
      <h4><p> $mbrID </p></h4>										       
	  <h4><input class="btn btn-primary" type="submit" value = "Continue" autofocus/></h4>
	</form>
	
    </div>
  </div>
</section>
HERE;
break;

case "I": 
print <<<HERE
<section class="jumbotron">
  <div class="container">
    <div class="row text-center">

      <h2>The Duval Group</h2><br>
      <h3>Moderated by James Lush and Dick Madler</h3><br>
	
      <form action="mbrProfile.php" method= "post">
      <h4><p> logged in as</p></h4>
      <h4><p> $mbrID </p></h4>		
	  <h4><input class="btn btn-primary" type="submit" autofocus value = "Continue"/></h4>
	</form>
	
    </div>
  </div>
</section>
HERE;
break;

default:
print <<<HERE
<section class="jumbotron">
  <div class="container">
    <div class="row text-center">

      <h2>The Duval Group</h2><br>
      <h3>Moderated by James Lush and Dick Madler</h3><br>
	
      <form action="indexProcess.php" method= "post">
      <h4><p> logged in as</p></h4>
      <h4><p> $mbrID </p></h4>		
	  <h4><input class="btn btn-primary" type="submit" value = "Continue"/></h4>
	</form>
	
    </div>
  </div>
</section>
HERE;
}
?>

<?php

if ($leaguePeriodPosition != 0 ) {
	$leagueButtonText = "New Tennis League Info is available. Press Here";
print <<<HERE
    <style type = "text/css">            
        .league  {
		  background-color: lightyellow;
		} 		  
    </style>
HERE;
}
else {
	$leagueButtonText = "Tennis League";
}

if ($eventPeriodPosition != 0 ) {
	$eventButtonText = "New Special Event Info is available. Press Here";
print <<<HERE
    <style type = "text/css">            
        .event  {
		  background-color: lightyellow;
		} 		  
    </style>
HERE;
}
else {
	$eventButtonText = "Special Events";
}

$showLeagueText = '"' . $lowLeftText . '"';
$showEventsText = '"' . $lowRightText . '"';
 
print <<<HERE
  
<section class='container'>
 <div class='row'>
   <figure class='col-sm-6'>
    <input type = 'button' class='league'
			  value = '$leagueButtonText'
			  onclick = 'showDialog($showLeagueText)' /><br><br>
    <img src='league.jpg' alt= 'court photo'/>
    </figure>
   
    <figure class='col-sm-6'>
	 <input type = 'button' class='event'
              value = '$eventButtonText'
              onclick = 'showDialog($showEventsText)' /><br><br>
     <img src = 'event.jpg' alt= 'restaurant photo'/>
    </figure>  
 </div>
</section>
HERE;
?>

  <footer class="container">
  <div class="row">
    <p class="col-sm-4">&copy; 2017 Site design by jcerullo@yahoo.com</p>
    <ul class="col-sm-8">
      
      <li class="col-sm-1">
       <img src="https://s3.amazonaws.com/codecademy-content/projects/make-a-website/lesson-4/twitter.svg">
      </li>
      
     <li class="col-sm-1"> 
       <img src="https://s3.amazonaws.com/codecademy-content/projects/make-a-website/lesson-4/facebook.svg">
      </li>
      
     <li class="col-sm-1"> 
       <img src="https://s3.amazonaws.com/codecademy-content/projects/make-a-website/lesson-4/instagram.svg">
      </li>
      
     <li class="col-sm-1"> 
       <img src="https://s3.amazonaws.com/codecademy-content/projects/make-a-website/lesson-4/medium.svg">
      </li>
      
    </ul>
  </div>
  </footer>
  
<div id = "dialog"
    title = "The Duval Group" >
</div>

</body>
</html>
