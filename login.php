<?php
session_start();
require 'dpp.php'; // Database connection
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    if ($user_type == "student") {
        $stmt = $conn->prepare("SELECT roll_number, password_hash FROM students WHERE roll_number = ?");
    } else {
        $stmt = $conn->prepare("SELECT username, password_hash FROM users WHERE username = ?");
    }

    $stmt->bind_param("s", $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $identifier;
            $_SESSION['user_type'] = $user_type;

            if ($user_type == "student") {
                $_SESSION['roll_number'] = $user['roll_number'];
            } elseif ($user_type == "user") {
                $_SESSION['username'] = $user['username'];
            }

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Incorrect Password!";
        }
    } else {
        $error = "User Not Found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 1.5rem;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px;
            width: 100%;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            font-size: 0.875rem;
            margin-bottom: 10px;
        }
        @media (max-width: 576px) {
            .container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Student Login</h2>
        <?php if (isset($error) && $_POST['user_type'] == 'student') echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="hidden" name="user_type" value="student">
            <div class="mb-3">
                <input type="text" name="identifier" class="form-control" placeholder="Roll Number" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>

</body>
</html>
