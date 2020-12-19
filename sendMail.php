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
		  autoOpen : true,         		  
	  }); 
	  
    } // end init
	
    function openDialog()
	{		
       $("#dialog").dialog("open");	   
    } // end openDialog
	
    function closeDialog(){
      $("#dialog").dialog("close");
    } // end closeDialog
	
	function showDialog()	
	{	
        HTMLdata = 	"<img src = " + "'" + "logo.jpg" + "'" + 
		            " height = 100 width = 100" + 
					" alt = 'logo'> " +
					" <p>This window can be moved.</p> " + 
                    "<br>";
	   $("#dialog").html(HTMLdata);
       $("#dialog").dialog("open");	   
    } // end showDialog

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
	global $mrbTennisRanking;
	
	$_SESSION["isAdmin"] = true;  // testing...this paragragh will be removed
	$_SESSION["isMbr"] = true;
	$_SESSION["isActive"] = true;
	$isAdmin = $_SESSION["isAdmin"];  
	$isMbr = $_SESSION["isMbr"];     
	$isActive = $_SESSION["isActive"];  
	
	if (isSet ($_SESSION["mbrID"])) {
      $mbrID = $_SESSION["mbrID"];
	  $password = $_SESSION["password"];
	  $firstName = $_SESSION["firstName"];
	    // Set other globals: isMbr, isActive, isAdmin
	}   // end if
    else {
      $_SESSION["mbrID"] = "";
	  $_SESSION["password"] = "";
      $mbrID = "";
	  $password = "";
    }  //end else		
  }    //end init


  init();
                                                         // if form not yet displayed 
	try {                                                // retrieve SQL record
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
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
		  }
		 		  
	    }        
		
    }		 //end of try
 	
    catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }	  
?>  

<header class="container">
 <div class="row">
  <h1 class="col-sm-4"></h1>
    <nav class="col-sm-8 text-right">
     <p><a href="index.html" role="button">Login</a></p>
     <p><a href="mbrProfile.php" role="button">MemberProfile</a></p>
     <p><a href="availability.php" role="button">Availability</a></p> 
     <p><a href="assignments.html" role="button">CourtAssignments</a></p> 
     <p><a href="members.php" role="button">Members</a></p> 
     <p><a href="help.php" role="button">Help</a></p>
    </nav>
 </div>
</header>

<?php
print "<p>...... You are logged in as <strong> $mbrID </strong> on the <strong>Send Email </strong>  page</p>";
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
		  
  </style> 

    <form action = "sendEmail.php" method = "post" >
      <fieldset> <br>
	  	<p>  </p>
        <label>To:</label>
          <select id="selTo"  name="to" >
		    <option value = "A" >Active Members Only</option>
		    <option value = "I" >Inactive Members Only</option>
			<option value = "L" >Club Administrators Only</option>
		    <option value = "E" >All of the above</option>
		  </select>
		<label>Subject:</label>
		    <option value = "R" >Request for availability</option>
		    <option value = "N" >Notification of court assignments</option>
			<option value = "P" >Personal Subject</option>
		<label>Start Date</label>	
        <input type="text" value="" id="txt_fromDate" name="fromDate > <br>
        <label>End Date</label>	
        <input type="text" value="" id="txt_toDate" name="toDate > <br>
		<label>Body:</label>
		    <option value = "R" >Auto Format</option>
		    <option value = "N" >Empty</option>
						
        <input type="submit" value = "submit"/>

      </fieldset>
    </form>

  </div>
HERE;
?>

<div id = "dialog"
    title = "Email Confirmation" >
</div>
</body>
</html> 
