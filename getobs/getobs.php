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
if (isset($_POST['getobs'])){
	// 2. GetObservation
	echo "<h3> Sensor Observations </h3>";
	$procedureNsensor = $_POST['getobs'];
	$nProcNsens = explode(':',$procedureNsensor);
	$nProc = $nProcNsens[0];
	$Nsens = $nProcNsens[1];
	$myCurentOffering = $_POST['myCurentOffering'];
	$SenStart = $_POST['senStart'];
	$SenEnd = $_POST['senEnd'];	
	
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
	// http://localhost/istsos/krishisos?service=SOS&request=GetObservation&offering=temporary&procedure=temp_1&eventTime=2013-12-27T00:25:16.450706+0530/2013-12-28T00:25:16.450706+0530&observedProperty=air_temperature&responseFormat=text/xml;subtype=%27sensorML/1.0.0%27&service=SOS&version=1.0.0
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
		echo "<table><tr><td style='vertical-align: top;'><button onclick='goBack()'>Go Back</button></td><td>
		<form method='post' action='php_ot.php'>
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
	}
	else{
		echo "allow hist data : ".$allowHistData."</br>";
		echo "date selected ok : ".$firstCresult."</br>";
		echo "Wrong date selection : ".$nowCresult."</br>";
		echo "<br/>Enter Correct Time Interval <br/>" ;
	}
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
	echo "</body>
	</html>";
	// **************************************************************************
}
?>
