<?php 
session_start(); // Detect the current session
include("header.php");// Include the Page Layout header
if (! isset($_SESSION["SecurityQns"])) { // Check if there is security question
	// redirect to login page if the session variable shopperid is not set
	header ("Location: forgetPassword.php");
	exit;
}else{
    echo '
            <div style="width:80%; margin:auto;">
                <form name="securityqna" action="checkSecurityAnswer.php" method="post">
                    <div class="form-group row">
                        <div class="col-sm-9 offset-sm-3">
                            <span class="page-title"></span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="securityqns">
                            Your Security Question:</label>
                        <div class="col-sm-9">
                            <p>'.$_SESSION["SecurityQns"].'</p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="securityans">
                            Enter Security Answer:</label>
                        <div class="col-sm-9">
                            <input class="form-control" name="securityans" id="securityans" type="text" placeholder="Enter Your Security Answer" required /> 
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
}
?>



<?php // Detect the current session
include("footer.php"); // Include the Page Layout header
?>

