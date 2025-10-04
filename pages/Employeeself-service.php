<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Self-Service Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .active-tab {
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
        }
        .list-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <?php
    // Database connection and session start
    session_start();
    
    // Sample data for demonstration
    $employee = [
        'first_name' => 'Juan',
        'last_name' => 'Dela Cruz',
        'email' => 'juan.delacruz@company.com',
        'phone_number' => '09123456789',
        'address' => '123 Main Street, Manila',
        'department_name' => '',
        'position_title' => ''
    ];
    
    $leave_balance = [
        'vacation' => 12,
        'sick' => 8,
        'emergency' => 3,
        'maternity' => 60,
        'paternity' => 7
    ];
    
    $payroll_data = [
        'pay_date' => '2024-01-15',
        'salary' => 50000,
        'deductions' => 7500
    ];
    
    $benefits_data = [
        ['benefit_type' => 'Health Insurance', 'provider' => 'PhilHealth'],
        ['benefit_type' => 'Retirement Plan', 'provider' => 'SSS'],
        ['benefit_type' => 'Life Insurance', 'provider' => 'InsureLife']
    ];
    
    $trainings = [
        ['training_id' => 1, 'title' => 'Web Development Fundamentals', 'date' => '2024-02-15', 'trainer' => 'John Smith'],
        ['training_id' => 2, 'title' => 'Project Management', 'date' => '2024-03-10', 'trainer' => 'Maria Garcia'],
        ['training_id' => 3, 'title' => 'Leadership Skills', 'date' => '2024-04-05', 'trainer' => 'Robert Johnson']
    ];
    
    $registered_trainings = [
        ['title' => 'Communication Skills', 'date' => '2024-01-20', 'status' => 'completed'],
        ['title' => 'Team Building', 'date' => '2024-02-28', 'status' => 'upcoming']
    ];
    
    $activeTab = $_GET['tab'] ?? 'personal';
    
    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'update_personal':
                $_SESSION['success'] = "";
                break;
                
            case 'submit_leave':
                $_SESSION['success'] = "";
                break;
                
            case 'register_training':
                $_SESSION['success'] = "";
                break;
        }
        
        header('Location: ' . $_SERVER['PHP_SELF'] . '?tab=' . $activeTab);
        exit();
    }
    ?>
    
    <div class="flex min-h-screen">
         <?php include '../includes/sidebar.php'; ?>
        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Employee Self-Service</h1>
                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($employee['department_name'] ?? 'No Department'); ?></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($employee['position_title'] ?? 'No Position'); ?></p>
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
            <div class="flex-1 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
                <!-- Navigation Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button data-tab="personal" class="nav-tab <?php echo $activeTab === 'personal' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-user mr-2"></i>
                            Personal Update
                        </button>
                        <button data-tab="leave" class="nav-tab <?php echo $activeTab === 'leave' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            Leave & Time Off
                        </button>
                        <button data-tab="payroll" class="nav-tab <?php echo $activeTab === 'payroll' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-money-check-alt mr-2"></i>
                            Payroll
                        </button>
                        <button data-tab="benefits" class="nav-tab <?php echo $activeTab === 'benefits' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Benefits
                        </button>
                        <button data-tab="training" class="nav-tab <?php echo $activeTab === 'training' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?> whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors">
                            <i class="fas fa-graduation-cap mr-2"></i>
                            Training
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="mt-8 w-full">
                    <!-- Personal Information Update -->
                    <div id="personal" class="module-content <?php echo $activeTab !== 'personal' ? 'hidden' : ''; ?> w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Personal Information</h2>
                        </div>
                        
                        <!-- Personal Information Form -->
                        <div class="bg-white rounded-lg shadow p-6 mb-8 form-container">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Update Personal Information</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_personal">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                                        <input type="text" name="first_name" value="<?php echo htmlspecialchars($employee['first_name'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                                        <input type="text" name="last_name" value="<?php echo htmlspecialchars($employee['last_name'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                        <input type="tel" name="phone_number" value="<?php echo htmlspecialchars($employee['phone_number'] ?? ''); ?>" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                                        <textarea name="address" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo htmlspecialchars($employee['address'] ?? ''); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-save mr-2"></i>Update Information
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Personal Information Display -->
                        <div class="bg-white rounded-lg shadow p-6 form-container">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Current Information</h3>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <input type="text" placeholder="Search information..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 personal-search">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="personal-search">Clear</button>
                                </div>
                            </div>
                            <div class="list-container">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Value</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Updated</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="personal-list">
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">First Name</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['first_name'] ?? ''); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Last Name</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['last_name'] ?? ''); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Email</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['email'] ?? ''); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Phone Number</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['phone_number'] ?? ''); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Address</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['address'] ?? ''); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Department</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['department_name'] ?? 'No Department'); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Position</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($employee['position_title'] ?? 'No Position'); ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Leave & Time Off -->
                    <div id="leave" class="module-content <?php echo $activeTab !== 'leave' ? 'hidden' : ''; ?> w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Leave & Time Off</h2>
                        </div>
                        
                        <!-- Leave Request Form -->
                        <div class="bg-white rounded-lg shadow p-6 mb-8 form-container">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Submit Leave Request</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="submit_leave">
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Leave Type</label>
                                        <select name="leave_type" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Select Leave Type</option>
                                            <option value="vacation">Vacation Leave</option>
                                            <option value="sick">Sick Leave</option>
                                            <option value="emergency">Emergency Leave</option>
                                            <option value="maternity">Maternity Leave</option>
                                            <option value="paternity">Paternity Leave</option>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                        <input type="date" name="start_date" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                        <input type="date" name="end_date" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-paper-plane mr-2"></i>Submit Request
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Leave Information -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Leave Balance -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-800">Leave Balance</h3>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative">
                                            <input type="text" placeholder="Search leave types..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 leave-search">
                                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                        </div>
                                        <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="leave-search">Clear</button>
                                    </div>
                                </div>
                                <div class="list-container">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining Days</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Allocation</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="leave-list">
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Vacation Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo $leave_balance['vacation']; ?> days
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">15 days</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Sick Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo $leave_balance['sick']; ?> days
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">10 days</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Emergency Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo $leave_balance['emergency']; ?> days
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">5 days</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Maternity Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo $leave_balance['maternity']; ?> days
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">60 days</td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Paternity Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        <?php echo $leave_balance['paternity']; ?> days
                                                    </span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">7 days</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Leave History -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-800">Leave History</h3>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative">
                                            <input type="text" placeholder="Search history..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 history-search">
                                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                        </div>
                                        <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="history-search">Clear</button>
                                    </div>
                                </div>
                                <div class="list-container">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Leave Type</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Period</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="history-list">
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Vacation Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">Jan 5, 2024 to Jan 10, 2024</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                                                    <button class="text-green-600 hover:text-green-900 mr-2">Edit</button>
                                                    <button class="text-red-600 hover:text-red-900">Cancel</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Sick Leave</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">Feb 15, 2024 to Feb 16, 2024</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <button class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                                                    <button class="text-green-600 hover:text-green-900 mr-2">Edit</button>
                                                    <button class="text-red-600 hover:text-red-900">Cancel</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payroll -->
                    <div id="payroll" class="module-content <?php echo $activeTab !== 'payroll' ? 'hidden' : ''; ?> w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Payroll Information</h2>
                        </div>
                        
                        <!-- Latest Payslip -->
                        <div class="bg-white rounded-lg shadow p-6 mb-8 form-container">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Latest Payslip</h3>
                            <?php if ($payroll_data): ?>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pay Period</label>
                                    <p class="text-sm text-gray-900"><?php echo date('M j, Y', strtotime($payroll_data['pay_date'])); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary</label>
                                    <p class="text-sm text-gray-900">₱<?php echo number_format($payroll_data['salary'], 2); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Deductions</label>
                                    <p class="text-sm text-red-600">-₱<?php echo number_format($payroll_data['deductions'], 2); ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Net Pay</label>
                                    <p class="text-sm font-semibold text-green-600">₱<?php echo number_format($payroll_data['salary'] - $payroll_data['deductions'], 2); ?></p>
                                </div>
                            </div>
                            <?php else: ?>
                            <p class="text-gray-500">No payroll data available.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Payroll History -->
                        <div class="bg-white rounded-lg shadow p-6 form-container">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Payroll History</h3>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <input type="text" placeholder="Search payroll..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 payroll-search">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="payroll-search">Clear</button>
                                </div>
                            </div>
                            <div class="list-container">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pay Date</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deductions</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="payroll-list">
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">Jan 15, 2024</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">₱50,000.00</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-red-500">-₱7,500.00</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600">₱42,500.00</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">Dec 15, 2023</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">₱50,000.00</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-red-500">-₱7,200.00</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600">₱42,800.00</td>
                                        </tr>
                                        <tr>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">Nov 15, 2023</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">₱48,000.00</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-red-500">-₱6,800.00</td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-green-600">₱41,200.00</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div id="benefits" class="module-content <?php echo $activeTab !== 'benefits' ? 'hidden' : ''; ?> w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Benefits Information</h2>
                        </div>
                        
                        <!-- Benefits Overview -->
                        <div class="bg-white rounded-lg shadow p-6 mb-8 form-container">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Benefits Overview</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Health Insurance</label>
                                    <p class="text-sm text-gray-900"><?php echo $benefits_data ? 'Active' : 'Not Enrolled'; ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Retirement Plan</label>
                                    <p class="text-sm text-gray-900"><?php echo $benefits_data ? 'Active' : 'Not Enrolled'; ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Life Insurance</label>
                                    <p class="text-sm text-gray-900"><?php echo $benefits_data ? 'Active' : 'Not Enrolled'; ?></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dental Coverage</label>
                                    <p class="text-sm text-gray-900"><?php echo $benefits_data ? 'Active' : 'Not Enrolled'; ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Benefits Details -->
                        <div class="bg-white rounded-lg shadow p-6 form-container">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold text-gray-800">Benefits Details</h3>
                                <div class="flex items-center space-x-2">
                                    <div class="relative">
                                        <input type="text" placeholder="Search benefits..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 benefits-search">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="benefits-search">Clear</button>
                                </div>
                            </div>
                            <div class="list-container">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Benefit Type</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Provider</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coverage</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="benefits-list">
                                        <?php if ($benefits_data): ?>
                                            <?php foreach ($benefits_data as $benefit): ?>
                                            <tr>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($benefit['benefit_type']); ?></td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($benefit['provider']); ?></td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">Full Coverage</td>
                                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Active</span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan='4' class='px-4 py-4 text-sm text-gray-500 text-center'>No benefits data available</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Training -->
                    <div id="training" class="module-content <?php echo $activeTab !== 'training' ? 'hidden' : ''; ?> w-full">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-bold text-gray-800">Training Management</h2>
                        </div>
                        
                        <!-- Training Registration Form -->
                        <div class="bg-white rounded-lg shadow p-6 mb-8 form-container">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Register for Training</h3>
                            <form method="POST">
                                <input type="hidden" name="action" value="register_training">
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Training Program</label>
                                        <select name="training_id" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Select Training</option>
                                            <?php foreach ($trainings as $training): ?>
                                                <option value="<?php echo $training['training_id']; ?>">
                                                    <?php echo htmlspecialchars($training['title']); ?> - <?php echo date('M j, Y', strtotime($training['date'])); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Registration Date</label>
                                        <input type="date" name="registration_date" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                        <i class="fas fa-user-plus mr-2"></i>Register for Training
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Training Information -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Available Trainings -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-800">Available Trainings</h3>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative">
                                            <input type="text" placeholder="Search trainings..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 available-search">
                                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                        </div>
                                        <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="available-search">Clear</button>
                                    </div>
                                </div>
                                <div class="list-container">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Title</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trainer</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="available-list">
                                            <?php if ($trainings): ?>
                                                <?php foreach ($trainings as $training): ?>
                                                <tr>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($training['title']); ?></td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($training['date'])); ?></td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($training['trainer']); ?></td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan='4' class='px-4 py-4 text-sm text-gray-500 text-center'>No available trainings</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Registered Trainings -->
                            <div class="bg-white rounded-lg shadow p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-bold text-gray-800">My Registered Trainings</h3>
                                    <div class="flex items-center space-x-2">
                                        <div class="relative">
                                            <input type="text" placeholder="Search registered..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 registered-search">
                                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                        </div>
                                        <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="registered-search">Clear</button>
                                    </div>
                                </div>
                                <div class="list-container">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Title</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200" id="registered-list">
                                            <?php if ($registered_trainings): ?>
                                                <?php foreach ($registered_trainings as $reg_training): ?>
                                                <tr>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($reg_training['title']); ?></td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date('M j, Y', strtotime($reg_training['date'])); ?></td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            <?php echo $reg_training['status'] === 'upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800'; ?>">
                                                            <?php echo ucfirst($reg_training['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <button class="text-blue-600 hover:text-blue-900 mr-2">View</button>
                                                        <button class="text-green-600 hover:text-green-900 mr-2">Edit</button>
                                                        <button class="text-red-600 hover:text-red-900">Cancel</button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr><td colspan='4' class='px-4 py-4 text-sm text-gray-500 text-center'>No registered trainings</td></tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab Navigation
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.nav-tab');
            const contents = document.querySelectorAll('.module-content');
            
            // Show first tab by default
            if (tabs.length > 0) {
                tabs[0].classList.add('active-tab');
                contents[0].classList.remove('hidden');
            }
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-tab');
                    
                    // Update tabs
                    tabs.forEach(t => t.classList.remove('active-tab'));
                    this.classList.add('active-tab');
                    
                    // Update content
                    contents.forEach(content => {
                        content.classList.add('hidden');
                        if (content.id === targetId) {
                            content.classList.remove('hidden');
                        }
                    });
                    
                    // Update URL without page reload
                    const url = new URL(window.location);
                    url.searchParams.set('tab', targetId);
                    window.history.pushState({}, '', url);
                });
            });
            
            // Search functionality
         const searchInputs = document.querySelectorAll('input[type="text"]');
            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const tableId = this.classList.contains('personal-search') ? 'personal-list' :
                                  this.classList.contains('leave-search') ? 'leave-list' :
                                  this.classList.contains('history-search') ? 'history-list' :
                                  this.classList.contains('payroll-search') ? 'payroll-list' :
                                  this.classList.contains('benefits-search') ? 'benefits-list' :
                                  this.classList.contains('available-search') ? 'available-list' :
                                  this.classList.contains('registered-search') ? 'registered-list' : '';
                    
                    if (tableId) {
        //                const rows = document.querySelectorAll(#${tableId} tr);
                        rows.forEach(row => {
                           const text = row.textContent.toLowerCase();
                            row.style.display = text.includes(searchTerm) ? '' : 'none';
                        });
                    }
                });
            });
            
            // Clear search buttons
            const clearButtons = document.querySelectorAll('.clear-search');
              clearButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const searchClass = this.getAttribute('data-search');
              //      const searchInput = document.querySelector(.${searchClass});
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.dispatchEvent(new Event('input'));
                    }
                });
            });

            // Edit Training Functionality
            const editButtons = document.querySelectorAll('#registered-list .text-green-600');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const row = this.closest('tr');
                    const trainingTitle = row.cells[0].textContent;
                    const trainingDate = row.cells[1].textContent;
                    
                    // Show edit modal or form
                 //   alert(Editing training: ${trainingTitle}\nDate: ${trainingDate}\n\nEdit functionality would open a form here.);
                });
            });
        });
    </script>
</body>
</html>