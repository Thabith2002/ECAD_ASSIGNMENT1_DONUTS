<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 90% width of viewport -->
<div style='width:90%; margin:auto;'>

<?php 
$pid=$_GET["pid"]; // Read Product ID from query string

// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");
$qry = "SELECT * from product where ProductID=?";
$stmt = $conn->prepare($qry);
$stmt->bind_param("i", $pid); 	// "i" - integer 
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// To Do 1:  Display Product information. Starting ....
while ($row = $result->fetch_array()){
    //Display Page Header -
    //Product's name is read from the "ProductTitle" column of "product" table.
    echo "<div class='row' >";
    echo "<div class='col-sm-12' style='padding:5px' >";
    echo "<span class='page-title'>$row[ProductTitle]</span>";

    $offer = $row["Offered"];
    $offerStart = $row["OfferStartDate"];
	$offerEnd = $row["OfferEndDate"];
	$todayDate = date("Y-m-d");
    if ($offer == '1' && $offerStart < $todayDate && $offerEnd > $todayDate) { //If the offer time period is within range of today's date
		echo "<button id='loginBtn' style='background:#ffac47; width:10%; border-radius:70px;' disabled>On Offer</button>"; //Display 'on offer' sign
		echo "</br>";
    }

    echo "</div>";
    echo "</div>";

    echo "<div class='row'>";// Start a new row

    //Left column - display a product's specifications,
    echo "<div class='col-sm-9' style='padding:5px' >";
    echo "<p>$row[ProductDesc]</p>";

    //Left column - display a product's specifications,
    $qry2 = "SELECT s.SpecName, ps.SpecVal from productspec ps
            INNER JOIN specification s ON ps.SpecID=s.SpecID
            WHERE ps.ProductID=?
            ORDER BY ps.priority";
    $stmt = $conn->prepare($qry2);
    $stmt->bind_param("i", $pid); //"i" - integer
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    while ($row2 = $result2->fetch_array()){
        echo $row2["SpecName"].": ".$row2["SpecVal"]."<br/>";
    }
    echo "</div>"; // End of Left Column

    //Right Column - Display the Product's Image
    $img = "./Images/products/$row[ProductImage]";
    echo "<div class='col-sm-3' style='vertical-align:top; padding:5px'> ";
    echo "<p><img src=$img /></p>";

    //Right Column - Display the product's price
    $formattedPrice = number_format($row["Price"], 2);
    $offerPrice = number_format($row["OfferedPrice"], 2);
	$offer = $row["Offered"];
	$offerStart = $row["OfferStartDate"];
	$offerEnd = $row["OfferEndDate"];
	$todayDate = date("Y-m-d");
	$discountPercent = (($row["Price"] - $row["OfferedPrice"]) / $row["Price"] * 100);
    $formattedDiscount = number_format($discountPercent, 0);

    if ($offer == '1' && $offerStart < $todayDate && $offerEnd > $todayDate) { //If current date is within offer date period for donuts on offer
    echo "<p><span style='font-weight:bold; color:lightgrey; font-weight:normal; text-decoration: line-through;'>
		  S$ $formattedPrice</span> $formattedDiscount% Off</p>"; //Display original price and strike it, followed by the discount percentage
    echo "<p><span style='font-weight:bold; font-size: 18px; color:red;'>Discounted Price:
    S$ $offerPrice</span>"; //Display discounted price in larger font
    }
    else { //If donut is not on offer or within offer date period
		echo "<p>Price:<span style='font-weight:bold; color:red;'>
		  S$ $formattedPrice</span>"; //Display original price
	}
    
    echo "<form action='cartFunctions.php' method='post'>";
    echo "<input type='hidden' name='action' value='add' />";
    echo "<input type='hidden' name='product_id' value='$pid' />";
    echo "Quantity: <input type='number' name='quantity' value='1'
                    min='1' max='10' style='width:40px' required />";
    $quantity = $row["Quantity"];
    $name = $row["ProductTitle"];
    //check for sufficient quantity
    if($quantity <= 0){ //If the donut is out of stock
        echo "<button id='loginBtn' style='background:red;' type='submit' disabled>Add to Cart</button>"; //Disable add to cart button and change button to red
        echo "<p style='color:red; font-weight: bold;'>$name is currently out of stock!<br />"; //Let shopper know that the donut is OOS
    }
    else { //If donut is in stock
        echo "<button id='loginBtn' type='submit'>Add to Cart</button>"; //Display normal add to cart button
    }
    
    echo "</form>";
    
}
// To Do 1:  Ending ....

// To Do 2:  Create a Form for adding the product to shopping cart. Starting ....


echo "</div>"; // End of right column
echo "</div>"; // End of row

// To Do 2:  Ending ....

$conn->close(); // Close database connnection
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>
