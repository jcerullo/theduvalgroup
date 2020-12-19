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
	
	try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = 'SELECT  *
		          FROM    settings 
				  WHERE   rcdNbr > 0  ';
        
        //first pass just gets the column names
        $result = $con->query($query);
        //return only the first row (we only need field names)
        $row = $result->fetch(PDO::FETCH_ASSOC);

        foreach ($row as $field => $value){
        } // end foreach
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);		

        foreach($data as $row){
          foreach ($row as $name=>$value){              
						
			if ($name == 'lowLeftPhoto')    $leaguePhoto = $value;
			if ($name == 'lowLeftText')     $leagueText =  $value;
			if ($name == 'lowRightPhoto')   $eventPhoto =  $value;
			if ($name == 'lowRightText')    $eventText =   $value;
			
          } // end field loop
		  
        } // end record loop

    } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
    } // end try
	
    if (filter_has_var(INPUT_POST, "eventPhoto"))
	{
        $eventPhoto = filter_input(INPUT_POST, "eventPhoto");

		if (filter_has_var(INPUT_POST, "eventText")) {
			$eventText = filter_input(INPUT_POST, "eventText");
			$eventText = strtr($eventText,"'"," ");               //strip out ' and " (so that SQL is happy)
			$eventText = strtr($eventText,'"',' ');
			$eventText = trim($eventText);
		}

		if (filter_has_var(INPUT_POST, "leaguePhoto"))
			$leaguePhoto = filter_input(INPUT_POST, "leaguePhoto");
		
		if (filter_has_var(INPUT_POST, "leagueText")) {
			$leagueText = filter_input(INPUT_POST, "leagueText");
			$leagueText = strtr($leagueText,"'"," ");               //strip out ' and " (so that SQL is happy)
			$leagueText = strtr($leagueText,'"',' ');
			$leagueText = trim($leagueText);
	    }
                                              // update SQL record
		try {
        
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
                                                           // then update SQL record
			
	        $result = $con->prepare("UPDATE settings
		                              SET lowLeftPhoto = '$leaguePhoto',
					                      lowLeftText  = '$leagueText',
					                      lowRightPhoto= '$eventPhoto',
						                  lowRightText = '$eventText'
					                  WHERE rcdNbr = 1 ");
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
             <h1> <strong><span style='margin-left:12em'>First Page Settings </strong></h1>
			 
  <div class = "setting"> 
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
			margin-left: 420px;
		    background-color: lightyellow;
		  }
  </style>
  
<?php
$isAdmin = $_SESSION["isAdmin"]; 
if ($isAdmin) {
print <<<HERE

    <form action = "settings.php" method= "post">
      <fieldset> <br>
	  
        <label>Special Event Photo is </label>
        <input type="text" value="$eventPhoto" id="txt_eventPhoto" name="eventPhoto"> <br><br>
		
		<label>Special Event Text is </label>
        <textarea id="txt_eventText" 
				  name="eventText"
				  rows= "8"
				  columns="40"> $eventText </textarea><br>
				  
		<label>League Photo is </label>
        <input type="text" value="$leaguePhoto" id="txt_leaguePhoto" name="leaguePhoto"> <br><br>
		
        <label>League Text is </label>
        <textarea id="txt_leagueText" 
				  name="leagueText"
				  rows= "7"
				  columns="40"> $leagueText </textarea><br>
		
        <input type="submit" value = "submit" id = "submit"/> <br>
      </fieldset>
    </form>
HERE;
}         // end if admin
?>

  </div>  

</body>
</html>
