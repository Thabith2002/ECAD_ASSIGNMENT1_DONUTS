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

$qry2 = "SELECT * FROM RANKING WHERE ProductID = ?";
$stmt = $conn->prepare($qry2);
$stmt->bind_param("i", $pid); 	// "i" - integer 
$stmt->execute();
$ranking_result = $stmt->get_result();
$stmt->close();


// To Do 1:  Display Product information. Starting ....
while ($row = $result->fetch_array()){
    //Display Page Header -
    //Product's name is read from the "ProductTitle" column of "product" table.
    echo "<div class='row' >";
    echo "<div class='col-sm-12' style='padding:5px' >";
    echo "<span class='page-title'>$row[ProductTitle]</span>";
    echo "</div>";
    echo "</div>";

    echo "<div class='row'>";// Start a new row

    //Left column - display a product's specifications,
    echo "<div class='col-sm-9' style='padding:5px' >";
    echo "<p>$row[ProductDesc]</p>";

    //Left column - display a product's specifications,
    $qry = "SELECT s.SpecName, ps.SpecVal from productspec ps
            INNER JOIN specification s ON ps.SpecID=s.SpecID
            WHERE ps.ProductID=?
            ORDER BY ps.priority";
    $stmt = $conn->prepare($qry);
    $stmt->bind_param("i", $pid); //"i" - integer
    $stmt->execute();
    $result2 = $stmt->get_result();
    $stmt->close();
    while ($row2 = $result2->fetch_array()){
        echo $row2["SpecName"].": ".$row2["SpecVal"]."<br/>";
    }

    //Check if any ranking given to donut
    if($ranking_result->num_rows > 0) {
        
        $qry3 = "SELECT AVG(`Rank`) AS 'AvgRank' FROM `RANKING` WHERE `ProductID` = ?";
        $stmt = $conn->prepare($qry3);
        $stmt->bind_param("i", $pid); 	// "i" - integer 
        $stmt->execute();
        $average_ranking_result = $stmt->get_result();
        $stmt->close();
        while ($row3 = $average_ranking_result->fetch_array()){
            echo "<p><strong>Average Donut Ranking: $row3[AvgRank]</strong></p>";
        }

    }else{
        echo"<p><strong>No Ratings Given yet!</strong></p>";
    }

    echo "</div>"; // End of Left Column

    //Right Column - Display the Product's Image
    $img = "./Images/products/$row[ProductImage]";
    echo "<div class='col-sm-3' style='vertical-align:top; padding:5px'> ";
    echo "<p><img src=$img /></p>";

    //Right Column - Display the product's price
    $formattedPrice = number_format($row["Price"], 2);
    echo "Price:<span style='font-weight:bold; color:red;'>
        S$ $formattedPrice</span>";
}




$giveRanking = "giveRanking.php?pid=$pid";
echo "<p><a id='Ranking' href=$giveRanking ><button>Give Ranking</button></p>";

echo "</div>"; // End of right column
if($ranking_result->num_rows > 0) {
    
    echo "<div class='table-responsive' >"; // Bootstrap responsive table
    echo "<table class='table table-hover'>"; // Start of table
    echo "<thead class='cart-header'>"; // Start of table's header section
    echo "<tr>"; // Start of header row
    echo "<th>Shopper ID</th>";
    echo "<th>Given Rank</th>";
    echo "<th>Comment</th>";

    echo "</tr>"; // End of header row
    echo "</thead>"; //End of table's header section
    echo "<tbody>";
    while($row4 = $ranking_result->fetch_array()){
        echo "<tr>"; // Start of header row
        echo "<td >$row4[ShopperID]</td>"; // Start of header row
        echo "<td >$row4[Rank]</td>"; // Start of header row
        echo "<td >$row4[Comment]</td>"; // Start of header row
        echo "</tr>"; // End of header row
    }
    
    echo "</tbody>"; // End of table's body section
	echo "</table>"; // End of table
	echo "</div>"; // End of Bootstrap responsive table	
   
}






$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>
