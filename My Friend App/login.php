<?php
// Include the database connection file
include('connection.php');

// Initialize session
session_start();

// Function to validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate login credentials
function validateLogin($conn, $email, $password) {
    $query = "SELECT * FROM friends WHERE friend_email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0; // Returns true if login is valid
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['pass'];

    // Server-side validation
    $errors = array();

    if (!validateEmail($email)) {
        $errors[] = "Invalid email address format.";
    }

    // Check login credentials
    if (empty($errors) && validateLogin($conn, $email, $password)) {
        // Set the user_email session variable
        $_SESSION['user_email'] = $email;

        // Redirect to friendlist.php
        header("Location: friendlist.php");
        exit();
    } else {
        // Invalid login, display error or handle it according to your logic
        $errors[] = "Invalid email or password.";
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
        /* Your existing styles here */

        Html, body {
            height: 100%;
            font-family: 'Times New Roman', Times, serif;
        }

        .grandParentContaniner {
            display: table;
            height: 100%;
            width: 50%;
            margin: 0 auto;
        }

        .parentContainer {
            display: table-cell;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row" style="margin-top:15%;">
            <div class="col-3"></div>
            <div class="col-6">
                <form class="pt-2 ps-3 pb-2 pe-3" style="border: 5px solid gray" method="post" action="">
                    <div class="text-center">
                        <h5 class="fw-bold">My Friend System </h5>
                        <h5 class="fw-bold">Login Page </h5>
                    </div>

                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <div class="text-center mt-4">
                                <div class="mb-2 row">
                                    <label for="inputEmail" class="col-sm-2 col-form-label text-end">Email</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="email" name="email">
                                    </div>
                                </div>
                                <div class="mb-4 row">
                                    <label for="inputPassword" class="col-sm-2 col-form-label text-end">Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" id="pass" name="pass">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-1"></div>
                    </div>

                    <div class="row text-center mt-2">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <button class="btn btn-outline-dark rounded-0">Log in</button>
                        </div>
                        <div class="col-3">
                            <button type="button" class="btn btn-outline-dark rounded-0" onclick="clearForm()">&nbsp;Clear&nbsp;</button>
                        </div>
                        <div class="col-3"></div>
                    </div>

                    <div class="text-center mt-4">
                        <?php
                        // Display error messages
                        if (!empty($errors)) {
                            echo '<div class="alert alert-danger" role="alert">';
                            foreach ($errors as $error) {
                                echo $error . '<br>';
                            }
                            echo '</div>';
                        }
                        ?>
                    </div>
                </form>
            </div>
            <div class="col-3"></div>
        </div>
    </div>

    <script>
        function clearForm() {
            document.getElementById("email").value = "";
            document.getElementById("pass").value = "";
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>
</html>
