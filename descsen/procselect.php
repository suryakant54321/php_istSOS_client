<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Script to list and select service processes.
//----------------------------------------------------------
echo "<html>";
echo "<body>";
echo "<div align='center'>";
// tried for istsos same way as 52 north sos but failed
// may be http_get / http_post are not supported by IST SOS
// URL can be dynamic i.e. entered by user
//
include_once('../includes/settings.php');
// load functions
include_once('../includes/func_generic.php');
include_once('../includes/func_post_data.php');
include_once('../includes/func_parse_cap.php');
include_once('../includes/func_parse_describeSense.php');
include_once('../includes/func_parse_getobservation.php');
include_once('../includes/func_gen_urls.php');
include_once('../includes/all_html.php');
//set up variables
if (isset($_POST['userurl'])){
	$myCapURL = GetCapURL($_POST['userurl'], $CapString);
	session_start();
	$_SESSION['rawURL'] = $_POST['userurl']; 
	$_SESSION['myCapURL'] = $myCapURL; //to store URL as an session variable
	//1. GetCapabilities
	$getCstring = '';
	//
	//echo "<h2> The Details of Sensors in SOS </h2>";
	// use post_data function to post xml string and catch response
	$output = post_data($myCapURL, $getCstring);
	if ($output){
		//print_r($output);
		// c. procedure
		$procedure = array('procedure','xlink:href');
		$myProcedure = getOffering($output, $procedure);
		// get procedure as one array
		$myProcedure = getCleanProc($myProcedure);
		//$myProcHtml = getHTMLProc($myProcedure, $procedure);
		echo "<p> Select Procedure </p>";
		//print_r($myProcedure);
		echo "<form method='post' action='sendetails.php' target='bsec'>
			<select name='descsensor' >";
				for($i=0; $i<(sizeof($myProcedure)); $i++){
					echo "<option value='".$myProcedure[$i]."' >".$myProcedure[$i]."</option>";
				}
		echo "</select>
			<input type='submit' value='Describe Sensor'/>
			</form>";		
	}
	else{
		echo $error;
	}
}
echo "</div></body>";
echo "</html>";
?>
