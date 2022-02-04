<?php 
// Detect the current session
session_start(); 
// Include the Page Layout header
include("header.php"); 
?>

<script type="text/javascript">
function validateForm()
{
    // To Do 1 - Check if password matched
	if (document.register.password.value != document.register.password2.value){
        alert("Passwords do not match");
        return false; //cancel submission
    }


	// To Do 2 - Check if telephone number entered correctly
	//           Singapore telephone number consists of 8 digits,
	//           start with 6, 8 or 9
    if (document.register.phone.value !=""){
        var num = document.register.phone.value;
        if (num.length != 8){
            alert("Please enter a valid 8 digit phone number");
            return false; //cancel submission
        }
        else if (num.substr(0,1) != "6" &&
                 num.substr(0,1) != "8" &&
                 num.substr(0,1) != "9") {
                     alert("A Singapore phone number should start with either 6,8 or 9.");
                     return false; //cancel submission
                 }
    }
    
    return true;  // No error found
}
</script>

<div style="width:80%; margin:auto;">
<form name="register" action="addMember.php" method="post" 
      onsubmit="return validateForm()">
    <div class="form-group row">
        <div class="col-sm-9 offset-sm-3">
            <span class="page-title">Membership Registration</span>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="name">Name:</label>
        <div class="col-sm-9">
            <input class="form-control" name="name" id="name" 
                   type="text" required /> <p style="color:red;">(required)</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="DOB">
            Date of birth:</label>
        <div class="col-sm-9">
            <input class="form-control" name="DOB" id="DOB" 
                   type="date" required /> <p style="color:red;">(required)</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="address">Address:</label>
        <div class="col-sm-9">
            <textarea class="form-control" name="address" id="address"
                      cols="25" rows="4" ></textarea>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="country">Country:</label>
        <div class="col-sm-9">
            <input class="form-control" name="country" id="country" type="text" />
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="phone">Phone:</label>
        <div class="col-sm-9">
            <input class="form-control" name="phone" id="phone" type="text" />
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="email">
            Email Address:</label>
        <div class="col-sm-9">
            <input class="form-control" name="email" id="email" 
                   type="email" required /> <p style="color:red;">(required)</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="password">
            Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="password" id="password" 
                   type="password" required /> <p style="color:red;">(required)</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="password2">
            Retype Password:</label>
        <div class="col-sm-9">
            <input class="form-control" name="password2" id="password2" 
                   type="password" required /> <p style="color:red;">(required)</p>
        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="securityqns">
            Select a Security Question:</label>
        <div class="col-sm-9">
            <select class="form-control" name="securityqns" id="securityqns" required>
                <option value="Which polytechnic?">Which Polytechnic did you graduate from?</option>
                <option value="wife's name?">What is your wife's first name?</option>
                <option value="How many brothers and sisters?">How many Brothers and Sisters do you have?</option>
                <option value="What was your first car?">What was your first car?</option>
            </select>

        </div>
    </div>
    <div class="form-group row">
        <label class="col-sm-3 col-form-label" for="securityans">
            Security Answer:</label>
        <div class="col-sm-9">
            <input class="form-control" name="securityans" id="securityans" 
                   type="text" required /> <p style="color:red;">(required)</p>
        </div>
    </div>


    <div class="form-group row">       
        <div class="col-sm-9 offset-sm-3">
            <button id="loginBtn" type="submit">Register</button>
        </div>
    </div>
</form>
</div>
<?php 
// Include the Page Layout footer
include("footer.php"); 
?>