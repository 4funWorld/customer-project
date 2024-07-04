<?php
session_start();

$conn = new mysqli('localhost', 'root', '', 'customers');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function generateUniqueCode($conn) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    do {
        $uniqueCode = '';
        for ($i = 0; $i < 7; $i++) {
            $uniqueCode .= $characters[rand(0, $charactersLength - 1)];
        }
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE unique_code = ?");
        $stmt->bind_param("s", $uniqueCode);
        $stmt->execute();
        $stmt->store_result();
    } while ($stmt->num_rows > 0);
    $stmt->close();
    return $uniqueCode;
}

$uniqueCodeMessage = '';

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $uniqueCode = generateUniqueCode($conn);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, unique_code) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $uniqueCode);

    if ($stmt->execute()) {
        $uniqueCodeMessage = "Registration successful! Your unique code is: " . $uniqueCode;
    } else {
        $uniqueCodeMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
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
<body onload="showAlert('<?php echo $uniqueCodeMessage; ?>');">
    <div class="container">
        <h2>Register</h2>
        <form action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Password:</label>
            <input type="password" name="password" pattern="(?=.*\d)(?=.*[a-zA-Z]).{8,}" 
                   title="Password must contain at least one letter, one number, and be at least 8 characters long" required>
            <button type="submit" name="register">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
    <script>
        document.getElementById('email').addEventListener('input', function() {
            var email = this.value;
            var validEmail = email.toLowerCase().endsWith('@gmail.com');
            if (!validEmail) {
                this.setCustomValidity('Please enter a valid Gmail address (example@gmail.com)');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
