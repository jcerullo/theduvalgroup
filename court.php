<?php
  session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Duval</title>
  <meta charset = "UTF-8" />
  
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
     integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <link href='https://fonts.googleapis.com/css?family=Roboto:300,400,700' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" type="text/css" href="main.css">
  <script type="text/javascript" src = "jquery-3.3.1.js"> </script>
  
  <script> type = "text/javascript">
  
  $(init);
  
  function init() {
	  // load up facilities list from DB
	  $("#facilitiesList").load("facilitiesList.php");
  }  //end init
  
  </script>

</head>
<body>

<?php
	$isAdmin = $_SESSION["isAdmin"];
	
    if (filter_has_var(INPUT_POST, "facilityName")){
        $facilityName = filter_input(INPUT_POST, "facilityName");		
		$facilityNbr = filter_input(INPUT_POST, "facilityNbr");			
		$nbrOfCourts = filter_input(INPUT_POST, "nbrOfCourts");
		$startDate = filter_input(INPUT_POST, "startDate");
		$endDate = filter_input(INPUT_POST, "endDate");
		$startTime = filter_input(INPUT_POST, "startTime");
		$endTime = filter_input(INPUT_POST, "endTime");
	  
        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
                                                           // update SQL record
			if ($facilityNbr != null && $facilityNbr <= 9) {
	        $result = $con->prepare("UPDATE facility 
                      SET 	facilityNbr = '$facilityNbr' 			
			          WHERE facilityName = '$facilityName' ");
			}
			
			if ($nbrOfCourts != null && $nbrOfCourts <= 9) {
	        $result = $con->prepare("UPDATE facility 
                      SET 	nbrOfCourts = '$nbrOfCourts' 			
			          WHERE facilityName = '$facilityName' ");
			}
			
			if ($startDate != null && $startDate != "") {
	        $result = $con->prepare("UPDATE facility 
                      SET 	startDate = '$startDate' 			
			          WHERE facilityName = '$facilityName' ");
			}
			
			if ($startTime != null && $startTime != "") {
	        $result = $con->prepare("UPDATE facility 
                      SET 	startTime = '$startTime' 			
			          WHERE facilityName = '$facilityName' ");
			}
			
			if ($endDate != null && $endDate != "") {
	        $result = $con->prepare("UPDATE facility 
                      SET 	endDate = '$endDate' 			
			          WHERE facilityName = '$facilityName' ");
			}
			
			if ($endTime != null && $endTime != "") {
	        $result = $con->prepare("UPDATE facility 
                      SET 	endTime = '$endTime' 			
			          WHERE facilityName = '$facilityName' ");
			}
			
			$result->execute();
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
    }  //end if

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
				<li><a href="mbrRanking.php">Tennis Rankings</a></li>
				<li><a href="sumResults.html">MemberStats</a></li>				
			</ul>
		</li>
		<li><a href="#">Court Info &raquo;</a>
			<ul>
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
            display: block;
            margin-left: auto;
            margin-right: auto;
            clear: both;
          }
		  
		  p {
			margin-left: 100px;
		  }
		  
		  #submit {
		    background-color: lightyellow;
		  }
  </style>
  
    <form action = "facility.php" method= "post">
      <fieldset> <br>
		
		<label>Facility Name</label>
        <select id="facilitiesList" name="facilityName" >		
		</select><br><br>
 
		<label>Change Number of Courts to</label>
        <input type="text" value="" id="txt_nbrOfCourts" name="nbrOfCourts" value = "nbrOfCourts"> <br>
        <label>Change Start Date to</label>
        <input type="text" value="" id="txt_startDate" name="startDate" value = "startDate"> <br> 
        <label>Change End Date to</label>
        <input type="text" value="" id="txt_endDate" name="endDate" value = "endDate"> <br>
        <label>Change Start Time to</label>
        <input type="text" value="" id="txt_startTime" name="startTime" value = "startTime"> <br>
        <label>Change End Time to</label>
        <input type="text" value="" id="txt_endTime" name="endTime" value = "endTime"> <br>
 		
        <input type="submit" value = "submit" id = "submit"/>  <br><br>
      </fieldset>
    </form>
	
    </div>
  
  <div id = "memberList"> 
  <style type = "text/css">
		  
	#memberList {
		margin: 100px;
	}
  </style>
  
	  <h2><strong> The Duval Group facilities are: </strong></h2>  
       
<?php
      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT *
		                 FROM facility  
						 ORDER BY facilityNbr ';
        
        //first pass just gets the column names
        print "<table> \n";

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        print "  <tr> \n";
        foreach ($row as $field => $value){
		  if ($field == facilityName) print " <th> Name  <span style='margin-left:6em'></th> \n";
          if ($field == facilityNbr) print " <th> Facility ID <span style='margin-left:1em'></th> \n";  
		  if ($field == nbrOfCourts) print " <th> Number of Courts <span style='margin-left:1em'></th> \n";
          if ($field == startDate) print " <th> Start Date <span style='margin-left:2em'></th> \n";
          if ($field == endDate) print " <th> End Date <span style='margin-left:6em'></th> \n"; 
          if ($field == startTime) print " <th> Start Time <span style='margin-left:2em'></th> \n";
          if ($field == endTime) print " <th> End Time <span style='margin-left:6em'></th> \n"; 		  
        } // end foreach
        print "  </tr> \n";
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);		

        foreach($data as $row){
          print "  <tr> \n";

          foreach ($row as $name=>$value){              

		    if ($name=='facilityName') {
			  print "    <td>$value</td> \n";
			  $facilityName = $value;
            }
						
			if ($name == 'facilityNbr' ) {
				$facilityNbr = $value ;
				print "    <td>$facilityNbr</td> \n";
			}
				
			if ($name == 'nbrOfCourts') {
                $nbrOfCourts = $value;
				print "    <td>$nbrOfCourts</td> \n";
			}
			
			if ($name == 'startDate') {
                $startDate = $value;
				print "    <td>$startDate</td> \n";
			}

			if ($name == 'endDate') {
                $endDate = $value;
				print "    <td>$endDate</td> \n";
			}
			 
			if ($name == 'startTime') {
                $startTime = $value;
				$displayStartTime = substr($startTime,0,5);
				print "    <td>$displayStartTime</td> \n";
			}

			if ($name == 'endTime') {
                $endTime = $value;
				$displayEndTime = substr($endTime,0,5);
				print "    <td>$displayEndTime</td> \n";
			}
			
          } // end field loop
		  
          print "  </tr> \n";
        } // end record loop

        print "</table> \n";

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try

?>  
   
</body>
</html>
