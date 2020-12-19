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
	
    if (filter_has_var(INPUT_POST, "selMbr")){
        $selMbr = filter_input(INPUT_POST, "selMbr");		
		$mbrRanking = filter_input(INPUT_POST, "mbrRanking");			
		$adminRanking = filter_input(INPUT_POST, "adminRanking");
	  
        try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
                                                           // update SQL record
			if ($mbrRanking != "" && $adminRanking != "") {
	        $result = $con->prepare("UPDATE member 
                      SET 	tennisRanking = '$mbrRanking' ,
                            assignedRanking = '$adminRanking'			
			          WHERE mbrID = '$selMbr' ");
			}
			
			if ($mbrRanking != "" && $adminRanking == "") {
	        $result = $con->prepare("UPDATE member 
                      SET 	tennisRanking = '$mbrRanking'					  
			          WHERE mbrID = '$selMbr' ");
			}
			
			if ($mbrRanking == "" && $adminRanking != "") {
	        $result = $con->prepare("UPDATE member 
                      SET 	assignedRanking = '$adminRanking'			
			          WHERE mbrID = '$selMbr' ");
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
		    background-color: lightyellow;
		  }
  </style>
  
    <form action = "mbrRanking.php" method= "post">
      <fieldset> <br>
	  
        <label>MemberID</label>
        <input type="text" value="" id="txt_selMbr" name="selMbr" value = "selMbr" > <br>
		<label>Change Member's Ranking to</label>
        <input type="text" value="" id="txt_mbrRanking" name="mbrRanking" value = "mbrRanking"> <br>
		
		<label>Change Administrator's Ranking to</label>
        <input type="text" value="" id="txt_adminRanking" name="adminRanking" value = "adminRanking"> <br>
		
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
  
	  <h2><strong> The Duval Group member rankings are: </strong></h2>  
      <p>  
<?php
      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT mbrID,                          
						 tennisRanking,
						 assignedRanking
		                 FROM member  
						 ORDER BY assignedRanking DESC ';
        
        //first pass just gets the column names
        print "<table> \n";

        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        print "  <tr> \n";
        foreach ($row as $field => $value){
		  if ($field == mbrID) print " <th> Member  <span style='margin-left:6em'></th> \n";
          if ($field == tennisRanking) print " <th> Member's Ranking <span style='margin-left:6em'></th> \n";  
		  if ($field == assignedRanking) print " <th> Administrator's Ranking <span style='margin-left:6em'></th> \n"; 
        } // end foreach
        print "  </tr> \n";
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);		

        foreach($data as $row){
          print "  <tr> \n";

          foreach ($row as $name=>$value){              

		    if ($name=='mbrID') {
			  print "    <td>$value</td> \n";
			  $mbrID = $value;
            }
						
			if ($name == 'tennisRanking' ) {
				$mbrRanking = $value ;
				print "    <td>$mbrRanking</td> \n";
			}
				
			if ($name == 'assignedRanking') {
				if ($value == "") $adminRanking = $mbrRanking;
				else $adminRanking = $value;
				print "    <td>$adminRanking</td> \n";
			}			
          } // end field loop
		  
          print "  </tr> \n";
        } // end record loop

        print "</table> \n";

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try

?>  
</p>
   
</body>
</html>
