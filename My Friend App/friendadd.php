<?php
// Initialize session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's profile name and number of friends
$loggedInEmail = $_SESSION['user_email'];
include('connection.php');

$query = "SELECT friend_id, profile_name, num_of_friends FROM friends WHERE friend_email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loggedInEmail);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$loggedInUserId = $row['friend_id'];
$loggedInUser = $row['profile_name'];
$numOfFriends = $row['num_of_friends']; // Initialize $numOfFriends

// Pagination setup
$namesPerPage = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $namesPerPage;

// Query to get the list of users who are not friends of the logged-in user
$query = "SELECT friend_id, profile_name FROM friends WHERE profile_name <> ? AND friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?) LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("siii", $loggedInUser, $loggedInUserId, $offset, $namesPerPage);
$stmt->execute();
$result = $stmt->get_result();
$usersList = $result->fetch_all(MYSQLI_ASSOC);

// Count total users (for pagination)
$countQuery = "SELECT COUNT(*) AS total FROM friends WHERE profile_name <> ? AND friend_id NOT IN (SELECT friend_id2 FROM myfriends WHERE friend_id1 = ?)";
$countStmt = $conn->prepare($countQuery);
$countStmt->bind_param("si", $loggedInUser, $loggedInUserId);
$countStmt->execute();
$countResult = $countStmt->get_result();
$countRow = $countResult->fetch_assoc();
$totalUsers = $countRow['total'];

// Calculate total pages
$totalPages = ceil($totalUsers / $namesPerPage);

// Process add friend action
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['addFriend'] as $friendId => $value) {
        // Assuming you have a way to get the friend's profile name from the form
        $friendProfileName = $_POST['friendProfileName'][$friendId];

        // Get friend's ID
        $query = "SELECT friend_id FROM friends WHERE profile_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $friendProfileName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $friendId = $row['friend_id'];

        // Insert into myfriends table
        $query = "INSERT INTO myfriends (friend_id1, friend_id2) VALUES (?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $loggedInUserId, $friendId);
        $stmt->execute();

        // Update num_of_friends in friends table
        $numOfFriends++;

        $query = "UPDATE friends SET num_of_friends = ? WHERE friend_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $numOfFriends, $loggedInUserId);
        $stmt->execute();
    }

    // Redirect to refresh the page after adding friend
    header("Location: friendadd.php?page=$page");
    exit();
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

        #table {
            border-collapse: collapse;
            width: 100%;
            border-style: double;
            border: 2px solid black;
        }

        #table td, #table th {
            border: 2px solid black;
            padding: 8px;
            border-style: double;
        }

        #table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            color: black;
            border-style: double;
        }
    </style>
</head>
<body>
<div class="container">
        <div class="row" style="margin-top:5%;">
            <div class="col-3"></div>
            <div class="col-6">
                <form method="post"  style="border: 5px solid gray" action="">
                    <div class="text-center">
                        <h5 class="fw-bold">My Friend System</h5>
                        <h5 class="fw-bold"><?php echo $loggedInUser; ?>'s Friend List Page</h5>
                        <h5 class="fw-bold">Total number of friends is <?php echo $numOfFriends; ?></h5>
                    </div>

                    <div class="row">
                        <div class="col-1"></div>
                        <div class="col-10">
                            <div class="text-center mt-4">
                                <table id="table">
                                    <tbody>
                                        <?php
                                            // Display users in the table
                                            foreach ($usersList as $user) {
                                                echo '<tr>';
                                                echo '<td width="70%" class="p-3">' . $user['profile_name'] . '</td>';
                                                echo '<td width="30%" class="p-3">';
                                                $friendId = $user['friend_id'];
                                                echo '<input type="hidden" name="friendProfileName[' . $friendId . ']" value="' . $user['profile_name'] . '">';
                                                echo '<button type="submit" name="addFriend[' . $friendId . ']" class="btn btn-outline-dark btn-sm rounded-0">Add as Friend</button>';
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-1"></div>
                    </div>

                    <div class="row text-center mt-2">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <?php if ($page > 1) : ?>
                                <a href="./friendadd.php?page=<?php echo $page - 1; ?>">Previous</a>
                            <?php endif; ?>
                        </div>
                        <div class="col-3">
                            <?php if ($page < $totalPages) : ?>
                                <a href="./friendadd.php?page=<?php echo $page + 1; ?>">Next</a>
                            <?php endif; ?>
                        </div>
                        <div class="col-3"></div>
                    </div>
                    <div class="row text-center mt-2">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <a href="./friendlist.php">Friends List</a>
                        </div>
                        <div class="col-3">
                            <a href="./logout.php">&nbsp;Logout&nbsp;</a>
                        </div>
                        <div class="col-3"></div>
                    </div>
                </form>
            </div>
            <div class="col-3"></div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3" crossorigin="anonymous"></script>
</body>
</html>
