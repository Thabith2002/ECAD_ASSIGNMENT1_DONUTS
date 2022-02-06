<?php
$PayPalMode 		= 'sandbox';//sandbox or live
$PayPalApiUsername 	= 'sb-roljx8773164_api1.business.example.com'; 		//PayPal API Username
$PayPalApiPassword 	= 'YNC2JCBZB5SSRBTK'; 		//Paypal API password
$PayPalApiSignature = 'A7QQrEkquDbSg52pWKYXyAxP2oQjANoohrA8NooUahUPnc0Uh5XBoLUi'; 		//Paypal API Signature
$PayPalCurrencyCode = 'SGD'; 	//Paypal Currency Code
//URL to redirect to after PayPal has complete the online payment
$PayPalReturnURL 	= 'http://localhost:8081/ECAD_ASSIGNMENT1_DONUTS/checkoutProcess.php'; 
//URL to redirect to if user clicks cancel                
$PayPalCancelURL 	= 'http://localhost:8081/ECAD_ASSIGNMENT1_DONUTS/checkOut.php'; 
                	
function PPHttpPost($methodName_, $nvpStr_, $PayPalApiUsername, $PayPalApiPassword, 
                    $PayPalApiSignature, $PayPalMode) {
	// Set up your API credentials, PayPal end point, and API version.
	$API_UserName = urlencode($PayPalApiUsername);
	$API_Password = urlencode($PayPalApiPassword);
	$API_Signature = urlencode($PayPalApiSignature);
			
	if($PayPalMode=='sandbox'){
		$paypalmode = '.sandbox';
	}
	else {
		$paypalmode = '';
	}
	
	$API_Endpoint = "https://api-3t".$paypalmode.".paypal.com/nvp";
	$version = urlencode('98');
		
	// php_curl is a library that allows you to connect and communicate to many  
	// different types of servers with many different types of protocols. e.g, 
	// http, https, ftp, gopher, telnet, dict, file, and ldap protocols. 
	// libcurl also supports HTTPS certificates, HTTP POST, HTTP PUT, FTP uploading, 
	// HTTP form based upload, proxies, cookies, and user+password authentication. 
			
	// Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);			
			
	// Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);	
			
	// Set the API operation, version, and API signature in the request.	
	$nvpreq = "METHOD=$methodName_&VERSION=$version";
	$nvpreq .= "&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";
			
	// Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
		
	// Get response from the server.
	$httpResponse = curl_exec($ch);
		
	if(!$httpResponse) {
		exit("$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')');
	}
		
	// Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);
		
	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if(sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}
		
	if((sizeof($httpParsedResponseAr) == 0) || 
	   (!array_key_exists('ACK', $httpParsedResponseAr))) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}
		
	return $httpParsedResponseAr;
}
?>