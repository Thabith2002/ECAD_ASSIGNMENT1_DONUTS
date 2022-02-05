<?php 
session_start();

include("header.php"); // Include the Page Layout header

if (! isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}


echo "<div id='myShopCart' style='margin:auto'>"; // Start a container
if (isset($_SESSION["Items"])) {
	include_once("mysql_conn.php");

	if(! isset($_SESSION["DeliveryMode"])){
		$_SESSION["DeliveryMode"] = 'Normal';
	}
	if(! isset($_SESSION["DeliveryPrice"])){
		$_SESSION["DeliveryPrice"] = 2;
	}
	if(!isset($_SESSION["DeliveryTime"])){
		$_SESSION["DeliveryTime"] = '9am - 12 noon';
	}


	// To Do 1 (Practical 4): 
	// Retrieve from database and display shopping cart in a table
	echo "<p class='page-title' style='text-align:center'>Checkout</p>"; 
	echo "<p>Total Number of Donutted Donuts : --ANDERSON--</p>"; 
		
	$qry = "SELECT TaxRate FROM gst WHERE EffectiveDate=(SELECT MAX(EffectiveDate) FROM `gst` WHERE EffectiveDate < CURRENT_DATE)";
	$stmt = $conn->prepare($qry);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	while ($row = $result->fetch_array()){
		$_SESSION['Tax'] = $row['TaxRate'];
	}

	echo "<div class='card' style='width:30%; float:right'>
	<p>Total: $_SESSION[SubTotal] </p>";
	

	date_default_timezone_set('Asia/Singapore');

	$todaysDate = new DateTime('now');

	if($_SESSION['DeliveryMode']=='Normal'){
		$Normal = "checked";
		$todaysDate ->add(new DateInterval('P1D'));

	}
	else{
		$Normal = "";
	}

	if($_SESSION['DeliveryMode']=='Express'){
		$Express = "checked";
		$todaysDate ->add(new DateInterval('PT2H'));
	}
	else{
		$Express = "";

	}

	echo "
	<p> Delivery Type </p>
	<form action='cartFunctions.php' method='post'>

	<input type='radio' id='deliveryN' name='deliveryMode' value='Normal' onChange='this.form.submit()' $Normal>
	<label for='deliveryN'>Normal</label><br>
	<input type='radio' id='deliveryE' name='deliveryMode' value='Express' onChange='this.form.submit()' $Express>
	<label for='deliveryE'>Express</label><br>
	<input type='hidden' name='action' value='delivery' />

	</form>";

	if($_SESSION["DeliveryTime"] == '9am - 12 noon'){
		$Nine = "checked";
	}
	else{
		$Nine = "";

	}

	if($_SESSION["DeliveryTime"] == '12 noon - 3pm'){
		$Twelve = "checked";
	}
	else{
		$Twelve = "";

	}

	if($_SESSION["DeliveryTime"] == '3pm - 6pm'){
		$Three = "checked";
	}
	else{
		$Three = "";

	}


	echo "
	<p> Delivery Time </p>
	<form action='cartFunctions.php' method='post'>

	<input type='radio' id='deliveryNine' name='deliveryTime' value='Nine' onChange='this.form.submit()' $Nine>
	<label for='deliveryNine'>9 am - 12 Noon</label><br>
	<input type='radio' id='deliveryTwelve' name='deliveryTime' value='Twelve' onChange='this.form.submit()' $Twelve>
	<label for='deliveryTwelve'>12 Noon - 3 pm</label><br>
	<input type='radio' id='deliveryThree' name='deliveryTime' value='Three' onChange='this.form.submit()' $Three>
	<label for='deliveryTwelve'>3 pm - 6 pm</label><br>
	<input type='hidden' name='action' value='time' />

	</form>";

	$formattedPrice = number_format($_SESSION['DeliveryPrice'], 2);

	if ($_SESSION['IsWaived']==true && $_SESSION['DeliveryMode']=='Normal'){
		$TotalPayment = $_SESSION['SubTotal'];
	}
	else{
		$TotalPayment = ($_SESSION['SubTotal'] + $_SESSION['DeliveryPrice']);
	}


	$GST = ($TotalPayment/100) * $_SESSION['Tax'];

	$formattedGST = number_format($GST, 2);

	$TotalPaymentWGST = number_format($TotalPayment + $GST, 2);


	$Date = $todaysDate->format('d/m/y');


	echo "
	<p>Delivery Price: $$formattedPrice </p>
	<p>GST: S$formattedGST ($_SESSION[Tax]%)</p>	
	<p>Total Payment: $$TotalPaymentWGST</p> 
	<p>Estimated Delivery Date: $Date </p>
	<p>Time Slot: $_SESSION[DeliveryTime]</p>
	<label for='msgBox'>Message:</label>
	<textarea id='msgBox' rows'4' cols'50' placeholder='Write a message and we will deliver it!' style='font-family:Arial'></textarea>
	
	<form method='post' action='checkoutProcess.php'>
		<input type='image' style=';' src='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif'>
	</form>

	<p><a href='shoppingCart.php' ><button>Back to Cart</button></a></p>
	</div>";
	


	foreach($_SESSION['Items'] as $key=>$item){
		$qry = "SELECT ProductImage FROM Product WHERE ProductID=$item[productId]";
		$stmt = $conn->prepare($qry);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();

		$formattedPrice = number_format($item['price'], 2);

		while ($row = $result->fetch_array()){
			
			$img = "./Images/products/$row[ProductImage]";
			
		}
		echo "<div class='card' style='width:60%; float:left''>
					<div class='row'>
						<div class='col-sm-3' style='display: flex; align-items: center;'>
							<img src=$img style='width:100%; margin:10px;'/>
						</div>	
						<div class='col-sm-9'>
							<h4 style='margin-top:10px;'>$item[name]</h4>
							<span>Product ID: $item[productId]</span>
							<p>Price: $$formattedPrice</p>
							<p>Quantity: $item[quantity]</p>
						</div>
					</div>
				</div>";

	} 



		

	$conn->close(); // Close database connection

}
	

else {
	echo "<h3 style='text-align:center; color:red;'>Empty shopping cart!</h3>";
}
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>
