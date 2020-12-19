<?php
session_start();

$to = $_SESSION["to"];
$subj =	$_SESSION["subj"];
$body = $_SESSION["body"];
$headers = $_SESSION["headers"];

if ($to != "" && $subj != "" && $headers != "") {
	mail($to, $subj, $body, $headers);
	print <<<HERE
	<p> <strong> Email successfully sent </strong> </p>
HERE;
}

?>