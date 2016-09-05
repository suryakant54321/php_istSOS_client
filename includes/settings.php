<?php
//----------------------------------------------------------
// Author: Suryakant Sawant
// Date of last update: 05 Sept. 2016 
// Objective: All php-ISTSOS-Client settings.
//----------------------------------------------------------
// 1. Overall service configurations
$SOS_NAME_1 = "my sos 1";
$ServiceURL = "http://localhost/istsos/service_name";
//
$SOS_NAME_2 = "my sos 2";
$OtherService = "http://localhost/istsos/krishisos";
//
$SOS_NAME_20 = "my sos 2";
$OtherService2 = "http://localhost/istsos/krishisostwo";
//
$SOS_NAME_3 = "my sos 3";
$OtherService3 = "http://localhost/istsos/krishisosthree";
//
$SOS_NAME_4 = "my sos 4";
$OtherService4 = "http://localhost/istsos/krishisosfour";
// helps to sanitize URL
$phpSelf = filter_input(INPUT_SERVER, 'PHP_SELF', FILTER_SANITIZE_URL);

// Allow historical data
// TRUE / FALSE
$allowHistData = "FALSE";

// Date setting
date_default_timezone_set('Asia/Calcutta');

// List of working SOS url's 
$urlArray = array("http://localhost/istsos/krishisos", "http://localhost/istsos/krishisostwo");

// 2.  Service URL
// http://localhost/istsos/service_name?service=SOS&request=GetCapabilities
$CapString = "?service=SOS&request=GetCapabilities";//REQUEST=GetCapabilities&SERVICE=SOS&ACCEPTVERSIONS=1.0.0

// 3. Describe Sensor URL 
// http://localhost/istsos/service_name?service=SOS&request=describeSensor&procedure=hum_bs_1&responseFormat=text/xml;subtype='sensorML/1.0.0'&version=1.0.0
$DescribeSenStr = "?service=SOS&request=describeSensor&procedure=";
//$procedure = array('hum_bs_1','hum_bs_2','soil_moist_1','soil_moist_2');
$RespFormat = "&responseFormat=text/xml;subtype=%27sensorML/1.0.0%27&version=1.0.0";

// 4. Get Observation URL
// http://localhost/istsos/service_name?service=SOS&request=GetObservation&offering=temporary&procedure=temp_1&eventTime=2013-12-27T00:25:16.450706+0530/2013-12-28T00:25:16.450706+0530&observedProperty=air_temperature&responseFormat=text/xml;subtype=%27sensorML/1.0.0%27&service=SOS&version=1.0.0
$GetObsPre = "?service=SOS&request=GetObservation&offering=";
//$offeringName = "temporary";
$ProPre = "&procedure=";
//$ProName = "temp_1"; // can be array
$EvtTimePre = "&eventTime=";
//$StartTime = "2013-12-27T00:25:16.450706+0530";
$TimePre = "/";
//$EndTime = "2013-12-28T00:25:16.450706+0530";
$ObsProPre = "&observedProperty=";
//$ObsProp = "air_temperature"; // can be array 
$GetObsEnd = "&responseFormat=text/xml;subtype=%27sensorML/1.0.0%27&service=SOS&version=1.0.0";

// 5. Date limitation in days
$DateLimit = 365;

// 6. Error messsage
$error = "SOS not found for given URL";
$error1 = "No sensor Selected";
$error2 = "No sensor Data";
?>