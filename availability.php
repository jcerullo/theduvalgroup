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
	  currentMonth = date.getMonth();                  //0-11
	  firstDate = daysInMonth[currentMonth] +1;        //first date to display from today
      $('#datePicker').multiDatesPicker({
		           dateFormat: "yy-mm-dd",
	               minDate: firstDate-date.getDate(), 
	               maxDate: 71 });
		   
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
	global $status;
	global $isActive;

	if (isSet($_SESSION["mbrID"]) && $_SESSION["mbrID"] != NULL && $_SESSION["mbrID"] != "") 
	{
        $mbrID =   $_SESSION["mbrID"];
	    $isMbr =   $_SESSION["isMbr"];
	    $isAdmin = $_SESSION["isAdmin"];
		$status =  $_SESSION["status"];
		$isActive =  $_SESSION["isActive"];
	}
	else 
	{
		$mbrID = "Guest";
		$isMbr = false;
		$isAdmin = false;
		$isActive = false;
		$status = "";
	}
  }    //end init
  
  init();
  
  $monthNames = array("January", "February", "March", "April", "May", "June",
                     "July", "August", "September", "October", "November", "December");
					 
    if (filter_has_var(INPUT_POST, "dates") && $mbrID != "Guest"){
        $dates = filter_input(INPUT_POST, "dates");
		$dateArray = explode(",", $dates);
	  
        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);		

		$result = $con->prepare("DELETE FROM available 
		                         WHERE mbrID ='$mbrID' ");
		$result->execute();
			
		  for ($i = 0; $i < sizeof($dateArray); $i++) {
	        $result = $con->prepare("INSERT INTO available 
			            (rcdNbr,
						 mbrID,
						 dates)			
			          VALUES
			            ( NULL,
						 '$mbrID',
						 '$dateArray[$i]') ");
			$result->execute();
		  } //end for		 
        }		 //end of try
		
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
    }  // end if (input)
					   
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

<section class="jumbotron">
  <div class="container">
    <div class="row text-center">
<?php
	  $month = (int) date('m');
      print "<h2>We are currently scheduling courts for the month of $monthNames[$month] </h2>";
?>
      <h3>Press the dates that you are available to play; then press submit: </h3>
    </div>
  </div>
</section>

<?php
   print "<p>... You are logged in as <strong> $mbrID </strong></p>";
?>
  
<section>
    <figure class="col-sm-6">
	<div>
	<style type = "text/css">
            
          input {
			background-color: lightyellow; 
          }
		  
    </style>
	
	  <form action = "availability.php" method= "post">
      <input type = "text"
             id = "datePicker" placeholder="Press here for a calendar" name="dates" readonly/>
	  <input type="submit" value = "submit"/> <br>
	  </form>
    </div>
	<p> Take care that the correct month is displayed </p>
	<p> Only Saturdays, Mondays, and Tuesdays should be selected.  
	    Weekly court assignments are completed on Wednesday. 
		So you must call or email your club administrator if 
        your availability changes on or after Wednesday.	</p>
	</figure>
	
 
    <figure class="col-sm-6">
	<div>

<?php 	 
	try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT dates as 'You are available on the following dates:' 
		          FROM available 
				  WHERE mbrID = '$mbrID' 
				  ORDER BY dates ASC ";
        
        //first pass just gets the column names
        print "<table> \n";

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        print "  <tr> \n";
        foreach ($row as $field => $value){
          print "    <th>$field</th> \n";
        } // end foreach
        print "  </tr> \n";
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
          print "  <tr> \n";
          foreach ($row as $name=>$value){ 
		    $timestamp = strtotime($value);
            $dayName = 	date("l",$timestamp);	  
            print "    <td>   $dayName, $value</td> \n";              
          } // end field loop
          print "  </tr> \n";
        } // end record loop

        print "</table> \n";

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
?>  
    </div>  
    </figure> 

</section> 

</body>
</html>


