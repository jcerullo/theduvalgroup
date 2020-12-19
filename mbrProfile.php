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
	
</head>
<body>

<?php
  function init() {
	global $mbrID;
	global $isAdmin;
	global $isMbr;
	global $isActive;
	global $password;
	global $firstName;
    global $lastName;
	global $phonePrimary;
	global $phoneSecondary;
	global $village;
	global $streetAddr;
	global $cityAddr;
	global $zipAddr;
	global $emailAddr;
	global $status;
	global $assignedTennisRanking;
	global $mrbTennisRanking;
	
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
	}
  }               // end function init
  
  init();
                                                         // if form not yet displayed 
	try {                                                // retrieve SQL record
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $result = $con->query("SELECT * FROM member WHERE mbrID = '$mbrID' ");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {                               // populate form values for form display
			if ($name == 'firstName')  $firstName = $value;
			if ($name == 'lastName')   $lastName  = $value;
			if ($name == 'phonePrimary')   $phonePrimary  = $value;
			if ($name == 'phoneSecondary')   $phoneSecondary  = $value;
			if ($name == 'village')   $village  = $value;
			if ($name == 'streetAddr')   $streetAddr  = $value;
			if ($name == 'cityAddr')   $cityAddr  = $value;
			if ($name == 'zipAddr')   $zipAddr  = $value;
			if ($name == 'emailAddr')   $emailAddr  = $value;
			if ($name == 'emailAddr')   $_SESSION["myEmailAddr"] = $value;   // set my email address
			if ($name == 'status')   $status  = $value;
			if ($name == 'password')   $password  = $value;
			if ($name == 'tennisRanking')   $tennisRanking  = $value;
			if ($name == 'displayPhoto')   $displayPhoto  = $value;
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
print "<span style='margin-left:1em'> You are logged in as <strong> $mbrID </strong> on the <strong>Member Profile </strong> page. ";
if ($mbrID == "Guest") print " As a guest, be advised that all database updates are disabled.";

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
			box-shadow: 3px 3px 3px gray;
            display: block;
            margin-left: auto;
            margin-right: auto;
            clear: both;
          }
		  
		  #submit {
			  background-color: lightyellow;
		  }
		  
  </style> 

    <form action = "mbrProfileProcess.php" method = "post" >
      <fieldset> <br>
	  	<p> <strong>Starred items are required.  Don't forget to press Submit when done.</strong> </p>
        <label>*First Name</label>
        <input type="text" value="$firstName" id="txt_firstName" name="firstName" ><br>
		<label>*Last Name</label>
        <input type="text" value="$lastName" id="txt_lastName" name="lastName" > <br>
        <label>*Email</label>
        <input type="text" value="$emailAddr" id="txt_emailAddr" name="emailAddr" > <br>
        <label>*Primary Phone Number (for member to member)<br>
        (NNN) NNN-NNNN</label>
        <input type="text" value="$phonePrimary" id="txt_phonePrimary" name="phonePrimary" > <br>
		<label> Secondary Phone Number (for emergency)</label><br>
        <input type="text" value="$phoneSecondary" id="txt_phoneSecondary" name="phoneSecondary" > <br>
		<label>Village</label>
        <input type="text" value="$village" id="txt_village" name="village" > <br>
		<label>Street Address</label>
        <input type="text" value="$streetAddr" id="txt_streetAddr" name="streetAddr" > <br>
		<label>City</label>
        <input type="text" value="$cityAddr" id="txt_cityAddr" name="cityAddr" > <br>
		<label>Zip</label>
        <input type="text" value="$zipAddr" id="txt_zipAddr" name="zipAddr" > <br>
		
		<label>*Status (I=inactive, A=active)</label>
        <input type="text" value="$status" id="txt_status" name="status" > <br>
		  
		<label>*Password</label>
        <input type="text" value="$password" id="txt_password" name="password" readonly > <br>
		
		<label>Tennis Ranking (ok to guess)</label>
		<input type="text" value="$tennisRanking" id="txt_tennisRanking" name="tennisRanking" > <br><br>
		
		<label>Authorize Display of Photo (Y=yes, N=no)</label>
        <input type="text" value="$displayPhoto" id="txt_displayPhoto" name="displayPhoto" > <br>
								
        <input type="submit" id = "submit" value = "submit"/>

      </fieldset>
    </form>

  </div>
HERE;

?>
</body>
</html> 
