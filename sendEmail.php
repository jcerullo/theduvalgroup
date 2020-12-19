<?php
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Duval</title>
  <meta charset="utf-8"/>
  <link rel="stylesheet" href="bootstrap.css">
  <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="main.css">
  
  <link rel = "stylesheet"
        type = "text/css" href="jquery-ui-1.10.3.custom.css" />		
		
  <link rel = "stylesheet"
        type = "text/css" href="jquery-ui.multidatespicker.css" /> 
		
  <script type = "text/javascript"
          src = "jquery-1.9.1.js"></script>
  <script type = "text/javascript"
          src = "jquery-ui-1.10.3.custom.min.js"></script>
  <script type = "text/javascript"
          src = "jquery-ui.multidatespicker.js"></script>
  
  <script type = "text/javascript">
  
    $(init);

    function init(){
      $("h1").addClass("ui-widget-header");
      $("#tabs").tabs();

      var date = new Date();                      // set minDate to the first day of the folowing month

	var daysInMonth = new Array(12);
	  daysInMonth[0] = 31;
	  daysInMonth[1] = 28;
	  daysInMonth[2] = 31;
	  daysInMonth[3] = 30;
	  daysInMonth[4] = 31;
	  daysInMonth[5] = 30;
	  daysInMonth[6] = 31;
	  daysInMonth[7] = 31;
	  daysInMonth[8] = 30;
	  daysInMonth[9] = 31;
	  daysInMonth[10] = 30;
	  daysInMonth[11] = 31;
	  currentMonth = date.getMonth();                       //0-11
	  firstDate = daysInMonth[currentMonth] +1;        //first date to display from today
      $('#datePicker').multiDatesPicker({
		           dateFormat: "yy-mm-dd"});
				   
	  $.datepicker._selectDateOverload = $.datepicker._selectDate;
	  $.datepicker._selectDate = function (id,dateStr) {
		  var target = $(id);
		  var inst = this._getInst(target[0]);
		  inst.inline = true;
		  this._selectDateOverload(id,dateStr);
		  inst.inline = false;
		  if (target[0].multiDatesPicker != null) {
			  target[0].multiDatesPicker.changed = false;
		  } else {
		  target.multiDatesPicker.changed = false;
		  }
		  this._updateDatePicker(inst);
	  };		   
	  
      $("#dialog").dialog({
		  autoOpen : false,
	   });

    } // end init    
	      
    function openDialog()
	{		
       $("#dialog").dialog("open");	   
    } // end openDialog
	
    function closeDialog(){
      $("#dialog").dialog("close");
    } // end closeDialog
	
	function okDialog(){
	  $("#okDialog").load("email.php");
      $("#dialog").dialog("close");
    } // end okDialog
	
	function showDialog()	
	{	
        HTMLdata = "On a laptop this dialog box can be repositioned if necessaary. " +  
		           "Look over the displayed email carefully. <br><br>" + 
				   "<strong>Press OK to send; else Cancel</strong>" +
				   "<br><br><button onclick='okDialog()'>OK</button>" +
				   "<span style='margin-left:2em'><button onclick='closeDialog()'>Cancel</button>" +
				   "<br>"; 
	   $("#dialog").html(HTMLdata);
       $("#dialog").dialog("open");	   
    } // end showDialog
	
  </script>
  
</head>
<body>
<?php

	if (isSet ($_SESSION["mbrID"])) 
	{
        $mbrID =   $_SESSION["mbrID"];
	    $isMbr =   $_SESSION["isMbr"];
	    $isAdmin = $_SESSION["isAdmin"];
		$status =  $_SESSION["status"];
		$isActive =  $_SESSION["isActive"];
		$myEmailAddr = $_SESSION["myEmailAddr"];
	}
	else 
	{
		$mbrID = "Guest";
		$isMbr = false;
		$isAdmin = false;
		$isActive = false;
		$status = "";
		$myEmailAddr = "";
	}

    $to = "Selected email addresses";                                    // default email to
	$subj = "Topic";                             // default email subject
	$dates = "00/00/00";                         // optional dates
	$body = ".";                                 // default email body
	$emailOption = "N";                          // default is not to send email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
	
	$monthNames = array("January", "February", "March", "April", "May", "June",
                     "July", "August", "September", "October", "November", "December");
	$month = (int) date('m');
	$monthName = $monthNames[$month];
	$ballSuppliers = array();

    if (filter_has_var(INPUT_POST, "to"))
        $to = filter_input(INPUT_POST, "to");
	
	if (filter_has_var(INPUT_POST, "emailOption"))
        $emailOption = filter_input(INPUT_POST, "emailOption");
    		
	if (filter_has_var(INPUT_POST, "subj")) 
        $subj = filter_input(INPUT_POST, "subj");
	
	if (filter_has_var(INPUT_POST, "dates"))
	{
        $dates = filter_input(INPUT_POST, "dates");
		$dateArray = explode(",", $dates);
		
	    $assignments = "";                                               // loop though the entered dates	
	    for ($n=0; $n < sizeof($dateArray); $n++)
		{
			
		    $timestamp = strtotime($dateArray[$n]);                    // get day name
            $dayName = date("l", $timestamp);

			$assignments .= "<br><p><strong> $dayName, $dateArray[$n] </strong></p>";
			
                                                                        //  load up available members into $available string
		
	   try 
	   {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003"); 
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		$available = "";
        $query = "SELECT mbrID FROM available WHERE dates = '$dateArray[$n]' ";
        
        //first pass just gets the column names

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second pass gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
			foreach ($row as $name=>$value){ 
              $available .= $value . ",";
			} // end field loop
        } // end record loop

        $available .= "";
		
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
	  
    $assigned = "";	  //                                          load up assigned members into $assigned string
      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT facilityName as 'Courts', 
		                 mbrID as 'Players',
						 balls 
						 FROM assignment 
						 WHERE date = '$dateArray[$n]'
                         ORDER BY facilityName	 ";
        
        //first pass just gets the column names
		
		$assignments .= "<table> \n";

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);
		
        $assignments .= "  <tr> \n";
        foreach ($row as $field => $value){
          if ($field == 'Courts' || $field == 'Players') $assignments .= "<th><u>$field</u><span style='margin-left:6em'></th> \n";
        } // end foreach
        $assignments .= "  </tr> \n";
 
        //second pass gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);
		$courtChanged = false;
		$i = 0;
        foreach($data as $row){	
			
          $assignments .= "  <tr> \n";
		  
          foreach ($row as $name=>$value) {	
            if ($name == 'Courts' || $name == 'Players' ) $assignments .= "    <td>$value</td> \n";
			if ($name == 'Players' ) $player = $value; 
			if ($name == 'balls' && $value == 'Y') {
				$ballSuppliers[$i] = $player;
				$i = $i +1;
			}
          } // end field loop
		  
          $assignments .= "  </tr> \n";				  			
		  
        } // end record loop

        $assignments .= "</table> \n" ;
	                                                               // Second query is just for the member IDs
																   
	    $query = "SELECT mbrID FROM assignment WHERE date = '$dateArray[$n]' ";
        
        //first pass just gets the column name again

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second pass gets the IDs
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
          foreach ($row as $name=>$value) {
            $assigned .= $value . ",";			                
          } // end field loop
		$assigned  .= "\n";
        } // end record loop

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
	                                                              // load up ball suppliers
	  if ($ballSuppliers[0] != "") {
		$assignments .= "<br><p><strong> Ball Suppliers:   </strong> " ; 
		for ($i = 0; $i < sizeof($ballSuppliers); $i++) {
			$assignments .= "\t" . $ballSuppliers[$i] . "\t" ;
		}
		$assignments .= "</p>";
	  }	

	  $availableArray = explode(",", $available);
	  $assignedArray  = explode(",", $assigned);
      $unassignedArray = array();                                  // load up unassigned array for subs
	  	  
	  $i = 0;  $k =0;
      while ($i < sizeof($availableArray)-1){
		$j = 0;
		$availableAssigned = false;
		while ($j < sizeof($assignedArray)-1){
		  if( trim($availableArray[$i]) == trim($assignedArray[$j]) ) {			  
			$availableAssigned = true;
			break;
		  }
		  $j++;
		}
	    if ($availableAssigned == false) {
          $unassignedArray[$k] = $availableArray[$i];
          $k++;		  
        }		
		
	    $i++;
      }
	                                                              // show subs
	  if ($unassignedArray[0] != "") {
		$assignments .= "<br><p><strong> Subs:   </strong> " ; 
		for ($i = 0; $i < sizeof($unassignedArray); $i++) {
			$assignments .= "\t" . $unassignedArray[$i] . "\t" ;
		}
		$assignments .= "</p>";
	  }	

	} // end for (dates)
	}   // end if (filter)
	
    if (filter_has_var(INPUT_POST, "body"))
        $body = filter_input(INPUT_POST, "body");		
                                                                	 
    $active = "";                                                //  load up active members into $active string                                              
    $inactive = "";                                              //  load up inactive members
    $admins = "";                                                //  load up club administrators
		
	   try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003"); 
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		                                                               
        $query = "SELECT emailAddr FROM member WHERE status = 'A' ";    //start load of active members
        
        //first pass just gets the column names

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second pass gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
			foreach ($row as $name=>$value){ 
              $active .= $value . ",";
			} // end field loop
        } // end record loop

        $active .= "";                                                //  end of load up of active members   

        $query = "SELECT emailAddr FROM member WHERE status = 'I' ";  //start load of inactive members
        
        //first pass just gets the column names

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second pass gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
			foreach ($row as $name=>$value){ 
              $inactive .= $value . ",";
			} // end field loop
        } // end record loop

        $inactive .= "";                                             //  end of load up of inactive members	

        $query = "SELECT emailAddr FROM admin ";                     //start load of club administrators
        
        //first pass just gets the column names

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second pass gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
			foreach ($row as $name=>$value){ 
              $admins .= $value . ",";
			} // end field loop
        } // end record loop

        $admins .= "";                                              //  end of load up of club administrators


      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
	  
	                                                                // request for availability logic 
	if ($body != "B" && $subj == "R") {
		$body = "<p> Please provide your $monthNames[$month] availability to play <br>
                 tennis. We need this info ASAP to help us with line-ups <br>
                 beginning in $monthNames[$month]. </P> 

                <p>We are testing a new system that might make the scheduling a <br>
                   little easier for the club administrator in the future. </p>

                <p>You can help by entering your availability dates at <br> 
                   http://thevillages.asuscomm.com:8081/theduvalgroup</p>

                <p> Thank you for your help. </p> 

                <p>James Lush <br>
                   The Duval Group </p>
                ";                        
	}
                                                              // Welcome new member	
	elseif ($to == "M" && $subj == "W") 
	{
		$body = "<p>Sir -</p>

                <p>Welcome to The Duval Tennis Group.  After reading this email, please click on the link below
				to enter the information that we require for communication purposes.<br></p>
				
                 <p>  http://thevillages.asuscomm.com:8081/theduvalgroup </p>

                <p> N.B. If you are using an iPad or a smart phone, you should expand the portion of the screen you’re viewing, 
				which will make it easier to see and enter data. 

You log in with your first name and last initial, no spaces, e.g. JohnC. Capitalization is important. 
Your temporary password is: password. Your member ID is ######

If the system logs you in as guest, then either your member id or password was misspelled. 
You then have to press the login button at the top of the screen and log in again.

If the system logs you in with your name, then press the continue button. 
The screen displayed is where you enter your personal contact information and set your status to Active.

The menu list at the top of every screen takes you anywhere you want to go in the system. For example, press Members. 
It displays a list of all club members known to the system.

If you encounter any problems, please email jcerullo@yahoo.com
 </p> 

                <p>James Lush <br>
                   The Duval Group </p>
                ";                       
	}
	                                                           // notification of assignments logic
	elseif ($body != "B" && $subj == "N") 
	{
		$body = "<p>Gentlemen -</p>

                <p>START TIME: <strong>8:30</strong>AM</p>

                <p>PLEASE call James Lush if you need <br>
                    to make any change to your availability for <br>
					the line-ups below. </p>

                <p>$assignments</p>
				
				<p>Be advised that your court assignments are  <br>
				not finalized until you reply to this email to  <br>
				acknowledge the date, time and court selection.
				</p>
				
				<p><br>James Lush<br>
                   The Duval Group </p>
				   
				<img src='http://thevillages.asuscomm.com:8081/theduvalgroup/logo.jpg' alt= 'Duval Logo'/>
                ";                       
	}
                                                                    // Set To field	
	if ($to == "A") 
	{
		$to = $active;
	}
	elseif ($to == "M")
	{
		$to = $myEmailAddr;
	}
	
	elseif ($to == "I")
	{
		$to = $inactive;
	}
	elseif ($to == "L")
	{
		$to = $admins;
	}
	elseif ($to == "E")
	{
		$to = $active . $inactive;
	}
                                                                      // Set Subject field	
	if ($subj == "R") 
	{
		$subj = "Request for tennis availability";
	}
	elseif ($subj == "N")
	{
		$subj = "Notification of tennis court assignments";
	}
    elseif ($subj == "W")
	{
		$subj = "Welcome to the Duval Tennis Group";
	}		
	elseif ($subj == "P")
	{ 
		$subj = "Personal Subject";
	}
	
    if ($emailOption == "Y") {
		$_SESSION["to"] = $to;                                          // Save email parameters
		$_SESSION["subj"] = $subj;
		$_SESSION["body"] = $body;
		$_SESSION["headers"] = $headers;

		print <<<HERE
<script>
		$(showDialog);
</script>
HERE;

//		mail($to, $subj, $body, $headers);	 // this has been moved to a confirmation dialog processor: email.php	
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
		    text-align: center;
            display: block;
            margin-left: auto;
            margin-right: auto;
            clear: both;
          }
		  
		  p {
			margin-left: 50px;
			text-align: left;
		  }
		  
		  h1 {
			margin-left: 50px;
            margin-right:50px;
			text-align: center;
		  }

		  h2 {
			margin-left: 50px;
            margin-right:100px;
			text-align: center;
		  }
		  
		  h5 {
			margin-left: 150px;
            margin-right:100px;
			text-align: left;
		  }
		  
		  table {
			margin-left: 50px;
            margin-right:50px;
			text-align: left;
		  }
		  
		  #submit {
			margin-left: 165px;
			background-color: lightyellow;
		  }
			  
  </style>
  
	  <br><br><h1> Email Formater </h1>

    <form action = "sendEmail.php" method = "post" >
      <fieldset> <br>
	  	<p>  </p>
        <label>To:</label>
          <select id="selTo"  name="to" ><br>
		    <option value = "M" >Myself Only</option>
		    <option value = "A" >Active Members Only</option>
		    <option value = "I" >Inactive Members Only</option>
			<option value = "L" >Club Administrator Only</option>
		    <option value = "E" >Active & Inactive Members</option>
		  </select>
		  
		<label>Subject:</label>		
		<select id="selSubj"  name="subj" ><br>
		    <option value = "N" >Notification of court assignments</option>
		    <option value = "R" >Request for availability</option>
			<option value = "W" >Welcome new member</option>		    
			<option value = "P" >Personal Subject</option>
		</select>
				
		<label>Body:</label><br>
		<select id="selBody"  name="body" ><br>
		    <option value = "F" >Auto Format</option>
		    <option value = "B" >Empty</option>
		</select>
		
		<label>Dates:</label>
        <input type = "text"
             id = "datePicker" placeholder="Press to select dates" name="dates" readonly/> <br>

        <p><label>Send email after formatting:</label>			 
        <select id="sendEmail"  name="emailOption" >
		    <option value = "N" >No</option>
		    <option value = "Y" >Yes</option>		    
		</select></p><br>		
		
        <p><input type="submit" id = "submit" value = "submit"/></p>

      </fieldset>
    </form>

	  <h2> The constructed email appears below <br>(ready for cut and paste)</h2>
<?php
	if ($isMbr) {
    print (" <p> To: $to </p> ");
	print (" <p> Subject: $subj </p> ");
	print (" <p> $body </p> ");
	} 
?>	
  </div>	
	
<div id = "dialog"
    title = "Confirmation to send email" >			
</div>

<div id = "okDialog">			
</div>

</body>
</html> 
