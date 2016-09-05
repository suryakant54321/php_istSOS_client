<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: GetObservation as single file. Useful for debug.
//----------------------------------------------------------
echo "<html>";
echo "<head>
	<meta charset='utf-8'>
	<script src='datepick/htmlDatePicker.js' type='text/javascript'></script>
	<script>function goBack(){window.history.back()}</script>
	<link href='datepick/htmlDatePicker.css' rel='stylesheet'>";
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
//
echo "<form method='post' action='".$phpSelf."' class='modURL'>
		<select name='kurl' >
			<option  value='".$ServiceURL."' >".$SOS_NAME_1."</option>
			<option  value='".$OtherService."' >".$SOS_NAME_2."</option>			
		</select>
		<input type='submit' value='Get URL'/>
		</form>";
echo "<div id='modurl' class='modURL'>";		
if (isset($_POST['kurl'])){
	//echo $_POST['kurl'];
	echo "	<form method='post' action='".$phpSelf."' class='modURL2'>
			<input type='text' name='userurl' value='".$_POST['kurl']."'/>
			<input type='submit' value='Submit URL'/>
			</form>";
}
echo "</div>";
echo "<div id='modurl2' class='modURL2'>";
//set up variables
if (isset($_POST['userurl'])){
	$myCapURL = GetCapURL($_POST['userurl'], $CapString);
	session_start();
	$_SESSION['rawURL'] = $_POST['userurl']; 
	$_SESSION['myCapURL'] = $myCapURL; //to store URL as an session variable
	//1. GetCapabilities
	$getCstring = '';
	//
	echo "<h2> Select Sensors to View Observations </h2>";
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
		echo "<form method='post' action='".$phpSelf."'>
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
echo "</div>";
//
if (isset($_POST['getobs'])){
	// 2. GetObservation
	echo "<h2> Sensor Observations </h2>";
	$procedureNsensor = $_POST['getobs'];
	$nProcNsens = explode(':',$procedureNsensor);
	$nProc = $nProcNsens[0];
	$Nsens = $nProcNsens[1];
	$myCurentOffering = $_POST['myCurentOffering'];
	$SenStart = $_POST['senStart'];
	$SenEnd = $_POST['senEnd'];	
	//
	$cStartDate = date("Y-m-d\TH:i:s+05:30",strtotime($_POST['StartDate']));
	$cEndDate = date("Y-m-d\TH:i:s+05:30",strtotime($_POST['EndDate']));
	// echo $myCurentOffering."||".$nProc."||".$Nsens."||".$SenStart."||".$SenEnd."||".$cStartDate."||".$cEndDate."<br/>";
	// Date check 
	$firstCresult = FirstCompareDates($SenStart, $SenEnd, $cStartDate, $cEndDate);
	$nowCresult = CompareDates($cStartDate, $cEndDate, $DateLimit);
	//echo $firstCresult."||".$nowCresult;
	// form getObservation URL
	session_start();
	$rawServUrl = $_SESSION['rawURL'];
	// sample URL
	// http://localhost/istsos/service_name?service=SOS&request=GetObservation&offering=temporary&procedure=temp_1&eventTime=2013-12-27T00:25:16.450706+0530/2013-12-28T00:25:16.450706+0530&observedProperty=air_temperature&responseFormat=text/xml;subtype=%27sensorML/1.0.0%27&service=SOS&version=1.0.0
	echo $getObsURL."<br/>";
	if(($allowHistData=="TRUE")&&($firstCresult=="TRUE")){
		echo "its old data may take time";
	}
	else if(($allowHistData=="FALSE")&&($firstCresult=="TRUE")&&($nowCresult=="TRUE")){
		$getObsURL = $rawServUrl.$GetObsPre.$myCurentOffering.$ProPre.$nProc.$EvtTimePre.$cStartDate.$TimePre.$cEndDate.$ObsProPre.$Nsens.$GetObsEnd;
		$csvFileName = $myCurentOffering."-".$nProc."-".$Nsens.".csv";
		$GetObsStr = "";
		$GetObsOutput = post_data($getObsURL, $GetObsStr);
		//print_r($GetObsOutput);
		// extract
		$cleanOut = parse_GetObs($GetObsOutput);
		// format to a. csv. b. 'TarraY' three dimensional array, c. table, d. 'TnV' only time and value
		// 1. output format csv file
		$dFormat = "csv";
		$formatOut = format_out($dFormat,$cleanOut);
		echo "<table><tr><td style='vertical-align: top;'><button onclick='goBack()'>Go Back</button></td><td><form method='post' action='php_ot.php'>
		<input type='hidden' name='dataSend' value='".$formatOut."'/>
		<input type='hidden' name='FileName' value='".$csvFileName."'/>
		<input type='submit' value='Download CSV'/></form></td>";
		// 3. Output format graph
		$dFormat = "TarraY";
		$formatOut = format_out($dFormat,$cleanOut);
		$formatOut = TwoArrays($formatOut);
		$xdata = QuotedXdata($formatOut[0]); // quoted output required for serialization
		$ydata = implode(',',$formatOut[1]);
		//print_r($xdata);
		echo "<td><form method='post' action='".$phpSelf."'>
		<input type='hidden' name='xdata' value=".serialize($xdata)."/>
		<input type='hidden' name='ydata' value='".$ydata."'/>
		<input type='submit' value='Output Chart'/></form></td>";		
		// 2. Output Format table
		$dFormat = "table";
		$formatOut = format_out($dFormat,$cleanOut);
		print_r($formatOut);		
		// sub-functions 1. asCSVStr, 2. asTable
		// Should be used only after TnV function
		//$formatOut2 = asTable($formatOut);
		//print_r($formatOut2);
	}
	else{
		echo "<br/>Enter Correct Time Interval <br/>" ;
	}
	//session_start();
}
echo "</div></body>";
echo"</html>";
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
		<script src='rgrlib/RGraph.common.core.js' ></script>
		<script src='rgrlib/RGraph.common.dynamic.js' ></script>
		<script src='rgrlib/RGraph.common.tooltips.js' ></script>
		<script src='rgrlib/RGraph.thermometer.js' ></script>
		<script src='rgrlib/RGraph.line.js' ></script>
		<script src='rgrlib/RGraph.gauge.js' ></script>
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
	echo "</body>
	</html>";
	// **************************************************************************
}
?>
