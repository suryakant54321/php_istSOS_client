<?php
// func_gen_urls.php
include_once('settings.php');
//*************************************************
// 1. Generate URL for GetCapabilities
function GetCapURL($ServiceURL, $CapString){
	$MyGetCapURL = $ServiceURL.$CapString;
	return $MyGetCapURL;
}
// Implementation of 1.
// print (GetCapURL($ServiceURL, $CapString));
//*************************************************
// 2. Generate URL for DescribeSensor
function GetDescribeSenURL ($DescribeSenStr, $procedure, $RespFormat){
	$DescribeSenOut = array();
	if(sizeof($procedure)>1){
		for($i=0; $i<sizeof($procedure); $i++){
			$DescribeSenOut[$i] = $DescribeSenStr.$procedure[$i].$RespFormat;
		}
	}
	else{
		$DescribeSenOut[0] = "NO URL"; 
	}
	return $DescribeSenOut;
}
// 2.1 Generate URL for DescribeSensor
function GetDescribeSenURL2 ($DescribeSenStr, $procedure, $RespFormat){
	$DescribeSenOut = "";
	if(strlen($procedure)>3){
			$DescribeSenOut = $DescribeSenStr.$procedure.$RespFormat;
	}
	else{
		$DescribeSenOut = "NO URL"; 
	}
	return $DescribeSenOut;
}
// Implementation of 2.
//$DSURLOut = GetDescribeSenURL ($DescribeSenStr, $procedure, $RespFormat);
//echo "<br/><br/>";
//print_r($DSURLOut);
//*************************************************
?>