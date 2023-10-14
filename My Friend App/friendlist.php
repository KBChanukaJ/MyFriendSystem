<?php
// Include the database connection file
include('connection.php');

// Initialize session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's profile name
$loggedInEmail = $_SESSION['user_email'];

if ($loggedInEmail) {
    $query = "SELECT friend_id, profile_name, num_of_friends FROM friends WHERE friend_email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $loggedInEmail);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $loggedInUserId = $row['friend_id'];
    $loggedInUser = $row['profile_name'];
    $numOfFriends = $row['num_of_friends'];
} else {
    // Handle the case when user_email is not set in the session
    // Redirect to login or handle it according to your logic
    header("Location: login.php");
    exit();
}

// Function to get friends list for the logged-in user with mutual friend count
function getFriendsListWithMutualCount($conn, $loggedInUser) {
    $query = "SELECT friends.profile_name, friends.friend_id,
              (SELECT COUNT(*)
               FROM myfriends AS mutual
               WHERE mutual.friend_id1 = friends.friend_id
                 AND mutual.friend_id2 IN (SELECT friend_id2
                                           FROM myfriends
                                           WHERE friend_id1 = (SELECT friend_id
                                                              FROM friends
                                                              WHERE profile_name = ?))) AS mutual_friend_count
              FROM friends
              WHERE friends.friend_id IN (SELECT friend_id2
                                           FROM myfriends
                                           WHERE friend_id1 = (SELECT friend_id
                                                              FROM friends
                                                              WHERE profile_name = ?))";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $loggedInUser, $loggedInUser);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Check if the form is submitted and the unfriend button is clicked
// Process unfriend action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unfriend'])) {
    // Assuming you have a way to get the friend's profile name from the form
    $friendProfileName = isset($_POST['friendProfileName']) ? $_POST['friendProfileName'] : null;

    if ($friendProfileName) {
        // Get friend's ID
        $query = "SELECT friend_id FROM friends WHERE profile_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $friendProfileName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $friendId = $row['friend_id'];

        // Delete the friend record from myfriends table
        $deleteQuery = "DELETE FROM myfriends WHERE friend_id1 = ? AND friend_id2 = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("ii", $loggedInUserId, $friendId);
        $deleteStmt->execute();

        // Update num_of_friends in friends table
        $numOfFriends--;

        $updateQuery = "UPDATE friends SET num_of_friends = ? WHERE friend_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $numOfFriends, $loggedInUserId);
        $updateStmt->execute();

        // Redirect to refresh the page after unfriending
        header("Location: friendlist.php");
        exit();
    }
}

// Get the friends list for the logged-in user with mutual friend count
$friendsListWithMutualCount = getFriendsListWithMutualCount($conn, $loggedInUser);
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
    <form method="post" style="border: 5px solid gray" action="">
        <div class="text-center">
                    <h5 class="fw-bold">My Friend System </h5>
                    <h5 class="fw-bold"><?php echo $loggedInUser; ?>'s Friend List Page</h5>
                    <h5 class="fw-bold">Total number of friends is <?php echo $numOfFriends; ?></h5>
                </div>

                <div class="row">
                    <div class="col-1"></div>
                    <div class="col-10">
                        <div class="text-center mt-4">
                            <table id="table">
                                <!-- <thead>
                                <tr>
                                    <th width="50%" class="p-3">Profile Name</th>
                                    <th width="30%" class="p-3">Mutual Friends</th>
                                    <th width="20%" class="p-3">Actions</th>
                                </tr>
                                </thead> -->
                                <tbody>
        <?php foreach ($friendsListWithMutualCount as $friend) : ?>
            <tr>
                <td width="50%" class="p-3"><?php echo $friend['profile_name']; ?></td>
                <td width="30%" class="p-3"><?php echo $friend['mutual_friend_count']; ?> mutual friends</td>
                <td width="20%" class="p-3">
                    <input type="hidden" name="friendProfileName" value="<?php echo $friend['profile_name']; ?>">
                    <button type="submit" name="unfriend" class="btn btn-outline-dark btn-sm rounded-0">Unfriend</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-1"></div>
                    <div class="row text-center mt-2">
                        <div class="col-3"></div>
                        <div class="col-3">
                            <a href="./friendadd.php">Add Friends</a>
                        </div>
                        <div class="col-3">
                            <a href="./logout.php">&nbsp;Logout&nbsp;</a>
                        </div>
                        <div class="col-3"></div>
                    </div>

                </div>
            </div>
            <div class="col-1"></div>
        </div>
    </form>
</div>
</body>
</html>
