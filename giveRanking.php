<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
if (! isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}

?>

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
    //Right Column - Display the Product's Image
    $img = "./Images/products/$row[ProductImage]";

    echo '
            <div style="width:80%; margin:auto;">
                <form name="rate" action="postRanking.php" method="post">
                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <img src='.$img.' />
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <span class="page-title">'.$row['ProductTitle'].'</span>
                            <input class="form-control" name="pid" id="pid" value="'.$pid.'" type="hidden" />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="RatingScore">
                            Select a Score:</label>
                        <div class="col-sm-9">
                            <select class="form-control" name="RatingScore" id="RatingScore" required>
                                <option value=1>1 - Very Bad</option>
                                <option value=2>2 - Bad</option>
                                <option value=3>3 - Average</option>
                                <option value=4>4 - Good</option>
                                <option value=5>5 - Excellent</option>
                            </select>
    
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="comment">Comment:</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" name="comment" id="comment"  cols="25" rows="4" type="text" required ></textarea>
                        </div>
                    </div>
                    
    
                    <div class="form-group row">       
                        <div class="col-sm-9 offset-sm-3">
                            <button id="loginBtn" type="submit">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
    
            ';

    echo "</div>";
    
   
    

  
}











$conn->close(); // Close database connnection
include("footer.php"); // Include the Page Layout footer
?>