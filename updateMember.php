<?php
session_start(); //Detect the current session

//Read the data input of previous page
$name = $_POST["name"];
$address = $_POST["address"];
$country = $_POST["country"];
$dob = $_POST["DOB"];
$phone = $_POST["phone"];
$email = $_POST["email"];
$password = $_POST["password"];
$securityqns = $_POST["securityqns"];
$securityans = $_POST["securityans"];
//Include the PHP file that establishes database connection handdle: $conn
include_once("mysql_conn.php");

$email_qry = "SELECT * FROM Shopper WHERE Email = '$email'";



$email_result = $conn -> query($email_qry);
if($email_result->num_rows > 0)
{
    $Message = "<h3 style='color:red'>Email Entered already exists!</h3>";
    $conn -> close();
}
else{
    $shopperID = $_SESSION["ShopperID"];
     //Define the INSERT SQL statement
     
     $qry = "UPDATE Shopper SET Name=?, BirthDate=?, Address=?, Country=?, Phone=?, EMail=?, Password=?, PwdQuestion=?, PwdAnswer=? WHERE ShopperID=?";
     $stmt = $conn->prepare($qry);
     // "ssssss" - 6 string parameters
     $stmt->bind_param("sssssssssi", $name, $dob ,$address, $country, $phone, $email, $password, $securityqns, $securityans, $shopperID);
 
     if ($stmt->execute()){ //SQL statement executed successfully
         //Retrieve the Shopper ID assigned to the new shopper
         $qry = "SELECT LAST_INSERT_ID() AS ShopperID";
         $result = $conn->query($qry); //Execute the SQL and get the returned result
         while ($row = $result->fetch_array()){
         $_SESSION["ShopperID"] = $row["ShopperID"];
         }
 
    
         $Message = "Update successful! <br/>";
        
     }
     else{ //Error Message
         $Message = "<h3 style='color:red'>Error in Updating record</h3>";
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