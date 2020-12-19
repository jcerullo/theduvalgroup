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
  <script type="text/javascript" src = "jquery-3.3.1.js"> </script>
  <script> type = "text/javascript">
  
  $(init);
  
  function init() {
	  // load up facilities list from DB
	  $("#facilitiesList").load("facilitiesList.php");

	  // load up assigned members list from DB
	  $("#assignedList").load("assignedList.php");
  }  //end init
  
  </script>  
</head>
<body>
<?php
//                                               This function loads the facility array in the exact order
//                                               in which facilities are assigned.  Randomness is achieved
//                                               by shuffling the members rather than the facilities.
	function loadFacilityArray() {
		global $facilityArray;
		global $oldFacilityNbr;
		global $newFacilityNbr;
		
		$facilityArray = array();
		$oldFacilityNbr = array();
		$newFacilityNbr = array();
		
/*		$facilityArray = array(
		                       "Truman","Truman","Truman","Truman",
		                       "Odell","Odell","Odell","Odell",
							   "Bacall","Bacall","Bacall","Bacall",							   
							   "Allamanda","Allamanda","Allamanda","Allamanda",							   
							   "ColonyCottage","ColonyCottage","ColonyCottage","ColonyCottage",
							   "SeaBreeze","SeaBreeze","SeaBreeze","SeaBreeze",
							   "Bacall","Bacall","Bacall","Bacall", 
							   "Allamanda","Allamanda","Allamanda","Allamanda",
							   "ColonyCottage","ColonyCottage","ColonyCottage","ColonyCottage",
							   "Odell","Odell","Odell","Odell",
							   "Truman","Truman","Truman","Truman",
							   "SeaBreeze","SeaBreeze","SeaBreeze","SeaBreeze"
							  );
*/

      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT *
		                 FROM facility  
						 ORDER BY facilityNbr ';
        
        //first pass just gets the column names
		
        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){ 		  
        } // end foreach
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);
		
		$n = -1;                                 // facilityArray index
		$j = 0;                                  // oldFacilityNbr index
        foreach($data as $row){
          foreach ($row as $name=>$value){              

		    if ($name=='facilityName') {
			  $facilityName = $value;
            }
						
			if ($name == 'facilityNbr' ) {
				$facilityNbr = $value;
				$oldFacilityNbr[$j] = $facilityNbr;
				$j += 1;
			}
				
			if ($name == 'nbrOfCourts') {
                $nbrOfCourts = $value;
			}
			
          } // end field loop
		  
		  for ($i=0; $i < 4*$nbrOfCourts; $i++) {
			$n += 1;
			$facilityArray[$n] = $facilityName;
		  }	
		  
        } // end record loop

	  } catch(PDOException $e) {
		echo 'ERROR: ' . $e->getMessage();
	  } // end try

	}  //end function
	
	function shuffleFacilities() {
		global $facilityArray;
		global $oldFacilityNbr;
		global $newFacilityNbr;
		
		$newFacilityNbr = $oldFacilityNbr;
		shuffle($newFacilityNbr);
		
		try {
        
			$con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
			$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
			for ($i=0; $i < sizeof($oldFacilityNbr); $i++) {
				$oldFacility = $oldFacilityNbr[$i];
				$newFacility = $newFacilityNbr[$i] + sizeof($oldFacilityNbr);
				$result = $con->prepare("UPDATE facility 
                      SET 	facilityNbr = '$newFacility' 			
			          WHERE facilityNbr = '$oldFacility' ");
				$result->execute();
			}
					 
        }		 //end of try

        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    } 
			
	} // end function
					 
    if (filter_has_var(INPUT_POST, "schedDate")) {
        $schedDate = filter_input(INPUT_POST, "schedDate");
        if ($schedDate == "") $schedDate = "0000-00-00";		
	    $_SESSION["schedDate"] = $schedDate;
	} 
	else {
		$schedDate = $_SESSION["schedDate"];
	}
	
	if (filter_has_var(INPUT_POST, "likeDate")) {
        $likeDate = filter_input(INPUT_POST, "likeDate");
        if ($likeDate == "") $likeDate = "0000-00-00";        	
	}
	else {
		$likeDate = "0000-00-00";
	}
	
	if (filter_has_var(INPUT_POST, "preloadOption")) {
        $preloadOption = filter_input(INPUT_POST, "preloadOption");			
	}
	else {
		$preloadOption = "N";
	}

	$isAdmin = $_SESSION["isAdmin"];
	
	$player = "";
	$court = "";
	$action = "";
	$ballSupplier = "";
	$ballSuppliers = array();

//                                                                   duplicate assignments from another date

    if ($likeDate != "0000-00-00" && $preloadOption == "L" ) {
	
		try {			        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	        $result = $con->prepare("DELETE FROM assignment
			   WHERE date= '$schedDate'");
			   
			$result->execute();
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
		
		try {                                                // retrieve SQL record		
        $result = $con->query("SELECT * FROM assignment WHERE date = '$likeDate' ");
		$result->setFetchMode(PDO::FETCH_ASSOC);

		foreach ($result as $row) {
		  foreach ($row as $name =>$value ) {               // populate values 
			if ($name == 'mbrID')                $mbrID = $value;
			if ($name == 'date')                 $date  = $value;
			if ($name == 'facilityName') $facilityName  = $value;
		  }
        $prep = $con->prepare("INSERT INTO assignment 
			            (rcdNbr,  
						 mbrID,
						 date,
						 facilityName
						)			
			     VALUES
			            (NULL,
						'$mbrID',
						'$schedDate',
						'$facilityName'
                        )            ");

		$prep->execute();		  
	    }        // end of foreach row      
		
        }		 //end of try
 	
        catch(PDOException $e) {
           echo 'ERROR: ' . $e->getMessage();
        } 	
		
	}  // end like if
		
//                                                                	 load up available members into $available string
        $available = "";
		
	    try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003"); 
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
        $query = "SELECT   available.mbrID,
                           member.assignedRanking		
		          FROM     available INNER JOIN member
				  ON       available.mbrID = member.mbrID
				  WHERE    available.dates = '$schedDate'
				  ORDER BY member.assignedRanking DESC
				  ";
        
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
			  if ($name == 'mbrID') $available .= $value . ",";              
			} // end field loop
        } // end record loop

        $available .= "";

        } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
        } // end try
		
	                                                            //  if assignment is random or by ranking

	if ($preloadOption == "T" || $preloadOption == "R") {
																//  first delete prior assignments                                                                  	
	  try {			        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	        $result = $con->prepare("DELETE FROM assignment
			   WHERE date= '$schedDate'");
			   
			$result->execute();
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }

	  $availableArray = explode(",", $available);                // already ordered by tennis ranking (T) 
	  array_pop($availableArray);                                // remove blank item
	  if ($preloadOption == "R") shuffle($availableArray);       // shuffle players if requested random assignment (R)
	  loadFacilityArray();

	  $i = 0;
	  $j = -1;

		for ($i=0; $i <= sizeof($availableArray); $i++) {
			$player = $availableArray[$i];                       // pick a player
			$j += 1;			
			$facility = $facilityArray[$j];                      // pick a facility
 
			if ($j == sizeof(facilityArray) - 1 ) $j=0;
									
			if ($player != "" && $schedDate != "0000-00-00" ){	

				try {			        
				$con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
				$result = $con->prepare("DELETE FROM assignment
					WHERE mbrID = '$player' AND date= '$schedDate'");
			   
				$result->execute();					 
				}		 //end of try
 	
				catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
				}

				try {
				$con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		 
					$result = $con->prepare("INSERT INTO assignment 
			            (rcdNbr,  
						 mbrID,
						 date,
						 facilityName
						)			
					VALUES
			            (NULL,
						'$player',
						'$schedDate',
						'$facility'
                        )            ");

					$result->execute();					 
				}		 //end of try

				catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
				}
			} //end if player 
		}     //end of for i
	}         // end if R option
	
	//                                                                   if assignment by tennis ranking
	
	if ($preloadOption == "T") {
		shuffleFacilities();
	}
//                                                                       process input fields
    if (filter_has_var(INPUT_POST, "player"))
        $player = filter_input(INPUT_POST, "player");
   		
	if (filter_has_var(INPUT_POST, "facilityName")) {
        $court = filter_input(INPUT_POST, "facilityName");
    }
	
    if (filter_has_var(INPUT_POST, "action"))
        $action = filter_input(INPUT_POST, "action");

	if (filter_has_var(INPUT_POST, "ballSupplier")) {						
		$values = $_POST['ballSupplier'];
		$i = 0;
		foreach ($values as $ballSupplier ) {
			$ballSuppliers[$i] = $ballSupplier;
			$i = $i + 1;
		}

                                                    //    if there are ball suppliers, first undo ball assignments			
		if ($ballSuppliers[0] != "") {
	
			try {			        
				$con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
				$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				
				$result = $con->prepare("UPDATE assignment
		                             SET balls = 'N'
					                 WHERE date = '$schedDate' ");
			   
				$result->execute();
				                                    //   then set new ball assignments
				for ($i=0; $i < sizeof($ballSuppliers); $i++) { 
				
					$result = $con->prepare("UPDATE assignment
		                              SET balls = 'Y'
					                  WHERE date = '$schedDate' AND mbrID = '$ballSuppliers[$i]' ");
			   
					$result->execute();	
				}
			}
			catch(PDOException $e) {
				echo 'ERROR: ' . $e->getMessage();
			}				
																	  			
		} //end if ballSupplier
    }     //end if filter 

    if ($player != "" && $schedDate != "0000-00-00" ){	
		
        try {			
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
	        $result = $con->prepare("DELETE FROM assignment
			   WHERE mbrID = '$player' AND date= '$schedDate'");
			   
			$result->execute();
					 
        }		 //end of try
 	
        catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
	    }
		
    } //end if
	
    if ($player != "" && $court != "" && $schedDate != "0000-00-00" && $action = "A"){		
        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			 
	        $result = $con->prepare("INSERT INTO assignment 
			            (rcdNbr,  
						mbrID,
						date,
						facilityName
						)			
			     VALUES
			            (NULL,
						'$player',
						'$schedDate',
						'$court'
                        )            ");

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
			margin-left: 100px;
			text-align: center;
		  }
		  
		  h1 {
			margin-left: 100px;
            margin-right:100px;
			text-align: center;
		  }
		  
		  h2 {
			margin-left: 100px;
            margin-right:100px;
			text-align: center;
		  }
		  
		  #submit {
		    background-color: lightyellow;
			margin-left: 30px;
		  }
  </style>
  
<?php

$timestamp = strtotime($schedDate);                // get day name
$dayName = date("l", $timestamp);
  
print ("	  <h1><strong> Court Assignments </strong></h1> "); 
print ("	  <h2><strong> for $dayName, $schedDate </strong></h2> ");
if ($isAdmin) {
print <<<HERE
    <form action = "" method= "post">
      <fieldset>
		<br><label>Player</label>
        <input type="text" id="txt_member" name="player" >
		
        <label>Court</label>
        <select id="facilitiesList" name="facilityName" >
		</select>
		
        <label>Action</label>
        <select id="selAction"  name="action" >
		  <option value = "A" >Add</option>
		  <option value = "D" >Remove</option>
		</select>
		
		<label>Ball Supplier(s)</label>
        <select id="assignedList"  name="ballSupplier[]" multiple size=5 >
		</select>
		
        <input type="submit" id="submit" value ="submit"/>
      </fieldset>
    </form>
HERE;
}  //end if
?>
  </div>
	
<section>
    <figure class="col-sm-6">
	<style type = "text/css">
		  
	#mbrAssignedList {
		margin-top:  40px;
		margin-left: 350px;
		
        h3 {
			margin-left: 150px;
            margin-right:150px;
			text-align: left;
		  }
	}
    </style>
	
  <div id = "mbrAssignedList"> 
  
  <h3><span style='margin-left:1em'><strong> Assigned </strong></h3>

<?php

    $assigned = "";	                                                    //       load up assigned members into $assigned string
      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT facilityName as 'Courts  ', 
		                 mbrID as 'Players' FROM assignment WHERE date = '$schedDate'
                         ORDER BY facilityName	 ";
        
        //first pass just gets the column names
        print "<table> \n";

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        print "  <tr> \n";
        foreach ($row as $field => $value){
          print "    <th>$field <span style='margin-left:8em'></th> \n";
        } // end foreach
        print "  </tr> \n";
 
        //second pass gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);

        foreach($data as $row){
          print "  <tr> \n";
          foreach ($row as $name=>$value) {			  
            print "    <td>$value</td> \n";              
          } // end field loop
          print "  </tr> \n";
        } // end record loop

        print "</table> \n";

	    // Second query is just for the member IDs
	    $query = "SELECT mbrID, balls FROM assignment WHERE date = '$schedDate' ";
        
        //first pass just gets the column name again

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second pass gets the IDs
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);
		
		$i = 0;
        foreach($data as $row){
          foreach ($row as $name=>$value) {
            if ($name == "mbrID") { 
				$assigned .= $value . ",";
				$member = $value;
			}
			if ($name == "balls" && $value == "Y") {
				$ballSuppliers[$i] = $member;
				$i = $i +1;
			}
          } // end field loop
		$assigned  .= "\n";
        } // end record loop

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
?>
   </div>
   </figure>
   
    <style type = "text/css">
		  
	#unassignedList {
		margin-top:  40px;
		margin-left: 100px;
		
        h3 {
			margin-left: 150px;
            margin-right:150px;
			text-align: left;
		  }
	}
    </style> 
	
   <figure class="col-sm-6">
   <div id = "unassignedList">  
    
<?php
      $availableArray = explode(",", $available);                     //       load up unassigned members into $unassigned array
	  $assignedArray  = explode(",", $assigned);
      $unassignedArray = array();
	  
		 
	  
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
		  if ($k == 0) {
		    print "<h3><strong> Unassigned, but available </strong></h3>";
          }
		  print ("$availableArray[$i]<br>");
          $unassignedArray[$k] = $availableArray[$i]; 
          $k++;
        }		
		
	    $i++;
      }
	                                                                   //         display ball suppliers 
      if ($ballSuppliers[0] != "" && $ballSuppliers[0] != "*None") {
		  print "<h3><strong> Ball Suppliers </strong></h3>";
		$i = 0;
		while ($i < sizeof($ballSuppliers)){	
			print "$ballSuppliers[$i]<br>";
			$i = $i +1;
		}
	  }
?>
   </div>  
   </figure> 
	
</section>   
</body>
</html>
