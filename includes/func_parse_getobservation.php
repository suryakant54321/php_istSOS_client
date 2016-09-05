<?PHP
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Functions to parse GetObservation response XML.
//----------------------------------------------------------
include_once('settings.php');
//a. 
function parse_GetObs($output){
	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
		// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			if($xmlReader->localName == 'values'){
				// move to its textnode / child
				$xmlReader->read();
				$bookList[$i]['values'] = $xmlReader->value;
				$i=$i+1;
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
// b. 
function format_out($dFormat,$cleanOut){
	$FormOut=array();
	if ($dFormat=='TarraY'||$dFormat=='csv'||$dFormat=='table'||$dFormat=='TnV'){
		$myOutNow = explode('@',$cleanOut[0]['values']);
		$FormTmp = array();
		//print_r($myOutNow[0]);
		for($i=0; $i<sizeof($myOutNow); $i++){
			$MoreClean = explode(',',$myOutNow[$i]); 
			$FormTmp[$i]['time']=$MoreClean[0];
			$FormTmp[$i]['value']=$MoreClean[1];
		}
			if($dFormat=='TarraY'){
				$FormOut=$FormTmp;
			}
			else if($dFormat=='csv'){
				$FormOut=asCSVStr($FormTmp);
			}
			else if($dFormat=='table'){
				$FormOut=asTable($FormTmp);
			}
			else if($dFormat == 'TnV'){
				$FormOut = keepTime($FormTmp);
			}
	}
	else {
		$FormOut = $cleanOut;
	}
	return $FormOut;
}
// b.1 formats array data into CSV String
function asCSVStr($FormTmp){
	$myCSVStr="timestamp,value";
	for($i=0; $i<sizeof($FormTmp);$i++){
		$NewTmp = implode(',',$FormTmp[$i]);
		$myCSVStr = $myCSVStr."\n".$NewTmp;
	}
	return $myCSVStr; 
}
// b.2 formats array data into HTML table
function asTable($FormTmp){
	$myTableStr = "<table border=1> <tr><th>TimeStamp</th><th>Value</th></tr>";
	for($i=0; $i<sizeof($FormTmp);$i++){
		$TabLine = "<tr><td>".$FormTmp[$i]['time']."</td><td>".$FormTmp[$i]['value']."</td></tr>";
		$myTableStr = $myTableStr."\n".$TabLine;
	}
	$myTableStr = $myTableStr."</table>";
	return $myTableStr;
}
// b.n removes date and keeps only time and value 
function keepTime($FormTmp){
	$myNewForm = array();
	for($i=0; $i<sizeof($FormTmp);$i++){
		$TimeForm = explode('T',$FormTmp[$i]['time']);
		$TimeForm = explode('+',$TimeForm[1]);
		$myNewForm[$i]['time'] = $TimeForm[0];
		$myNewForm[$i]['value'] = $FormTmp[$i]['value'];
	}
	return $myNewForm;
}
// c. form two CSV arrays 
function TwoArrays($DataArray){
	$combineA=array();
	$timeA = array();
	$valueA = array();
	for ($i=0; $i<sizeof($DataArray); $i++){
		$timeA[$i] = $DataArray[$i]['time'];
		$valueA[$i] = $DataArray[$i]['value'];
	}
	$combineA[0]=$timeA;
	$combineA[1]=$valueA;
	return ($combineA);
}
//
function QuotedXdata($formatOut){
	$QuotedOp = "'";
	for($i=0; $i<(sizeof($formatOut)); $i++){
		$aa=$i+1;
		if($aa==sizeof($formatOut)){
			$QuotedOp = $QuotedOp.$formatOut[$i]."'";
		}
		else{
			$QuotedOp = $QuotedOp.$formatOut[$i]."','";
		}
	}
	return $QuotedOp;
}
// to thin array by perticular number
function arrayThin($formatOut, $GapValue){
	$thinOut=array();
	$aa=0;
	$bb=0;
	$some="' '";
	for($i=0;$i<sizeof($formatOut);$i++){
		if($aa<10){
			$aa=$aa+1;
			$thinOut[$i]=$some;
		}
		else{
			$aa=0;
			$thinOut[$i]=$formatOut[$i];
			$bb=$bb+1;
		}
	}
	return $thinOut;
}
?>