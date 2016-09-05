<?php
header("Content-type: text/xml");
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Script to generate XML for Google maps API.
// IMP Note: service specific hard coaded.
//----------------------------------------------------------
include_once('../includes/settings.php');
// load functions
include_once('../includes/func_generic.php');
include_once('../includes/func_post_data.php');
include_once('../includes/func_parse_cap.php');
include_once('../includes/func_parse_describeSense.php');
include_once('../includes/func_parse_getobservation.php');
include_once('../includes/func_gen_urls.php');
// start of marker
echo "<markers>";
// Note: $urlArray is located in settings.php 
// the check on working and non working url is not added here.
// its added in Ubuntu/Linux version.
for($kk=0; $kk<sizeof($urlArray); $kk++){
	$URL = $urlArray[$kk];
	$myCapURL = GetCapURL($URL, $CapString);
	session_start();
	$_SESSION['rawURL'] = $URL; 
	$_SESSION['myCapURL'] = $myCapURL; //to store URL as an session variable
	//1. GetCapabilities
	$getCstring = '';
	//
	//echo "<h2> Select Date to View Observations </h2>";
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
			//echo "<p>Offering : ".$myCurrentOffering."</p>";
			//print_r($myCleanOff);	
			// b. eventTime
			$timePosition=array('beginPosition','endPosition');
			$myBeginTime = getTime($output, $timePosition[0]);
			$FrmT = getTimeSeperated($myBeginTime[0]['beginPosition']);
			//echo "<br/> begin position :";
			//print_r($myBeginTime);

			$myEndTime = getTime($output, $timePosition[1]);
			$ToT = getTimeSeperated ($myEndTime[0]['endPosition']);
			//echo "<br/> End position :";
			//print_r($myEndTime);
			//
			// time interchange
			// to acquire data of last 7 days
			//echo "<br/>";
			$NewStartDate = (strtotime($myEndTime[0]['endPosition'])- (7 * 24 * 60 * 60));
			$myBeginTime[0]['beginPosition'] = date("Y-m-d\TH:i:s+05:30", ($NewStartDate)); //changed format
			//print_r($myBeginTime[0]['beginPosition']);
			//echo "<br/>";
			//
			/*echo "<p> Observation availability (Date and Time) <br/> 
			*			From : ".$FrmT[0]." ".$FrmT[1]." Time Zone ".$FrmT[2]."<br/>
			*			To : ".$ToT[0]." ".$ToT[1]." Time Zone ".$ToT[2]."</p>";
			*/
			//$obsTimeRange = getObsRange($myBeginTime, $myEndTime, $timePosition);
			//print_r($obsTimeRange);		
			// c. procedure
			$procedure = array('procedure','xlink:href');
			$myProcedure = getOffering($output, $procedure);
			// get procedure as one array
			$myProcedure = getCleanProc($myProcedure);
			//$myProcHtml = getHTMLProc($myProcedure, $procedure);
			//echo "Available Procedures:";
			//print_r($myProcedure);
			//echo "<br/>";
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
			//echo "Available Observed Properties:";
			//print_r($myObservedProperty);
			
			$mySplitObsProp = SplitObsProp($myObservedProperty);// 2 dimensional array
			$mySplitObsProp = realignSensors($mySplitObsProp); // realign the array elements returns 2D array
			//echo "<br/>";
			//print_r($mySplitObsProp);
			$rawObsProp = $mySplitObsProp[0];
			$realObsProp = $mySplitObsProp[1];
			// select procedure
			// select observed property
			//		
			//echo "<p> Select Sensor </p>";
			// Get Arranged
			$realProp = selectRealObsProp($myObservedProperty);
			$procPropCombine = combineProcNobsProp($realProp, $myProcedure);
			//echo "<br/>";
			//print_r($realProp);
			//echo "<br/>";
			//print_r($procPropCombine);
			// Required params
			// proc, prop, time start, time end, offering
			// $myBeginTime[0]['beginPosition'], $myEndTime[0]['endPosition']
			// $procPropCombine[0]['proc'], $procPropCombine[0]['prop']
			// $myCurrentOffering
			// 2. GetObservation
			//echo "<h2> Sensor Observations </h2>";
			$SensorData = 'x,y,z';
			$correctDateNotifier = '';
			$NoDataNotifier = '';
			$rawSenData = array();
			for($i=0; $i<sizeof($realProp); $i++){
			//for($i=0;$i<3;$i++){
				$nProc = $procPropCombine[$i]['proc'];
				$Nsens = $procPropCombine[$i]['prop'];
				//echo "|Procedure| $nProc |Sensor| $Nsens |id| $i <br/>";
				$SenStart = $myBeginTime[0]['beginPosition'];
				$SenEnd = $myEndTime[0]['endPosition'];	
				//
				$cStartDate = date("Y-m-d\TH:i:s+05:30",strtotime(date("Y-m-d",(time()))));
				//$cStartDate = strtotime($cStartDate);
				// modified start and end date as same
				$cEndDate = date("Y-m-d\TH:i:s+05:30",strtotime(date("Y-m-d",(time()))));
				// added one day to selected day
				$cEndDate = strtotime(date("Y-m-d\TH:i:s+05:30", strtotime($cEndDate)) . " + 1 day");
				// reformated date
				$cEndDate = date("Y-m-d\TH:i:s+05:30",$cEndDate);
				//echo "$cStartDate $cEndDate"; // verify the result
				// adjusted to select one day only
				// echo $myCurrentOffering."||".$nProc."||".$Nsens."||".$SenStart."||".$SenEnd."||".$cStartDate."||".$cEndDate."<br/>";
				// Date check 
				$firstCresult = FirstCompareDates($SenStart, $SenEnd, $cStartDate, $cEndDate);
				$nowCresult = CompareDates($cStartDate, $cEndDate, $DateLimit);
				// for one day observations
				$diffDate = ((strtotime($cEndDate) - strtotime($cStartDate))/(24*3600));
				//echo "$diffDate <br/>";
				if (($firstCresult=="FALSE")&&($diffDate==1)){
					$firstCresult="TRUE";
					$cEndDate=$SenEnd;
				}
				//for long time non responsive sensor
				$longDiff = ((strtotime($cEndDate) - strtotime($cStartDate))/(24*3600));
				//echo round($longDiff,1)."<br/>".$firstCresult;
				if (($firstCresult=="TRUE")&&($longDiff < -2)){
					$firstCresult="TRUE";
					$cStartDate = strtotime(date("Y-m-d\TH:i:s+05:30", strtotime($cEndDate)) . " - 1 day");
					$cStartDate = date("Y-m-d\TH:i:s+05:30",$cStartDate);
				}
				//echo $cStartDate."||".$cEndDate."||".$SenEnd."<br/>";
				//echo "$allowHistData $firstCresult $nowCresult<br/>";
				//echo $firstCresult."||".$nowCresult;
				// form getObservation URL
				session_start();
				$rawServUrl = $_SESSION['rawURL'];
				// sample URL
				// http://localhost/istsos/service_name?service=SOS&request=GetObservation&offering=temporary&procedure=temp_1&eventTime=2013-12-27T00:25:16.450706+0530/2013-12-28T00:25:16.450706+0530&observedProperty=air_temperature&responseFormat=text/xml;subtype=%27sensorML/1.0.0%27&service=SOS&version=1.0.0
				if(($allowHistData=="FALSE")&&($firstCresult=="TRUE")&&($nowCresult=="TRUE")){
					$getObsURL = $rawServUrl.$GetObsPre.$myCurrentOffering.$ProPre.$nProc.$EvtTimePre.$cStartDate.$TimePre.$cEndDate.$ObsProPre.$Nsens.$GetObsEnd;
					//echo "$getObsURL";
					$csvFileName = $myCurrentOffering."-".$nProc."-".$Nsens.".csv";
					$GetObsStr = "";
					$GetObsOutput = post_data($getObsURL, $GetObsStr);
					//print_r($GetObsOutput);
					// extract
					$cleanOut = parse_GetObs($GetObsOutput);
					//print_r($cleanOut[0]);
					$SenData = $cleanOut[0][values];
					$SenData = explode('@',$SenData);
					//print_r ($SenData);
					// total quantaties 12
					$rawSenData[$i]['Start Date'] = $cStartDate;
					$rawSenData[$i]['End Date'] = $cEndDate;
					//$rawSenData[$i]['proc'] = $nProc;
					$rawSenData[$i]['Sensor'] = $Nsens;
					//getBaseStat returns array 'sum' 'avg' 'min' 'max'
					$getProcess = getBaseStat($SenData);
					//$rawSenData[$i]['ocount'] = sizeof($SenData);
					$rawSenData[$i]['Average'] = $getProcess['avg'];
					$rawSenData[$i]['Sum'] = $getProcess['sum'];
					$rawSenData[$i]['Minimum'] = $getProcess['min'];
					$rawSenData[$i]['Maximum'] = $getProcess['max'];				
					//$rawSenData[$i]['type'] = "admin";
					//
					if($SenData==''){
						$NoDataNotifier = "Partial / all data points are not avaialable";
					}
					else{
						//print_r($cleanOut[0][values]);
						// parsing function to seperate lat lon of sensor observation
						$reference = array('Point','srsName','gml:id');
						$myCoOrd = getCoOrdinates($GetObsOutput, $reference);
						//print_r($myCoOrd);
						$rawSenData[$i]['lat'] = round($myCoOrd[0]['coordinates']['lon'],6);
						$rawSenData[$i]['lon'] = round($myCoOrd[0]['coordinates']['lat'],6);
					}
				}
				else{
					$correctDateNotifier = "Enter Correct Date";	
				}
				//session_start();
			}
			//print_r(array_keys($rawSenData[0]));
			$GoodExData = extractForXML($rawSenData);
			//print_r($GoodExData);
			$myOutThis = genSenXML($GoodExData);
			print_r($myOutThis);
			//$xmlOut = getMyxml($rawSenData);
			//echo $xmlOut;
			// useful
			//$getXMLhere = genXML($rawSenData);
			//echo $getXMLhere;
			if($correctDateNotifier == 'Enter Correct Date'){
				$toTakeThen = "$correctDateNotifier<br/>";
			}
			else{
				if($NoDataNotifier != ''){
					$toTakeThen = $NoDataNotifier;
				}
				else{
					$toTakeThen = 'All data points were available';
				}
			}
			//WriteCSV($SensorData);		
	}
}
echo "</markers>";
//
if((isset($_POST['xdata']))&&(isset($_POST['ydata']))){
	// function to plot line chart
	function plotLine($pltName, $pltWidth, $pltHeight, $xData, $yData){
		$myfirst = "<canvas id='".$pltName."' width='".$pltWidth."' height='".$pltHeight."'>[No canvas support]</canvas>";
		$mysecond = "var line = new RGraph.Line('".$pltName."', [".implode(",",$yData)."])				
					.Set('tooltips', ['".implode("','",$yData)."'])
					.Set('text.angle',45)
					.Set('gutter.bottom', 200)
					.Set('labels', [".$xData."])
					.Draw();";
		$dataOut = array($myfirst, $mysecond);
		return $dataOut;
	}
	// 1. Line plot Input Variables 
	$pltWidth = "1200";
	$pltHeight = "600";
	//$yData = array(18.369141,19.013672,19.658203,19.335938,19.335938,19.980469,20.302734,19.335938,19.980469,19.658203,19.658203);
	$yData = explode(',',$_POST['ydata']);
	//print_r($yData);
	//$xData = array('2014-02-26T23:58:47.036646+0500','2014-02-27T00:14:48.082441+0500','2014-02-27T00:30:49.399500+0500','2014-02-27T00:46:50.793413+0500','2014-02-27T01:02:52.022292+0500','2014-02-27T01:18:53.060864+0500','2014-02-27T01:34:54.290513+0500','2014-02-27T01:50:55.188447+0500','2014-02-27T02:06:56.509256+0500','2014-02-27T02:22:57.587390+0500','2014-02-27T02:38:58.736172+0500');
	$xData = unserialize($_POST['xdata']);
	$xData = explode(',',$xData);
	$GapValue = 10;
	$xData = arrayThin($xData, $GapValue);
	$xData = implode(",",$xData);
	// **************************************************************************
	// Implementation Start
	echo "<html>
	<head>
		<link rel='stylesheet' href='demos.css' type='text/css' media='screen' />
		<script src='../includes/rgrlib/RGraph.common.core.js' ></script>
		<script src='../includes/rgrlib/RGraph.common.dynamic.js' ></script>
		<script src='../includes/rgrlib/RGraph.common.tooltips.js' ></script>
		<script src='../includes/rgrlib/RGraph.thermometer.js' ></script>
		<script src='../includes/rgrlib/RGraph.line.js' ></script>
		<script src='../includes/rgrlib/RGraph.gauge.js' ></script>
	</head>
	<body>";
	// using function
	$pltName = "csvs";
	$myLine = plotLine($pltName, $pltWidth, $pltHeight, $xData, $yData);
	$pltName = "csv";
	$myLines = plotLine($pltName, $pltWidth, $pltHeight, $xData, $yData);
	print_r ($myLines[0]);
	echo "<script>";
	echo "window.onload = function (){";
	print_r ($myLines[1]);
	// end of html file
	echo "}";
	echo "</script>";
	echo"</body></html>";
}
// to avoid confusion
// plotting and file save code below is removed  
// New Function 
function WriteCSV($MyContent) {						
	//write the values in the sql file "test.sql" file
	$filename1 = 'data_2.csv';
	if (!$handle1 = fopen($filename1, 'wr')) {
	//echo "Cannot open file ($filename1)";
	exit;
	}
	// Write $MyContent to our opened file.
	if (fwrite($handle1, $MyContent) === FALSE) {
	//echo "Cannot write to file ($filename1)";
	exit;
	}
	//echo "Success, wrote ($MyContent) to file ($filename1)";
}
?>
