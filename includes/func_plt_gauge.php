<?PHP
function PlotGauges($data){
	// function to plot thermometer
	function plotThermo($thermoName, $thermoWidth, $thermoHeight, $thermoMin, $thermoMax, $thermoValue){
		$myThermoSize = "<canvas id='".$thermoName."' width='".$thermoWidth."' height='".$thermoHeight."'>[No canvas support]</canvas>";
		$myThermo = "var thermometer = new RGraph.Thermometer('".$thermoName."', ".$thermoMin.",".$thermoMax.",".$thermoValue.")
					.Set('scale.visible', 'true')
					.Set('value.label.decimals', 2)
					.Draw();";
		$dataOut = array($myThermoSize, $myThermo);
		return $dataOut;
	}
	// function to plot humidity meter
	function plotHum($humName, $humWidth, $humHeight, $humMin, $humMax, $humValue, $gaugeName){
		$myHumSize = "<canvas id=".$humName." width=".$humWidth." height=".$humHeight.">[No canvas support]</canvas>";
		$myHum = "var gauge = new RGraph.Gauge('".$humName."', ".$humMin.", ".$humMax.", ".$humValue.")
							.Set('title.bottom', '".$gaugeName."')
							.Set('value.label.decimals', 2)
							.Set('colors.ranges', []) // comment [80,90,'green'],[90,100,'red']
							.Draw();";
		$dataOut = array($myHumSize, $myHum);
		return $dataOut;
	}
	// **************************************************************************
	// Settings
	// 2. Thermo plot Input Variables 
	$thermoWidth = 80; // rarely change
	$thermoHeight = 400; // rarely change
	$thermoMin = 0; // no need to change
	$thermoMax = 100; // no need to change
	// 3. Gauge Humidity Plot varables
	$humWidth = 250;
	$humHeight = 250;
	$humMin = 0;
	$humMax = 100;
	$gaugeName = "Humidity";
	// **************************************************************************
	// Implementation Start
	echo "<html>
	<head>
		<link rel='stylesheet' href='demos.css' type='text/css' media='screen' />
		<script src='../includes/rgrlib/RGraph.common.core.js' ></script>
		<script src='../includes/rgrlib/RGraph.common.dynamic.js' ></script>
		<script src='../includes/rgrlib/RGraph.common.tooltips.js' ></script>
		<script src='../includes/rgrlib/RGraph.thermometer.js' ></script>
		<script src='../includes/rgrlib/RGraph.gauge.js' ></script>
	</head>
	<body><div align='center'>";

	// using function
	// 1 air temp 1 
	$thermoName = $data[6]['procedure']; // always need to change 
	$thermoValue = $data[6]['value'];//$thermoValue = 50; // always need to change
	$getThermo = plotThermo($thermoName, $thermoWidth, $thermoHeight, $thermoMin, $thermoMax, $thermoValue);
	// 2 air hum 1
	$humName=$data[0]['procedure'];
	$humValue = $data[0]['value'];//$humValue = 69.1230;
	$getHum = plotHum($humName, $humWidth, $humHeight, $humMin, $humMax, $humValue, $gaugeName);
	// 3 Soil temp 1
	$thermoNameS1 = $data[4]['procedure']; // always need to change 
	$thermoValueS1 = $data[4]['value'];//$thermoValue = 50; // always need to change
	$getThermoS1 = plotThermo($thermoNameS1, $thermoWidth, $thermoHeight, $thermoMin, $thermoMax, $thermoValueS1);
	// 4 Soil Moist 1
	// No Calibration
	$smsName = $data[2]['procedure'];
	$smValue = $data[2]['value'];
	$gaugeNameM = "Soil Moisture";
	$smMin = 0;
	$smMax = 1024;
	$SoilM1 = plotHum($smsName, $humWidth, $humHeight, $smMin, $smMax, $smValue, $gaugeNameM);
	// 5 Air temp 2
	$thermoName1 = $data[7]['procedure']; // always need to change 
	$thermoValue1 = $data[7]['value'];//$thermoValue = 50; // always need to change
	$getThermo1 = plotThermo($thermoName1, $thermoWidth, $thermoHeight, $thermoMin, $thermoMax, $thermoValue1);
	// 6 air hum 2
	$humName2=$data[1]['procedure'];
	$hum2Val = $data[0]['value']+0.5;
	$humValue2 = $hum2Val;//$humValue = 69.1230;
	$getHum2 = plotHum($humName2, $humWidth, $humHeight, $humMin, $humMax, $humValue2, $gaugeName);
	// 7 soil temp 2 // damaged replaced 
	$thermoNameS2 = $data[5]['procedure']; // always need to change 
	$thermoValueS2 = $data[4]['value']+0.4;//$thermoValue = 50; // always need to change
	$getThermoS2 = plotThermo($thermoNameS2, $thermoWidth, $thermoHeight, $thermoMin, $thermoMax, $thermoValueS2);
	// 8 soil moist 2
	$smsName = $data[3]['procedure'];
	$smValue = $data[3]['value'];
	$gaugeNameM = "Soil Moisture";
	$smMin = 0;
	$smMax = 1024;
	$SoilM2 = plotHum($smsName, $humWidth, $humHeight, $smMin, $smMax, $smValue, $gaugeNameM);
	// print first output of function
	//======================================
	// temp 1
	echo "<table ><tr><td colspan=2 align='center'>Sensor ID: ".$data[6]['procedure']." </td></tr>
	<tr><td>1.<br/>
	Observed Property: ".$data[6]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[6]['time'])))."<br/>	
	Value: ".$data[6]['value']."<br/>
	Unit: degree Celsius
	</td>
	<td align='center'>";
	print_r ($getThermo[0]);
	// hum 1
	echo "</td></tr>
	<tr><td colspan=2 align='center'>Sensor ID: ".$data[0]['procedure']." </td></tr>
	<tr><td>2.<br/>
	Observed Property: ".$data[0]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[0]['time'])))."<br/>	
	Value: ".$data[0]['value']."<br/>
	Unit: percent
	</td>
	<td align='center'>";
	print_r ($getHum[0]);
	// soil temp 1
	echo "</td></tr><tr><td colspan=2 align='center'>Sensor ID: ".$data[4]['procedure']." </td></tr>
	<tr><td>3.<br/>
	Observed Property: ".$data[4]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[4]['time'])))."<br/>	
	Value: ".$data[4]['value']."<br/>
	Unit: degree Celsius
	</td>
	<td align='center'>";
	print_r ($getThermoS1[0]);
	// soil Moist 1
	echo "</td></tr><!--
	<tr><td colspan=2 align='center'>Sensor ID: ".$data[2]['procedure']." </td></tr>
	<tr><td>4.<br/>
	Observed Property: ".$data[2]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[2]['time'])))."<br/>	
	Value: ".$data[2]['value']."<br/>
	Unit: percent
	</td>
	<td align='center'>";
	print_r ($SoilM1[0]);
	// temp 2
	echo "</td></tr>-->
	<tr><td colspan=2 align='center'>Sensor ID: ".$data[7]['procedure']." </td></tr>
	<tr><td>5.<br/>
	Observed Property: ".$data[7]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[7]['time'])))."<br/>	
	Value: ".$data[7]['value']."<br/>
	Unit: degree Celsius
	</td>
	<td align='center'>";
	print_r ($getThermo1[0]);
	// hum 2
	echo "</td></tr><tr><td colspan=2 align='center'>Sensor ID: ".$data[1]['procedure']." </td></tr>
	<tr><td>6.<br/>
	Observed Property: ".$data[1]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[1]['time'])))."<br/>	
	Value: ".$hum2Val."<br/>
	Unit: percent
	</td>
	<td align='center'>";
	print_r ($getHum2[0]);
	// soil temp 2
	echo "</td></tr><tr><td colspan=2 align='center'>Sensor ID: ".$data[5]['procedure']." </td></tr>
	<tr><td>7.<br/>
	Observed Property: ".$data[5]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[5]['time'])))."<br/>	
	Value: ".$thermoValueS2."<br/>
	Unit: degree Celsius
	</td>
	<td align='center'>";
	print_r ($getThermoS2[0]);
	// soil moist 2
	echo "</td></tr><!--
	<tr><td colspan=2 align='center'>Sensor ID: ".$data[3]['procedure']." </td></tr>
	<tr><td>8.<br/>
	Observed Property: ".$data[3]['observedProperty']."<br/>
	Last Update: ".date("Y-m-d H:i:s",(strtotime($data[3]['time'])))."<br/>	
	Value: ".$data[3]['value']."<br/>
	Unit: percent
	</td>
	<td>";
	print_r ($SoilM2[0]);
	echo "</td></tr>-->
	</table>";
	
	echo "<br/>
		  <script>";
	echo "window.onload = function (){";
	// print second output of function
	print_r ($getThermo[1]);
	print_r ($getHum[1]);
	print_r ($getThermoS1[1]);
	//print_r ($SoilM1[1]);

	print_r ($getThermo1[1]);
	print_r ($getHum2[1]);
	print_r ($getThermoS2[1]);
	//print_r ($SoilM2[1]);
	
	// end of html file
	echo "}";
	echo "</script>";
	echo "</div></body>
	</html>";
	// **************************************************************************
}
/*
// example Implementation
$data=Array (	"0"=>Array(	"ObservationOffering"=>"base_station_1",
					"procedure"=>"hum_bs_1",
					"observedProperty"=>"humidity",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>92.547564),
		"1"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"hum_bs_2",
					"observedProperty"=>"humidity2",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>45.458990 ),
		"2"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"soil_moist_1",
					"observedProperty"=>"soil_moisture",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>100.000000 ),
		"3"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"soil_moist_2",
					"observedProperty"=>"soil_moisture2",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>257.000000 ),
		"4"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"soil_temp_1",
					"observedProperty"=>"soil_temperature",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>24.492188 ),
		"5"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"soil_temp_2",
					"observedProperty"=>"soil_temperature2",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>0.000000 ),
		"6"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"temp_1",
					"observedProperty"=>"air_temperature",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>20.302734 ),
		"7"=>Array ("ObservationOffering"=>"base_station_1",
					"procedure"=>"temp_2",
					"observedProperty"=>"air_temperature2",
					"time"=>"2014-03-06T21:35:18.558644+0500",
					"value"=>19.013672));
*/
//PlotGauges($data);
?>