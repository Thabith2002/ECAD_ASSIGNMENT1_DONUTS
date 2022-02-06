<?php
session_start();
include("header.php"); // Include the Page Layout header
include_once("myPayPal.php"); // Include the file that contains PayPal settings
include_once("mysql_conn.php"); 


if($_POST) //Post Data received from Check Out page.
{	
	$_SESSION['msg'] = $_POST["message"];
	$_SESSION['SubTotal'] = round($_SESSION['SubTotal'],2);
	$_SESSION['TaxValue'] = round($_SESSION['TaxValue'],2);
	$_SESSION['DeliveryPrice'] = round($_SESSION['DeliveryPrice'],2);
	$_SESSION['Discount'] = round($_SESSION['Discount'],2);
	$_SESSION['TotalPaymentWGST'] = round($_SESSION['TotalPaymentWGST'],2);

	// To Do 6 (DIY): Check to ensure each product item saved in the associative
	//                array is not out of stock
	foreach($_SESSION['Items'] as $key=>$item) 
		{
		$quantity = $item["quantity"];
		$productId = $item["productId"];
		$qry = "SELECT Quantity FROM product WHERE ProductID=$productId;";
		$stmt = $conn->prepare($qry);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		if ($result->num_rows > 0){ //Selected products exists  in shopping cart
			while ($row = $result->fetch_array()){
				//check for sufficient quantity
				if($quantity > $row["Quantity"]){
					echo "Product $item[productId] : $item[name] is out of stock!<br />";
					echo "<p>Please return to ";
					echo '<a href="shoppingCart.php">Shopping Cart</a>';
					echo " to amend your purchase.</p>";

					include("footer.php");
					exit;
				}
			}
		}
	}
	// End of To Do 6
	
	$paypal_data = '';
	// Get all items from the shopping cart, concatenate to the variable $paypal_data
	// $_SESSION['Items'] is an associative array
	foreach($_SESSION['Items'] as $key=>$item) {
		$paypal_data .= '&L_PAYMENTREQUEST_0_QTY'.$key.'='.urlencode($item["quantity"]);
	  	$paypal_data .= '&L_PAYMENTREQUEST_0_AMT'.$key.'='.urlencode($item["price"]);
	  	$paypal_data .= '&L_PAYMENTREQUEST_0_NAME'.$key.'='.urlencode($item["name"]);
		$paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER'.$key.'='.urlencode($item["productId"]);
	}
	
	
	
	//Data to be sent to PayPal
	$padata = '&CURRENCYCODE='.urlencode($PayPalCurrencyCode).
			  '&PAYMENTACTION=Sale'.
			  '&ALLOWNOTE=1'.
			  '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode).
			  '&PAYMENTREQUEST_0_AMT='.urlencode($_SESSION['TotalPaymentWGST']).
			  '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($_SESSION["SubTotal"]). 
			  '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($_SESSION["DeliveryPrice"]). 
			  '&PAYMENTREQUEST_0_TAXAMT='.urlencode($_SESSION["TaxValue"]). 	
			  '&BRANDNAME='.urlencode("Donut Shop").
			  $paypal_data.				
			  '&RETURNURL='.urlencode($PayPalReturnURL ).
			  '&CANCELURL='.urlencode($PayPalCancelURL);	
		
	//We need to execute the "SetExpressCheckOut" method to obtain paypal token
	$httpParsedResponseAr = PPHttpPost('SetExpressCheckout', $padata, $PayPalApiUsername, 
	                                   $PayPalApiPassword, $PayPalApiSignature, $PayPalMode);
		
	//Respond according to message we receive from Paypal
	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
	   "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {					
		if($PayPalMode=='sandbox')
			$paypalmode = '.sandbox';
		else
			$paypalmode = '';
				
		//Redirect user to PayPal store with Token received.
		$paypalurl ='https://www'.$paypalmode. 
		            '.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token='.
					$httpParsedResponseAr["TOKEN"].'';
		header('Location: '.$paypalurl);
	}
	else {
		//Show error message
		echo "<div style='color:red'><b>SetExpressCheckOut failed : </b>".
		      urldecode($httpParsedResponseAr["L_LONGMESSAGE0"])."</div>";
		echo "<pre>".print_r($httpParsedResponseAr)."</pre>";
	}
}

//Paypal redirects back to this page using ReturnURL, We should receive TOKEN and Payer ID
if(isset($_GET["token"]) && isset($_GET["PayerID"])) 
{	
	//we will be using these two variables to execute the "DoExpressCheckoutPayment"
	//Note: we haven't received any payment yet.
	$token = $_GET["token"];
	$playerid = $_GET["PayerID"];
	$paypal_data = '';
	
	// Get all items from the shopping cart, concatenate to the variable $paypal_data
	// $_SESSION['Items'] is an associative array
	foreach($_SESSION['Items'] as $key=>$item) 
	{
		$paypal_data .= '&L_PAYMENTREQUEST_0_QTY'.$key.'='.urlencode($item["quantity"]);
	  	$paypal_data .= '&L_PAYMENTREQUEST_0_AMT'.$key.'='.urlencode($item["price"]);
	  	$paypal_data .= '&L_PAYMENTREQUEST_0_NAME'.$key.'='.urlencode($item["name"]);
		$paypal_data .= '&L_PAYMENTREQUEST_0_NUMBER'.$key.'='.urlencode($item["productId"]);
	}
	
	//Data to be sent to PayPal
	$padata = '&TOKEN='.urlencode($token).
			  '&PAYERID='.urlencode($playerid).
			  '&PAYMENTREQUEST_0_PAYMENTACTION='.urlencode("SALE").
			  $paypal_data.	
			  '&PAYMENTREQUEST_0_ITEMAMT='.urlencode($_SESSION["SubTotal"]).
              '&PAYMENTREQUEST_0_TAXAMT='.urlencode($_SESSION["TaxValue"]).
              '&PAYMENTREQUEST_0_SHIPPINGAMT='.urlencode($_SESSION["DeliveryPrice"]).
			  '&PAYMENTREQUEST_0_AMT='.urlencode($_SESSION['TotalPaymentWGST']).
			  '&PAYMENTREQUEST_0_CURRENCYCODE='.urlencode($PayPalCurrencyCode);
	
	//We need to execute the "DoExpressCheckoutPayment" at this point 
	//to receive payment from user.
	$httpParsedResponseAr = PPHttpPost('DoExpressCheckoutPayment', $padata, 
	                                   $PayPalApiUsername, $PayPalApiPassword, 
									   $PayPalApiSignature, $PayPalMode);
	
	//Check if everything went ok..
	if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
	   "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
	{
		// To Do 5 (DIY): Update stock inventory in product table 
		//                after successful checkout
		foreach($_SESSION['Items'] as $key=>$item) 
		{
		$quantity = $item["quantity"];
		$productId = $item["productId"];

		$qry = "UPDATE product SET Quantity = Quantity - $quantity WHERE ProductID=$productId";
		$stmt = $conn->prepare($qry);
		$stmt->execute();
		$stmt->close();
		}
	
		// End of To Do 5
	
		// To Do 2: Update shopcart table, close the shopping cart (OrderPlaced=1)
		$total = $_SESSION['TotalPaymentWGST'];
		$qry = "UPDATE shopcart SET
				OrderPlaced=1, 
				Quantity=$_SESSION[NumCartItem],
				SubTotal=$_SESSION[SubTotal], 
				Tax=$_SESSION[TaxValue], 
				ShipCharge=$_SESSION[DeliveryPrice], 
				Discount=$_SESSION[Discount], 
				Total=$_SESSION[TotalPaymentWGST] 
				WHERE ShopCartID=$_SESSION[Cart]";
		$stmt = $conn->prepare($qry);
		$stmt->execute();
		$stmt->close();
		// End of To Do 2
		
		//We need to execute the "GetTransactionDetails" API Call at this point 
		//to get customer details
		$transactionID = urlencode(
		                 $httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
		$nvpStr = "&TRANSACTIONID=".$transactionID;
		$httpParsedResponseAr = PPHttpPost('GetTransactionDetails', $nvpStr, 
		                                   $PayPalApiUsername, $PayPalApiPassword, 
										   $PayPalApiSignature, $PayPalMode);

		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || 
		   "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) 
		   {
			//gennerate order entry and feed back orderID information
			//You may have more information for the generated order entry 
			//if you set those information in the PayPal test accounts.
			
			$ShipName = addslashes(urldecode($httpParsedResponseAr["SHIPTONAME"]));
			
			$ShipAddress = urldecode($httpParsedResponseAr["SHIPTOSTREET"]);
			if (isset($httpParsedResponseAr["SHIPTOSTREET2"]))
				$ShipAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOSTREET2"]);
			if (isset($httpParsedResponseAr["SHIPTOCITY"]))
			    $ShipAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOCITY"]);
			if (isset($httpParsedResponseAr["SHIPTOSTATE"]))
			    $ShipAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOSTATE"]);
			$ShipAddress .= ' '.urldecode($httpParsedResponseAr["SHIPTOCOUNTRYNAME"]). 
			                ' '.urldecode($httpParsedResponseAr["SHIPTOZIP"]);
				
			$ShipCountry = urldecode(
			               $httpParsedResponseAr["SHIPTOCOUNTRYNAME"]);
			
			$ShipEmail = urldecode($httpParsedResponseAr["EMAIL"]);	
					
				$qry = "INSERT INTO orderdata (DeliveryDate ,DeliveryTime,DeliveryMode,Message, ShipName, ShipAddress, ShipCountry,
										   ShipEmail, ShopCartID)
					VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
			$stmt = $conn->prepare($qry);
			// "i"-integer , "s" - string
			$stmt->bind_param("ssssssssi",$_SESSION["ShipDate"],$_SESSION["DeliveryTime"],$_SESSION["DeliveryMode"],$_SESSION["msg"], $ShipName, $ShipAddress, $ShipCountry, 
								$ShipEmail, $_SESSION["Cart"]);
			$stmt->execute();
			$stmt->close();
			$qry = "SELECT LAST_INSERT_ID() AS OrderID";
			$result = $conn->query($qry);
			$row = $result->fetch_array();
			$_SESSION["OrderID"] = $row["OrderID"];
				

				
			$conn->close();
				  
			// To Do 4A: Reset the "Number of Items in Cart" session variable to zero.
			$_SESSION["NumCartItem"] = 0;
			unset($_SESSION["NumCartItem"]);
	  		
			// To Do 4B: Clear the session variable that contains Shopping Cart ID.
			unset($_SESSION["Cart"]);
			
			// To Do 4C: Redirect shopper to the order confirmed page.
			header("Location: orderConfirmed.php");
			exit;
		} 
		else 
		{
		    echo "<div style='color:red'><b>GetTransactionDetails failed:</b>".
			                urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
			echo "<pre>".print_r($httpParsedResponseAr)."</pre>";
			$conn->close();
		}
	}
	else {
		echo "<div style='color:red'><b>DoExpressCheckoutPayment failed : </b>".
		                urldecode($httpParsedResponseAr["L_LONGMESSAGE0"]).'</div>';
		echo "<pre>".print_r($httpParsedResponseAr)."</pre>";
	}
}

include("footer.php"); // Include the Page Layout footer
?>