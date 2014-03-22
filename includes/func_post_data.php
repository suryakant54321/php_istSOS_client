<?PHP
include_once('settings.php');
//
function post_data($url, $xml_string){
	$url = $url;
	$theData = $xml_string;
	//
	$credentials = 'user@example.com:password';
	$header_array = array('Expect' => '',
					'From' => 'User A');
	$options = array(headers => $header_array,
					httpauthtype => HTTP_AUTH_BASIC,
					protocol => HTTP_VERSION_1_1);

	//create the httprequest object               
	$httpRequest_OBJ = new httpRequest($url, HTTP_METH_GET, $options);
	//add the content type
	$httpRequest_OBJ->setContentType = 'Content-Type: text/xml';
	//add the raw post data
	$httpRequest_OBJ->setBody($theData);
	//
	try {
		//send the http request
		$result = $httpRequest_OBJ->send()->getBody();
		$resp_code = $httpRequest_OBJ->getResponseCode();
		//print_r($result);
		if ($resp_code == 200){
			return $result;
		}
		//return $result;
	} catch (HttpException $ex) {
		echo $error;
	}
}
?>