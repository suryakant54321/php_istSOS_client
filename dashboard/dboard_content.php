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
// $url = 'http://localhost/istsos/service_name?service=SOS&request=GetCapabilities';
include_once('../includes/settings.php');
// load functions
include_once('../includes/func_generic.php');
include_once('../includes/func_post_data.php');
include_once('../includes/func_parse_cap.php');
include_once('../includes/func_parse_describeSense.php');
include_once('../includes/func_parse_getobservation.php');
include_once('../includes/func_gen_urls.php');
include_once('../includes/all_html.php');
include_once('../includes/func_plt_gauge.php');
// 2.
if (isset($_POST['userurl'])){
	$myCapURL = GetCapURL($_POST['userurl'], $CapString);
	$StoreRawURL = $_POST['userurl'];
	session_start();
	$_SESSION['rawURL'] = $StoreRawURL; 
	$_SESSION['myCapURL'] = $myCapURL; //to store URL as an session variable
	//1. GetCapabilities
	$getCstring = '';
	//
	echo "<p><b> Recent Sensor Observations </b></p>";
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
		echo "<p><b>Offering : ".$myCurrentOffering."</b></p>";
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
		// To get procedure wise start and end time
		function ActualStEdTime($StoreRawURL, $DescribeSenStr, $myProcedure, $RespFormat){
			$ArrayOfTime = array();
			for($i=0;$i<sizeof($myProcedure);$i++){
				$dSensorURL = GetDescribeSenURL2($DescribeSenStr, $myProcedure[$i], $RespFormat);
				$dSensorURL = $StoreRawURL.$dSensorURL;
				$dSensorstring = "";
				//
				//echo "URL Used / Accessed: ".$dSensorURL."</br>";
				$dSoutput = post_data($dSensorURL, $dSensorstring);
				//print_r($dSoutput);
				// c. Sensing time Interval
				$reference = 'interval';
				$myInterval = getInterval($dSoutput, $reference);
				//echo "</br></br>";
				//print_r($myInterval);
				$SampleTbegin = $myInterval[0]['interval']['begin'];
				$SampleTend = $myInterval[0]['interval']['end'];
				$ArrayOfTime[$i][0]=$SampleTbegin;
				$ArrayOfTime[$i][1]=$SampleTend;
			}
			return $ArrayOfTime;
		}
		$MyProcWiseTime = ActualStEdTime($StoreRawURL,$DescribeSenStr, $myProcedure, $RespFormat);
		//print_r($MyProcWiseTime);
		// form array of all parameters for each sensor (i.e. observed_property) 
		// (offering, procedure, observed_property, endPosition)
		function FormAllParams($myCurrentOffering, $myProcedure, $rawObsProp, $realObsProp, $MyProcWiseTime){
			$AllParamsOut = array();
			$aa=0;
			for($i=0; $i<(sizeof($myProcedure)); $i++){
				/*
				$AllParamsOut[$aa]['ObservationOffering'] = $myCurrentOffering;
				$AllParamsOut[$aa]['procedure'] = $myProcedure[$i];
				$AllParamsOut[$aa]['observedProperty'] = $rawObsProp[$i];
				$AllParamsOut[$aa]['beginPosition'] = $MyProcWiseTime[$i][0];
				$AllParamsOut[$aa]['endPosition'] = $MyProcWiseTime[$i][1];
				$aa=$aa+1;
				*/
				//
				$AllParamsOut[$aa]['ObservationOffering'] = $myCurrentOffering;
				$AllParamsOut[$aa]['procedure'] = $myProcedure[$i];
				$AllParamsOut[$aa]['observedProperty'] = $realObsProp[$i];
				$AllParamsOut[$aa]['beginPosition'] = $MyProcWiseTime[$i][0];
				$AllParamsOut[$aa]['endPosition'] = $MyProcWiseTime[$i][1];
				$aa=$aa+1;
			}
			return $AllParamsOut;
		}
		$AllThisTog = FormAllParams($myCurrentOffering, $myProcedure, $rawObsProp, $realObsProp, $MyProcWiseTime);
		//print_r($AllThisTog);
		// Get all observations
		function GetAllObs($StoreRawURL, $AllThisTog, $GetObsPre, $ProPre, $EvtTimePre, $ObsProPre, $GetObsEnd){
			//echo "<h3> Sensor Observations </h3>";
			$AllSensorObsData = array();
			$rawServUrl = $StoreRawURL;
			for($i=0;$i<sizeof($AllThisTog);$i++){
				$myCurentOffering = $AllThisTog[$i]['ObservationOffering'];
				$nProc = $AllThisTog[$i]['procedure'];
				$Nsens = $AllThisTog[$i]['observedProperty'];
				$SenStart = $AllThisTog[$i]['beginPosition'];
				$SenEnd = $AllThisTog[$i]['endPosition'];	
				//echo $myCurentOffering."||".$nProc."||".$Nsens."||".$SenStart."||".$SenEnd."<br/>";
				$getObsURL = $rawServUrl.$GetObsPre.$myCurentOffering.$ProPre.$nProc.$EvtTimePre.$SenEnd.$ObsProPre.$Nsens.$GetObsEnd;
				$GetObsStr = "";
				$GetObsOutput = post_data($getObsURL, $GetObsStr);
				// extract
				$cleanOut = parse_GetObs($GetObsOutput);
				// format to a. csv. b. 'TarraY' three dimensional array, c. table, d. 'TnV' only time and value
				// 1. output format csv file
				$dFormat = "TarraY";
				$formatOut = format_out($dFormat,$cleanOut);
				$AllSensorObsData[$i]['ObservationOffering'] = $myCurentOffering;
				$AllSensorObsData[$i]['procedure'] = $nProc;
				$AllSensorObsData[$i]['observedProperty'] = $Nsens;
				$AllSensorObsData[$i]['time'] = $formatOut[0]['time'];
				$AllSensorObsData[$i]['value'] = $formatOut[0]['value'];
			}
			return $AllSensorObsData;
		}
		$AllSensorData = GetAllObs($StoreRawURL, $AllThisTog, $GetObsPre, $ProPre, $EvtTimePre, $ObsProPre, $GetObsEnd);
		//print_r($AllSensorData);
		echo "</div></body>";
		echo"</html>";
		PlotGauges($AllSensorData);
	}
	else{
		echo $error;
	}
}
?>
