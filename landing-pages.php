<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HVH Enterprises</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        .background-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(rgba(26, 58, 108, 0.85), rgba(26, 58, 108, 0.9)),
                        url('assets/img/mainlogo.png');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header Styles */
        header {
            color: white;
            padding: 30px 0;
            text-align: center;
        }

        .logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .logo img {
            max-width: 180px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .tagline {
            font-size: 18px;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .sub-tagline {
            font-size: 16px;
            font-style: italic;
            opacity: 0.9;
        }

        /* Main Content */
        .main-content {
            padding: 50px 0;
            text-align: center;
        }

        .section-title {
            font-size: 36px;
            margin-bottom: 40px;
            color: white;
            text-transform: uppercase;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .options {
            display: flex;
            justify-content: center;
            gap: 50px;
            margin-top: 30px;
        }

        .option-box {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 300px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .option-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
        }

        .option-title {
            font-size: 28px;
            margin-bottom: 15px;
            color: #1a3a6c;
        }

        .option-description {
            color: #666;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            background-color: #1a3a6c;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, transform 0.2s;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #2a4a7c;
            transform: scale(1.05);
        }

        /* Footer */
        footer {
            background-color: rgba(26, 58, 108, 0.9);
            color: white;
            padding: 30px 0;
            text-align: center;
            margin-top: 50px;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .footer-column {
            flex: 1;
            text-align: left;
        }

        .footer-column h3 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .footer-column ul {
            list-style: none;
        }

        .footer-column ul li {
            margin-bottom: 8px;
        }

        .footer-column ul li a {
            color: #ddd;
            text-decoration: none;
        }

        .footer-column ul li a:hover {
            text-decoration: underline;
        }

        .copyright {
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 14px;
            color: #ccc;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .options {
                flex-direction: column;
                align-items: center;
                gap: 30px;
            }

            .footer-content {
                flex-direction: column;
                gap: 20px;
            }

            .footer-column {
                text-align: center;
            }

            .logo img {
                max-width: 120px;
            }

            .tagline {
                font-size: 16px;
            }

            .section-title {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <!-- Background Container -->
    <div class="background-container"></div>

    <!-- Header Section -->
    <header>
        <div class="container">
            <div class="logo">
                <img src="assets/img/mainlogo.png" alt="HVH Logo">
            </div>
            <div class="tagline">PORTAL</div>
            <div class="sub-tagline">Your trusted partner for human resources management and employee development</div>
        </div>
    </header>

    <!-- Main Content -->
    <section class="main-content">
        <div class="container">
            <h2 class="section-title">Select Your Portal</h2>
            <div class="options">
                <div class="option-box">
                    <h3 class="option-title">EMPLOYEE</h3>
                    <p class="option-description">Access your employee dashboard for schedules, payroll, and personal information.</p>
                    <a href="./admin-login.php" class="btn">Employee Login</a>
                </div>
                
                <div class="option-box">
                    <h3 class="option-title">HR</h3>
                    <p class="option-description">Access the HR portal for employee management and administrative functions.</p>
                    <a href="./employee-login.php" class="btn">HR Login</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>About HVH</h3>
                    <p>Providing quality services through our dedicated employees and efficient HR management.</p>
                </div>
                
                <div class="footer-column">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Employee Portal</a></li>
                        <li><a href="">HR Portal</a></li>
                        <li><a href="#">Contact</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contact</h3>
                    <ul>
                        <li>Email: info@hvh.com</li>
                        <li>Phone: (02) 123-4567</li>
                        <li>Address: Metro Manila, Philippines</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 HVH Enterprises. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
