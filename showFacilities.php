<?php

$facilityName = filter_input(INPUT_POST, 'facilityName');

      try {
        $con= new PDO('mysql:host=localhost;dbname=duval', "root", "jjc003");
        $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $con->prepare("SELECT *
		                 FROM facility  
						 WHERE facilityName = ? ";
						 
		$stmt->execute(array($facilityName));
        
        $result = stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row ){
			foreach ($row as $field => $value) {
				print <<<HERE
				$field: $value <br>
		
HERE;
			}
        } // end foreach

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      } // end try