<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: Functions to parse DescribeSensor response XML.
//----------------------------------------------------------
include_once('settings.php');
//a. gives output array of all Quantaties and their values 
function parse_describeSense($output, $reference){
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
				//$xmlReader->read();
				//$i=$i+1;
			}
			if($xmlReader->localName == 'value'){
				// move to its textnode / child
				$xmlReader->read();
				$bookList[$i]['value'] = $xmlReader->value;
				$i=$i+1;
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
// clean quantity
function cleanQuantity($myQuantity){
	$qtyNvalue = array();
	$tmpQtyValue = array();
	$id=0;
	for($i=0;$i<sizeof($myQuantity); $i++){
		if(sizeof($myQuantity[$i])==1){
			//echo "<br/>size 1: ";
			//print_r($myQuantity[$i]);
		}
		else if(sizeof($myQuantity[$i])==2){
			//echo "<br/>size 2: ";
			$someQty = explode(':',$myQuantity[$i]['Quantity']);
			//print_r($someQty[6]);
			
			$tmpQtyValue[$id]['Quantity']=($someQty[6]);
			$tmpQtyValue[$id]['Value']=$myQuantity[$i]['value'];
			
			$id=$id+1;
		}
		else{
			//echo "<br/>size unknown: ";
			//print_r($myQuantity[$i]);
		}
	}
	$qtyNvalue = $tmpQtyValue;
	return $qtyNvalue;
	//print (sizeof($myQuantity));
}
// b. gives 
function getCoOrdinates($output, $reference){
	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
		// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			if($xmlReader->localName == $reference[0]) {
				$bookList[$i][$reference[1]] = $xmlReader->getAttribute($reference[1]);
				$bookList[$i][$reference[2]] = $xmlReader->getAttribute($reference[2]);
			}
			if($xmlReader->localName == 'coordinates'){
				// move to its textnode / child
				$xmlReader->read();
				$allCosNele = $xmlReader->value;
				$allCosNele = explode(',',$allCosNele);
				$bookList[$i]['coordinates']['lon'] = $allCosNele[0];
				$bookList[$i]['coordinates']['lat'] = $allCosNele[1];
				$bookList[$i]['coordinates']['elevation'] = $allCosNele[2];				
				$i=$i+1;
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
// *. 
function getInterval($output, $reference){
	$bookList = array();
	$i=0;
	$xmlReader = new XMLReader();
	$xmlReader->xml($output);
	while($xmlReader->read()) {
		// check to ensure nodeType is an Element not attribute or #Text 
		if($xmlReader->nodeType == XMLReader::ELEMENT) {
			if($xmlReader->localName == $reference){
				// move to its textnode / child
				$xmlReader->read();
				$allInterval = $xmlReader->value;
				$allInterval = explode(' ',$allInterval);
				$bookList[$i][$reference]['begin'] = $allInterval[0];
				$bookList[$i][$reference]['end'] = $allInterval[1];
				$i=$i+1;
			}
		}
	}
	//print_r($bookList);
	return $bookList;
}
?>