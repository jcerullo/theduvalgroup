<?php
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Duval</title>
  <meta charset="utf-8"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="main.css">
  
  <link rel = "stylesheet"
        type = "text/css" href="jquery-ui-1.10.3.custom.css" />
 	
</head>
<body>

<?php
  function init() {
	global $mbrID;
	global $isAdmin;
	global $isMbr;
	global $status;
	global $facility;
	global $playDate;
	
	$providedBalls = "N";	  
    $setsWon = 0;	  
    $setsPlayed = 0;
	
	if ($_SESSION["facility"] == "") $_SESSION["facility"] = "No Facility";

	$playDate = $_SESSION["playDate"];
	$facility = $_SESSION["facility"];
	
	if (isSet (  $_SESSION["mbrID"])) 
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
	}
  }    //end function init


  init();
  
  // retrieve form fields
  if (filter_has_var(INPUT_POST, "playDate") && $mbrID != "Guest"){
        $playDate = filter_input(INPUT_POST, "playDate");
		$_SESSION["playDate"] = $playDate;
        $_SESSION["facility"] = "No Facility";		
  }
    try {                                                // retrieve SQL record
		$con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $result = $con->query("SELECT * FROM assignment WHERE mbrID = '$mbrID' AND date = '$playDate' ");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {            // populate form values for form display
		    if ($name == 'facilityName') {
				$facility = $value;
				$_SESSION["facility"] = $facility;
			}
			if ($name == 'balls')  $providedBalls = $value;
			if ($name == 'setsWon')   $setsWon  = $value;
			if ($name == 'setsPlayed')   $setsPlayed  = $value;
		  }
		 		  
	    }        
		
    }		 //end of try
 	
    catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
	
?>  

<header class="container">
<div id="wrapper">

<nav id="nav">
	<ul id="navigation">
		<li><a href="indexProcess.php">Login</a></li>
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
		<li><a href="help.php">Help</a></li><br>
	</ul>
</nav>

</div><!--end wrapper-->
</header>

<?php
            $timestamp = strtotime($playDate);
            $dayName = 	date("l",$timestamp);	    

print "<h1>You played at <strong> $facility </strong> on <strong>$dayName, $playDate</strong> </h1>";
print <<<HERE
  <div class = "member"> 
  <style type = "text/css">
            fieldset {
            width: 600px;
            background-color: #eeeeee;
            margin-left: auto;
            margin-right: auto;
            box-shadow: 5px 5px 5px gray;
          }
          label {
            float: left;
            clear: left;
            width: 250px;
            text-align: right;
            padding-right: 1em;
          }
                   
          input {
            float: left;
          }
		  
		  select {
			  float:left;
		  }
          
          :required {
            border: 1px solid red;
          }
          
          :invalid {
            color: white;
            background-color: red;
          }
          
          button {
            display: block;
            margin-left: auto;
            margin-right: auto;
            clear: both;
          }
		  
		  #submit {
		    background-color: lightyellow;
		  }
  </style>
  
  
    <form action = "setResultsProcess.php" method= "post" >
      <fieldset> <br>
		<p>  </p>
		
        <label>Provided Balls? (Y=yes, N=no) </label>
          <input type="text" value="$providedBalls" id="txt_balls" name="providedBalls" ><br>

		<label>Sets Won (1-3)</label>
          <input type="number" value="$setsWon" id="txt_setsWon" name="setsWon" ><br>
		  
		<label>Sets Played (1-3)</label>
          <input type="number" value="$setsPlayed" id="txt_setsPlayed" name="setsPlayed" ><br>

		<input type="submit" value = "submit" id = "submit"/>

      </fieldset>
    </form>
  </div>
HERE;

?>
</body>
</html> 
