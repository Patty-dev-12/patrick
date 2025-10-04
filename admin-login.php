<?php
session_start();
require 'config/db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Check user in hr_staff table
    $stmt = $pdo->prepare("SELECT * FROM hr_staff WHERE email = :username AND name LIKE '%Admin%'");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // For demo purposes, using simple password check
        // In real application, use password_verify()
        if ($password === "admin123") { // Default password for demo
            $_SESSION['user_id'] = $user['hr_id'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = 'admin';
            
            header("Location: pages/dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HVH - Admin Login</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100..900&display=swap');
    
    * { 
        margin: 0; 
        padding: 0; 
        box-sizing: border-box; 
        font-family: "Poppins", sans-serif; 
        text-decoration: none; 
        list-style: none; 
    }
    
    body { 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        min-height: 100vh; 
        background: linear-gradient(90deg, #e2e2e2, #c9d6ff); 
    }
    
    .login-container {
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        width: 400px;
        text-align: center;
    }
    
    .logo {
        margin-bottom: 30px;
    }
    
    .logo img {
        max-width: 120px;
        height: auto;
    }
    
    .login-container h1 {
        font-size: 28px;
        margin-bottom: 10px;
        color: #1a3a6c;
    }
    
    .login-container p {
        color: #666;
        margin-bottom: 30px;
    }
    
    .input-box {
        position: relative;
        margin: 25px 0;
    }
    
    .input-box input {
        width: 100%;
        padding: 15px 50px 15px 20px;
        background: #f5f5f5;
        border-radius: 10px;
        border: 2px solid transparent;
        outline: none;
        font-size: 16px;
        color: #333;
        transition: all 0.3s ease;
    }
    
    .input-box input:focus {
        border-color: #1a3a6c;
        background: #fff;
    }
    
    .input-box input::placeholder {
        color: #888;
    }
    
    .input-box i {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 20px;
        color: #666;
    }
    
    .btn {
        width: 100%;
        height: 50px;
        background: #1a3a6c;
        border-radius: 10px;
        border: none;
        cursor: pointer;
        font-size: 16px;
        color: #fff;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        background: #2a4a7c;
        transform: translateY(-2px);
    }
    
    .error-message {
        color: #e74c3c;
        margin: 15px 0;
        padding: 10px;
        background: #ffeaea;
        border-radius: 5px;
        border: 1px solid #f5c6cb;
    }
    
    .back-link {
        margin-top: 20px;
    }
    
    .back-link a {
        color: #1a3a6c;
        text-decoration: none;
        font-weight: 500;
    }
    
    .back-link a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/img/mainlogo.png" alt="HVH Logo">
        </div>
        
        <h1>Admin Login</h1>
        <p>Enter your credentials to access the HR Portal</p>
        
        <form method="POST">
            <!-- Username -->
            <div class="input-box">
                <input type="text" name="username" placeholder="Email Address" required value="admin@example.com">
                <i class='bx bxs-user'></i>
            </div>

            <!-- Password -->
            <div class="input-box">
                <input type="password" name="password" placeholder="Password" required value="admin123">
                <i class='bx bxs-lock-alt'></i>
            </div>

            <!-- Error message -->
            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Login Button -->
            <button type="submit" class="btn">Login to HR Portal</button>
        </form>
        
        <div class="back-link">
            <a href="landing-pages.php">← Back to Home</a>
        </div>
        
        <!-- Demo Credentials -->
        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-size: 12px; color: #666;">
            <strong>Demo Credentials:</strong><br>
            Email: admin@example.com<br>
            Password: admin123
        </div>
    </div>
</body>
</html>