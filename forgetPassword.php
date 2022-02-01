<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header
?>
<!-- Create a cenrally located container -->
<div style="width:80%; margin:auto;">
<form method="post">
	<div class="form-group row">
		<div class="col-sm-9 offset-sm-3">
			<span class="page-title">Forget Password</span>
		</div>
	</div>
	<div class="form-group row">
		<label class="col-sm-3 col-form-label" for="eMail">
         Email Address:</label>
		<div class="col-sm-9">
			<input class="form-control" name="eMail" id="eMail"
                   type="email" required />
		</div>
	</div>
	<div class="form-group row">      
		<div class="col-sm-9 offset-sm-3">
			<button id="loginBtn" type="submit">Submit</button>
		</div>
	</div>
</form>

<?php 
// Process after user click the submit button
if (isset($_POST["eMail"])) {
	// Read email address entered by user
	$eMail = $_POST["eMail"];
	// Retrieve shopper record based on e-mail address
	include_once("mysql_conn.php");
	$qry = "SELECT * FROM Shopper WHERE Email=?";
	$stmt = $conn->prepare($qry);
	$stmt->bind_param("s", $eMail); 	// "s" - string 
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	if ($result->num_rows > 0) {
		// To Do 1: Update the default new password to shopper"s account
		$row = $result->fetch_array();
		$shopperId = $row["ShopperID"];
		$new_pwd = "password"; //Default Password
		$hashed_pwd = password_hash($new_pwd, PASSWORD_DEFAULT); //Hash the defualt password
		$qry = "UPDATE Shopper SET Password=? WHERE ShopperID=?";
		$stmt = $conn->prepare($qry);
		$stmt->bind_param("si", $hashed_pwd, $shopperId); //"s" - string, "i" - integer
		$stmt->execute();
		$stmt->close();
		// End of To Do 1
		
		// To Do 2: e-Mail the new password to user
		include("myMail.php");
		// The 'Send To' should be the email address indicated
		// by shopper, i.e $eMail. In this case, use a testing e-mail address
		// as the shopper's email address in our database may not be valid
		$to=$eMail; //user recieving
		$from="mamayabookstoreprivatelimited@gmail.com"; // user sending
		$from_name="Mamaya e-Bookstore";
		$subject="Mamaya e-BookStore Forgot Password"; // Email Title
		//HTML body message
		$body= "<span style='color:black; font-size:12px'>
				Your new password is <span style ='font-weight:bold'>
				$new_pwd</span>.<br />
				Do change this default password.</span>";
		//initate the emailing sending process
		if(smtpmailer($to, $from, $from_name, $subject, $body)){
			echo "<div class='col-sm-9 offset-sm-3'><p> Your new password has been sent to:
				  <span style='font-weight:bold'>$to</span>.</p> </div>";
		}
		else{
			echo "<div class='col-sm-9 offset-sm-3'><p><span style='color:red;'>
				  Mailer Error: " . $error . "</span></p></div>";
		}
		

		// End of To Do 2
	}
	else {
		echo "<div class='col-sm-9 offset-sm-3'>
        <p style='color:red; font-weight:bold;'>Invalid E-mail address!</p>
        </div>";
	}
	$conn->close();
}
?>

</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>