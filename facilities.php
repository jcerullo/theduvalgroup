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

</head>
<body>

<?php
	$isAdmin = $_SESSION["isAdmin"];
//                                                          load facilities array to determine max facility nbr
    $facilityNumber = array();
	
	try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT facilityName, facilityNbr FROM facility";  
        
        //first pass just gets the column names

        $result = $con->query($query);
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);
        $i = 0;
		
        foreach($data as $row){
          foreach ($row as $name=>$value){  
			if ($name == 'facilityNbr' && $value != 0) {
				$facilityNumber[$i] = $value;
				$i = $i + 1;
            } // end field loop	
		  }
        } // end record loop
 
		$newFacilityNbr = max($facilityNumber) + 1;
	
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
//                                                      if new facility	
    if (filter_has_var(INPUT_POST, "addFacility")){
        $facility = filter_input(INPUT_POST, "addFacility");	
	  if ($facility != "") {
        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		                                                   // first delete in case of duplicate
			$result = $con->prepare("DELETE FROM facility 
			   WHERE facilityName = '$facility' ");
			   
			$result->execute();
                                                           // then add SQL record
			 
	        $result = $con->prepare("INSERT INTO facility 
			            (facilityName, facilityNbr)			
			     VALUES
			            ('$facility', '$newFacilityNbr') ");

			$result->execute();
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
	  } //end if
    }   //end if
 	
    if (filter_has_var(INPUT_POST, "deleteFacility")){
        $facility = filter_input(INPUT_POST, "deleteFacility");
		
        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
                                                           // then update SQL record
			 
	        $result = $con->prepare("DELETE FROM facility 
			   WHERE facilityName = '$facility' ");
			   
			$result->execute();
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
		
    } //end if
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
if ($isAdmin) {
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
		  
		  p {
			margin-left: 100px;
		  }
		  
		  #submit {
			margin-left: 430px;
		    background-color: lightyellow;
		  }
  </style>
  
    <form action = "" method= "post">
      <fieldset> <br>
	  
        <label>Add this facility</label>
        <input type="text" value="" id="txt_addFacility" name="addFacility" pattern="[A-Za-z0-9/]{1,20}"> <br>
		<label>Delete this facility</label>
        <input type="text" value="" id="txt_deleteFacility" name="deleteFacility" pattern="[A-Za-z0-9/]{1,20}"> <br>       
        <input type="submit" value = "submit" id = "submit"/> <br>
      </fieldset>
    </form>
	
    </div>
HERE;
}         // end if
?>
  
  <div id = "memberList"> 
  <style type = "text/css">
		  
	#memberList {
		margin: 100px;
	}
  </style>

	   <h2><strong> The Duval Group facilities are: </strong></h2>
       <p> 
	   
	   
<?php
      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT facilityName as "Facility Name" 		
		            FROM facility  
					ORDER BY facilityNbr ';
        
        //first pass just gets the column names
        print "<table> \n";

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        print "  <tr> \n";
        foreach ($row as $field => $value){
          print "    <th>$field <span style='margin-left:6em'></th> \n";
        } // end foreach
        print "  </tr> \n";
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
          print "  <tr> \n";
          foreach ($row as $name=>$value){  
            print "    <td>$value</td> \n"; 
          } // end field loop	

          print "  </tr> \n";
        } // end record loop

        print "</table> \n";

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try

?>
   </p>
   </div> 
   
</body>
</html>
