<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a container, 60% width of viewport -->
<div style="width:60%; margin:auto;">
<!-- Display Page Header -->
<div class="row" style="padding:5px"> <!-- Start of header row -->
    <div class="col-12">
        <span class="page-title">Product Categories</span>
        <p>Select a category listed below:</p>
    </div>
</div> <!-- End of header row -->

<?php 
// Include the PHP file that establishes database connection handle: $conn
include_once("mysql_conn.php");

// To Do:  Starting ....
$qry = "SELECT * FROM Category ORDER BY CatName"; // Order by Alphabetical Order of Category Name
$result = $conn->query($qry);

//Display Category in each row
while ($row = $result->fetch_array()) {
    echo "<div class='row' style='padding:5px'>"; // Start a new row

    //Left column - display a text link showing the category's name,
    //              display category's description in a new paragraph
    $catname = urlencode($row["CatName"]);
    $catproduct = "catProduct.php?cid=$row[CategoryID]&catName=$catname";
    echo "<div class='col-8'>"; //67% of row width
    echo "<p><a id='forgotPw' href=$catproduct>$row[CatName]s</a></p>";
    echo "$row[CatDesc]";
    echo "</div>";

    //Right Column - display the category's image
    $img = "./Images/category/$row[CatImage]";
    echo "<div class='col-4'>";//33% of row-width
    echo "<img src='$img' />";
    echo "</div>";
    echo "</div>";
}


$conn->close(); // Close database connnection
echo "</div>"; // End of container
include("footer.php"); // Include the Page Layout footer
?>
