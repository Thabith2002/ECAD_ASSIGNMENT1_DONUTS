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
echo "<hr>";
echo "<p style='color:red; text-align:center;' class='page-title'>Donuts on Offer!</p>";
$qry = "SELECT * FROM product"; // Retrieve Product
$stmt = $conn->prepare($qry); 	// "i" - integer 
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

while ($row = $result->fetch_array()) {


     $productName = $row["ProductTitle"];
     $productDesc = $row["ProductDesc"];
     $productLink = "productDetails.php?pid=$row[ProductID]";
     $img = "./Images/Products/$row[ProductImage]";
     $formattedPrice = number_format($row["Price"], 2);
	$offerPrice = number_format($row["OfferedPrice"], 2);
     $discountPercent = (($row["Price"] - $row["OfferedPrice"]) / $row["Price"] * 100);
     $formattedDiscount = number_format($discountPercent, 0);
     $offer = $row["Offered"];
     $offerStart = $row["OfferStartDate"];
     $offerEnd = $row["OfferEndDate"];
     $todayDate = date("Y-m-d");
     if ($offer == '1' && $offerStart < $todayDate && $offerEnd > $todayDate) { //If the offer time period is within range of today's date
          echo "<div class='row'>";
               echo "<div class='col-lg-8 mx-auto'>";
                    //List group
                    echo "<ul class='list-group shadow'>";
                    //<!-- list group item-->
                    echo "<li class='list-group-item'>";
                    //<!-- Custom content-->
                    echo "<div class='media align-items-lg-center flex-column flex-lg-row p-3'>";
                         echo "<div class='media-body order-2 order-lg-1'>";
                              echo "<h5 class='mt-0 font-weight-bold mb-2'>$productName</h5>";
                              echo "<p class='font-italic text-muted mb-0 small'>$productDesc</p>";
                                   echo "<div class='d-flex align-items-center justify-content-between mt-1'>";
                                        echo "<p><span style='font-weight:bold; color:lightgrey; font-weight:normal; text-decoration: line-through;'>
                                             S$ $formattedPrice</span> <span style='color:#ffac47;'>$formattedDiscount% Off</span></p>";
                                   echo "</div>";
                                   
                                   echo "<p><span style='font-weight:bold; font-size: 18px; color:red;'>Discounted Price:
                                             S$ $offerPrice</span>";
                                   echo "<a id='Ranking' href=$productLink><button id='loginBtn' style='background:#ffac47; width:30%; border-radius:70px;' type='submit'>View Product</button></a>";
                                   //echo "<button onClick='$productLink' id='loginBtn' style='background:#ffac47; width:30%; border-radius:70px;' type='submit'>View Product</button>";
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
