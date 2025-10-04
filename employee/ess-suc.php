<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Succession Planning Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1e40af',
                        secondary: '#3b82f6',
                        accent: '#60a5fa'
                    }
                }
            }
        }
    </script>
    <style>
        .active-tab {
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
        }
        .list-container {
            max-height: 400px;
            overflow-y: auto;
        }
        .edit-form {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <?php include '../employee/ess-sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="flex-1">
            <?php 
            // Database connection
            $servername = "localhost";
            $username = "root"; // Change if needed
            $password = ""; // Change if needed
            $dbname = "hr2_db";
            
            try {
                $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                die("Connection failed: " . $e->getMessage());
            }
            
            // Get current active tab from URL or default to candidate-pool
            $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'candidate-pool';
            
            // Handle form submissions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['add_candidate'])) {
                    $fk_employee_id = $_POST['employee_id'];
                    $fk_plan_id = $_POST['plan_id'];
                    $potential_score = $_POST['potential_score'];
                    $remarks = $_POST['remarks'];
                    
                    $stmt = $pdo->prepare("INSERT INTO candidate_pool (fk_employee_id, fk_plan_id, potential_score, remarks) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$fk_employee_id, $fk_plan_id, $potential_score, $remarks]);
                    $active_tab = 'candidate-pool';
                }
                
                if (isset($_POST['edit_candidate'])) {
                    $candidate_id = $_POST['candidate_id'];
                    $fk_employee_id = $_POST['employee_id'];
                    $fk_plan_id = $_POST['plan_id'];
                    $potential_score = $_POST['potential_score'];
                    $remarks = $_POST['remarks'];
                    
                    $stmt = $pdo->prepare("UPDATE candidate_pool SET fk_employee_id = ?, fk_plan_id = ?, potential_score = ?, remarks = ? WHERE candidate_id = ?");
                    $stmt->execute([$fk_employee_id, $fk_plan_id, $potential_score, $remarks, $candidate_id]);
                    $active_tab = 'candidate-pool';
                }
                
                if (isset($_POST['add_position'])) {
                    $title = $_POST['title'];
                    $required_skills = $_POST['required_skills'];
                    
                    $stmt = $pdo->prepare("INSERT INTO position (title, required_skills) VALUES (?, ?)");
                    $stmt->execute([$title, $required_skills]);
                    $active_tab = 'positions';
                }
                
                if (isset($_POST['edit_position'])) {
                    $position_id = $_POST['position_id'];
                    $title = $_POST['title'];
                    $required_skills = $_POST['required_skills'];
                    
                    $stmt = $pdo->prepare("UPDATE position SET title = ?, required_skills = ? WHERE position_id = ?");
                    $stmt->execute([$title, $required_skills, $position_id]);
                    $active_tab = 'positions';
                }
                
                if (isset($_POST['add_training'])) {
                    $title = $_POST['title'];
                    $date = $_POST['date'];
                    $trainer = $_POST['trainer'];
                    
                    $stmt = $pdo->prepare("INSERT INTO training (title, date, trainer) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $date, $trainer]);
                    $active_tab = 'training';
                }
                
                if (isset($_POST['edit_training'])) {
                    $training_id = $_POST['training_id'];
                    $title = $_POST['title'];
                    $date = $_POST['date'];
                    $trainer = $_POST['trainer'];
                    
                    $stmt = $pdo->prepare("UPDATE training SET title = ?, date = ?, trainer = ? WHERE training_id = ?");
                    $stmt->execute([$title, $date, $trainer, $training_id]);
                    $active_tab = 'training';
                }
                
                if (isset($_POST['add_registration'])) {
                    $fk_employee_id = $_POST['employee_id'];
                    $fk_training_id = $_POST['training_id'];
                    $registration_date = $_POST['registration_date'];
                    
                    $stmt = $pdo->prepare("INSERT INTO training_registration (fk_employee_id, fk_training_id, registration_date) VALUES (?, ?, ?)");
                    $stmt->execute([$fk_employee_id, $fk_training_id, $registration_date]);
                    $active_tab = 'training-registration';
                }
                
                if (isset($_POST['edit_registration'])) {
                    $registration_id = $_POST['registration_id'];
                    $fk_employee_id = $_POST['employee_id'];
                    $fk_training_id = $_POST['training_id'];
                    $registration_date = $_POST['registration_date'];
                    
                    $stmt = $pdo->prepare("UPDATE training_registration SET fk_employee_id = ?, fk_training_id = ?, registration_date = ? WHERE registration_id = ?");
                    $stmt->execute([$fk_employee_id, $fk_training_id, $registration_date, $registration_id]);
                    $active_tab = 'training-registration';
                }
                
                if (isset($_POST['add_succession_plan'])) {
                    $fk_employee_id = $_POST['employee_id'];
                    $position_target = $_POST['position_target'];
                    $readiness_level = $_POST['readiness_level'];
                    $last_reviewed = $_POST['last_reviewed'];
                    
                    $stmt = $pdo->prepare("INSERT INTO succession_plan (fk_employee_id, position_target, readiness_level, last_reviewed) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$fk_employee_id, $position_target, $readiness_level, $last_reviewed]);
                    $active_tab = 'succession-plans';
                }
                
                if (isset($_POST['edit_succession_plan'])) {
                    $plan_id = $_POST['plan_id'];
                    $fk_employee_id = $_POST['employee_id'];
                    $position_target = $_POST['position_target'];
                    $readiness_level = $_POST['readiness_level'];
                    $last_reviewed = $_POST['last_reviewed'];
                    
                    $stmt = $pdo->prepare("UPDATE succession_plan SET fk_employee_id = ?, position_target = ?, readiness_level = ?, last_reviewed = ? WHERE plan_id = ?");
                    $stmt->execute([$fk_employee_id, $position_target, $readiness_level, $last_reviewed, $plan_id]);
                    $active_tab = 'succession-plans';
                }
            }
            
            // Handle deletions with tab preservation
            if (isset($_GET['delete_candidate'])) {
                $stmt = $pdo->prepare("DELETE FROM candidate_pool WHERE candidate_id = ?");
                $stmt->execute([$_GET['delete_candidate']]);
                $active_tab = 'candidate-pool';
            }
            
            if (isset($_GET['delete_position'])) {
                $stmt = $pdo->prepare("DELETE FROM position WHERE position_id = ?");
                $stmt->execute([$_GET['delete_position']]);
                $active_tab = 'positions';
            }
            
            if (isset($_GET['delete_training'])) {
                $stmt = $pdo->prepare("DELETE FROM training WHERE training_id = ?");
                $stmt->execute([$_GET['delete_training']]);
                $active_tab = 'training';
            }
            
            if (isset($_GET['delete_registration'])) {
                $stmt = $pdo->prepare("DELETE FROM training_registration WHERE registration_id = ?");
                $stmt->execute([$_GET['delete_registration']]);
                $active_tab = 'training-registration';
            }
            
            if (isset($_GET['delete_succession_plan'])) {
                $stmt = $pdo->prepare("DELETE FROM succession_plan WHERE plan_id = ?");
                $stmt->execute([$_GET['delete_succession_plan']]);
                $active_tab = 'succession-plans';
            }
            
            // Fetch data from database
            // Candidates
            $candidates = $pdo->query("
                SELECT cp.*, e.first_name, e.last_name, sp.position_target 
                FROM candidate_pool cp 
                LEFT JOIN employee e ON cp.fk_employee_id = e.employee_id 
                LEFT JOIN succession_plan sp ON cp.fk_plan_id = sp.plan_id
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Positions
            $positions = $pdo->query("SELECT * FROM position")->fetchAll(PDO::FETCH_ASSOC);
            
            // Training programs
            $trainings = $pdo->query("SELECT * FROM training")->fetchAll(PDO::FETCH_ASSOC);
            
            // Training registrations
            $registrations = $pdo->query("
                SELECT tr.*, e.first_name, e.last_name, t.title as training_title 
                FROM training_registration tr 
                LEFT JOIN employee e ON tr.fk_employee_id = e.employee_id 
                LEFT JOIN training t ON tr.fk_training_id = t.training_id
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Succession plans
            $succession_plans = $pdo->query("
                SELECT sp.*, e.first_name, e.last_name 
                FROM succession_plan sp 
                LEFT JOIN employee e ON sp.fk_employee_id = e.employee_id
            ")->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch employees for dropdowns
            $employees = $pdo->query("SELECT employee_id, first_name, last_name FROM employee")->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch existing succession plans for dropdown
            $existing_succession_plans = $pdo->query("SELECT plan_id, position_target FROM succession_plan")->fetchAll(PDO::FETCH_ASSOC);
            ?>
            
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-md sticky top-0 z-10">
                <div class="container mx-auto px-4">
                    <div class="flex justify-between items-center py-4">
                        <div class="flex items-center space-x-10">
                            <h1 class="text-xl font-bold text-blue-700">Succession Planning</h1>
                            <nav class="flex space-x-1">
                                <a href="?tab=candidate-pool" class="nav-tab px-4 py-2 text-gray-600 hover:text-blue-600 font-medium <?= $active_tab == 'candidate-pool' ? 'active-tab' : '' ?>">Candidate Pool</a>
                                <a href="?tab=positions" class="nav-tab px-4 py-2 text-gray-600 hover:text-blue-600 font-medium <?= $active_tab == 'positions' ? 'active-tab' : '' ?>">Positions</a>
                                <a href="?tab=training" class="nav-tab px-4 py-2 text-gray-600 hover:text-blue-600 font-medium <?= $active_tab == 'training' ? 'active-tab' : '' ?>">Training</a>
                                <a href="?tab=training-registration" class="nav-tab px-4 py-2 text-gray-600 hover:text-blue-600 font-medium <?= $active_tab == 'training-registration' ? 'active-tab' : '' ?>">Training Registration</a>
                                <a href="?tab=succession-plans" class="nav-tab px-4 py-2 text-gray-600 hover:text-blue-600 font-medium <?= $active_tab == 'succession-plans' ? 'active-tab' : '' ?>">Succession Plans</a>
                            </nav>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="container mx-auto px-4 py-8">
                <!-- Candidate Pool Module -->
                <div id="candidate-pool" class="module-content <?= $active_tab != 'candidate-pool' ? 'hidden' : '' ?>">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Candidate Pool</h2>
                    </div>

                    <!-- Candidate List Container -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Candidate List</h3>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" placeholder="Search candidates..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 candidate-search">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="candidate-search">Clear</button>
                            </div>
                        </div>
                        <div class="list-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Potential Score</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="candidate-list">
                                    <?php foreach ($candidates as $candidate): ?>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($candidate['first_name'] . ' ' . $candidate['last_name']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($candidate['position_target'] ?? 'N/A') ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?= $candidate['potential_score'] ?>%
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($candidate['remarks']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Positions Module -->
                <div id="positions" class="module-content <?= $active_tab != 'positions' ? 'hidden' : '' ?>">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Positions</h2>
                    </div>

                    <!-- Position List Container -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Position List</h3>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" placeholder="Search positions..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 position-search">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="position-search">Clear</button>
                            </div>
                        </div>
                        <div class="list-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required Skills</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="position-list">
                                    <?php foreach ($positions as $position): ?>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($position['title']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($position['required_skills']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Training Module -->
                <div id="training" class="module-content <?= $active_tab != 'training' ? 'hidden' : '' ?>">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Training Programs</h2>
                    </div>

                    <!-- Training List Container -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Training List</h3>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" placeholder="Search training..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 training-search">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="training-search">Clear</button>
                            </div>
                        </div>
                        <div class="list-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trainer</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="training-list">
                                    <?php foreach ($trainings as $training): ?>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($training['title']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($training['date']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($training['trainer']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Training Registration Module -->
                <div id="training-registration" class="module-content <?= $active_tab != 'training-registration' ? 'hidden' : '' ?>">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Training Registration</h2>
                    </div>

                    <!-- Registration List Container -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Registration List</h3>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" placeholder="Search registrations..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 registration-search">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="registration-search">Clear</button>
                            </div>
                        </div>
                        <div class="list-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Training Program</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registration Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="registration-list">
                                    <?php foreach ($registrations as $registration): ?>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($registration['training_title']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($registration['registration_date']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Succession Plans Module -->
                <div id="succession-plans" class="module-content <?= $active_tab != 'succession-plans' ? 'hidden' : '' ?>">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-800">Succession Plans</h2>
                    </div>

                    <!-- Succession Plan List Container -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800">Succession Plan List</h3>
                            <div class="flex items-center space-x-2">
                                <div class="relative">
                                    <input type="text" placeholder="Search plans..." class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 w-64 plan-search">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                                <button class="clear-search px-4 py-2 text-gray-700 border rounded-lg hover:bg-gray-100" data-search="plan-search">Clear</button>
                            </div>
                        </div>
                        <div class="list-container">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position Target</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Readiness Level</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Reviewed</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="plan-list">
                                    <?php foreach ($succession_plans as $plan): ?>
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($plan['first_name'] . ' ' . $plan['last_name']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($plan['position_target']) ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?= 
                                                    $plan['readiness_level'] == 'Ready Now' ? 'bg-green-100 text-green-800' : 
                                                    ($plan['readiness_level'] == '1-2 Years' ? 'bg-yellow-100 text-yellow-800' : 
                                                    ($plan['readiness_level'] == '3-5 Years' ? 'bg-orange-100 text-orange-800' : 
                                                    'bg-blue-100 text-blue-800')) 
                                                ?>">
                                                <?= htmlspecialchars($plan['readiness_level']) ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?= htmlspecialchars($plan['last_reviewed']) ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        // Tab Navigation
        document.addEventListener('DOMContentLoaded', function() {
            // Search functionality
            const searchInputs = document.querySelectorAll('input[type="text"]');
            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase();
                    const tableId = this.classList[this.classList.length - 1].replace('-search', '-list');
                    const table = document.getElementById(tableId);
                    
                    if (table) {
                        const rows = table.getElementsByTagName('tr');
                        
                        for (let i = 0; i < rows.length; i++) {
                            const row = rows[i];
                            const text = row.textContent.toLowerCase();
                            
                            if (text.includes(searchTerm)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        }
                    }
                });
            });
            
            // Clear search buttons
            const clearButtons = document.querySelectorAll('.clear-search');
            clearButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const searchClass = this.getAttribute('data-search');
                    //const searchInput = document.querySelector(.${searchClass});
                    
                    if (searchInput) {
                        searchInput.value = '';
                        
                        // Trigger the input event to reset the table
                        const event = new Event('input');
                        searchInput.dispatchEvent(event);
                    }
                });
            });
        });
    </script>
</body>
</html>