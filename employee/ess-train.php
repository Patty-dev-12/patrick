<?php
session_start();
require_once '../config/db_train.php';

$current_module = isset($_GET['module']) ? $_GET['module'] : 'department';

// Handle form submissions for each module
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        if ($current_module == 'department' && isset($_POST['add_department'])) {
            $stmt = $pdo->prepare("INSERT INTO department (department_code, department_name, fk_hr_staff_id) VALUES (?, ?, ?)");
            $stmt->execute([$_POST['department_code'], $_POST['department_name'], $_POST['fk_hr_staff_id']]);
            header("Location: ?module=department");
            exit;
        }
        elseif ($current_module == 'training_program' && isset($_POST['add_program'])) {
            $stmt = $pdo->prepare("INSERT INTO training_program (program_name, program_description, fk_employee_id, duration_hours, skill_level) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['program_name'], $_POST['program_description'], $_POST['fk_employee_id'], $_POST['duration_hours'], $_POST['skill_level']]);
            header("Location: ?module=training_program");
            exit;
        }
        elseif ($current_module == 'training_analytics' && isset($_POST['add_analytics'])) {
            $stmt = $pdo->prepare("INSERT INTO training_analytics (fk_program_id, metric_name, metric_category, dimension_type) VALUES (?, ?, ?, ?)");
            $stmt->execute([$_POST['fk_program_id'], $_POST['metric_name'], $_POST['metric_category'], $_POST['dimension_type']]);
            header("Location: ?module=training_analytics");
            exit;
        }
        elseif ($current_module == 'training_session' && isset($_POST['add_session'])) {
            $stmt = $pdo->prepare("INSERT INTO training_session (session_name, start_date, end_date, venue, fk_program_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_POST['session_name'], $_POST['start_date'], $_POST['end_date'], $_POST['venue'], $_POST['fk_program_id']]);
            header("Location: ?module=training_session");
            exit;
        }
        elseif ($current_module == 'training_calendar' && isset($_POST['add_calendar'])) {
            $stmt = $pdo->prepare("INSERT INTO training_calendar (event_start, event_name, event_title, location, fk_session_id) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['event_start'],
                $_POST['event_name'],
                $_POST['event_title'],
                $_POST['location'],
                $_POST['fk_session_id']
            ]);
            header("Location: ?module=training_calendar");
            exit;
        }
        elseif ($current_module == 'training_attendance' && isset($_POST['add_attendance'])) {
            $stmt = $pdo->prepare("INSERT INTO training_attendance (fk_employee_id, attendance_status, attendance_date, time_in, time_out, hours_attended) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['fk_employee_id'],
                $_POST['attendance_status'],
                $_POST['attendance_date'],
                $_POST['time_in'],
                $_POST['time_out'],
                $_POST['hours_attended']
            ]);
            header("Location: ?module=training_attendance");
            exit;
        }
        // Training Completion form submission
        elseif ($current_module == 'training_completion' && $_POST) {
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare("
                    INSERT INTO training_completion 
                    (fk_employee_id, completion_status, completion_date, certificate_issued, final_grade, assessment_score) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['fk_employee_id'],
                    $_POST['completion_status'],
                    $_POST['completion_date'],
                    $_POST['certificate_issued'],
                    $_POST['final_grade'],
                    $_POST['assessment_score']
                ]);
                
                
            } elseif ($_POST['action'] === 'edit') {
                $stmt = $pdo->prepare("
                    UPDATE training_completion 
                    SET fk_employee_id = ?, completion_status = ?, completion_date = ?, 
                        certificate_issued = ?, final_grade = ?, assessment_score = ? 
                    WHERE completion_id = ?
                ");
                $stmt->execute([
                    $_POST['fk_employee_id'],
                    $_POST['completion_status'],
                    $_POST['completion_date'],
                    $_POST['certificate_issued'],
                    $_POST['final_grade'],
                    $_POST['assessment_score'],
                    $_POST['completion_id']
                ]);
                
            }
            
            header('Location: ?module=training_completion' . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''));
            exit;
        }
        // Employee form submission
        elseif ($current_module == 'employee' && $_POST) {
            if ($_POST['action'] === 'add') {
                $stmt = $pdo->prepare("
                    INSERT INTO employee 
                    (first_name, last_name, email, phone_number, hire_date, employment_status, fk_department_id, fk_position_id, address) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['email'],
                    $_POST['phone_number'],
                    $_POST['hire_date'],
                    $_POST['employment_status'],
                    $_POST['fk_department_id'],
                    $_POST['fk_position_id'],
                    $_POST['address']
                ]);
                
                
            } elseif ($_POST['action'] === 'edit') {
                $stmt = $pdo->prepare("
                    UPDATE employee 
                    SET first_name = ?, last_name = ?, email = ?, phone_number = ?, hire_date = ?, 
                        employment_status = ?, fk_department_id = ?, fk_position_id = ?, address = ? 
                    WHERE employee_id = ?
                ");
                $stmt->execute([
                    $_POST['first_name'],
                    $_POST['last_name'],
                    $_POST['email'],
                    $_POST['phone_number'],
                    $_POST['hire_date'],
                    $_POST['employment_status'],
                    $_POST['fk_department_id'],
                    $_POST['fk_position_id'],
                    $_POST['address'],
                    $_POST['employee_id']
                ]);
                
            }
            
            header('Location: ?module=employee' . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''));
            exit;
        }
        // Update functionality
        elseif ($current_module == 'department' && isset($_POST['update_department'])) {
            $stmt = $pdo->prepare("UPDATE department SET department_code = ?, department_name = ?, fk_hr_staff_id = ? WHERE department_id = ?");
            $stmt->execute([$_POST['department_code'], $_POST['department_name'], $_POST['fk_hr_staff_id'], $_POST['department_id']]);
            header("Location: ?module=department");
            exit;
        }
        elseif ($current_module == 'training_program' && isset($_POST['update_program'])) {
            $stmt = $pdo->prepare("UPDATE training_program SET program_name = ?, program_description = ?, fk_employee_id = ?, duration_hours = ?, skill_level = ? WHERE program_id = ?");
            $stmt->execute([$_POST['program_name'], $_POST['program_description'], $_POST['fk_employee_id'], $_POST['duration_hours'], $_POST['skill_level'], $_POST['program_id']]);
            header("Location: ?module=training_program");
            exit;
        }
        // Update functionality for training analytics
        elseif ($current_module == 'training_analytics' && isset($_POST['update_analytics'])) {
            $stmt = $pdo->prepare("UPDATE training_analytics SET fk_program_id = ?, metric_name = ?, metric_category = ?, dimension_type = ? WHERE analytic_id = ?");
            $stmt->execute([
                $_POST['fk_program_id'],
                $_POST['metric_name'],
                $_POST['metric_category'],
                $_POST['dimension_type'],
                $_POST['analytic_id']
            ]);
            header("Location: ?module=training_analytics");
            exit;
        }
        // Update functionality for training session
        elseif ($current_module == 'training_session' && isset($_POST['update_session'])) {
            $stmt = $pdo->prepare("UPDATE training_session SET session_name = ?, start_date = ?, end_date = ?, venue = ?, fk_program_id = ? WHERE session_id = ?");
            $stmt->execute([
                $_POST['session_name'], 
                $_POST['start_date'], 
                $_POST['end_date'], 
                $_POST['venue'], 
                $_POST['fk_program_id'],
                $_POST['session_id']
            ]);
            header("Location: ?module=training_session");
            exit;
        }
        // Update functionality for training calendar
        elseif ($current_module == 'training_calendar' && isset($_POST['update_calendar'])) {
            $stmt = $pdo->prepare("UPDATE training_calendar SET event_start = ?, event_name = ?, event_title = ?, location = ?, fk_session_id = ? WHERE calendar_id = ?");
            $stmt->execute([
                $_POST['event_start'],
                $_POST['event_name'],
                $_POST['event_title'],
                $_POST['location'],
                $_POST['fk_session_id'],
                $_POST['calendar_id']
            ]);
            header("Location: ?module=training_calendar");
            exit;
        }
        // Update functionality for training attendance
        elseif ($current_module == 'training_attendance' && isset($_POST['update_attendance'])) {
            $stmt = $pdo->prepare("UPDATE training_attendance SET fk_employee_id = ?, attendance_status = ?, attendance_date = ?, time_in = ?, time_out = ?, hours_attended = ? WHERE attendance_id = ?");
            $stmt->execute([
                $_POST['fk_employee_id'],
                $_POST['attendance_status'],
                $_POST['attendance_date'],
                $_POST['time_in'],
                $_POST['time_out'],
                $_POST['hours_attended'],
                $_POST['attendance_id']
            ]);
            header("Location: ?module=training_attendance");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle delete operations
if (isset($_GET['delete'])) {
    try {
        $module = $_GET['module'];
        $id = $_GET['delete'];
        
        switch($module) {
            case 'department':
                $stmt = $pdo->prepare("DELETE FROM department WHERE department_id = ?");
                break;
            case 'training_program':
                $stmt = $pdo->prepare("DELETE FROM training_program WHERE program_id = ?");
                break;
            case 'training_analytics':
                $stmt = $pdo->prepare("DELETE FROM training_analytics WHERE analytic_id = ?");
                break;
            case 'training_session':
                $stmt = $pdo->prepare("DELETE FROM training_session WHERE session_id = ?");
                break;
            case 'training_calendar':
                $stmt = $pdo->prepare("DELETE FROM training_calendar WHERE calendar_id = ?");
                break;
            case 'training_attendance':
                $stmt = $pdo->prepare("DELETE FROM training_attendance WHERE attendance_id = ?");
                break;
            case 'training_completion':
                $stmt = $pdo->prepare("DELETE FROM training_completion WHERE completion_id = ?");
                break;
            case 'employee':
                $stmt = $pdo->prepare("DELETE FROM employee WHERE employee_id = ?");
                break;
        }
        
        if (isset($stmt)) {
            $stmt->execute([$id]);
            header("Location: ?module=$module");
            exit;
        }
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}

// Handle training completion actions
if (isset($_GET['action']) && $current_module == 'training_completion') {
    try {
        switch ($_GET['action']) {
            case 'delete':
                if (isset($_GET['id'])) {
                    $stmt = $pdo->prepare("DELETE FROM training_completion WHERE completion_id = ?");
                    $stmt->execute([$_GET['id']]);
                  
                }
                break;
        }
        header('Location: ?module=training_completion' . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''));
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header('Location: ?module=training_completion');
        exit;
    }
}

// Handle employee actions
if (isset($_GET['action']) && $current_module == 'employee') {
    try {
        switch ($_GET['action']) {
            case 'delete':
                if (isset($_GET['id'])) {
                    $stmt = $pdo->prepare("DELETE FROM employee WHERE employee_id = ?");
                    $stmt->execute([$_GET['id']]);
                  
                }
                break;
        }
        header('Location: ?module=employee' . (isset($_GET['search']) ? '&search=' . urlencode($_GET['search']) : ''));
        exit;
    } catch(PDOException $e) {
        $_SESSION['error'] = 'Error: ' . $e->getMessage();
        header('Location: ?module=employee');
        exit;
    }
}

// Get single department for view/edit
$view_department = null;
$edit_department = null;
if (isset($_GET['view'])) {
    $stmt = $pdo->prepare("
        SELECT d.*, h.name as hr_staff_name 
        FROM department d 
        LEFT JOIN hr_staff h ON d.fk_hr_staff_id = h.hr_id 
        WHERE d.department_id = ?
    ");
    $stmt->execute([$_GET['view']]);
    $view_department = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM department WHERE department_id = ?");
    $stmt->execute([$_GET['edit']]);
    $edit_department = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get single training program for view/edit
$view_program = null;
$edit_program = null;
if (isset($_GET['view_program'])) {
    $stmt = $pdo->prepare("
        SELECT tp.*, e.first_name, e.last_name 
        FROM training_program tp 
        LEFT JOIN employee e ON tp.fk_employee_id = e.employee_id 
        WHERE tp.program_id = ?
    ");
    $stmt->execute([$_GET['view_program']]);
    $view_program = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit_program'])) {
    $stmt = $pdo->prepare("SELECT * FROM training_program WHERE program_id = ?");
    $stmt->execute([$_GET['edit_program']]);
    $edit_program = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get single training analytics for view/edit
$view_analytics = null;
$edit_analytics = null;
if (isset($_GET['view_analytics'])) {
    $stmt = $pdo->prepare("
        SELECT ta.*, tp.program_name 
        FROM training_analytics ta 
        LEFT JOIN training_program tp ON ta.fk_program_id = tp.program_id 
        WHERE ta.analytic_id = ?
    ");
    $stmt->execute([$_GET['view_analytics']]);
    $view_analytics = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit_analytics'])) {
    $stmt = $pdo->prepare("SELECT * FROM training_analytics WHERE analytic_id = ?");
    $stmt->execute([$_GET['edit_analytics']]);
    $edit_analytics = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get single training session for view/edit
$view_session = null;
$edit_session = null;
if (isset($_GET['view_session'])) {
    $stmt = $pdo->prepare("
        SELECT ts.*, tp.program_name 
        FROM training_session ts 
        LEFT JOIN training_program tp ON ts.fk_program_id = tp.program_id 
        WHERE ts.session_id = ?
    ");
    $stmt->execute([$_GET['view_session']]);
    $view_session = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit_session'])) {
    $stmt = $pdo->prepare("
        SELECT ts.*, tp.program_name 
        FROM training_session ts 
        LEFT JOIN training_program tp ON ts.fk_program_id = tp.program_id 
        WHERE ts.session_id = ?
    ");
    $stmt->execute([$_GET['edit_session']]);
    $edit_session = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get single training calendar for view/edit
$view_calendar = null;
$edit_calendar = null;
if (isset($_GET['view_calendar'])) {
    $stmt = $pdo->prepare("
        SELECT tc.*, ts.session_name, ts.start_date, ts.end_date, ts.venue,
               tp.program_name, e.first_name, e.last_name
        FROM training_calendar tc 
        LEFT JOIN training_session ts ON tc.fk_session_id = ts.session_id
        LEFT JOIN training_program tp ON ts.fk_program_id = tp.program_id
        LEFT JOIN employee e ON tp.fk_employee_id = e.employee_id
        WHERE tc.calendar_id = ?
    ");
    $stmt->execute([$_GET['view_calendar']]);
    $view_calendar = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit_calendar'])) {
    $stmt = $pdo->prepare("
        SELECT tc.*, ts.session_name 
        FROM training_calendar tc 
        LEFT JOIN training_session ts ON tc.fk_session_id = ts.session_id 
        WHERE tc.calendar_id = ?
    ");
    $stmt->execute([$_GET['edit_calendar']]);
    $edit_calendar = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get single training attendance for view/edit
$view_attendance = null;
$edit_attendance = null;
if (isset($_GET['view_attendance'])) {
    $stmt = $pdo->prepare("
        SELECT ta.*, e.first_name, e.last_name 
        FROM training_attendance ta 
        LEFT JOIN employee e ON ta.fk_employee_id = e.employee_id 
        WHERE ta.attendance_id = ?
    ");
    $stmt->execute([$_GET['view_attendance']]);
    $view_attendance = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_GET['edit_attendance'])) {
    $stmt = $pdo->prepare("SELECT * FROM training_attendance WHERE attendance_id = ?");
    $stmt->execute([$_GET['edit_attendance']]);
    $edit_attendance = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Training Completion specific variables
$edit_completion = null;
$view_completion = null;
$completions = [];
$search = $_GET['search'] ?? '';

if ($current_module == 'training_completion') {
    // Search functionality for training completion
    $where_conditions = [];
    $params = [];

    if (!empty($search)) {
        $where_conditions[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.employee_id LIKE ? OR tc.final_grade LIKE ?)";
        $search_param = "%$search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    }

    // Build the WHERE clause
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }

    // Fetch all training completions with employee names
    try {
        $stmt = $pdo->prepare("
            SELECT tc.*, e.first_name, e.last_name, e.employee_id as emp_id
            FROM training_completion tc
            JOIN employee e ON tc.fk_employee_id = e.employee_id
            $where_clause
            ORDER BY tc.completion_status ASC, tc.completion_date DESC
        ");
        $stmt->execute($params);
        $completions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch specific completion for editing
        if (isset($_GET['edit_id'])) {
            $editStmt = $pdo->prepare("
                SELECT tc.*, e.first_name, e.last_name 
                FROM training_completion tc
                JOIN employee e ON tc.fk_employee_id = e.employee_id
                WHERE tc.completion_id = ?
            ");
            $editStmt->execute([$_GET['edit_id']]);
            $edit_completion = $editStmt->fetch(PDO::FETCH_ASSOC);
        }

        // Fetch specific completion for viewing
        if (isset($_GET['view_id'])) {
            $viewStmt = $pdo->prepare("
                SELECT tc.*, e.first_name, e.last_name 
                FROM training_completion tc
                JOIN employee e ON tc.fk_employee_id = e.employee_id
                WHERE tc.completion_id = ?
            ");
            $viewStmt->execute([$_GET['view_id']]);
            $view_completion = $viewStmt->fetch(PDO::FETCH_ASSOC);
        }

    } catch(PDOException $e) {
        $error = "Error fetching training completion data: " . $e->getMessage();
    }
}

// Employee specific variables - FIXED
$edit_employee = null;
$view_employee = null;
$employees = [];
$employee_search = $_GET['search'] ?? '';

if ($current_module == 'employee') {
    // Search functionality for employees
    $where_conditions = [];
    $params = [];

    if (!empty($employee_search)) {
        $where_conditions[] = "(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR employee_id LIKE ?)";
        $search_param = "%$employee_search%";
        $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
    }

    // Build the WHERE clause
    $where_clause = '';
    if (!empty($where_conditions)) {
        $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    }

    // Fetch employees - with position title
    try {
        // Check if position table exists and has the correct structure
        $table_exists = $pdo->query("SHOW TABLES LIKE 'position'")->fetch(PDO::FETCH_ASSOC);
        
        if ($table_exists) {
            $position_check = $pdo->query("SHOW COLUMNS FROM position")->fetchAll(PDO::FETCH_ASSOC);
            $position_columns = array_column($position_check, 'Field');
            
            $position_name_col = 'position_id'; // default
            if (in_array('position_name', $position_columns)) {
                $position_name_col = 'position_name';
            } elseif (in_array('name', $position_columns)) {
                $position_name_col = 'name';
            } elseif (in_array('title', $position_columns)) {
                $position_name_col = 'title';
            } elseif (in_array('position_title', $position_columns)) {
                $position_name_col = 'position_title';
            }
            
            $stmt = $pdo->prepare("
                SELECT e.*, d.department_name, p.$position_name_col as position_title 
                FROM employee e 
                LEFT JOIN department d ON e.fk_department_id = d.department_id 
                LEFT JOIN position p ON e.fk_position_id = p.position_id 
                $where_clause 
                ORDER BY first_name, last_name
            ");
        } else {
            // If position table doesn't exist, just get employee and department data
            $stmt = $pdo->prepare("
                SELECT e.*, d.department_name, NULL as position_title 
                FROM employee e 
                LEFT JOIN department d ON e.fk_department_id = d.department_id 
                $where_clause 
                ORDER BY first_name, last_name
            ");
        }
        
        $stmt->execute($params);
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch specific employee for editing - FIXED
        if (isset($_GET['edit'])) {
            $editStmt = $pdo->prepare("
                SELECT e.*, d.department_name, p.$position_name_col as position_title 
                FROM employee e 
                LEFT JOIN department d ON e.fk_department_id = d.department_id 
                LEFT JOIN position p ON e.fk_position_id = p.position_id 
                WHERE e.employee_id = ?
            ");
            $editStmt->execute([$_GET['edit']]);
            $edit_employee = $editStmt->fetch(PDO::FETCH_ASSOC);
        }

        // Fetch specific employee for viewing - FIXED
        if (isset($_GET['view'])) {
            $viewStmt = $pdo->prepare("
                SELECT e.*, d.department_name, p.$position_name_col as position_title 
                FROM employee e 
                LEFT JOIN department d ON e.fk_department_id = d.department_id 
                LEFT JOIN position p ON e.fk_position_id = p.position_id 
                WHERE e.employee_id = ?
            ");
            $viewStmt->execute([$_GET['view']]);
            $view_employee = $viewStmt->fetch(PDO::FETCH_ASSOC);
        }

    } catch (PDOException $e) {
        $error = "Error fetching employee data: " . $e->getMessage();
    }
}

// Kunin ang data mula sa database
try {
    // Departments
    $stmt_departments = $pdo->query("
        SELECT d.*, h.name as hr_staff_name 
        FROM department d 
        LEFT JOIN hr_staff h ON d.fk_hr_staff_id = h.hr_id
    ");
    $departments = $stmt_departments->fetchAll(PDO::FETCH_ASSOC);

    // Training Programs with employee info
    $stmt_programs = $pdo->query("
        SELECT tp.*, e.first_name, e.last_name 
        FROM training_program tp 
        LEFT JOIN employee e ON tp.fk_employee_id = e.employee_id
    ");
    $training_programs = $stmt_programs->fetchAll(PDO::FETCH_ASSOC);

    // Training Analytics with program info
    $stmt_analytics = $pdo->query("
        SELECT ta.*, tp.program_name 
        FROM training_analytics ta 
        LEFT JOIN training_program tp ON ta.fk_program_id = tp.program_id
    ");
    $training_analytics = $stmt_analytics->fetchAll(PDO::FETCH_ASSOC);

    // Training Sessions with program info
    $stmt_sessions = $pdo->query("
        SELECT ts.*, tp.program_name 
        FROM training_session ts 
        LEFT JOIN training_program tp ON ts.fk_program_id = tp.program_id
    ");
    $training_sessions = $stmt_sessions->fetchAll(PDO::FETCH_ASSOC);

    // Training Calendar with session info
    $stmt_calendar = $pdo->query("
        SELECT tc.*, ts.session_name, ts.start_date, ts.end_date, ts.venue,
               tp.program_name, e.first_name, e.last_name
        FROM training_calendar tc 
        LEFT JOIN training_session ts ON tc.fk_session_id = ts.session_id
        LEFT JOIN training_program tp ON ts.fk_program_id = tp.program_id
        LEFT JOIN employee e ON tp.fk_employee_id = e.employee_id
        ORDER BY tc.event_start ASC
    ");
    $calendar_events = $stmt_calendar->fetchAll(PDO::FETCH_ASSOC);

    // Training Attendance with employee info
    $stmt_attendance = $pdo->query("
        SELECT ta.*, e.first_name, e.last_name 
        FROM training_attendance ta 
        LEFT JOIN employee e ON ta.fk_employee_id = e.employee_id
        ORDER BY ta.attendance_date DESC
    ");
    $training_attendance = $stmt_attendance->fetchAll(PDO::FETCH_ASSOC);

    // Get dropdown data
    $hr_staff = $pdo->query("SELECT * FROM hr_staff")->fetchAll(PDO::FETCH_ASSOC);
    
    // Get employees for dropdowns
    try {
        $all_employees = $pdo->query("SELECT * FROM employee")->fetchAll(PDO::FETCH_ASSOC);
        
        // If no employees found, try alternative table names
        if (empty($all_employees)) {
            // Try common alternative table names
            $tables_to_try = ['employees', 'staff', 'users', 'personnel'];
            foreach ($tables_to_try as $table) {
                try {
                    $all_employees = $pdo->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
                    if (!empty($all_employees)) {
                        break;
                    }
                } catch (PDOException $e) {
                    // Continue to next table
                    continue;
                }
            }
        }
        
        // If still no employees, create dummy data for testing
        if (empty($all_employees)) {
            $all_employees = [
                ['employee_id' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
                ['employee_id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith'],
                ['employee_id' => 3, 'first_name' => 'Michael', 'last_name' => 'Johnson']
            ];
        }
        
    } catch (PDOException $e) {
        // If there's an error, create dummy employees
        $all_employees = [
            ['employee_id' => 1, 'first_name' => 'John', 'last_name' => 'Doe'],
            ['employee_id' => 2, 'first_name' => 'Jane', 'last_name' => 'Smith'],
            ['employee_id' => 3, 'first_name' => 'Michael', 'last_name' => 'Johnson']
        ];
    }

    // Fix for position table - handle case where position table might not exist or has different structure
    try {
        // First check if position table exists
        $table_exists = $pdo->query("SHOW TABLES LIKE 'position'")->fetch(PDO::FETCH_ASSOC);
        
        if ($table_exists) {
            // Check what columns exist in position table
            $position_check = $pdo->query("SHOW COLUMNS FROM position")->fetchAll(PDO::FETCH_ASSOC);
            $position_columns = array_column($position_check, 'Field');
            
            // Determine the correct column name for position name
            $position_name_col = 'position_id'; // default fallback
            
            if (in_array('position_name', $position_columns)) {
                $position_name_col = 'position_name';
            } elseif (in_array('name', $position_columns)) {
                $position_name_col = 'name';
            } elseif (in_array('title', $position_columns)) {
                $position_name_col = 'title';
            } elseif (in_array('position_title', $position_columns)) {
                $position_name_col = 'position_title';
            }
            
            // Fetch positions with the correct column name
            $all_positions = $pdo->query("SELECT position_id, $position_name_col as position_title FROM position")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // If position table doesn't exist, create empty array
            $all_positions = [];
        }
        
    } catch (PDOException $e) {
        // If there's any error with position table, create empty array
        $all_positions = [];
    }
    
    $all_programs = $pdo->query("SELECT * FROM training_program")->fetchAll(PDO::FETCH_ASSOC);
    $all_sessions = $pdo->query("SELECT * FROM training_session")->fetchAll(PDO::FETCH_ASSOC);
    $all_departments = $pdo->query("SELECT * FROM department")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Set current month and year for calendar
$current_month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$current_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Calculate previous and next months
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year = $current_year - 1;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year = $current_year + 1;
}

// Get first day of month and number of days
$first_day = date('N', strtotime("$current_year-$current_month-01"));
$days_in_month = date('t', strtotime("$current_year-$current_month-01"));

// Month names for display
$month_names = [
    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
];

// Function to truncate text
function truncateText($text, $length = 50) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        function confirmDelete(module, id, name) {
            if (confirm('Are you sure you want to delete ' + name + '?')) {
                window.location.href = '?module=' + module + '&delete=' + id;
            }
        }

        function clearSearch() {
            const searchInput = document.getElementById('searchProgramInput') || document.getElementById('searchInput') || document.getElementById('searchSessionInput') || document.getElementById('searchCalendarInput') || document.getElementById('searchAnalyticsInput') || document.getElementById('searchAttendanceInput') || document.getElementById('searchCompletionInput') || document.getElementById('searchEmployeeInput');
            if (searchInput) {
                searchInput.value = '';
                // Refresh the table
                if (typeof searchPrograms === 'function') {
                    searchPrograms();
                }
                if (typeof searchDepartments === 'function') {
                    searchDepartments();
                }
                if (typeof searchSessions === 'function') {
                    searchSessions();
                }
                if (typeof searchCalendar === 'function') {
                    searchCalendar();
                }
                if (typeof searchAnalytics === 'function') {
                    searchAnalytics();
                }
                if (typeof searchAttendance === 'function') {
                    searchAttendance();
                }
                if (typeof searchCompletion === 'function') {
                    searchCompletion();
                }
                if (typeof searchEmployees === 'function') {
                    searchEmployees();
                }
            }
        }
        
        // Search functionality for department table
        function searchDepartments() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('departmentTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdCode = tr[i].getElementsByTagName('td')[0];
                const tdName = tr[i].getElementsByTagName('td')[1];
                const tdHR = tr[i].getElementsByTagName('td')[2];
                
                if (tdCode || tdName || tdHR) {
                    const txtValueCode = tdCode.textContent || tdCode.innerText;
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValueHR = tdHR.textContent || tdHR.innerText;
                    
                    if (txtValueCode.toUpperCase().indexOf(filter) > -1 || 
                        txtValueName.toUpperCase().indexOf(filter) > -1 || 
                        txtValueHR.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for training program table
        function searchPrograms() {
            const input = document.getElementById('searchProgramInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('programTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName('td')[0];
                const tdDesc = tr[i].getElementsByTagName('td')[1];
                const tdEmployee = tr[i].getElementsByTagName('td')[2];
                
                if (tdName || tdDesc || tdEmployee) {
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValueDesc = tdDesc.textContent || tdDesc.innerText;
                    const txtValueEmployee = tdEmployee.textContent || tdEmployee.innerText;
                    
                    if (txtValueName.toUpperCase().indexOf(filter) > -1 || 
                        txtValueDesc.toUpperCase().indexOf(filter) > -1 || 
                        txtValueEmployee.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for training analytics table
        function searchAnalytics() {
            const input = document.getElementById('searchAnalyticsInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('analyticsTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdProgram = tr[i].getElementsByTagName('td')[0];
                const tdMetric = tr[i].getElementsByTagName('td')[1];
                const tdCategory = tr[i].getElementsByTagName('td')[2];
                const tdDimension = tr[i].getElementsByTagName('td')[3];
                
                if (tdProgram || tdMetric || tdCategory || tdDimension) {
                    const txtValueProgram = tdProgram.textContent || tdProgram.innerText;
                    const txtValueMetric = tdMetric.textContent || tdMetric.innerText;
                    const txtValueCategory = tdCategory.textContent || tdCategory.innerText;
                    const txtValueDimension = tdDimension.textContent || tdDimension.innerText;
                    
                    if (txtValueProgram.toUpperCase().indexOf(filter) > -1 || 
                        txtValueMetric.toUpperCase().indexOf(filter) > -1 || 
                        txtValueCategory.toUpperCase().indexOf(filter) > -1 || 
                        txtValueDimension.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for training session table
        function searchSessions() {
            const input = document.getElementById('searchSessionInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('sessionTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName('td')[0];
                const tdStart = tr[i].getElementsByTagName('td')[1];
                const tdEnd = tr[i].getElementsByTagName('td')[2];
                const tdVenue = tr[i].getElementsByTagName('td')[3];
                const tdProgram = tr[i].getElementsByTagName('td')[4];
                
                if (tdName || tdStart || tdEnd || tdVenue || tdProgram) {
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValueStart = tdStart.textContent || tdStart.innerText;
                    const txtValueEnd = tdEnd.textContent || tdEnd.innerText;
                    const txtValueVenue = tdVenue.textContent || tdVenue.innerText;
                    const txtValueProgram = tdProgram.textContent || tdProgram.innerText;
                    
                    if (txtValueName.toUpperCase().indexOf(filter) > -1 || 
                        txtValueStart.toUpperCase().indexOf(filter) > -1 || 
                        txtValueEnd.toUpperCase().indexOf(filter) > -1 || 
                        txtValueVenue.toUpperCase().indexOf(filter) > -1 || 
                        txtValueProgram.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for calendar table
        function searchCalendar() {
            const input = document.getElementById('searchCalendarInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('calendarTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName('td')[0];
                const tdTitle = tr[i].getElementsByTagName('td')[1];
                const tdDate = tr[i].getElementsByTagName('td')[2];
                const tdLocation = tr[i].getElementsByTagName('td')[3];
                const tdSession = tr[i].getElementsByTagName('td')[4];
                
                if (tdName || tdTitle || tdDate || tdLocation || tdSession) {
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValueTitle = tdTitle.textContent || tdTitle.innerText;
                    const txtValueDate = tdDate.textContent || tdDate.innerText;
                    const txtValueLocation = tdLocation.textContent || tdLocation.innerText;
                    const txtValueSession = tdSession.textContent || tdSession.innerText;
                    
                    if (txtValueName.toUpperCase().indexOf(filter) > -1 || 
                        txtValueTitle.toUpperCase().indexOf(filter) > -1 || 
                        txtValueDate.toUpperCase().indexOf(filter) > -1 || 
                        txtValueLocation.toUpperCase().indexOf(filter) > -1 || 
                        txtValueSession.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for training attendance table
        function searchAttendance() {
            const input = document.getElementById('searchAttendanceInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('attendanceTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdEmployee = tr[i].getElementsByTagName('td')[0];
                const tdStatus = tr[i].getElementsByTagName('td')[1];
                const tdDate = tr[i].getElementsByTagName('td')[2];
                const tdTimeIn = tr[i].getElementsByTagName('td')[3];
                const tdTimeOut = tr[i].getElementsByTagName('td')[4];
                const tdHours = tr[i].getElementsByTagName('td')[5];
                
                if (tdEmployee || tdStatus || tdDate || tdTimeIn || tdTimeOut || tdHours) {
                    const txtValueEmployee = tdEmployee.textContent || tdEmployee.innerText;
                    const txtValueStatus = tdStatus.textContent || tdStatus.innerText;
                    const txtValueDate = tdDate.textContent || tdDate.innerText;
                    const txtValueTimeIn = tdTimeIn.textContent || tdTimeIn.innerText;
                    const txtValueTimeOut = tdTimeOut.textContent || tdTimeOut.innerText;
                    const txtValueHours = tdHours.textContent || tdHours.innerText;
                    
                    if (txtValueEmployee.toUpperCase().indexOf(filter) > -1 || 
                        txtValueStatus.toUpperCase().indexOf(filter) > -1 || 
                        txtValueDate.toUpperCase().indexOf(filter) > -1 || 
                        txtValueTimeIn.toUpperCase().indexOf(filter) > -1 || 
                        txtValueTimeOut.toUpperCase().indexOf(filter) > -1 || 
                        txtValueHours.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for training completion table
        function searchCompletion() {
            const input = document.getElementById('searchCompletionInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('completionTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdEmployee = tr[i].getElementsByTagName('td')[0];
                const tdStatus = tr[i].getElementsByTagName('td')[1];
                const tdDate = tr[i].getElementsByTagName('td')[2];
                const tdCertificate = tr[i].getElementsByTagName('td')[3];
                const tdGrade = tr[i].getElementsByTagName('td')[4];
                const tdScore = tr[i].getElementsByTagName('td')[5];
                
                if (tdEmployee || tdStatus || tdDate || tdCertificate || tdGrade || tdScore) {
                    const txtValueEmployee = tdEmployee.textContent || tdEmployee.innerText;
                    const txtValueStatus = tdStatus.textContent || tdStatus.innerText;
                    const txtValueDate = tdDate.textContent || tdDate.innerText;
                    const txtValueCertificate = tdCertificate.textContent || tdCertificate.innerText;
                    const txtValueGrade = tdGrade.textContent || tdGrade.innerText;
                    const txtValueScore = tdScore.textContent || tdScore.innerText;
                    
                    if (txtValueEmployee.toUpperCase().indexOf(filter) > -1 || 
                        txtValueStatus.toUpperCase().indexOf(filter) > -1 || 
                        txtValueDate.toUpperCase().indexOf(filter) > -1 || 
                        txtValueCertificate.toUpperCase().indexOf(filter) > -1 || 
                        txtValueGrade.toUpperCase().indexOf(filter) > -1 || 
                        txtValueScore.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Search functionality for employee table
        function searchEmployees() {
            const input = document.getElementById('searchEmployeeInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('employeeTable');
            if (!table) return;
            
            const tr = table.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdName = tr[i].getElementsByTagName('td')[0];
                const tdEmail = tr[i].getElementsByTagName('td')[1];
                const tdPhone = tr[i].getElementsByTagName('td')[2];
                const tdHireDate = tr[i].getElementsByTagName('td')[3];
                const tdStatus = tr[i].getElementsByTagName('td')[4];
                const tdDepartment = tr[i].getElementsByTagName('td')[5];
                const tdPosition = tr[i].getElementsByTagName('td')[6];
                
                if (tdName || tdEmail || tdPhone || tdHireDate || tdStatus || tdDepartment || tdPosition) {
                    const txtValueName = tdName.textContent || tdName.innerText;
                    const txtValueEmail = tdEmail.textContent || tdEmail.innerText;
                    const txtValuePhone = tdPhone.textContent || tdPhone.innerText;
                    const txtValueHireDate = tdHireDate.textContent || tdHireDate.innerText;
                    const txtValueStatus = tdStatus.textContent || tdStatus.innerText;
                    const txtValueDepartment = tdDepartment.textContent || tdDepartment.innerText;
                    const txtValuePosition = tdPosition.textContent || tdPosition.innerText;
                    
                    if (txtValueName.toUpperCase().indexOf(filter) > -1 || 
                        txtValueEmail.toUpperCase().indexOf(filter) > -1 || 
                        txtValuePhone.toUpperCase().indexOf(filter) > -1 || 
                        txtValueHireDate.toUpperCase().indexOf(filter) > -1 || 
                        txtValueStatus.toUpperCase().indexOf(filter) > -1 || 
                        txtValueDepartment.toUpperCase().indexOf(filter) > -1 || 
                        txtValuePosition.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        function closeModal() {
            window.location.href = '?module=<?= $current_module ?>';
        }

        function closeProgramModal() {
            window.location.href = '?module=training_program';
        }

        function closeAnalyticsModal() {
            window.location.href = '?module=training_analytics';
        }

        function closeSessionModal() {
            window.location.href = '?module=training_session';
        }

        function closeCalendarModal() {
            window.location.href = '?module=training_calendar';
        }

        function closeAttendanceModal() {
            window.location.href = '?module=training_attendance';
        }

        function closeCompletionModal() {
            window.location.href = '?module=training_completion';
        }

        function closeEmployeeModal() {
            <?php if (!empty($employee_search)): ?>
                window.location.href = '?module=employee&search=<?= urlencode($employee_search) ?>';
            <?php else: ?>
                window.location.href = '?module=employee';
            <?php endif; ?>
        }

        // Calculate hours attended automatically
        function calculateHours() {
            const timeIn = document.getElementById('time_in');
            const timeOut = document.getElementById('time_out');
            const hoursAttended = document.getElementById('hours_attended');
            
            if (timeIn && timeOut && hoursAttended && timeIn.value && timeOut.value) {
                const start = new Date('1970-01-01T' + timeIn.value + 'Z');
                const end = new Date('1970-01-01T' + timeOut.value + 'Z');
                const diff = (end - start) / (1000 * 60 * 60); // difference in hours
                
                if (diff > 0) {
                    hoursAttended.value = diff.toFixed(2);
                } else {
                    hoursAttended.value = '0';
                }
            }
        }

        // Limit assessment score to 0-100
        function limitScore(input) {
            if (input.value > 100) {
                input.value = 100;
            } else if (input.value < 0) {
                input.value = 0;
            }
        }
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex">
    <!-- ✅ Included Sidebar -->
    <?php include '../employee/ess-sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Header -->
        <header class="bg-white shadow">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-gray-800">Training Management</h1>
                </div>
            </div>
        </header>

        <!-- Top Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="px-6">
                <div class="flex space-x-8">
                    <a href="?module=department" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'department' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-building mr-2"></i>Department
                    </a>
                    <a href="?module=employee" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'employee' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-users mr-2"></i>Employee
                    </a>
                    <a href="?module=training_program" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'training_program' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-graduation-cap mr-2"></i>Training Program
                    </a>
                    <a href="?module=training_analytics" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'training_analytics' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-chart-bar mr-2"></i>Training Analytics
                    </a>
                    <a href="?module=training_session" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'training_session' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-calendar-day mr-2"></i>Training Session
                    </a>
                    <a href="?module=training_calendar" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'training_calendar' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-calendar-alt mr-2"></i>Training Calendar
                    </a>
                    <a href="?module=training_attendance" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'training_attendance' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-clipboard-check mr-2"></i>Training Attendance
                    </a>
                    <a href="?module=training_completion" class="py-4 border-b-2 font-medium text-sm <?= $current_module == 'training_completion' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' ?>">
                        <i class="fas fa-certificate mr-2"></i>Training Completion
                    </a>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="p-6">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- Success/Error Messages for Training Completion -->
            <?php if ($current_module == 'training_completion' && isset($_SESSION['success'])): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($current_module == 'training_completion' && isset($_SESSION['error'])): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Success/Error Messages for Employee -->
            <?php if ($current_module == 'employee' && isset($_SESSION['success'])): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            </div>
            <?php endif; ?>

            <?php if ($current_module == 'employee' && isset($_SESSION['error'])): ?>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Debug info for employees -->
            <?php if (empty($all_employees)): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4">
                    <strong>Warning:</strong> No employees found in database. Using sample data for demonstration.
                </div>
            <?php endif; ?>

            <div class="space-y-6">
                <?php if ($current_module == 'department'): ?>
                    <!-- =============================== -->
                    <!-- DEPARTMENT MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Department List Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Department List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchInput" placeholder="Search..." 
                                               onkeyup="searchDepartments()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($departments) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="departmentTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Department Code</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Department Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">HR Staff</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($departments as $dept): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($dept['department_code']) ?></td>
                                                    <td class="p-4 text-sm text-gray-800"><?= htmlspecialchars($dept['department_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($dept['hr_staff_name'] ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-building text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No departments found</p>
                                    <p class="text-sm mt-2">Add departments to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'employee'): ?>
                    <!-- =============================== -->
                    <!-- EMPLOYEE MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Employee List Card - READ ONLY -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Employee List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchEmployeeInput" placeholder="Search..." 
                                               onkeyup="searchEmployees()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($employees) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="employeeTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Email</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Phone</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Hire Date</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Department</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Position</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($employees as $emp): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($emp['email']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($emp['phone_number']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= date('M j, Y', strtotime($emp['hire_date'])) ?></td>
                                                    <td class="p-4">
                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                            <?= $emp['employment_status'] == 'Active' ? 'bg-green-100 text-green-800' : 
                                                               ($emp['employment_status'] == 'Inactive' ? 'bg-red-100 text-red-800' : 
                                                               ($emp['employment_status'] == 'On Leave' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) ?>">
                                                            <?= $emp['employment_status'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($emp['department_name'] ?? 'N/A') ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($emp['position_title'] ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No employees found</p>
                                    <p class="text-sm mt-2">Add employees to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'training_program'): ?>
                    <!-- =============================== -->
                    <!-- TRAINING PROGRAM MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Program List Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Training Program List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchProgramInput" placeholder="Search..." 
                                               onkeyup="searchPrograms()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($training_programs) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="programTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Program Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Employee</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Duration</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Skill Level</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($training_programs as $program): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($program['program_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600" title="<?= htmlspecialchars($program['program_description']) ?>">
                                                        <?= htmlspecialchars(truncateText($program['program_description'], 50)) ?>
                                                    </td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($program['first_name'] . ' ' . $program['last_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($program['duration_hours']) ?> hrs</td>
                                                    <td class="p-4">
                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                            <?= $program['skill_level'] == 'Beginner' ? 'bg-green-100 text-green-800' : 
                                                               ($program['skill_level'] == 'Intermediate' ? 'bg-yellow-100 text-yellow-800' : 
                                                               ($program['skill_level'] == 'Advanced' ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800')) ?>">
                                                            <?= $program['skill_level'] ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No training programs found</p>
                                    <p class="text-sm mt-2">Create training programs to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'training_analytics'): ?>
                    <!-- =============================== -->
                    <!-- TRAINING ANALYTICS MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Analytics List Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Training Analytics List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchAnalyticsInput" placeholder="Search..." 
                                               onkeyup="searchAnalytics()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($training_analytics) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="analyticsTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Program</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Metric Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Dimension Type</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($training_analytics as $analytic): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800"><?= htmlspecialchars($analytic['program_name'] ?? 'N/A') ?></td>
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($analytic['metric_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($analytic['metric_category']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($analytic['dimension_type']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-chart-bar text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No training analytics found</p>
                                    <p class="text-sm mt-2">Add training analytics to track metrics</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'training_session'): ?>
                    <!-- =============================== -->
                    <!-- TRAINING SESSION MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Session List Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Training Session List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchSessionInput" placeholder="Search..." 
                                               onkeyup="searchSessions()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($training_sessions) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="sessionTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Session Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Start Date</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">End Date</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Venue</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Program</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($training_sessions as $session): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($session['session_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= date('M j, Y', strtotime($session['start_date'])) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= date('M j, Y', strtotime($session['end_date'])) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($session['venue']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($session['program_name'] ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-calendar-day text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No training sessions found</p>
                                    <p class="text-sm mt-2">Add training sessions to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'training_calendar'): ?>
                    <!-- =============================== -->
                    <!-- TRAINING CALENDAR MODULE -->
                    <!-- =============================== -->
                    
                    <!-- Top Row: Calendar View -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Calendar View -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="p-6 border-b">
                                <div class="flex justify-between items-center">
                                    <h2 class="text-xl font-bold text-gray-800">Training Calendar - <?= $month_names[$current_month] ?> <?= $current_year ?></h2>
                                    <div class="flex space-x-2">
                                        <a href="?module=training_calendar&month=<?= $prev_month ?>&year=<?= $prev_year ?>" 
                                           class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition duration-200">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                        <a href="?module=training_calendar&month=<?= date('n') ?>&year=<?= date('Y') ?>" 
                                           class="bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 transition duration-200">
                                            Today
                                        </a>
                                        <a href="?module=training_calendar&month=<?= $next_month ?>&year=<?= $next_year ?>" 
                                           class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition duration-200">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-6">
                                <!-- Calendar Grid -->
                                <div class="grid grid-cols-7 gap-1 mb-2">
                                    <?php 
                                    $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                    foreach ($days as $day): 
                                    ?>
                                        <div class="text-center text-sm font-semibold text-gray-600 py-2">
                                            <?= $day ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="grid grid-cols-7 gap-1">
                                    <!-- Empty cells for days before the first day of month -->
                                    <?php for ($i = 1; $i < $first_day; $i++): ?>
                                        <div class="h-24 bg-gray-50 rounded-lg"></div>
                                    <?php endfor; ?>

                                    <!-- Days of the month -->
                                    <?php for ($day = 1; $day <= $days_in_month; $day++): ?>
                                        <?php
                                        $current_date = sprintf('%04d-%02d-%02d', $current_year, $current_month, $day);
                                        $day_events = array_filter($calendar_events, function($event) use ($current_date) {
                                            return date('Y-m-d', strtotime($event['event_start'])) == $current_date;
                                        });
                                        $is_today = $current_date == date('Y-m-d');
                                        ?>
                                        <div class="h-24 border border-gray-200 rounded-lg p-1 <?= $is_today ? 'bg-blue-50 border-blue-300' : 'bg-white' ?>">
                                            <div class="text-right">
                                                <span class="text-sm font-medium <?= $is_today ? 'text-blue-600' : 'text-gray-700' ?>">
                                                    <?= $day ?>
                                                </span>
                                            </div>
                                            <div class="mt-1 space-y-1 max-h-16 overflow-y-auto">
                                                <?php foreach ($day_events as $event): ?>
                                                    <div class="text-xs bg-blue-100 text-blue-800 px-1 py-0.5 rounded truncate" 
                                                         title="<?= htmlspecialchars($event['event_name']) ?>">
                                                        <?= htmlspecialchars(truncateText($event['event_name'], 15)) ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Events List Card -->
                    <div class="bg-white rounded-lg shadow mt-6">
                        <div class="p-6 border-b">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Training Events List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchCalendarInput" placeholder="Search..." 
                                               onkeyup="searchCalendar()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($calendar_events) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="calendarTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Event Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Event Title</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Event Start</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Location</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Session</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($calendar_events as $event): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($event['event_name']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($event['event_title']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= date('M j, Y g:i A', strtotime($event['event_start'])) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($event['location']) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($event['session_name'] ?? 'N/A') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-calendar-alt text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No training events found</p>
                                    <p class="text-sm mt-2">Add training events to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'training_attendance'): ?>
                    <!-- =============================== -->
                    <!-- TRAINING ATTENDANCE MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Attendance List Card -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Training Attendance List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchAttendanceInput" placeholder="Search..." 
                                               onkeyup="searchAttendance()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($training_attendance) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="attendanceTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Employee</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Time In</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Time Out</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Hours</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($training_attendance as $attendance): ?>
                                                <tr class="hover:bg-gray-50 transition duration-150">
                                                    <td class="p-4 text-sm text-gray-800 font-medium"><?= htmlspecialchars($attendance['first_name'] . ' ' . $attendance['last_name']) ?></td>
                                                    <td class="p-4">
                                                        <span class="px-3 py-1 text-xs rounded-full 
                                                            <?= $attendance['attendance_status'] == 'Present' ? 'bg-green-100 text-green-800' : 
                                                               ($attendance['attendance_status'] == 'Late' ? 'bg-yellow-100 text-yellow-800' : 
                                                               ($attendance['attendance_status'] == 'Absent' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) ?>">
                                                            <?= $attendance['attendance_status'] ?>
                                                        </span>
                                                    </td>
                                                    <td class="p-4 text-sm text-gray-600"><?= date('M j, Y', strtotime($attendance['attendance_date'])) ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= $attendance['time_in'] ? date('g:i A', strtotime($attendance['time_in'])) : 'N/A' ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= $attendance['time_out'] ? date('g:i A', strtotime($attendance['time_out'])) : 'N/A' ?></td>
                                                    <td class="p-4 text-sm text-gray-600"><?= htmlspecialchars($attendance['hours_attended']) ?> hrs</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-clipboard-check text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No training attendance records found</p>
                                    <p class="text-sm mt-2">Add attendance records to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($current_module == 'training_completion'): ?>
                    <!-- =============================== -->
                    <!-- TRAINING COMPLETION MODULE - READ ONLY -->
                    <!-- =============================== -->

                    <!-- Training Completion List Card - READ ONLY -->
                    <div class="bg-white rounded-lg shadow">
                        <div class="p-6">
                            <div class="flex justify-between items-center">
                                <h2 class="text-xl font-bold text-gray-800">Training Completion List</h2>
                                <div class="flex space-x-4">
                                    <div class="relative">
                                        <input type="text" id="searchCompletionInput" placeholder="Search..." 
                                               onkeyup="searchCompletion()"
                                               class="border border-gray-300 rounded-lg px-4 py-2 pl-10 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-64">
                                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                    </div>
                                    <button onclick="clearSearch()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200">
                                        Clear
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="p-6">
                            <?php if (count($completions) > 0): ?>
                                <div class="overflow-x-auto" style="max-height: 400px;">
                                    <table id="completionTable" class="w-full">
                                        <thead class="bg-gray-50 sticky top-0">
                                            <tr>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Employee Name</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Completion Date</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Certificate</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Final Grade</th>
                                                <th class="p-4 text-left text-sm font-semibold text-gray-700 uppercase tracking-wider">Score</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($completions as $completion): ?>
                                            <tr class="hover:bg-gray-50 transition duration-150">
                                                <td class="p-4 text-sm text-gray-800 font-medium">
                                                    <?= htmlspecialchars($completion['first_name'] . ' ' . $completion['last_name']) ?>
                                                </td>
                                                <td class="p-4">
                                                    <?php
                                                    $status_badge = [
                                                        1 => ['color' => 'bg-yellow-100 text-yellow-800', 'text' => 'In Progress'],
                                                        2 => ['color' => 'bg-green-100 text-green-800', 'text' => 'Completed'],
                                                        3 => ['color' => 'bg-red-100 text-red-800', 'text' => 'Failed'],
                                                        4 => ['color' => 'bg-gray-100 text-gray-800', 'text' => 'Withdrawn']
                                                    ];
                                                    $status = $completion['completion_status'];
                                                    ?>
                                                    <span class="px-3 py-1 text-xs rounded-full <?= $status_badge[$status]['color'] ?>">
                                                        <?= $status_badge[$status]['text'] ?>
                                                    </span>
                                                </td>
                                                <td class="p-4 text-sm text-gray-600">
                                                    <?= date('M j, Y', strtotime($completion['completion_date'])) ?>
                                                </td>
                                                <td class="p-4">
                                                    <?php if($completion['certificate_issued'] == 1): ?>
                                                    <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                        Yes
                                                    </span>
                                                    <?php else: ?>
                                                    <span class="px-3 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                                        No
                                                    </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="p-4 text-sm text-gray-600">
                                                    <?= $completion['final_grade'] ? htmlspecialchars($completion['final_grade']) : 'N/A' ?>
                                                </td>
                                                <td class="p-4 text-sm text-gray-600">
                                                    <?= $completion['assessment_score'] ? $completion['assessment_score'] . '%' : 'N/A' ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fas fa-certificate text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No training completion records found</p>
                                    <p class="text-sm mt-2">Add training completion records to get started</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>