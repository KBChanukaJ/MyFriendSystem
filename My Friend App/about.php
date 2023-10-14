<?php
// Start session (if not started)
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <style>
        html, body {
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
        <div class="row" style="margin-top: 15%;">
            <div class="col-3"></div>
            <div class="col-6">
                <form class="pt-2 ps-3 pb-2 pe-3" style="border: 5px solid gray">
                    <div class="text-center">
                        <h5 class="fw-bold">My Friend System </h5>
                        <h5 class="fw-bold">About Page </h5>
                    </div>
                    <h2 class="mt-4">Tasks Completed:</h2>
                    <ul>
                        <li>Task 1: Home Page - index.php</li>
                        <li>Task 2: Registration Page - register.php</li>
                        <!-- Include other completed tasks -->
                    </ul>

                    <h2>Tasks Not Attempted or Not Completed:</h2>
                    <ul>
                        <li>Task 3: Login Page - login.php</li>
                        <!-- Include other incomplete tasks -->
                    </ul>

                    <h2>Special Features:</h2>
                    <p>Include any special features or functionalities you implemented.</p>

                    <h2>Trouble Areas:</h2>
                    <p>Describe any challenges or difficulties you encountered.</p>

                    <h2>Areas for Improvement:</h2>
                    <p>What you would like to do better next time?</p>

                    <h2>Additional Features:</h2>
                    <p>List any extra features you added to the assignment.</p>

                    <h2>Links to Pages:</h2>
                    <ul>
                        <li><a href="friendlist.php">Friend List</a></li>
                        <li><a href="friendadd.php">Add Friends</a></li>
                        <li><a href="index.php">Home Page</a></li>
                    </ul>

                    <h2>Discussion Response Screenshot:</h2>
                    <!-- Include a screenshot or description of the discussion response -->


                    <br>
                    <div class="text-center mt-4"><a href="index.php">Home</a></div>
                </form>
            </div>
            <div class="col-3"></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>
</html>
