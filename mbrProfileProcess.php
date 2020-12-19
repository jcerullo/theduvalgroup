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
 
		
  <script type = "text/javascript"
          src = "jquery-1.9.1.js"></script>
  <script type = "text/javascript"
          src = "jquery-ui-1.10.3.custom.min.js"></script>

  
  <script type = "text/javascript">
  

    $(init);
	
    function init(){
      $("h1").addClass("ui-widget-header");
	  $("#tabs").tabs();
      $('#datePicker').datepicker();
      $("#slider").slider()
      .bind("slide", reportSlider);
      $("#selectable").selectable();
      $("#sortable").sortable();
      $("#dialog").dialog();
      //initially close dialog
      $("#dialog").dialog("close");
    } // end init
  
  </script>
	
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
	global $tennisRanking;
	
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
                                                         // form displayed
                                                         // retrieve form fields
	if ($mbrID != "Guest") {
        $firstName = filter_input(INPUT_POST, "firstName");	  
        $lastName = filter_input(INPUT_POST, "lastName");	  
        $phonePrimary = filter_input(INPUT_POST, "phonePrimary");	  
        $phoneSecondary = filter_input(INPUT_POST, "phoneSecondary");	  
        $village = filter_input(INPUT_POST, "village");	  
        $streetAddr = filter_input(INPUT_POST, "streetAddr");	  
        $cityAddr = filter_input(INPUT_POST, "cityAddr");	  
        $zipAddr = filter_input(INPUT_POST, "zipAddr");	  
        $emailAddr = filter_input(INPUT_POST, "emailAddr");	  
        $status = filter_input(INPUT_POST, "status");	  
        $password = filter_input(INPUT_POST, "password");	  
        $tennisRanking = filter_input(INPUT_POST, "tennisRanking");
		$displayPhoto = filter_input(INPUT_POST, "displayPhoto");
		$assignedRanking = filter_input(INPUT_POST, "assignedRanking");

		if ($status == "a") $status = "A";
		if ($status == "active") $status = "A";
		if ($status == "Active") $status = "A";
		
		if ($status == "i") $status = "I";
		if ($status == "inactive") $status = "I";
		if ($status == "Inactive") $status = "I";
		
		if ($status == "d") $status = "D";
		if ($status == "deleted") $status = "D";
		if ($status == "Deleted") $status = "D";
		
		if ($displayPhoto == "y") $displayPhoto = "Y";
		if ($displayPhoto == "yes") $displayPhoto = "Y";
		if ($displayPhoto == "Yes") $displayPhoto = "Y";
		
		if ($assignedRanking == "" && $tennisRanking != "") $assignedRanking = $tennisRanking;

        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
                                                           // then update SQL record
			
	        $result = $con->prepare("UPDATE member
		              SET firstName = '$firstName',
					      lastName = '$lastName',
					      phonePrimary = '$phonePrimary',
					      phoneSecondary = '$phoneSecondary',
					      village = '$village',
					      streetAddr = '$streetAddr',
					      cityAddr = '$cityAddr',
					      zipAddr = '$zipAddr',
					      emailAddr = '$emailAddr',
					      status = '$status',
					      password = '$password',
					      tennisRanking = '$tennisRanking',
						  assignedRanking = '$assignedRanking',
						  displayPhoto = '$displayPhoto'
					  WHERE mbrID = '$mbrID' ");
			$result->execute();
			$_SESSION["myEmailAddr"] = $emailAddr;
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
 
	try {                                                // retrieve SQL record
		
        $result = $con->query("SELECT * FROM member WHERE mbrID = '$mbrID' ");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {            // populate form values for form display
			if ($name == 'firstName')  $firstName = $value;
			if ($name == 'lastName')   $lastName  = $value;
			if ($name == 'phonePrimary')   $phonePrimary  = $value;
			if ($name == 'phoneSecondary')   $phoneSecondary  = $value;
			if ($name == 'village')   $village  = $value;
			if ($name == 'streetAddr')   $streetAddr  = $value;
			if ($name == 'cityAddr')   $cityAddr  = $value;
			if ($name == 'zipAddr')   $zipAddr  = $value;
			if ($name == 'emailAddr')   $emailAddr  = $value;
			if ($name == 'status')   $status  = $value;
			if ($name == 'password')   $password  = $value;
			if ($name == 'tennisRanking')   $tennisRanking  = $value;
			if ($name == 'displayPhoto')   $displayPhoto  = $value;
			if ($name == 'assignedRanking' ) $assignedRanking = $value; 
		  }
		 		  
	    }        
		
    }		 //end of try
 	
    catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    } 
	
	}  // end of if (Guest)
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
print "<p>...... You are logged in as <strong> $mbrID </strong> on the <strong>Member Profile </strong>  page</p>";
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
  
  
    <form action = "mbrProfileProcess.php" method= "post" >
      <fieldset> <br>
	  <p>Starred items are required.  Don't forget to press Submit when done. </p>
        <label>*First Name</label>
        <input type="text" value="$firstName" id="txt_firstName" name="firstName" ><br>
		<label>*Last Name</label>
        <input type="text" value="$lastName" id="txt_lastName" name="lastName" > <br>
        <label>*Email</label>
        <input type="text" value="$emailAddr" id="txt_emailAddr" name="emailAddr" > <br>
        <label>*Primary Phone Number (for tennis)<br>
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
        		
		<label>Authorize Display of Photo</label>
        <input type="text" value="$displayPhoto" id="txt_displayPhoto" name="displayPhoto" > <br>
		
		<input type="text" value="$assignedRanking" id="txt_assignedRanking" name="assignedRanking" hidden> 

		<input type="submit" value = "submit" id = "submit"/>

      </fieldset>
    </form>
  </div>
HERE;

?>
</body>
</html> 
