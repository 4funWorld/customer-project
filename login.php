<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'customers');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$loginErrorMessage = '';

if (isset($_POST['login'])) {
    $username = $_POST['login_username'];
    $password = $_POST['login_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['username'] = $username;
        
        header("Location: welcome.php");
        exit();
    } else {
        $loginErrorMessage = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label, input {
            margin-bottom: 10px;
        }
        input {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            font-size: 16px;
            background-color: #3d9cbb;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #029fd2;
        }
        p {
            text-align: center;
        }
        a {
            color: blue;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function showAlert(message) {
            if (message) {
                alert(message);
            }
        }
    </script>
</head>
<body onload="showAlert('<?php echo $loginErrorMessage; ?>');">
    <div class="container">
        <h2>Login</h2>
        <form action="" method="POST">
            <label for="login_username">Username:</label>
            <input type="text" name="login_username" required>
            <label for="login_password">Password:</label>
            <input type="password" name="login_password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
