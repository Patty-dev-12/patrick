<?php
session_start();

// Database connection
$host = '127.0.0.1';
$dbname = 'hr2_db';
$username = 'root'; // Change as needed
$password = ''; // Change as needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get employee data from database (using employee_id = 1 for demo)
$employee_id = 1; // You can change this based on logged-in user

// First, let's check what employees exist in the database
$check_stmt = $pdo->query("SELECT employee_id, first_name, last_name FROM employee LIMIT 5");
$available_employees = $check_stmt->fetchAll(PDO::FETCH_ASSOC);

// Debug: Show available employees
error_log("Available employees: " . print_r($available_employees, true));

$stmt = $pdo->prepare("
    SELECT e.*, d.department_name, p.title as position_title 
    FROM employee e 
    LEFT JOIN department d ON e.fk_department_id = d.department_id 
    LEFT JOIN position p ON e.fk_position_id = p.position_id 
    WHERE e.employee_id = ?
");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

// If employee not found, try to get any employee
if (!$employee) {
    $fallback_stmt = $pdo->query("SELECT * FROM employee LIMIT 1");
    $employee = $fallback_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$employee) {
        die("No employees found in database. Please check your database.");
    }
    $employee_id = $employee['employee_id'];
}

// Format employee data for the template
$employee_data = [
    'id' => $employee['employee_id'],
    'name' => $employee['first_name'] . ' ' . $employee['last_name'],
    'employee_id' => 'EMP' . str_pad($employee['employee_id'], 3, '0', STR_PAD_LEFT),
    'department' => $employee['department_name'] ?? 'Not assigned',
    'position' => $employee['position_title'] ?? 'Not assigned',
    'email' => $employee['email'],
    'phone' => $employee['phone_number'] ?? 'Not provided',
    'address' => $employee['address'] ?? 'Not provided',
    'hire_date' => $employee['hire_date'],
    'emergency_contact' => 'Not provided' // You can add this field to your employee table
];

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'update_personal':
                // Update personal information
                $stmt = $pdo->prepare("UPDATE employee SET first_name = ?, last_name = ?, email = ?, phone_number = ?, address = ? WHERE employee_id = ?");
                $name_parts = explode(' ', $_POST['full_name'], 2);
                $first_name = $name_parts[0];
                $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
                
                $stmt->execute([
                    $first_name,
                    $last_name,
                    $_POST['email'],
                    $_POST['phone'],
                    $_POST['address'],
                    $employee_id
                ]);
                
                $_SESSION['success'] = 'Personal information updated successfully!';
                break;
                
            case 'submit_leave':
                // Submit leave request
                $stmt = $pdo->prepare("INSERT INTO leave_request (fk_employee_id, type, start_date, end_date, status) VALUES (?, ?, ?, ?, 'Pending')");
                $stmt->execute([
                    $employee_id,
                    $_POST['leave_type'],
                    $_POST['start_date'],
                    $_POST['end_date']
                ]);
                
                $_SESSION['success'] = 'Leave request submitted successfully!';
                break;
                
            case 'register_training':
                // Register for training
                // First, get training_id from training table based on title
                $training_stmt = $pdo->prepare("SELECT training_id FROM training WHERE title = ?");
                $training_stmt->execute([$_POST['training_name']]);
                $training = $training_stmt->fetch();
                
                if ($training) {
                    // Check if already registered
                    $check_reg_stmt = $pdo->prepare("SELECT * FROM training_registration WHERE fk_employee_id = ? AND fk_training_id = ?");
                    $check_reg_stmt->execute([$employee_id, $training['training_id']]);
                    
                    if ($check_reg_stmt->fetch()) {
                        $_SESSION['error'] = 'You are already registered for this training!';
                    } else {
                        $stmt = $pdo->prepare("INSERT INTO training_registration (fk_employee_id, fk_training_id, registration_date) VALUES (?, ?, CURDATE())");
                        $stmt->execute([
                            $employee_id,
                            $training['training_id']
                        ]);
                        $_SESSION['success'] = 'Training registration submitted successfully!';
                    }
                } else {
                    $_SESSION['error'] = 'Training not found!';
                }
                break;
        }
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Get leave requests for this employee
$leave_stmt = $pdo->prepare("SELECT * FROM leave_request WHERE fk_employee_id = ? ORDER BY start_date DESC LIMIT 5");
$leave_stmt->execute([$employee_id]);
$leave_requests = $leave_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get payroll information
$payroll_stmt = $pdo->prepare("SELECT * FROM payroll WHERE fk_employee_id = ? ORDER BY pay_date DESC LIMIT 1");
$payroll_stmt->execute([$employee_id]);
$latest_payroll = $payroll_stmt->fetch(PDO::FETCH_ASSOC);

// Get benefits information
$benefits_stmt = $pdo->prepare("SELECT * FROM benefits WHERE fk_employee_id = ?");
$benefits_stmt->execute([$employee_id]);
$benefits = $benefits_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get training registrations
$training_stmt = $pdo->prepare("
    SELECT tr.*, t.title, t.date, t.trainer 
    FROM training_registration tr 
    JOIN training t ON tr.fk_training_id = t.training_id 
    WHERE tr.fk_employee_id = ? 
    ORDER BY tr.registration_date DESC
");
$training_stmt->execute([$employee_id]);
$my_trainings = $training_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available trainings (not yet registered)
$available_trainings_stmt = $pdo->prepare("
    SELECT t.* 
    FROM training t
    WHERE t.date >= CURDATE() 
    AND t.training_id NOT IN (
        SELECT fk_training_id FROM training_registration WHERE fk_employee_id = ?
    )
    ORDER BY t.date ASC
");
$available_trainings_stmt->execute([$employee_id]);
$available_trainings = $available_trainings_stmt->fetchAll(PDO::FETCH_ASSOC);

// If no trainings in database, add some sample ones
if (empty($available_trainings)) {
    $check_trainings = $pdo->query("SELECT COUNT(*) as count FROM training")->fetch();
    if ($check_trainings['count'] == 0) {
        // Add sample trainings
        $sample_trainings = [
            ['Leadership Development', '2025-02-15', 'John Smith'],
            ['Digital Marketing Fundamentals', '2025-03-05', 'Maria Garcia'],
            ['Project Management', '2025-04-10', 'Robert Johnson']
        ];
        
        foreach ($sample_trainings as $training) {
            $stmt = $pdo->prepare("INSERT INTO training (title, date, trainer) VALUES (?, ?, ?)");
            $stmt->execute($training);
        }
        
        // Refresh available trainings
        $available_trainings_stmt->execute([$employee_id]);
        $available_trainings = $available_trainings_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Get current active tab
$activeTab = $_GET['tab'] ?? 'personal';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Self-Service Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64">
            <?php include 'ess-sidebar.php'; ?>
        </div>
        
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center">
                            <i class="fas fa-user-circle text-3xl text-blue-600 mr-3"></i>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Employee Self-Service</h1>
                                <p class="text-sm text-gray-500">Welcome, <?php echo htmlspecialchars($employee_data['name']); ?></p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Employee ID: <?php echo $employee_data['employee_id']; ?></p>
                                <p class="text-sm text-gray-500"><?php echo $employee_data['department']; ?></p>
                            </div>
                            <!-- Logout Button -->
                            <div class="relative group">
                                <button onclick="toggleDropdown()" class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg transition-colors">
                                    <i class="fas fa-user-circle text-blue-600"></i>
                                    <span class="text-sm font-medium"><?php echo explode(' ', $employee_data['name'])[0]; ?></span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden group-hover:block z-50">
                                    <div class="py-2">
                                        <a href="?tab=personal" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-user-edit mr-3 text-gray-400"></i>
                                            My Profile
                                        </a>
                                        <a href="?tab=payroll" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-file-invoice-dollar mr-3 text-gray-400"></i>
                                            Payslips
                                        </a>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <a href="?logout=true" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt mr-3"></i>
                                            Logout
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Success Message -->
            <?php if (isset($_SESSION['success'])): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($_SESSION['error'])): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Main Content -->
            <div class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Navigation Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <a href="?tab=personal" class="<?php echo $activeTab === 'personal' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-user mr-2"></i>
                            Personal Update
                        </a>
                        <a href="?tab=leave" class="<?php echo $activeTab === 'leave' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Leave & Time Off
                        </a>
                        <a href="?tab=payroll" class="<?php echo $activeTab === 'payroll' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-money-check-alt mr-2"></i>
                            Payroll & Benefits
                        </a>
                        <a href="?tab=training" class="<?php echo $activeTab === 'training' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Training Registration
                        </a>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="mt-8">
                    <?php if ($activeTab === 'personal'): ?>
                    <!-- Personal Information Update -->
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-xl font-semibold text-gray-900">
                                <i class="fas fa-user-edit mr-2 text-blue-600"></i>
                                Personal Information
                            </h2>
                        </div>
                        <form method="POST" class="p-6">
                            <input type="hidden" name="action" value="update_personal">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                    <input type="text" name="full_name" value="<?php echo htmlspecialchars($employee_data['name']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($employee_data['email']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                    <input type="tel" name="phone" value="<?php echo htmlspecialchars($employee_data['phone']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact</label>
                                    <input type="text" name="emergency_contact" value="<?php echo htmlspecialchars($employee_data['emergency_contact']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Home Address</label>
                                    <textarea name="address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($employee_data['address']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex justify-end">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Update Information
                                </button>
                            </div>
                        </form>
                    </div>

                    <?php elseif ($activeTab === 'leave'): ?>
                    <!-- Leave & Time Off -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Leave Request Form -->
                        <div class="lg:col-span-2">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-900">
                                        <i class="fas fa-calendar-plus mr-2 text-blue-600"></i>
                                        Submit Leave Request
                                    </h2>
                                </div>
                                <form method="POST" class="p-6">
                                    <input type="hidden" name="action" value="submit_leave">
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Leave Type</label>
                                            <select name="leave_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                                <option value="">Select Leave Type</option>
                                                <option value="vacation">Vacation Leave</option>
                                                <option value="sick">Sick Leave</option>
                                                <option value="emergency">Emergency Leave</option>
                                                <option value="maternity">Maternity Leave</option>
                                                <option value="paternity">Paternity Leave</option>
                                            </select>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                                            <textarea name="reason" rows="4" placeholder="Please provide the reason for your leave..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-6 flex justify-end">
                                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-paper-plane mr-2"></i>Submit Request
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Leave Balance and Recent Requests -->
                        <div class="space-y-6">
                            <div class="bg-white shadow rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-chart-bar mr-2 text-green-600"></i>
                                    Leave Balance
                                </h3>
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Vacation Leave</span>
                                        <span class="text-sm font-medium text-gray-900">15 days</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Sick Leave</span>
                                        <span class="text-sm font-medium text-gray-900">10 days</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Emergency Leave</span>
                                        <span class="text-sm font-medium text-gray-900">5 days</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                    <i class="fas fa-history mr-2 text-blue-600"></i>
                                    Recent Requests
                                </h3>
                                <div class="space-y-3">
                                    <?php if (empty($leave_requests)): ?>
                                        <p class="text-sm text-gray-500">No leave requests found.</p>
                                    <?php else: ?>
                                        <?php foreach ($leave_requests as $request): ?>
                                        <div class="border-l-4 border-<?php echo $request['status'] === 'Approved' ? 'green' : ($request['status'] === 'Pending' ? 'yellow' : 'red'); ?>-400 pl-4">
                                            <p class="text-sm font-medium text-gray-900"><?php echo ucfirst($request['type']); ?> Leave</p>
                                            <p class="text-xs text-gray-500"><?php echo $request['start_date'] . ' to ' . $request['end_date']; ?> • <?php echo $request['status']; ?></p>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'payroll'): ?>
                    <!-- Payroll & Benefits -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Payroll Information -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-file-invoice-dollar mr-2 text-green-600"></i>
                                    <?php echo $latest_payroll ? 'Latest Payslip' : 'No Payslip Available'; ?>
                                </h2>
                            </div>
                            <div class="p-6">
                                <?php if ($latest_payroll): ?>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Pay Date</span>
                                        <span class="text-sm font-medium text-gray-900"><?php echo $latest_payroll['pay_date']; ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Basic Salary</span>
                                        <span class="text-sm font-medium text-gray-900">₱<?php echo number_format($latest_payroll['salary'], 2); ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Deductions</span>
                                        <span class="text-sm font-medium text-red-600">-₱<?php echo number_format($latest_payroll['deductions'], 2); ?></span>
                                    </div>
                                    <hr class="my-3">
                                    <div class="flex justify-between items-center text-lg font-semibold">
                                        <span class="text-gray-900">Net Pay</span>
                                        <span class="text-green-600">₱<?php echo number_format($latest_payroll['salary'] - $latest_payroll['deductions'], 2); ?></span>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <button class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                                        <i class="fas fa-download mr-2"></i>Download Payslip
                                    </button>
                                </div>
                                <?php else: ?>
                                <p class="text-gray-500">No payroll information available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Benefits Information -->
                        <div class="space-y-6">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h2 class="text-xl font-semibold text-gray-900">
                                        <i class="fas fa-shield-alt mr-2 text-blue-600"></i>
                                        Benefits Overview
                                    </h2>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-4">
                                        <?php if (empty($benefits)): ?>
                                            <p class="text-gray-500">No benefits information available.</p>
                                        <?php else: ?>
                                            <?php foreach ($benefits as $benefit): ?>
                                            <div class="border border-gray-200 rounded-lg p-4">
                                                <h4 class="font-medium text-gray-900"><?php echo $benefit['benefit_type']; ?></h4>
                                                <p class="text-sm text-gray-600 mt-1"><?php echo $benefit['provider']; ?></p>
                                                <p class="text-xs text-green-600 mt-1">Active</p>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <i class="fas fa-history mr-2 text-purple-600"></i>
                                        Payroll History
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <?php
                                    $payroll_history_stmt = $pdo->prepare("SELECT * FROM payroll WHERE fk_employee_id = ? ORDER BY pay_date DESC LIMIT 5");
                                    $payroll_history_stmt->execute([$employee_id]);
                                    $payroll_history = $payroll_history_stmt->fetchAll(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="space-y-3">
                                        <?php if (empty($payroll_history)): ?>
                                            <p class="text-gray-500">No payroll history available.</p>
                                        <?php else: ?>
                                            <?php foreach ($payroll_history as $payroll): ?>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-600"><?php echo $payroll['pay_date']; ?></span>
                                                <span class="text-sm font-medium text-gray-900">₱<?php echo number_format($payroll['salary'] - $payroll['deductions'], 2); ?></span>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php elseif ($activeTab === 'training'): ?>
                    <!-- Training Registration -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Available Trainings -->
                        <div class="bg-white shadow rounded-lg">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h2 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-book-open mr-2 text-blue-600"></i>
                                    Available Trainings
                                </h2>
                            </div>
                            <div class="p-6 space-y-4">
                                <?php if (empty($available_trainings)): ?>
                                    <p class="text-gray-500">No available trainings at the moment.</p>
                                <?php else: ?>
                                    <?php foreach ($available_trainings as $training): ?>
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-medium text-gray-900"><?php echo $training['title']; ?></h4>
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded font-medium">Available</span>
                                        </div>
                                        <p class="text-sm text-gray-600 mb-3">Trainer: <?php echo $training['trainer']; ?></p>
                                        <div class="text-xs text-gray-500 space-y-1">
                                            <p><i class="fas fa-calendar mr-1"></i> <?php echo $training['date']; ?></p>
                                        </div>
                                        <button onclick="registerTraining('<?php echo addslashes($training['title']); ?>')" class="mt-3 w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                                            <i class="fas fa-user-plus mr-1"></i>Register
                                        </button>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- My Trainings -->
                        <div class="space-y-6">
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <i class="fas fa-user-graduate mr-2 text-blue-600"></i>
                                        My Registered Trainings
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-4">
                                        <?php if (empty($my_trainings)): ?>
                                            <p class="text-gray-500">No registered trainings.</p>
                                        <?php else: ?>
                                            <?php foreach ($my_trainings as $training): ?>
                                            <div class="border-l-4 border-blue-400 pl-4">
                                                <h4 class="font-medium text-gray-900"><?php echo $training['title']; ?></h4>
                                                <p class="text-sm text-gray-600"><?php echo $training['date']; ?></p>
                                                <p class="text-sm text-gray-600">Trainer: <?php echo $training['trainer']; ?></p>
                                                <p class="text-xs text-blue-600">Registered on: <?php echo $training['registration_date']; ?></p>
                                            </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-white shadow rounded-lg">
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <h3 class="text-lg font-semibold text-gray-900">
                                        <i class="fas fa-chart-line mr-2 text-green-600"></i>
                                        Training Progress
                                    </h3>
                                </div>
                                <div class="p-6">
                                    <div class="space-y-4">
                                        <div>
                                            <div class="flex justify-between text-sm">
                                                <span>Trainings Completed</span>
                                                <span><?php echo count($my_trainings); ?> trainings</span>
                                            </div>
                                        </div>
                                        
                                        <div>
                                            <div class="flex justify-between text-sm">
                                                <span>Upcoming Trainings</span>
                                                <span><?php echo count(array_filter($my_trainings, function($t) { return strtotime($t['date']) > time(); })); ?> scheduled</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php endif; ?>
                </div>
            </div>

            <!-- Training Registration Modal (Hidden by default) -->
            <div id="trainingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">Training Registration</h3>
                            <button onclick="closeTrainingModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <form method="POST" class="p-6">
                        <input type="hidden" name="action" value="register_training">
                        <input type="hidden" name="training_name" id="training_name" value="">
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Training Program</label>
                                <input type="text" id="training_display" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Interest</label>
                                <textarea name="reason" rows="3" placeholder="Why are you interested in this training?" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeTrainingModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>Submit Registration
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function registerTraining(trainingName) {
                    document.getElementById('training_name').value = trainingName;
                    document.getElementById('training_display').value = trainingName;
                    document.getElementById('trainingModal').classList.remove('hidden');
                    document.getElementById('trainingModal').classList.add('flex');
                }
                
                function closeTrainingModal() {
                    document.getElementById('trainingModal').classList.add('hidden');
                    document.getElementById('trainingModal').classList.remove('flex');
                }
                
                // Close modal when clicking outside
                document.getElementById('trainingModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeTrainingModal();
                    }
                });
                
                // User dropdown functionality
                function toggleDropdown() {
                    const dropdown = document.getElementById('userDropdown');
                    dropdown.classList.toggle('hidden');
                }
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(event) {
                    const dropdown = document.getElementById('userDropdown');
                    const button = event.target.closest('button');
                    
                    if (!dropdown.contains(event.target) && !button) {
                        dropdown.classList.add('hidden');
                    }
                });
            </script>
        </div>
    </div>
</body>
</html>