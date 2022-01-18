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

    $qry = "SELECT ProductID, ProductTitle FROM PRODUCT WHERE ProductTitle LIKE '%$search%' OR ProductDesc LIKE '%$search%' 
    ORDER BY ProductTitle"; //The SQL Query

	$result = $conn->query($qry); //The result of the query

	if ($result->num_rows > 0){ //Check if query returns any rows
        echo "<h5 style='font-weight:bold; text-align:center;'> Search Results for $search: </h5>";

		while ($row = $result->fetch_array()){
            $product = "productDetails.php?pid=$row[ProductID]";
            echo "<h6 style='text-align:center;'><a id='forgotPw' href=$product>$row[ProductTitle]</a></h6>";
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