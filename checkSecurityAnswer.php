<?php
//Display Page Layout header with updated session state and links
include("header.php");
session_start(); //Detect the current session

//Read the data input of previous page
$securityans = $_POST["securityans"];
$email = $_SESSION["Email"];
//Include the PHP file that establishes database connection handdle: $conn
include_once("mysql_conn.php");


if(($email != "") && ($securityans  != "")){
    $qry = "SELECT * FROM Shopper WHERE Email = '$email' AND PwdAnswer = '$securityans'";
    $result = $conn->query($qry);
    if ($result->num_rows > 0){ //Check if query returns any rows
		$newpassword = "D4faultP@ssword";
        $qry2 = "UPDATE Shopper SET Password=? WHERE Email=?";
        $stmt = $conn->prepare($qry2);
        // "ssssss" - 6 string parameters
        $stmt->bind_param("ss",  $newpassword, $email);
       
        
        $stmt->execute();
        $stmt->close();

        unset($_SESSION["Email"]);
        unset($_SESSION["SecurityQns"]);
        
        echo "<h3 style='color:green'>Your New Password for $email is $newpassword</h3>";
        echo "<a href='login.php'><button>Login</button></a>";
        exit;
		
	}else{
        echo  "<h3 style='color:red'>Wrong Security Answer!</h3>";
        echo "<a href='securityQuestion.php'><button>Try Again</button></a>";
    }   



}else{
    echo  "<h3 style='color:red'>Fill Up All Fields!</h3>";
    echo "<a href='securityQuestion.php'><button>Try Again</button></a>";
}   







//Close database connection

$conn -> close();


//Display Message
//Display Page Layout Footer
include("footer.php");
?>