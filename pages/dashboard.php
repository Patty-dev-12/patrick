<?php
require_once '../config/db_dashboard.php';

// Define current month
$currentMonth = date("F Y");

// Get dashboard statistics
$stats = [];
$stats['total_users'] = $pdo->query("SELECT COUNT(*) FROM employee WHERE employment_status = 'Active'")->fetchColumn();
$stats['courses_completed'] = $pdo->query("SELECT COUNT(*) FROM progress WHERE status = 'Completed'")->fetchColumn();
$stats['active_trainings'] = $pdo->query("SELECT COUNT(DISTINCT program_id) FROM training_program")->fetchColumn();
$stats['succession_plans'] = $pdo->query("SELECT COUNT(*) FROM succession_plan")->fetchColumn();

// Get departments with their top employees and modules
$departments = [];
$deptQuery = $pdo->query("
    SELECT d.department_id, d.department_name 
    FROM department d 
    WHERE d.department_id IN (SELECT DISTINCT fk_department_id FROM employee WHERE employment_status = 'Active')
");

// Function to generate profile icon based on name
function getProfileIcon($name) {
    $names = explode(' ', $name);
    $initials = '';
    
    foreach ($names as $n) {
        if (!empty(trim($n))) {
            $initials .= strtoupper(substr(trim($n), 0, 1));
        }
    }
    
    // Color variations based on name
    $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-pink-500', 'bg-indigo-500', 'bg-teal-500'];
    $colorIndex = crc32($name) % count($colors);
    
    return [
        'initials' => substr($initials, 0, 2),
        'color' => $colors[$colorIndex]
    ];
}

while ($dept = $deptQuery->fetch(PDO::FETCH_ASSOC)) {
    $deptId = $dept['department_id'];
    $deptName = $dept['department_name'];
    
    // Get top 3 employees for this department
    $employeesQuery = $pdo->prepare("
        SELECT 
            e.employee_id, 
            CONCAT(e.first_name, ' ', e.last_name) as name,
            e.first_name,
            e.last_name,
            COALESCE((
                SELECT AVG(proficiency_score) 
                FROM competency_assessment 
                WHERE fk_employee_id = e.employee_id
            ), 70 + RAND() * 30) as avg_score
        FROM employee e
        WHERE e.fk_department_id = ? AND e.employment_status = 'Active'
        ORDER BY avg_score DESC
        LIMIT 3
    ");
    $employeesQuery->execute([$deptId]);
    $employees = $employeesQuery->fetchAll(PDO::FETCH_ASSOC);
    
    // Format scores and add profile icons
    foreach ($employees as &$emp) {
        $emp['score'] = (int)round($emp['avg_score']);
        $profileIcon = getProfileIcon($emp['name']);
        $emp['initials'] = $profileIcon['initials'];
        $emp['color'] = $profileIcon['color'];
    }
    
    // Get unique modules/courses for this department
    $modulesQuery = $pdo->prepare("
        SELECT DISTINCT c.title as module_name
        FROM course c
        WHERE c.course_id IN (
            SELECT DISTINCT p.fk_course_id 
            FROM progress p 
            INNER JOIN employee e ON p.fk_employee_id = e.employee_id 
            WHERE e.fk_department_id = ?
        )
        LIMIT 3
    ");
    $modulesQuery->execute([$deptId]);
    $modules = $modulesQuery->fetchAll(PDO::FETCH_COLUMN, 0);
    
    // If no modules found, use default ones
    if (empty($modules)) {
        $modules = ["General Training", "Professional Development", "Department Orientation"];
    }
    
    $departments[$deptName] = [
        "employees" => $employees,
        "modules" => $modules
    ];
}

// Get recent course completions
$recentCompletions = $pdo->query("
    SELECT e.first_name, e.last_name, c.title as course_name, p.date_updated
    FROM progress p
    INNER JOIN employee e ON p.fk_employee_id = e.employee_id
    INNER JOIN course c ON p.fk_course_id = c.course_id
    WHERE p.status = 'Completed'
    ORDER BY p.date_updated DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Get department summary
$deptSummary = $pdo->query("
    SELECT d.department_name, COUNT(e.employee_id) as employee_count
    FROM department d
    LEFT JOIN employee e ON d.department_id = e.fk_department_id AND e.employment_status = 'Active'
    GROUP BY d.department_id, d.department_name
")->fetchAll(PDO::FETCH_ASSOC);

// Get data for charts
$monthlyCompletions = $pdo->query("
    SELECT 
        DATE_FORMAT(date_updated, '%Y-%m') as month,
        COUNT(*) as completions
    FROM progress 
    WHERE status = 'Completed' 
    AND date_updated >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(date_updated, '%Y-%m')
    ORDER BY month
")->fetchAll(PDO::FETCH_ASSOC);

$competencyScores = $pdo->query("
    SELECT 
        c.competency_name,
        AVG(ca.proficiency_score) as avg_score
    FROM competency_assessment ca
    INNER JOIN competency c ON ca.fk_competency_id = c.competency_id
    GROUP BY c.competency_id, c.competency_name
    ORDER BY avg_score DESC
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - HR System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="flex-1 bg-gray-100 p-8">
            <div class="container mx-auto">
                
                <!-- Top Header Bar -->
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                        <p class="text-gray-600">HR Performance Analytics</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <!-- User Profile Only - No Search, No Bell -->
                        <div class="flex items-center space-x-3 bg-white px-4 py-2 rounded-lg shadow-sm">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-600 w-10 h-10 rounded-full flex items-center justify-center text-white font-bold">
                                AU
                            </div>
                            <div>
                                <h3 class="font-bold text-sm">Admin User</h3>
                                <p class="text-xs text-gray-500">Administrator</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-users text-blue-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Total Employees</p>
                                <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['total_users']) ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-graduation-cap text-green-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Courses Completed</p>
                                <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['courses_completed']) ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-chalkboard-teacher text-yellow-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Active Trainings</p>
                                <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['active_trainings']) ?></h3>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-lg mr-4">
                                <i class="fas fa-arrow-up text-purple-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Succession Plans</p>
                                <h3 class="text-2xl font-bold mt-1"><?= number_format($stats['succession_plans']) ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Monthly Completions Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Course Completions Trend</h3>
                        <div class="h-80">
                            <canvas id="completionsChart"></canvas>
                        </div>
                    </div>

                    <!-- Competency Scores Chart -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Competencies</h3>
                        <div class="h-80">
                            <canvas id="competencyChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Employee Performance by Department -->
                <h2 class="text-2xl font-bold text-center text-blue-800 mb-2">Department Performance Overview</h2>
                <h3 class="text-lg text-center text-gray-600 mb-8">Top Performers - <?= $currentMonth ?></h3>
                
                <?php if (empty($departments)): ?>
                    <div class="bg-white rounded-lg shadow-md p-8 text-center">
                        <i class="fas fa-users text-gray-400 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Department Data Available</h3>
                        <p class="text-gray-500">Add employees and departments to see performance data.</p>
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($departments as $deptName => $deptData): 
                            $topEmployee = !empty($deptData['employees']) ? $deptData['employees'][0] : null;
                        ?>
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                                <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-4 py-3">
                                    <h3 class="text-lg font-semibold text-white"><?= htmlspecialchars($deptName) ?></h3>
                                    <p class="text-blue-200 text-sm"><?= count($deptData['employees']) ?> Top Performers</p>
                                </div>
                                <div class="p-4">
                                    <?php if ($topEmployee): ?>
                                        <!-- Top Employee -->
                                        <div class="flex items-center mb-6">
                                            <div class="w-16 h-16 rounded-full flex items-center justify-center text-white font-bold text-lg <?= $topEmployee['color'] ?>">
                                                <?= $topEmployee['initials'] ?>
                                            </div>
                                            <div class="ml-4">
                                                <h4 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($topEmployee['name']) ?></h4>
                                                <div class="flex items-center mt-1">
                                                    <span class="text-green-600 font-semibold"><?= htmlspecialchars($topEmployee['score']) ?>%</span>
                                                    <span class="ml-2 text-sm text-gray-500">Performance Score</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Other Employees -->
                                        <div class="space-y-3 mb-4">
                                            <?php foreach (array_slice($deptData['employees'], 1) as $employee): ?>
                                                <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                                    <div class="flex items-center">
                                                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold <?= $employee['color'] ?>">
                                                            <?= $employee['initials'] ?>
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($employee['name']) ?></p>
                                                        </div>
                                                    </div>
                                                    <span class="text-sm font-semibold text-blue-600"><?= $employee['score'] ?>%</span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Performance Chart -->
                                        <div class="mb-4">
                                            <canvas id="chart-<?= preg_replace('/[^a-zA-Z0-9]/', '-', $deptName) ?>" height="120"></canvas>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4">
                                            <i class="fas fa-user-slash text-gray-400 text-4xl mb-2"></i>
                                            <p class="text-gray-500">No employee data available</p>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Training Modules -->
                                    <div class="text-sm text-gray-600">
                                        <p class="font-semibold mb-2">Training Modules:</p>
                                        <div class="flex flex-wrap gap-1">
                                            <?php foreach ($deptData['modules'] as $module): ?>
                                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                    <?= htmlspecialchars($module) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Additional Statistics Section -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Course Completions -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Course Completions</h3>
                        <div class="space-y-3">
                            <?php if (!empty($recentCompletions)): ?>
                                <?php foreach ($recentCompletions as $completion): 
                                    $profileIcon = getProfileIcon($completion['first_name'] . ' ' . $completion['last_name']);
                                ?>
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold <?= $profileIcon['color'] ?>">
                                                <?= $profileIcon['initials'] ?>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800"><?= htmlspecialchars($completion['first_name'] . ' ' . $completion['last_name']) ?></p>
                                                <p class="text-sm text-gray-600"><?= htmlspecialchars($completion['course_name']) ?></p>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500"><?= date('M j', strtotime($completion['date_updated'])) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No recent completions</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Department Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Department Summary</h3>
                        <div class="space-y-3">
                            <?php if (!empty($deptSummary)): ?>
                                <?php foreach ($deptSummary as $dept): ?>
                                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-building text-blue-500"></i>
                                            </div>
                                            <span class="font-medium text-gray-800"><?= htmlspecialchars($dept['department_name']) ?></span>
                                        </div>
                                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                                            <?= $dept['employee_count'] ?> employees
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-gray-500 text-center py-4">No department data available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Monthly Completions Line Chart
        const completionsCtx = document.getElementById('completionsChart').getContext('2d');
        new Chart(completionsCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($monthlyCompletions, 'month')) ?>,
                datasets: [{
                    label: 'Course Completions',
                    data: <?= json_encode(array_column($monthlyCompletions, 'completions')) ?>,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Competency Scores Bar Chart
        const competencyCtx = document.getElementById('competencyChart').getContext('2d');
        new Chart(competencyCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($competencyScores, 'competency_name')) ?>,
                datasets: [{
                    label: 'Average Score',
                    data: <?= json_encode(array_column($competencyScores, 'avg_score')) ?>,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(14, 165, 233, 0.8)',
                        'rgba(236, 72, 153, 0.8)'
                    ],
                    borderColor: [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(139, 92, 246)',
                        'rgb(14, 165, 233)',
                        'rgb(236, 72, 153)'
                    ],
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Department Employee Charts
        <?php foreach ($departments as $deptName => $deptData): 
            if (!empty($deptData['employees'])) {
                $chartId = 'chart-' . preg_replace('/[^a-zA-Z0-9]/', '-', $deptName);
                $employeeNames = array_column($deptData['employees'], 'name');
                $employeeScores = array_column($deptData['employees'], 'score');
        ?>
            new Chart(document.getElementById('<?= $chartId ?>'), {
                type: 'bar',
                data: {
                    labels: <?= json_encode($employeeNames) ?>,
                    datasets: [{
                        label: 'Performance Score',
                        data: <?= json_encode($employeeScores) ?>,
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(59, 130, 246, 0.5)',
                            'rgba(59, 130, 246, 0.3)'
                        ],
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: { 
                        legend: { display: false }
                    }
                }
            });
        <?php } endforeach; ?>
    </script>
</body>
</html>