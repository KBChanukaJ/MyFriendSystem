<?php
// Include the database connection file
include('connection.php');

// Initialize session
session_start();

// Function to validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to check if email already exists in the 'friends' table
function isEmailUnique($conn, $email) {
    $query = "SELECT * FROM friends WHERE friend_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows === 0; // Returns true if email is unique
}

// Function to validate profile name (only letters, not blank)
function validateProfileName($profileName) {
    return !empty($profileName) && ctype_alpha($profileName);
}

// Function to validate password (only letters and numbers, and match)
function validatePassword($password, $confirmPassword) {
    return ctype_alnum($password) && $password === $confirmPassword;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $profileName = $_POST['profileName'];
    $password = $_POST['pass'];
    $confirmPassword = $_POST['cpass'];

    // Server-side validation
    $errors = array();

    if (!validateEmail($email)) {
        $errors[] = "Invalid email address format.";
    } elseif (!isEmailUnique($conn, $email)) {
        $errors[] = "Email address already exists. Please choose another email.";
    }

    if (!validateProfileName($profileName)) {
        $errors[] = "Profile name must contain only letters and cannot be blank.";
    }

    if (!validatePassword($password, $confirmPassword)) {
        $errors[] = "Invalid password or passwords do not match.";
    }

    // If no validation errors, proceed to insert data into the 'friends' table
    if (empty($errors)) {
        $dateStarted = date("Y-m-d");
        $numOfFriends = 0;

        // Insert data into 'friends' table
        $query = "INSERT INTO friends (friend_email, password, profile_name, date_started, num_of_friends) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssi", $email, $password, $profileName, $dateStarted, $numOfFriends);
        $stmt->execute();

        // Set session variable for successful login status
        $_SESSION['login_status'] = true;

        // Redirect to friendadd.php
        header("Location: friendadd.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Friend System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>

	Html, body{
		height:100%;
		font-family:'Times New Roman', Times, serif;
	}

	.grandParentContaniner{
		display:table; 
		height:100%; 
		width:50%;
		margin: 0 auto;
	}

	.parentContainer{
		display:table-cell; vertical-align:middle;
	}	
    </style>
</head>
<body>
    <div class="container">
        <div class="row" style="margin-top:15%;">
            <div  class="col-2"></div>
            <div  class="col-8">
                <form class="pt-2 ps-3 pb-2 pe-3" style="border: 5px solid gray" method="post" action="">
                    <div class="text-center">
					 <h5 class="fw-bold">My Friend System </h5>
					 <h5 class="fw-bold">Registration Page </h5>
				</div>
				
                <div class="row">
                <div class="col-10">
				<div class="text-center mt-4">
				<?php
                // Display error messages
                if (!empty($errors)) {
                    echo '<script>alert("';
                    foreach ($errors as $error) {
                        echo $error . '\\n';
                    }
                    echo '");</script>';
                }
                ?>
				<div class="mb-2 row">
					<label for="inputEmail" class="col-sm-4 col-form-label text-end">Email</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="email" name="email" oninput="emailValidation()">
						<small style="float:left; margin-top:1%;" id="email_Status"></small>
					</div>
				</div>
				<div class="mb-2 row">
					<label for="inputEmail" class="col-sm-4 col-form-label text-end">Profile Name</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" id="profileName" name="profileName">
					</div>
				</div>
				<div class="mb-4 row">
					<label for="inputPassword" class="col-sm-4 col-form-label text-end">Password</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="pass" name="pass" oninput="passStrongCheck()">
					</div>
				</div>
				<div class="mb-4 row">
					<label for="inputPassword" class="col-sm-4 col-form-label text-end">Confirm Password</label>
					<div class="col-sm-8">
						<input type="password" class="form-control" id="cpass" name="cpass">
					</div>
				</div>
				</div>
				</div>
                <div class="col-2"></div>
				</div>
			
				<div class="row text-center mt-2">
					<div class="col-3">
						
					</div>
					<div class="col-3">
						<button class="btn btn-outline-dark rounded-0">Register</button>
					</div>
					<div class="col-3">
                            <button type="button" class="btn btn-outline-dark rounded-0" onclick="clearForm()">&nbsp;Clear&nbsp;</button>
                    </div>
					<div class="col-3">
					</div>
				</div>
                <div class="text-center mt-4"><a href="index.php">Home</a></div>
                </form>
            </div>
            <div  class="col-2"></div>
        </div>
    </div>
    <script>
		function validateEmail(email) {
		var re = /\S+@\S+\.\S+/;
		return re.test(email);
		}

		function emailValidation(){
			
			var emailInput = document.getElementById("email").value;
			if(validateEmail(emailInput) == true)
			{
				document.getElementById("email_Status").innerHTML = "Valid email address.";
				document.getElementById("email_Status").style.color = "green";
			}
			else
			{
				document.getElementById("email_Status").innerHTML = "Invalid email address.";
				document.getElementById("email_Status").style.color = "red";
			}
		}

		function passStrongCheck(){
			var passInput = document.getElementById("pass").value;
		}
		function clearForm() {
            document.getElementById("email").value = "";
			document.getElementById("profileName").value = "";
			document.getElementById("pass").value = "";
            document.getElementById("cpass").value = "";
        }
	</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>
</html>
