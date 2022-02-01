<?php 
session_start(); // Detect the current session
include("header.php"); // Include the Page Layout header

// To Do 1: Check if user logged in 
if (! isset($_SESSION["ShopperID"])) { // Check if user logged in 
	// redirect to login page if the session variable shopperid is not set
	header ("Location: login.php");
	exit;
}
// End of To Do 1
?>

<script type="text/javascript">
function validateForm()
{
    // Check if password matched
	if (document.changePwd.pwd1.value != document.changePwd.pwd2.value) {
 	    alert("Passwords not matched!");
        return false;   // cancel submission
    }
    return true;  // No error found
}
</script>
<!-- Create a cenrally located container -->
<div style="width:80%; margin:auto;">
<form name="changePwd" method="post" onsubmit="return validateForm()">
    <div class="form-group row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Change Password</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="pwd1">
         New Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="pwd1" id="pwd1" 
                   type="password" required />
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="pwd2">
         Retype Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="pwd2" id="pwd2"
                   type="password" required />
        </div>
    </div>
    <div class="form-group row">       
        <div class="col-sm-9 offset-sm-3">
            <button id="loginBtn" type="submit">Update</button>
        </div>
    </div>
</form>

<?php
// Process after user click the submit button
if (isset($_POST["pwd1"])) {
	// To Do 2: Read new password entered by user
	$pwd = $_POST["pwd1"];

	
	// To Do 3: Hash the default password
	$hashed_pwd = password_hash($pwd, PASSWORD_DEFAULT);

	
	// To Do 4: Update the new password hash
	include_once("mysql_conn.php");
    $shopperID = $_SESSION["ShopperID"];
	$qry = "SELECT * FROM Shopper WHERE ShopperID = '$shopperID'; ";
    $result = $conn->query($qry); //The result of the query

	if ($result->num_rows > 0) {
		// To Do 1: Update the default new password to shopper"s account
		$row = $result->fetch_array();
		$shopperId = $row["ShopperID"];
		$qry = "UPDATE Shopper SET Password=? WHERE ShopperID=?";
		$stmt = $conn->prepare($qry);
		$stmt->bind_param("si", $hashed_pwd, $shopperId); //"s" - string, "i" - integer
		$stmt->execute();
		$stmt->close();
        echo "<div class='col-sm-9 offset-sm-3'>
        <p style='font-weight:bold;'>Your password has been reset.</p>
        </div>";    }
}
?>

</div> <!-- Closing container -->
<?php 
include("footer.php"); // Include the Page Layout footer
?>