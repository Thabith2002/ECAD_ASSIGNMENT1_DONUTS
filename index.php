<?php 
// Detect the current session
session_start();
// Include the Page Layout header
include("header.php"); 
?>
<img src="Images/welcome2mamaya.jpg" class="img-fluid" 
     style="display:block; margin:auto;"/>
     <div class="container py-5">
     </div>

<?php 
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

// Display Products on Offer
echo "<hr>"; //Horizontal line to separate welcome message and products on offer
echo "<p style='color:red; text-align:center;' class='page-title'>Donuts on Offer!</p>"; //Products on offer Title
$qry = "SELECT * FROM product"; // Retrieve all Product details
$stmt = $conn->prepare($qry);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

while ($row = $result->fetch_array()) {


     $productName = $row["ProductTitle"]; //Define product name for each donut
     $productDesc = $row["ProductDesc"]; //Define product description for each donut
     $productLink = "productDetails.php?pid=$row[ProductID]"; //Define the hyperlink to the product details page of the selected donut
     $img = "./Images/Products/$row[ProductImage]"; //Define product image for each donut
     $formattedPrice = number_format($row["Price"], 2); //Get price of donut and format it to 2 decimal places
	$offerPrice = number_format($row["OfferedPrice"], 2); //Get discounted price of donuts on offer and format it to 2 decimal places
     $discountPercent = (($row["Price"] - $row["OfferedPrice"]) / $row["Price"] * 100); //Calculcate discount percentage
     $formattedDiscount = number_format($discountPercent, 0); //Format discounted percentage to 2 decimal places
     $offer = $row["Offered"]; //Define whether the donut is on offer or not
     $offerStart = $row["OfferStartDate"]; //Define start date of donut on offer
     $offerEnd = $row["OfferEndDate"]; //Define end date of donut on offer
     $todayDate = date("Y-m-d"); //Define current date to compare with offer time period
     if ($offer == '1' && $offerStart < $todayDate && $offerEnd > $todayDate) { //If the offer time period is within range of today's date, and donut is on offer
          //Display donuts that are on offer
          echo "<div class='row'>"; 
               echo "<div class='col-lg-8 mx-auto'>";
                    //List group
                    echo "<ul class='list-group shadow'>";
                    //<!-- list group item-->
                    echo "<li class='list-group-item'>";
                    //<!-- Custom content-->
                    echo "<div class='media align-items-lg-center flex-column flex-lg-row p-3'>";
                         echo "<div class='media-body order-2 order-lg-1'>";
                              echo "<h5 class='mt-0 font-weight-bold mb-2'>$productName</h5>"; //Print product name
                              echo "<p class='font-italic text-muted mb-0 small'>$productDesc</p>"; //Print product description
                                   echo "<div class='d-flex align-items-center justify-content-between mt-1'>";
                                        echo "<p><span style='font-weight:bold; color:lightgrey; font-weight:normal; text-decoration: line-through;'>
                                             S$ $formattedPrice</span> <span style='color:#ffac47;'>$formattedDiscount% Off</span></p>"; //Print strikethrough original price and discounted price and discount %
                                   echo "</div>";
                                   
                                   echo "<p><span style='font-weight:bold; font-size: 18px; color:red;'>Discounted Price:
                                             S$ $offerPrice</span>"; //Print product discounted price
                                   echo "<a id='Ranking' href=$productLink><button id='loginBtn' style='background:#ffac47; width:30%; border-radius:70px;' type='submit'>View Product</button></a>"; //Display button that links to specified donut
                                   
                         echo "</div>";
                         echo "<img src='$img' width='200' class='ml-lg-5 order-1 order-lg-2'>";
                    echo "</div>";
                    
                    //<!-- End -->
                    echo "</li>";
                    //<!-- End -->
                    echo "</ul>";
               //<!-- End -->
               echo "</div>";
          echo "</div>";
    }
}
// Include the Page Layout footer
include("footer.php"); 
?>
