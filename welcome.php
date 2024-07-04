<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'customers');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT unique_code FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($unique_code);
$stmt->fetch();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .header {
            width: 100%;
            background-color: #029fd2;
            padding: 40px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            left: 0;
        }
        .header p {
            margin: 0;
            color: white;
            font-size: 20px;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-weight: bold;
            margin-right: 40px;
            font-size: 20px;
        }
        .header a:hover {
            text-decoration: underline;
        }
        .content {
            text-align: center;
            margin-top: 100px;
        }
        .content h1 {
            font-size: 2em;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p>user id: <?php echo htmlspecialchars($unique_code); ?></p>
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h1>Hello, <?php echo htmlspecialchars($username); ?>!</h1>
    </div>
</body>
</html>
