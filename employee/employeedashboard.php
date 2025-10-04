<?php
// ---- DB Connection ----
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3307;dbname=db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB Connection failed: " . $e->getMessage());
}

// Employee ID (sample lang muna, dapat naka-login session ito)
$empId = 1;

// ---- Employee Info ----
$employee = $pdo->query("SELECT * FROM employee WHERE employee_id=$empId")->fetch(PDO::FETCH_ASSOC);
if (!$employee) {
    $employee = [
        'first_name' => 'Unknown',
        'last_name'  => 'Employee'
    ];
}

// ---- Recent Leave Requests ----
$leaves = $pdo->query("SELECT * FROM leave_request WHERE fk_employee_id=$empId ORDER BY start_date DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

// ---- Available Trainings ----
$trainings = $pdo->query("SELECT * FROM training ORDER BY date ASC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="en">
  <meta charset="utf-8">
  <title>Employee Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
  <div class="flex">
    <!-- Sidebar -->
    <div class="w-64">
      <?php include 'sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
      <p class="text-gray-600 mb-6 flex items-center gap-2">
        <i class="fas fa-user-circle text-blue-500 text-2xl"></i>
        Welcome back, <span class="font-semibold">
          <?= htmlspecialchars(($employee['first_name'] ?? '') . " " . ($employee['last_name'] ?? '')) ?>
        </span>
      </p>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- Recent Leave Requests -->
        <div class="bg-white p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
            <i class="fas fa-history text-yellow-500"></i> Recent Leave Requests
          </h2>
          <ul class="space-y-2 text-sm">
            <?php if ($leaves): ?>
              <?php foreach($leaves as $l): ?>
                <li class="border-b pb-2">
                  <span class="font-medium"><?= htmlspecialchars($l['type']) ?></span>
                  <div class="text-gray-500 text-xs">
                    <?= htmlspecialchars($l['start_date']) ?> → <?= htmlspecialchars($l['end_date']) ?> 
                    (<?= htmlspecialchars($l['status']) ?>)
                  </div>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="text-gray-500">No leave requests found.</li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Available Trainings -->
        <div class="bg-white p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
            <i class="fas fa-chalkboard-teacher text-blue-500"></i> Available Trainings
          </h2>
          <ul class="space-y-2 text-sm">
            <?php if ($trainings): ?>
              <?php foreach($trainings as $t): ?>
                <li class="border-b pb-2">
                  <p class="font-medium"><?= htmlspecialchars($t['title']) ?></p>
                  <p class="text-gray-500 text-xs">📅 <?= htmlspecialchars($t['date']) ?> | 👨‍🏫 <?= htmlspecialchars($t['trainer']) ?></p>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="text-gray-500">No available trainings.</li>
            <?php endif; ?>
          </ul>
        </div>

        <!-- Quick Links -->
        <div class="bg-white p-6 rounded-xl shadow">
          <h2 class="text-lg font-semibold mb-3 flex items-center gap-2">
            <i class="fas fa-link text-green-500"></i> Quick Links
          </h2>
          <ul class="space-y-2 text-sm">
            <li><a href="EmployeeSelfService.php?tab=personal" class="text-blue-600 hover:underline">👤 Personal Information</a></li>
            <li><a href="EmployeeSelfService.php?tab=leave" class="text-blue-600 hover:underline">📝 Apply for Leave</a></li>
            <li><a href="EmployeeSelfService.php?tab=training" class="text-blue-600 hover:underline">📚 Register Training</a></li>
            <li><a href="EmployeeSelfService.php?tab=payroll" class="text-blue-600 hover:underline">💰 Payroll & Benefits</a></li>
          </ul>
        </div>

      </div>
    </div>
  </div>
</body>
</html>
