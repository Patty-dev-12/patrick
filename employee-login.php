<?php
session_start();
include 'db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $password = md5($_POST['password']); // MD5 just for example

    $query = "SELECT * FROM employees WHERE employee_id='$employee_id' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION['employee_id'] = $employee_id;
        header("Location: employee-dashboard.php");
        exit();
    } else {
        $error = "Invalid Employee ID or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Login - HVH</title>
    <style>
        body {
            background-color: #0b1d40;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-container img {
            width: 100px;
            margin-bottom: 20px;
        }

        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }

        .form-group label {
            font-weight: bold;
            color: #0b1d40;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #d4af37;
            color: #0b1d40;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <img src="assets/img/mainlogo.png" alt="HVH Logo">
        <h2>Employee Login</h2>

        <form action="" method="POST">
            <div class="form-group">
                <label for="employee_id">Employee ID</label>
                <input type="text" name="employee_id" id="employee_id" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>

            <button type="submit" class="btn-login">Log In</button>
        </form>

        <?php if ($error != ""): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
