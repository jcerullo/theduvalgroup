<?php
	  session_start();
      $schedDate = $_SESSION["schedDate"] ;
	  
      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $query = "SELECT mbrID
		                 FROM assignment  
						 WHERE date = '$schedDate' ";
        
        //first pass just gets the column names

        $result = $con->query($query);
        $row = $result->fetch(PDO::FETCH_ASSOC);
		print ' <option value = ""  disabled hidden> </option> ';
        print " <option value = '*None' > *None </option>";
        foreach ($row as $field => $value){ 		  
        } // end foreach
 
        //second query gets the data
        $data = $con->query($query);
        $data->setFetchMode(PDO::FETCH_ASSOC);		

        foreach($data as $row){
          foreach ($row as $name=>$value){              

		    if ($name=="mbrID") {
			  $mbrID = $value;
			  print "<option value = '$mbrID'> $mbrID </option>";
            }

          } // end field loop

        } // end record loop

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try
	  
?>