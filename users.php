<?php
session_start();
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

require_once "db_connect.php";

// Function to list users
function listUsers($conn) {
    $sql = "SELECT id, username FROM admins";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<h2>User List</h2>";
        echo "<table class='table'>";
        echo "<thead><tr><th>ID</th><th>Username</th><th>Actions</th></tr></thead>";
        echo "<tbody>";
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"]. "</td>";
            echo "<td>" . $row["username"]. "</td>";
            echo "<td><a href='?edit=" . $row["id"] . "'>Edit</a> | <a href='?delete=" . $row["id"] . "'>Delete</a></td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "No users found.";
    }
}

// Function to add a user
function addUser($conn, $username, $password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO admins (username, password) VALUES ('$username', '$hashed_password')";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>User added successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding user: " . $conn->error . "</div>";
    }
}

// Function to edit a user
function editUser($conn, $id) {
    // Implement edit user form here
}

// Function to delete a user
function deleteUser($conn, $id) {
    $sql = "DELETE FROM admins WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>User deleted successfully</div>";
    } else {
        echo "<div class='alert alert-danger'>Error deleting user: " . $conn->error . "</div>";
    }
}

// Handle actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["addUser"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        addUser($conn, $username, $password);
    }
}

if (isset($_GET["delete"])) {
    $id = $_GET["delete"];
    deleteUser($conn, $id);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 80%; padding: 20px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>User Management</h1>
        <?php listUsers($conn); ?>

        <h2>Add New User</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <input type="submit" name="addUser" class="btn btn-primary" value="Add User">
        </form>
        <p>
            <a href="dashboard.php" class="btn btn-secondary ml-3">Back to Dashboard</a>
        </p>
    </div>
</body>
</html>

<?php
$conn->close();
?>