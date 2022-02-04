<?php
session_start(); //Detect the current session

//Read the data input of previous page
$RatingScore = $_POST["RatingScore"];
$comment = $_POST["comment"];
$pid = $_POST["pid"];
$shopperID = $_SESSION["ShopperID"];
//Include the PHP file that establishes database connection handdle: $conn
include_once("mysql_conn.php");

$rank_qry = "SELECT * FROM `ranking` WHERE `ShopperID` = '$shopperID' AND `ProductID` = '$pid' ";



$rank_result = $conn -> query($rank_qry);
if($rank_result->num_rows > 0)
{
    $Message = "<h3 style='color:red'>You have already ranked this Donut!</h3>";
    $conn -> close();
}
else{
    $shopperID = $_SESSION["ShopperID"];
     //Define the INSERT SQL statement
     
     $qry = "insert into Ranking(ShopperId, ProductId, `Rank`, Comment) values(?, ?, ?, ?)";
     $stmt = $conn->prepare($qry);
     // "ssssss" - 6 string parameters
     $stmt->bind_param("iiis", $shopperID, $pid, $RatingScore, $comment);
 
     if ($stmt->execute()){ 

        header("Location: productRanking.php?pid=$pid");
        
     }
     else{ //Error Message
         $Message = "<h3 style='color:red'>Error in Inserting record</h3>";
     }
 
  
     $stmt->close();
     $conn -> close();
}




//Close database connection
// $conn -> close();

//Display Page Layout header with updated session state and links
include("header.php");
//Display Message
echo $Message;
//Display Page Layout Footer
include("footer.php");
?>