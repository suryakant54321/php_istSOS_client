<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Script to display sensor details.
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
//
if (isset($_POST['descsensor'])){
	// 2. DescribeSensor
	$procedure = $_POST['descsensor'];
	//echo $procedure."<br/>";
	//$dSensorURL = "http://localhost/istsos/service_name?service=SOS&request=describeSensor&procedure=hum_bs_1&responseFormat=text/xml;subtype='sensorML/1.0.0'&version=1.0.0";
	$dSensorURL = GetDescribeSenURL2($DescribeSenStr, $procedure, $RespFormat);
	session_start();
	$dSensorURL = $_SESSION['rawURL'].$dSensorURL;
	$dSensorstring = "";
	//
	//echo "URL Used / Accessed: ".$dSensorURL."</br>";
	$dSoutput = post_data($dSensorURL, $dSensorstring);
	//print_r($dSoutput);
	// a. Quantity
	$Quantity = array('Quantity','definition');
	$myQuantity = parse_describeSense($dSoutput, $Quantity);
	$myCleanQuantity = cleanQuantity($myQuantity);
	//print_r($myCleanQuantity);
	// b. co-ordinates
	$reference = array('Point','srsName','gml:id');
	$myCoOrd = getCoOrdinates($dSoutput, $reference);
	//echo "</br></br>";
	//print_r($myCoOrd);
	// c. Sensing time Interval
	$reference = 'interval';
	$myInterval = getInterval($dSoutput, $reference);
	//echo "</br></br>";
	//print_r($myInterval);
	$SampleTbegin = getTimeSeperated($myInterval[0]['interval']['begin']);
	$SampleTend = getTimeSeperated($myInterval[0]['interval']['end']);
	echo "<table border=0>";
		echo "<tr><td><b> Server URL </b></td><td>".$_SESSION['rawURL']."</td></tr>";	
		echo "<tr><td><b> Sensor Name </b></td><td>".$myCoOrd[0]['gml:id']."</td></tr>";
		
		echo "<tr><td><b> Sensor Deployment Date </b></td><td>Date : ".$SampleTbegin[0]." Time : ".$SampleTbegin[1]."</td></tr>";
		echo "<tr><td><b> Last Update </b></td><td>Date : ".$SampleTend[0]." Time : ".$SampleTend[1]."</td></tr>";
		
		echo "<tr><td><b>".$myCleanQuantity[0]['Quantity']."</b></td><td>".$myCleanQuantity[0]['Value']." mV</td></tr>";
		echo "<tr><td><b>".$myCleanQuantity[1]['Quantity']."</b></td><td>".$myCleanQuantity[1]['Value']." bytes </td></tr>";
		echo "<tr><td><b>".$myCleanQuantity[2]['Quantity']."</b></td><td>".$myCleanQuantity[2]['Value']." minutes </td></tr>";
		echo "<tr><td><b>".$myCleanQuantity[3]['Quantity']."</b></td><td>".$myCleanQuantity[3]['Value']." minutes </td></tr>";		

		echo "<tr><td><b> Co-Ordinate Reference System </b></td><td>".$myCoOrd[0]['srsName']."</td></tr>";
		echo "<tr><td><b> Latitude </b></td><td>".$myCoOrd[0]['coordinates']['lat']." decimal degrees </td></tr>";
		echo "<tr><td><b> Longitude </b></td><td>".$myCoOrd[0]['coordinates']['lon']." decimal degrees </td></tr>";
		echo "<tr><td><b> Elevation </b></td><td>".$myCoOrd[0]['coordinates']['elevation']." meters from MSL </td></tr>";		
	echo "</table>";
}
echo "</div></body>";
echo"</html>";
?>
