<?PHP
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Functions for parsing XML, comparing dates, etc.
//----------------------------------------------------------
include_once('settings.php');
//******************************************************************	
// 1. Get all values from specific key in a multidimensional array	
function array_value_recursive($key, array $arr){
	$val = array();
	array_walk_recursive($arr, function($v, $k) use($key, &$val){
		if($k == $key) array_push($val, $v);
	});
	return count($val) > 1 ? $val : array_pop($val);
}
//******************************************************************
// 2. Compare the date input of client / user
//$FromDate = "2014-03-01";
//$ToDate = "2014-03-03";
function FirstCompareDates($SenStart, $SenEnd, $uStart, $uEnd){
	$boolOut = "FALSE";
	if((strtotime($uStart) >= strtotime($SenStart))&&(strtotime($uEnd) <= strtotime($SenEnd))){
		$boolOut = "TRUE";
	}
	else {
		$boolOut = "FALSE";
	}
	return 	$boolOut;
}
// boolian output
function CompareDates($FromDate, $ToDate, $DateLimit){
	$boolOut = "FALSE";
	$StartLim = date("Y-m-d H:i:s",(time() - $DateLimit * 24 * 60 * 60));
	$EndLim =  date("Y-m-d H:i:s",(time()+ 1 * 24 * 60 * 60));
	if((strtotime($FromDate) >= (time() - $DateLimit * 24 * 60 * 60))&&(strtotime($FromDate) < time())){
		//echo "<br/>From date is OK ".$FromDate."<br/>"; 
		if(strtotime($ToDate) > (time()+ 1 * 24 * 60 * 60)){
			//echo "<br/>Date Should be between ".$StartLim." and ".$EndLim."<br/>";
			$boolOut = "FALSE";
		}
		else if((strtotime($ToDate) <= (time()+ 1 * 24 * 60 * 60)) && (strtotime($ToDate) > strtotime($FromDate))){
			//echo "<br/>Gr8 :) Proceed <br/>";
			$boolOut = "TRUE";
		}
		else {
			//echo "<br/>Date Should be between ".$StartLim." and ".$EndLim."<br/>";
			$boolOut = "FALSE";
		}
	}
	else {
		//echo "<br/>Date Should be between ".$StartLim." and ".$EndLim."<br/>";
		$boolOut = "FALSE";
	}
	return 	$boolOut;
}
// Implementation
//$CompareOutcome = CompareDates($FromDate, $ToDate, $DateLimit);
//echo $CompareOutcome;
// To select only real observations and avoid adc's
function selectRealObsProp($prop){
	$bookList = array();
	$aa = 0;
	for ($i=0; $i<sizeof($prop); $i++){
		//$prop[$i]
		if (strlen($prop[$i])>4){
			$bookList[$aa]=$prop[$i];
			$aa = $aa+1;
		}
	}
	return $bookList;
}
// This issue can be resolved with Ontology / triple store based system
// Hardcoaded to combine respectice procedures and obs. properties 
function combineProcNobsProp($prop,$proc){
	$bookList = array();
	$aa=0;
	for($i=0; $i<sizeof($prop); $i++){
		for($j=0; $j<sizeof($proc); $j++){
			if($prop[$i]=='air_temperature' && $proc[$j]=='temp_1'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			if($prop[$i]=='air_temperature2' && $proc[$j]=='temp_2'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			if($prop[$i]=='humidity' && $proc[$j]=='hum_bs_1'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			if($prop[$i]=='humidity2' && $proc[$j]=='hum_bs_2'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}			
			if($prop[$i]=='soil_moisture' && $proc[$j]=='soil_moist_1'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}	
			if($prop[$i]=='soil_moisture2' && $proc[$j]=='soil_moist_2'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			if($prop[$i]=='soil_temperature' && $proc[$j]=='soil_temp_1'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			if($prop[$i]=='soil_temperature2' && $proc[$j]=='soil_temp_2'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			if($prop[$i]=='rainfall' && $proc[$j]=='rain_1'){
				$bookList[$aa]['proc']=$proc[$j];
				$bookList[$aa]['prop']=$prop[$i];
				$aa = $aa+1;
			}
			
		}
	}
	return $bookList;
}
//
//******************************************************************
// 3. Generate XML consisting sensor Lat Lon for showing on map 
	//
function parseToXML($htmlStr){ 
		$xmlStr=str_replace('<','&lt;',$htmlStr); 
		$xmlStr=str_replace('>','&gt;',$xmlStr); 
		$xmlStr=str_replace('"','&quot;',$xmlStr); 
		$xmlStr=str_replace("'",'&#39;',$xmlStr); 
		$xmlStr=str_replace("&",'&amp;',$xmlStr); 
	return $xmlStr; 
}
//
function getMyxml($sensorDetails){
		// Start XML file, echo parent node
		$sxe = new SimpleXMLElement('<markers/>');
		for($i=0; $i<sizeof($sensorDetails); $i++){
		  // ADD TO XML DOCUMENT NODE
		  $sense=$sxe->addChild('marker');
		  // adding attribute to above child
		  $sense->addAttribute('ocount', $sensorDetails[$i]['ocount'] );
		  $sense->addAttribute('davg', $sensorDetails[$i]['davg'] );
		  $sense->addAttribute('proc', $sensorDetails[$i]['proc'] );
		  $sense->addAttribute('lat', $sensorDetails[$i]['lat'] );
		  $sense->addAttribute('lon', $sensorDetails[$i]['lon'] );
		  $sense->addAttribute('type', $sensorDetails[$i]['type'] );
		  $sense->addAttribute('obsprop', $sensorDetails[$i]['obsprop'] );
		}
		// End XML file
		$newSTR = $sxe->asXML();
		// echo $newSTR;
		$xml_file = '..//tmp//marker_'.date('m-d-Y_hia').'.xml';
		
		//session_start();
		//$_SESSION['xml_file'] = $xml_file; //to store XML file name as a session var
		
		$fh = fopen($xml_file, 'w') or die();
		fwrite($fh, $newSTR);
		fclose($fh);
		return $xml_file;
}
// 
function genXML($sensorDetails){
	//header("Content-type: text/xml");
	// Start XML file, echo parent node
	$data = "<?xml version='1.0'?> \n";
	$data = '<markers>';
		
	for($i=0; $i<sizeof($sensorDetails); $i++){
		$data = $data .'<marker ';
		$data = $data . 'ocount="' . $sensorDetails[$i]['ocount'] . '" ';
		$data = $data . 'davg="' . $sensorDetails[$i]['davg'] . '" ';
		$data = $data . 'proc="' . $sensorDetails[$i]['proc'] . '" ';
		$data = $data . 'lat="' . $sensorDetails[$i]['lat'] . '" ';
		$data = $data . 'lon="' . $sensorDetails[$i]['lon'] . '" ';
		$data = $data . 'type="' . $sensorDetails[$i]['type'] . '" ';
		$data = $data . 'obsprop="' . $sensorDetails[$i]['obsprop'] . '" ';
		$data = $data . '/>';
	}
	// End XML file
	$data = $data . '</markers>';
	return $data;
}
//
function getBaseStat($SenData){
	$dataSize = sizeof($SenData);
	$dataStat = array();
	if ($dataSize>0){
		$valS = 0;
		$myMin = 1000;
		$myMax = 0;
		for ($i=0; $i<$dataSize; $i++){
			$sepVars = explode(",",$SenData[$i]);
			$valS=$valS+ $sepVars[1];
			if($myMin > $sepVars[1]){
				$myMin = $sepVars[1];
			}
			if($myMax < $sepVars[1]){
				$myMax = $sepVars[1];
			}
		}
		$dAvg = round(($valS/$dataSize),2);
		$dataStat['sum'] = round($valS,2);
		$dataStat['avg'] = round($dAvg,2);
		$dataStat['min'] = round($myMin,2);
		$dataStat['max'] = round($myMax,2);
	}
	return $dataStat;
}
// Hardcode 
function extractForXML($rawSenData){
	$dataHere = array();
	$aa=0;
	$ThisData = "";
	$ThisLatLon = "";
	for($i=0; $i<sizeof($rawSenData); $i++){
			if($i<1){
				$ThisData = "Parameter : ".$rawSenData[$i]['Sensor'];
				$ThisData = $ThisData.", Start Date : ".$rawSenData[$i]['Start Date'];
				$ThisData = $ThisData.", End Date : ".$rawSenData[$i]['End Date'];
				$ThisData = $ThisData.", Average : ".$rawSenData[$i]['Average'];
				$ThisData = $ThisData.", Sum : ".$rawSenData[$i]['Sum'];
				$ThisData = $ThisData.", Minimum : ".$rawSenData[$i]['Minimum'];
				$ThisData = $ThisData.", Maximum : ".$rawSenData[$i]['Maximum']."@";
				//
				$ThisLatLon = "lat : ".$rawSenData[$i]['lat'];
				$ThisLatLon = $ThisLatLon.", lon : ".$rawSenData[$i]['lon']."@";
				}
			else if ($i == (sizeof($rawSenData)-1)){
				$ThisData = $ThisData."Parameter : ".$rawSenData[$i]['Sensor'];
				$ThisData = $ThisData.", Start Date : ".$rawSenData[$i]['Start Date'];
				$ThisData = $ThisData.", End Date : ".$rawSenData[$i]['End Date'];
				$ThisData = $ThisData.", Average : ".$rawSenData[$i]['Average'];
				$ThisData = $ThisData.", Sum : ".$rawSenData[$i]['Sum'];
				$ThisData = $ThisData.", Minimum : ".$rawSenData[$i]['Minimum'];
				$ThisData = $ThisData.", Maximum : ".$rawSenData[$i]['Maximum'];
				//
				$ThisLatLon = $ThisLatLon."lat : ".$rawSenData[$i]['lat'];
				$ThisLatLon = $ThisLatLon.", lon : ".$rawSenData[$i]['lon'];
			}
			else{
				$ThisData = $ThisData."Parameter : ".$rawSenData[$i]['Sensor'];
				$ThisData = $ThisData.", Start Date : ".$rawSenData[$i]['Start Date'];
				$ThisData = $ThisData.", End Date : ".$rawSenData[$i]['End Date'];
				$ThisData = $ThisData.", Average : ".$rawSenData[$i]['Average'];
				$ThisData = $ThisData.", Sum : ".$rawSenData[$i]['Sum'];
				$ThisData = $ThisData.", Minimum : ".$rawSenData[$i]['Minimum'];
				$ThisData = $ThisData.", Maximum : ".$rawSenData[$i]['Maximum']."@";
				//
				$ThisLatLon = $ThisLatLon."lat : ".$rawSenData[$i]['lat'];
				$ThisLatLon = $ThisLatLon.", lon : ".$rawSenData[$i]['lon']."@";
			}			
		//$dataHere[$i] = $ThisData;
	}
	$dataHere[0] = $ThisData;
	$dataHere[1] = $ThisLatLon;
	//sample output:  [0] => Sensor : air_temperature, Start Date : 2015-02-01T00:00:00+05:30, End Date : 2015-02-01T16:01:53+05:30, Average : 22.12, Sum : 1305.18, Minimum : 9.023438, Maximum : 34.160156, lat : 75, lon : 21
	$dataHere = getItRight($dataHere);
	return $dataHere;
}
//
function getItRight($myDtStr){
	$ArrR = array();
	// Seperate Lat Lon
	$latLon = array();
	$some = explode('@',$myDtStr[1]);
	$aa = sizeof($some)-1;
	$some = explode(',',$some[$aa]);
	$inTrim = explode(' : ',$some[0]);
	$latLon[0] = $inTrim[1];
	$inTrim = explode(' : ',$some[1]);
	$latLon[1] = $inTrim[1];
	//
	$some = explode('@',$myDtStr[0]);
	for($i=0; $i<sizeof($some); $i++){
		$ArrR[0][$i] = $some[$i];
	}
	//
	//$ArrR[0]= $myDtStr[0];
	$ArrR[1]= $latLon;
	return $ArrR;
}
//
function genSenXML($sDetails){
	// Sample: Array ( 	[0] => 
	//				Array ( [0] => Sensor : air_temperature, Start Date : 2015-02-01T00:00:00+05:30, End Date : 2015-02-01T16:49:56+05:30, Average : 22.66, Sum : 1405.08, Minimum : 9.02, Maximum : 34.16 
	//						[1] => Sensor : air_temperature2, Start Date : 2015-02-01T00:00:00+05:30, End Date : 2015-02-01T16:49:56+05:30, Average : 20.9, Sum : 1295.51, Minimum : 6.45, Maximum : 35.45 
	//						[2] => Sensor : humidity, Start Date : 2015-02-01T00:00:00+05:30, End Date : 2015-02-01T16:49:56+05:30, Average : 45.35, Sum : 2811.98, Minimum : 28.63, Maximum : 59.7 ) 
	//					[1] => 
	//				Array ( [0] => 21.454517 [1] => 78.155118 ) ) 
	//header("Content-type: text/xml");
	// Start XML file, echo parent node
	$data = "<?xml version='1.0'?> \n";
	//$data = '<markers>';
	$data = '<marker ';
	for($i=0; $i<sizeof($sDetails[0]); $i++){
		$aj = $i+1;
		$data = $data . 'sensor'.$aj.'="' . $sDetails[0][$i]. '" ';
	}
	$data = $data . 'lat="' . $sDetails[1][0] . '" ';
	$data = $data . 'lon="' . $sDetails[1][1] . '" ';
	$data = $data . 'type="admin" ';
	$data = $data . '/>';
	// End XML file
	//$data = $data . '</markers>';
	return $data;
}
//******************************************************************
?>	