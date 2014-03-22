<?php
echo "<html>";
echo "<head>
	<meta charset='utf-8'>
	<script src='../includes/datepick/htmlDatePicker.js' type='text/javascript'></script>
	<script>function goBack(){window.history.back()}</script>
	<link href='../includes/datepick/htmlDatePicker.css' rel='stylesheet'>";
echo "<body>";
echo "<div align='center'>";
// tried for istsos same way as 52 north sos but failed
// may be http_get / http_post are not supported by IST SOS
// URL can be dynamic i.e. entered by user
include_once('../includes/settings.php');
// load functions
include_once('../includes/func_generic.php');
include_once('../includes/func_post_data.php');
include_once('../includes/func_parse_cap.php');
include_once('../includes/func_parse_describeSense.php');
include_once('../includes/func_parse_getobservation.php');
include_once('../includes/func_gen_urls.php');
include_once('../includes/all_html.php');
//
//set up variables
if (isset($_POST['userurl'])){
	$myCapURL = GetCapURL($_POST['userurl'], $CapString);
	session_start();
	$_SESSION['rawURL'] = $_POST['userurl']; 
	$_SESSION['myCapURL'] = $myCapURL; //to store URL as an session variable
	//1. GetCapabilities
	$getCstring = '';
	//
	echo "<p><b> Select Sensors to View Observations </b></p>";
	// use post_data function to post xml string and catch response
	$output = post_data($myCapURL, $getCstring);
	if ($output){
		//print_r($output);
		// func from func_parse_cap 3.
		// a. offering
		$offering = array('ObservationOffering','gml:id');
		$myOffering = getOffering($output, $offering);
		$myCurrentOffering = $myOffering[0]['ObservationOffering'];
		//$myCleanOff = cleanOff($myOffering);
		echo "<p>Offering : ".$myCurrentOffering."</p>";
		//print_r($myCleanOff);	
		// b. eventTime
		$timePosition=array('beginPosition','endPosition');
		$myBeginTime = getTime($output, $timePosition[0]);
		$FrmT = getTimeSeperated($myBeginTime[0]['beginPosition']);
		//print_r($myBeginTime);
		$myEndTime = getTime($output, $timePosition[1]);
		$ToT = getTimeSeperated ($myEndTime[0]['endPosition']);
		//print_r($myEndTime);
		//$obsTimeRange = getObsRange($myBeginTime, $myEndTime, $timePosition);
		//print_r($obsTimeRange);		
		// c. procedure
		$procedure = array('procedure','xlink:href');
		$myProcedure = getOffering($output, $procedure);
		// get procedure as one array
		$myProcedure = getCleanProc($myProcedure);
		//$myProcHtml = getHTMLProc($myProcedure, $procedure);
		//print_r($myProcedure);
		// d. observedProperty
		$observedProperty = array('observedProperty','xlink:href');
		$myObservedProperty = getOffering($output, $observedProperty);
		$myObservedProperty = CleanOffering($myObservedProperty, $observedProperty);
		$myObservedProperty = getOnlyOffering($myObservedProperty);
		//$myHTMLobsProp = getHTMLProc($myObservedProperty, $observedProperty);
		//echo "</br></br>";
		// e. featureOfInterest
		$featureOfInterest = array('featureOfInterest','xlink:href');
		$myFeatureOfInterest = getOffering($output, $featureOfInterest);
		$myFeatureOfInterest = CleanOffering($myFeatureOfInterest, $featureOfInterest);
		$myFeatureOfInterest = getOnlyFOI($myFeatureOfInterest);
		//$myFOIhtml = getHTMLProc($myFeatureOfInterest, $featureOfInterest);
		//print_r($myFOIhtml);
		//*****************
		// Note: Careful Hardcoaded Section
		$mySplitObsProp = SplitObsProp($myObservedProperty);// 2 dimensional array
		$mySplitObsProp = realignSensors($mySplitObsProp); // realign the array elements returns 2D array
		$rawObsProp = $mySplitObsProp[0];
		$realObsProp = $mySplitObsProp[1];
		// select procedure
		// select observed property
		//		
		echo "<p> Select Sensor </p>";
		echo "<form method='post' action='getobs.php' target='bsec'>
			<select name='getobs' >";
				for($i=0; $i<(sizeof($myProcedure)); $i++){
					echo "<option value='".$myProcedure[$i].":".$rawObsProp[$i]."' >".$rawObsProp[$i]."</option>";
					echo "<option value='".$myProcedure[$i].":".$realObsProp[$i]."' >".$realObsProp[$i]."</option>";
				}
		echo "</select>";
		echo "<p> Select Observation Time </p>";
		echo "<input type='text' placeholder='Click to select date' name='StartDate' id='StartDate' readonly onClick='GetDate(this);'/>
		<input type='text' placeholder='Click to select date' name='EndDate' id='EndDate' readonly onClick='GetDate(this);'/>
		<input type='hidden' name='myCurentOffering' value='".$myCurrentOffering."'/>		
		<input type='hidden' name='senStart' value='".$myBeginTime[0]['beginPosition']."'/>
		<input type='hidden' name='senEnd' value='".$myEndTime[0]['endPosition']."'/>";
		echo "<br/><br/>
			<input type='submit' value='Get Observations'/>
			</form>";
	}
	else{
		echo $error;
	}
}
echo "</div></body>";
echo "</html>";
?>
