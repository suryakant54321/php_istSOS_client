<?PHP
echo "<html>";
echo "<body>";
echo "<div align='center'>";
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
//set up variables
if (isset($_POST['userurl'])&&(strlen($_POST['userurl'])>10)){
	$_SESSION['userurl'] = $_POST['userurl']; // to store url into session
	$myCapURL = GetCapURL($_POST['userurl'], $CapString);
	//1. GetCapabilities
	$getCstring = '';
	//
	echo "<h2> The Details of Sensor Observation Service Capabilities </h2>";
	echo "<p>Service URL : ".$_SESSION['userurl']."</p>";
	// use post_data function to post xml string and catch response
	$output = post_data($myCapURL, $getCstring);
	if ($output){
		//print_r($output);
		// func from func_parse_cap 1.
		$cap_LL = cap_parse_LL($output);
		//take only one bounding box
		$one_LL = array_slice($cap_LL,-1);
		//print_r($one_LL);
		// func from func_generic 1.
		// seperate mix lat lon from xml
		$LL_Seperate = (array_value_recursive('mixlatlon', $one_LL));
		$LL_Seperate = explode(',',$LL_Seperate);
		//print_r($LL_Seperate);
		// top left and bottom right lat lon
		$LC = $LL_Seperate[0];
		$UC = $LL_Seperate[5];
		echo "<p>Approximate Latitude and Longitude<br/>Lat:".$LC ." & Lon: ". $UC."</p>";
		// func from func_parse_cap 3.
		// a. offering
		$offering = array('ObservationOffering','gml:id');
		$myOffering = getOffering($output, $offering);
		//$myCleanOff = cleanOff($myOffering);
		echo "<p>Offering : ".$myOffering[0]['ObservationOffering']."</p>";
		//print_r($myCleanOff);	
		// b. eventTime
		$timePosition=array('beginPosition','endPosition');
		$myBeginTime = getTime($output, $timePosition[0]);
		$FrmT = getTimeSeperated($myBeginTime[0]['beginPosition']);
		//print_r($myBeginTime);
		$myEndTime = getTime($output, $timePosition[1]);
		$ToT = getTimeSeperated ($myEndTime[0]['endPosition']);
		//print_r($myEndTime);
		echo "<p> Observation availability (Date and Time) <br/> 
					From : ".$FrmT[0]." ".$FrmT[1]." Time Zone ".$FrmT[2]."<br/>
					To : ".$ToT[0]." ".$ToT[1]." Time Zone ".$ToT[2]."</p>";
		//$obsTimeRange = getObsRange($myBeginTime, $myEndTime, $timePosition);
		//print_r($obsTimeRange);
		// c. procedure
		$procedure = array('procedure','xlink:href');
		$myProcedure = getOffering($output, $procedure);
		// get procedure as one array
		$myProcedure = getCleanProc($myProcedure);
		//$myProcHtml = getHTMLProc($myProcedure, $procedure);
		//print_r($myProcHtml);	
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
		//echo "</br></br>";
		//print_r($myFOIhtml);
		//*****************
		// Note: Careful Hardcoaded Section
		$mySplitObsProp = SplitObsProp($myObservedProperty);// 2 dimensional array
		$mySplitObsProp = realignSensors($mySplitObsProp); // realign the array elements returns 2D array
		$rawObsProp = $mySplitObsProp[0];
		$realObsProp = $mySplitObsProp[1];
		//print_r($mySplitObsProp);
		// useing four parameters $myProcedure, $myFeatureOfInterest, $rawObsProp, $realObsProp
		echo "<h4>Sensor Details </h4>";
		echo "<table border=1><tr><th>SN</th><th>Procedures</th><th>Feature of Interest</th><th>Raw Sensor</th><th>Real Sensor</th></tr>";
		for($i=0;$i<sizeof($myProcedure);$i++){
			$SN = $i+1; 
			echo "<tr><td> ".$SN." </td><td> ".$myProcedure[$i]."</td><td>".$myFeatureOfInterest[$i]."</td><td>".$rawObsProp[$i]."</td><td>".$realObsProp[$i]."</td></tr>";}
		echo "</table><br/>";
		//*****************
		// func from func_parse_cap 2.
		// service details
		echo "<h4>Service Details </h4>";
		$details = array('Title', 'Abstract', 'Keyword', 'ServiceType', 'ServiceTypeVersion', 'ProviderName', 'IndividualName', 'PositionName', 'Voice', 'DeliveryPoint', 'City', 'AdministrativeArea', 'PostalCode', 'Country', 'ElectronicMailAddress');
		$cap = cap_parse_service($output, $details);
		$serviceHTML = getServHTML($cap, $details);
		print_r($serviceHTML);
		echo "<br/>";		
	}
	else{
		echo $error;
	}
	//echo "<br/>GetCapabilities END<br/>";
}
else{
	echo "<br/>";
}
echo "</div></body>";
echo"</html>";
?>