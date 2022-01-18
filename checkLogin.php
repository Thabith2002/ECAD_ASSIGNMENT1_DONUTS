<?php
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php"); 

// Reading inputs entered in previous page
$email = $_POST["email"];
$pwd = $_POST["password"];

// To Do 1 (Practical 2): Validate login credentials with database

//Include the PHP file that establishes database connection handdle: $conn
include_once("mysql_conn.php");

if(($email != "") && ($pwd != "")){
	$qry = "SELECT * FROM Shopper WHERE Email = '$email' AND Password = '$pwd'"; //The SQL Query

	$result = $conn->query($qry); //The result of the query

	if ($result->num_rows > 0){ //Check if query returns any rows
		while ($row = $result->fetch_array()){
			$_SESSION["ShopperName"] = $row["Name"]; 
			$_SESSION["ShopperID"] = $row["ShopperID"]; //Set ShopperName and ShopperID accordingly
			
			// To Do 2 (Practical 4): Retrieve Shopping Cart with Uncheckout Items
			$shopperID = $_SESSION["ShopperID"];
			$qry2  = "SELECT ShopCartID FROM shopcart WHERE ShopperID = '$shopperID' AND OrderPlaced = 0; ";
			$result2 = $conn->query($qry2); //The result of the query
			
			if ($result2->num_rows > 0){ //Check if query returns any rows
				while ($row = $result2->fetch_array()){
					$_SESSION["Cart"] = $row["ShopCartID"];

					$shopCartID = 	$_SESSION["Cart"];
					$qry3 = "SELECT COUNT(*) AS 'NumCartItem' FROM shopcartitem WHERE ShopCartID ='$shopCartID'; ";
					$result3 = $conn->query($qry3); //The result of the query
					if ($result3->num_rows > 0){ //Check if query returns any rows
						while ($row = $result3->fetch_array()){
							$_SESSION["NumCartItem"] = $row["NumCartItem"];
						}
					}
				}
			}
			//Redirect to home page
			header("Location: index.php");
			exit;
		}
		
	}
	else {
		echo  "<h3 style='color:red'>Invalid Login Credentials</h3>";
	}
}

//Close database connection
$conn -> close();
	
// Include the Page Layout footer
include("footer.php");
?>