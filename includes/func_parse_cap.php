<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Functions to parse GetCapabilities response XML.
//----------------------------------------------------------
include_once('settings.php');
//1. gives output array of all bounding box lat and lon 
function cap_parse_LL($output){
	//print_r($output);
	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
			// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			if($xmlReader->localName == 'boundedBy') {
				$bookList[$i]['boundedBy'] = $xmlReader->getAttribute('srsName');
			}
			if($xmlReader->localName == 'coordinates') {
				// move to its textnode / child
				$xmlReader->read();
				$bookList[$i]['mixlatlon'] = $xmlReader->value;
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
//2. gives output array of service provider
function cap_parse_service($output, $details){
	//print_r($output);

	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
			// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			
			//if($xmlReader->localName == 'ServiceIdentification') {
			//	$bookList[$i]['ServiceIdentification'] = $xmlReader->getAttribute('srsName');
			//}
			for ($aa=0; $aa<sizeof($details); $aa++){
				$me = $details[$aa];
				if($xmlReader->localName == $me) {
					// move to its textnode / child
					$xmlReader->read();
					$bookList[$i][$me] = $xmlReader->value;
				}
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
// 2.1
function getServHTML($cap, $details){
	$ServHtml = "<table border=1>";
	for($i=0; $i<(sizeof($details));$i++){
		$term = $details[$i];
		$DataCol = $cap[0][$term];
		$ServHtml = $ServHtml."<tr><th>".$term."</th><td>".$DataCol."</td></tr>";
	}
	$ServHtml = $ServHtml."</table>";
	return $ServHtml;
}
//3. a. offering c. procedure d. observedProperty e. featureOfInterest
function getOffering($output, $reference){
	//print_r($output);
	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
		// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			if($xmlReader->localName == $reference[0]) {
				$bookList[$i][$reference[0]] = $xmlReader->getAttribute($reference[1]);
				$i=$i+1;
			}	
		}
	}
	//print_r($bookList);
	return $bookList;
}
//3. b. time
function getTime($output, $reference){
	//print_r($output);
	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
		// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			if($xmlReader->localName == $reference) {
				$xmlReader->read();
				$bookList[$i][$reference] = $xmlReader->value;
				$i=$i+1;
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
// 4. get clean procedure as array
function getCleanProc($myProcedure){
	$CleanProc = array();
	// divide by 2 as there are two services
	for($i=0; $i<(sizeof($myProcedure)/2); $i++){
		$CleanProc[$i] = $myProcedure[$i]['procedure'];
	}
	return $CleanProc;
}
// 5. clean offering as array
function cleanOff($myOffering){
	$ServHtml = "<table border=1>";
	$cleanOff = array();
	for($i=0; $i<(sizeof($myOffering)/2); $i++){
		$cleanOff[$i] = $myOffering[$i]['ObservationOffering'];
		$ServHtml = $ServHtml."<tr><th>ObservationOffering: </th><td>".$myOffering[$i]['ObservationOffering']."</td></tr>";
	}
	$ServHtml = $ServHtml."</table>";
	return $ServHtml;
	//return $cleanOff;
}
// 6. 
function getObsRange($myBeginTime, $myEndTime, $timePosition){
	$ServHtml = "<table border=1>";
	$ServHtml = $ServHtml."<tr><th>".$timePosition[0]."</th><td>".$myBeginTime[0][$timePosition[0]]."</td></tr>";
	$ServHtml = $ServHtml."<tr><th>".$timePosition[1]."</th><td>".$myEndTime[0][$timePosition[1]]."</td></tr>";	
	$ServHtml = $ServHtml."</table>";
	return $ServHtml;
}
// 7. 
function getHTMLProc($myProcedure, $procedure){
	$ServHtml = "<table border=1>";
	$ServHtml = $ServHtml."<tr><th>".$procedure[0]."</th></tr>";
	for($i=0; $i<sizeof($myProcedure);$i++){
		$ServHtml = $ServHtml."<tr><td>".$myProcedure[$i]."</td></tr>";
	}
	$ServHtml = $ServHtml."</table>";
	return $ServHtml;
}
// 8 
function CleanOffering($myObservedProperty, $observedProperty){
	$obsPropOut = array();
	$ObsProp = $observedProperty[0];
	for($i=0; $i<(sizeof($myObservedProperty)/2); $i++){
		$obsPropOut[$i] = $myObservedProperty[$i][$ObsProp]; 
	}
	return $obsPropOut;
}
// 9
function getOnlyOffering($myObservedProperty){
	$tmpProp = array();
	for($i=0; $i<sizeof($myObservedProperty); $i++){
		$someProp = explode(':',$myObservedProperty[$i]);
		$tmpProp[$i]=$someProp[9];
	}
	return $tmpProp;
}
// 10
function getOnlyFOI($myFeatureOfInterest){
	$tmpProp = array();
	for($i=0; $i<sizeof($myFeatureOfInterest); $i++){
		$someProp = explode(':',$myFeatureOfInterest[$i]);
		$tmpProp[$i]=$someProp[7];
	}
	return $tmpProp;
}
// 11
function getTimeSeperated($timeVar){
	$myOutTime=array();
	$myVar = explode('T',$timeVar);
	$myOutTime[0]=$myVar[0];
	$myVar = explode('+',$myVar[1]);
	$myOutTime[1]=$myVar[0];
	$myOutTime[2]=$myVar[1];
	return $myOutTime;
}
// 12
function SplitObsProp($myObservedProperty){
	$outObsProp=array();
	$aa=0;
	for($i=0;$i<sizeof($myObservedProperty);$i++){
		if($i<8){
			$outObsProp[0][$i]=$myObservedProperty[$i];
		}
		else{
			$outObsProp[1][$aa]=$myObservedProperty[$i];
			$aa=$aa+1;
		}
	}
	return $outObsProp;
}
// 13 Hard Coaded
function realignSensors($mySplitObsProp){
	$myReAlignOut = array();
	
	$myReAlignOut[0][6] = $mySplitObsProp[0][0];
	$myReAlignOut[0][4] = $mySplitObsProp[0][1];
	$myReAlignOut[0][0] = $mySplitObsProp[0][2];
	$myReAlignOut[0][2] = $mySplitObsProp[0][3];
	$myReAlignOut[0][7] = $mySplitObsProp[0][4];
	$myReAlignOut[0][5] = $mySplitObsProp[0][5];
	$myReAlignOut[0][1] = $mySplitObsProp[0][6];
	$myReAlignOut[0][3] = $mySplitObsProp[0][7];
	
	$myReAlignOut[1][6] = $mySplitObsProp[1][0];
	$myReAlignOut[1][7] = $mySplitObsProp[1][1];
	$myReAlignOut[1][0] = $mySplitObsProp[1][2];
	$myReAlignOut[1][1] = $mySplitObsProp[1][3];
	$myReAlignOut[1][2] = $mySplitObsProp[1][4];
	$myReAlignOut[1][3] = $mySplitObsProp[1][5];
	$myReAlignOut[1][4] = $mySplitObsProp[1][6];
	$myReAlignOut[1][5] = $mySplitObsProp[1][7];
	return $myReAlignOut;
}
?>