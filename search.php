<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>

<!-- HTML Form to collect search keyword and submit it to the same page in server -->
<div style="width:80%; margin:auto;"> <!-- Container -->
<form name="frmSearch" method="get" action="">
    <div class="form-group row"> <!-- 1st row -->
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Product Search</span>
        </div>
    </div> <!-- End of 1st row -->
    <div class="form-group row"> <!-- 2nd row -->
        <label for="keywords" 
               class="col-sm-3 col-form-label">Product Title:</label>
        <div class="col-sm-6" id="searchBox">
            <input class="form-control" name="keywords" id="keywords" 
                   type="search" />
        </div>
        <div class="col-sm-3" id="searchBtnCol">
            <button id="searchBtn" type="submit">Search</button>
        </div>
    </div>  <!-- End of 2nd row -->
</form>



<?php
//Include the PHP file that establishes database connection handdle: $conn
include_once("mysql_conn.php");

// The non-empty search keyword is sent to server
if (isset($_GET["keywords"]) && trim($_GET['keywords']) != "") {

    // To Do (DIY): Retrieve list of product records with "ProductTitle" 
	// contains the keyword entered by shopper, and display them in a table.
	
    $search = $_GET['keywords'];

    $qry = "SELECT * FROM PRODUCT WHERE ProductTitle LIKE '%$search%' OR ProductDesc LIKE '%$search%' 
    ORDER BY ProductTitle"; //The SQL Query

	$result = $conn->query($qry); //The result of the query

	if ($result->num_rows > 0){ //Check if query returns any rows
        echo "<h5 style='font-weight:bold; text-align:center;'> Search Results for $search: </h5>";

		while ($row = $result->fetch_array()){
            //$product = "productDetails.php?pid=$row[ProductID]";
            //echo "<h6 style='text-align:center;'><a id='forgotPw' href=$product>$row[ProductTitle]</a></h6>";

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

            echo "<div class='row'>"; 
               echo "<div class='col-lg-8 mx-auto'>";
                    //List group
                    echo "<ul class='list-group shadow'>";
                    //<!-- list group item-->
                    echo "<li class='list-group-item'>";
                    //<!-- Custom content-->
                    if ($offer == '1' && $offerStart < $todayDate && $offerEnd > $todayDate) { //If searched product is on offer
                    echo "<div class='media align-items-lg-center flex-column flex-lg-row p-3'>";
                         echo "<div class='media-body order-2 order-lg-1'>";
                              echo "<h5 class='mt-0 font-weight-bold mb-2'>$productName</h5>"; //Print product name
                              echo "<button id='loginBtn' style='background:#ffac47; width:30%; border-radius:70px;' 
                                    type='submit' disabled>On Offer</button>"; //Display 'on offer' for each donut on offer
                              echo "<br>";
                              echo "<p class='font-italic text-muted mb-0 small'>$productDesc</p>"; //Print product description
                                    
                                    echo "<div class='d-flex align-items-center justify-content-between mt-1'>";
                                        echo "<p><span style='font-weight:bold; color:lightgrey; font-weight:normal; text-decoration: line-through;'>
                                             S$ $formattedPrice</span> <span style='color:#ffac47;'>$formattedDiscount% Off</span></p>"; //Print strikethrough original price and discounted price and discount %
                                        
                                    echo "</div>";
                                   
                                        echo "<p><span style='font-weight:bold; font-size: 18px; color:red;'>Discounted Price:
                                                    S$ $offerPrice</span>"; //Print product discounted price
                                        echo "<a id='Ranking' href=$productLink><button id='loginBtn' style='background:darkcyan; width:50%; border-radius:70px;' 
                                        type='submit'>View Product</button></a>"; //Display button that links to specified donut
                                        
                         echo "</div>";
                         echo "<img src='$img' width='200' class='ml-lg-5 order-1 order-lg-2'>";
                    echo "</div>";
                    }
                    else { //If searched product is not on offer
                    echo "<div class='media align-items-lg-center flex-column flex-lg-row p-3'>";
                        echo "<div class='media-body order-2 order-lg-1'>";
                            echo "<h5 class='mt-0 font-weight-bold mb-2'>$productName</h5>"; //Print product name
                            echo "<p class='font-italic text-muted mb-0 small'>$productDesc</p>"; //Print product description
                                    echo "<div class='d-flex align-items-center justify-content-between mt-1'>";
                                        echo "<p><span>
                                             S$ $formattedPrice</span></p>"; 
                                    echo "</div>";
                                   echo "<a id='Ranking' href=$productLink><button id='loginBtn' style='background:darkcyan; width:50%; border-radius:70px;' 
                                            type='submit'>View Product</button></a>"; //Display button that links to specified donut
                                   
                         echo "</div>";
                         echo "<img src='$img' width='200' class='ml-lg-5 order-1 order-lg-2'>";
                    echo "</div>";
                    }
                    
                    //<!-- End -->
                    echo "</li>";
                    //<!-- End -->
                    echo "</ul>";
               //<!-- End -->
               echo "</div>";
          echo "</div>";

		}
		
	}
	else {
		echo  "<h6 style='color:red; font-weight:bold; text-align:center;'>No matching products available</h6>";
	}


	// To Do (DIY): End of Code
}

echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>