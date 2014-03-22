<?PHP
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
	$EndLim =  date("Y-m-d H:i:s",time());
	if((strtotime($FromDate) >= (time() - $DateLimit * 24 * 60 * 60))&&(strtotime($FromDate) < time())){
		//echo "<br/>From date is OK ".$FromDate."<br/>"; 
		if(strtotime($ToDate) > time()){
			//echo "<br/>Date Should be between ".$StartLim." and ".$EndLim."<br/>";
			$boolOut = "FALSE";
		}
		else if((strtotime($ToDate) <= time()) && (strtotime($ToDate) > strtotime($FromDate))){
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
//******************************************************************
// 3. Generate XML consisting sensor Lat Lon for showing on map 
	//
	function gen_XML($FOI_sensors){
		
		// Start XML file, echo parent node
		$sxe = new SimpleXMLElement('<markers/>');
		
		for($i=0; $i<sizeof($FOI_sensors); $i++){
		  // seperate latitute and longitude 
		  $lat_lon = $FOI_sensors[$i]['pos'];
		  $lat_lon_1 = explode (' ',$lat_lon);
		  //print_r($lat_lon_1);
		  
		  // ADD TO XML DOCUMENT NODE
		  $sense=$sxe->addChild('marker');
		  // adding attribute to above child
		  $sense->addAttribute('name', $FOI_sensors[$i]['SamplingPoint'] );
		  $sense->addAttribute('u_name', $FOI_sensors[$i]['name'] );
		  $sense->addAttribute('l_name', $FOI_sensors[$i]['name'] );
		  $sense->addAttribute('lat', $lat_lon_1['1'] );
		  $sense->addAttribute('lng', $lat_lon_1['0'] );
		  $sense->addAttribute('type', $FOI_sensors[$i]['name'] );
		  $sense->addAttribute('village', $FOI_sensors[$i]['name'] );
		  $sense->addAttribute('district', $FOI_sensors[$i]['name'] );
		}
		// End XML file
		$newSTR = $sxe->asXML();
		// echo $newSTR;
		$xml_file = '..\\tmp\\marker_'.date('m-d-Y_hia').'.xml';
		
		session_start();
		$_SESSION['xml_file'] = $xml_file; //to store XML file name as a session var
		
		$fh = fopen($xml_file, 'w') or die();
		fwrite($fh, $newSTR);
		fclose($fh);
	}
//******************************************************************
?>	