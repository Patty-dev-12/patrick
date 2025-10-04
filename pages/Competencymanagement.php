<?php
include '../config/db_compe.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_competency'])) {
        $competency_name = $_POST['competency_name'];
        $description = $_POST['description'];
        $is_core_competency = isset($_POST['is_core_competency']) ? 1 : 0;
        $competency_category = $_POST['competency_category'];
        $competency_type = $_POST['competency_type'];
        $proficiency_levels = $_POST['proficiency_levels'];
        $created_by = 'Admin';
        
        $sql = "INSERT INTO competency (competency_name, description, is_core_competency, competency_category, competency_type, proficiency_levels, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiisss", $competency_name, $description, $is_core_competency, $competency_category, $competency_type, $proficiency_levels, $created_by);
        
        if ($stmt->execute()) {
            // Success
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update competency
    if (isset($_POST['update_competency'])) {
        $competency_id = $_POST['competency_id'];
        $competency_name = $_POST['competency_name'];
        $description = $_POST['description'];
        $is_core_competency = isset($_POST['is_core_competency']) ? 1 : 0;
        $competency_category = $_POST['competency_category'];
        $competency_type = $_POST['competency_type'];
        $proficiency_levels = $_POST['proficiency_levels'];
        
        $sql = "UPDATE competency SET competency_name = ?, description = ?, is_core_competency = ?, competency_category = ?, competency_type = ?, proficiency_levels = ? WHERE competency_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssiissi", $competency_name, $description, $is_core_competency, $competency_category, $competency_type, $proficiency_levels, $competency_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete competency
    if (isset($_POST['delete_competency'])) {
        $competency_id = $_POST['competency_id'];
        
        $sql = "UPDATE competency SET is_active = 0 WHERE competency_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $competency_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    if (isset($_POST['add_assessment'])) {
        $fk_employee_id = $_POST['employee_id'];
        $fk_competency_id = $_POST['competency_id'];
        $assessment_type = $_POST['assessment_type'];
        $assessment_date = $_POST['assessment_date'];
        $proficiency_score = $_POST['proficiency_score'];
        $level_achieved = $_POST['level_achieved'];
        $assessment_method = $_POST['assessment_method'];
        $feedback = $_POST['feedback'];
        $recommendations = $_POST['recommendations'];
        $next_assessment_due = $_POST['next_assessment_due'];
        $status = $_POST['status'];
        
        $sql = "INSERT INTO competency_assessment (fk_employee_id, fk_competency_id, assessment_type, assessment_date, proficiency_score, level_achieved, assessment_method, feedback, recommendations, next_assessment_due, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissiisssss", $fk_employee_id, $fk_competency_id, $assessment_type, $assessment_date, $proficiency_score, $level_achieved, $assessment_method, $feedback, $recommendations, $next_assessment_due, $status);
        
        if ($stmt->execute()) {
            updateEmployeeCompetency($fk_employee_id, $fk_competency_id, $level_achieved, $proficiency_score);
            header("Location: Competencymanagement.php#assessment-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update assessment
    if (isset($_POST['update_assessment'])) {
        $assessment_id = $_POST['assessment_id'];
        $fk_employee_id = $_POST['employee_id'];
        $fk_competency_id = $_POST['competency_id'];
        $assessment_type = $_POST['assessment_type'];
        $assessment_date = $_POST['assessment_date'];
        $proficiency_score = $_POST['proficiency_score'];
        $level_achieved = $_POST['level_achieved'];
        $assessment_method = $_POST['assessment_method'];
        $feedback = $_POST['feedback'];
        $recommendations = $_POST['recommendations'];
        $next_assessment_due = $_POST['next_assessment_due'];
        $status = $_POST['status'];
        
        $sql = "UPDATE competency_assessment SET fk_employee_id = ?, fk_competency_id = ?, assessment_type = ?, assessment_date = ?, proficiency_score = ?, level_achieved = ?, assessment_method = ?, feedback = ?, recommendations = ?, next_assessment_due = ?, status = ? WHERE assessment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissiisssssi", $fk_employee_id, $fk_competency_id, $assessment_type, $assessment_date, $proficiency_score, $level_achieved, $assessment_method, $feedback, $recommendations, $next_assessment_due, $status, $assessment_id);
        
        if ($stmt->execute()) {
            updateEmployeeCompetency($fk_employee_id, $fk_competency_id, $level_achieved, $proficiency_score);
            header("Location: Competencymanagement.php#assessment-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete assessment
    if (isset($_POST['delete_assessment'])) {
        $assessment_id = $_POST['assessment_id'];
        
        $sql = "DELETE FROM competency_assessment WHERE assessment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $assessment_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#assessment-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    if (isset($_POST['add_employee_competency'])) {
        $fk_employee_id = $_POST['emp_employee_id'];
        $fk_competency_id = $_POST['emp_competency_id'];
        $current_level = $_POST['current_level'];
        $target_level = $_POST['target_level'];
        $development_priority = $_POST['development_priority'];
        $asses_by = $_POST['asses_by'];
        $last_assessed_date = $_POST['last_assessed_date'];
        
        $sql = "INSERT INTO employee_competency (fk_employee_id, fk_competency_id, current_level, target_level, development_priority, asses_by, last_assessed_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisss", $fk_employee_id, $fk_competency_id, $current_level, $target_level, $development_priority, $asses_by, $last_assessed_date);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#employee-competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update employee competency - FIXED
    if (isset($_POST['update_employee_competency'])) {
        $emp_competency_id = $_POST['emp_competency_id'];
        $fk_employee_id = $_POST['emp_employee_id'];
        $fk_competency_id = $_POST['emp_competency_id_edit'];
        $current_level = $_POST['current_level'];
        $target_level = $_POST['target_level'];
        $development_priority = $_POST['development_priority'];
        $asses_by = $_POST['asses_by'];
        $last_assessed_date = $_POST['last_assessed_date'];
        
        $sql = "UPDATE employee_competency SET fk_employee_id = ?, fk_competency_id = ?, current_level = ?, target_level = ?, development_priority = ?, asses_by = ?, last_assessed_date = ? WHERE emp_competency_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiisssi", $fk_employee_id, $fk_competency_id, $current_level, $target_level, $development_priority, $asses_by, $last_assessed_date, $emp_competency_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#employee-competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete employee competency
    if (isset($_POST['delete_employee_competency'])) {
        $emp_competency_id = $_POST['emp_competency_id'];
        
        $sql = "DELETE FROM employee_competency WHERE emp_competency_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $emp_competency_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#employee-competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    if (isset($_POST['add_position_competency'])) {
        $fk_position_id = $_POST['position_id'];
        $fk_competency_id = $_POST['pos_competency_id'];
        $required_level = $_POST['required_level'];
        $importance_weight = $_POST['importance_weight'];
        $is_mandatory = isset($_POST['is_mandatory']) ? 1 : 0;
        $minimum_score = $_POST['minimum_score'];
        
        $sql = "INSERT INTO position_competency (fk_position_id, fk_competency_id, required_level, importance_weight, is_mandatory, minimum_score) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidii", $fk_position_id, $fk_competency_id, $required_level, $importance_weight, $is_mandatory, $minimum_score);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#position-competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update position competency
    if (isset($_POST['update_position_competency'])) {
        $position_competency_id = $_POST['position_competency_id'];
        $fk_position_id = $_POST['position_id'];
        $fk_competency_id = $_POST['pos_competency_id'];
        $required_level = $_POST['required_level'];
        $importance_weight = $_POST['importance_weight'];
        $is_mandatory = isset($_POST['is_mandatory']) ? 1 : 0;
        $minimum_score = $_POST['minimum_score'];
        
        $sql = "UPDATE position_competency SET fk_position_id = ?, fk_competency_id = ?, required_level = ?, importance_weight = ?, is_mandatory = ?, minimum_score = ? WHERE position_competency_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiidiii", $fk_position_id, $fk_competency_id, $required_level, $importance_weight, $is_mandatory, $minimum_score, $position_competency_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#position-competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete position competency
    if (isset($_POST['delete_position_competency'])) {
        $position_competency_id = $_POST['position_competency_id'];
        
        $sql = "DELETE FROM position_competency WHERE position_competency_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $position_competency_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#position-competency-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    if (isset($_POST['add_competency_category'])) {
        $category_name = $_POST['category_name'];
        $description = $_POST['category_description'];
        $category_weight = $_POST['category_weight'];
        
        $sql = "INSERT INTO competency_category (category_name, description, category_weight) 
                VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssd", $category_name, $description, $category_weight);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#category-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update competency category
    if (isset($_POST['update_competency_category'])) {
        $category_id = $_POST['category_id'];
        $category_name = $_POST['category_name'];
        $description = $_POST['category_description'];
        $category_weight = $_POST['category_weight'];
        
        $sql = "UPDATE competency_category SET category_name = ?, description = ?, category_weight = ? WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdi", $category_name, $description, $category_weight, $category_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#category-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete competency category
    if (isset($_POST['delete_competency_category'])) {
        $category_id = $_POST['category_id'];
        
        $sql = "UPDATE competency_category SET is_active = 0 WHERE category_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $category_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#category-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    if (isset($_POST['add_development_plan'])) {
        $fk_employee_id = $_POST['dev_employee_id'];
        $fk_gap_id = $_POST['gap_id'];
        $plan_name = $_POST['plan_name'];
        $description = $_POST['dev_description'];
        $start_date = $_POST['start_date'];
        $target_completion_date = $_POST['target_completion_date'];
        $actual_completion_date = $_POST['actual_completion_date'];
        $development_methods = $_POST['development_methods'];
        $status = $_POST['dev_status'];
        $progress_status = $_POST['progress_status'];
        
        $sql = "INSERT INTO development_plan (fk_employee_id, fk_gap_id, plan_name, description, start_date, target_completion_date, actual_completion_date, development_methods, status, progress_status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssssss", $fk_employee_id, $fk_gap_id, $plan_name, $description, $start_date, $target_completion_date, $actual_completion_date, $development_methods, $status, $progress_status);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#development-plan-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update development plan
    if (isset($_POST['update_development_plan'])) {
        $plan_id = $_POST['plan_id'];
        $fk_employee_id = $_POST['dev_employee_id'];
        $fk_gap_id = $_POST['gap_id'];
        $plan_name = $_POST['plan_name'];
        $description = $_POST['dev_description'];
        $start_date = $_POST['start_date'];
        $target_completion_date = $_POST['target_completion_date'];
        $actual_completion_date = $_POST['actual_completion_date'];
        $development_methods = $_POST['development_methods'];
        $status = $_POST['dev_status'];
        $progress_status = $_POST['progress_status'];
        
        $sql = "UPDATE development_plan SET fk_employee_id = ?, fk_gap_id = ?, plan_name = ?, description = ?, start_date = ?, target_completion_date = ?, actual_completion_date = ?, development_methods = ?, status = ?, progress_status = ? WHERE dev_plan_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iissssssssi", $fk_employee_id, $fk_gap_id, $plan_name, $description, $start_date, $target_completion_date, $actual_completion_date, $development_methods, $status, $progress_status, $plan_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#development-plan-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete development plan
    if (isset($_POST['delete_development_plan'])) {
        $plan_id = $_POST['plan_id'];
        
        $sql = "DELETE FROM development_plan WHERE dev_plan_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $plan_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#development-plan-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Add competency gap
    if (isset($_POST['add_competency_gap'])) {
        $fk_employee_id = $_POST['gap_employee_id'];
        $fk_competency_id = $_POST['gap_competency_id'];
        $current_level = $_POST['current_level'];
        $required_level = $_POST['required_level'];
        $gap_score = $_POST['gap_score'];
        $priority_level = $_POST['priority_level'];
        $action_plan = $_POST['action_plan'];
        $target_compensation_date = $_POST['target_compensation_date'];
        $status = $_POST['gap_status'];
        
        $sql = "INSERT INTO competency_gap (fk_employee_id, fk_competency_id, current_level, required_level, gap_score, priority_level, action_plan, target_compensation_date, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiidssss", $fk_employee_id, $fk_competency_id, $current_level, $required_level, $gap_score, $priority_level, $action_plan, $target_compensation_date, $status);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#gap-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Update competency gap
    if (isset($_POST['update_competency_gap'])) {
        $gap_id = $_POST['gap_id'];
        $fk_employee_id = $_POST['gap_employee_id'];
        $fk_competency_id = $_POST['gap_competency_id'];
        $current_level = $_POST['current_level'];
        $required_level = $_POST['required_level'];
        $gap_score = $_POST['gap_score'];
        $priority_level = $_POST['priority_level'];
        $action_plan = $_POST['action_plan'];
        $target_compensation_date = $_POST['target_compensation_date'];
        $status = $_POST['gap_status'];
        
        $sql = "UPDATE competency_gap SET fk_employee_id = ?, fk_competency_id = ?, current_level = ?, required_level = ?, gap_score = ?, priority_level = ?, action_plan = ?, target_compensation_date = ?, status = ? WHERE gap_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiidssssi", $fk_employee_id, $fk_competency_id, $current_level, $required_level, $gap_score, $priority_level, $action_plan, $target_compensation_date, $status, $gap_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#gap-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
    // Delete competency gap
    if (isset($_POST['delete_competency_gap'])) {
        $gap_id = $_POST['gap_id'];
        
        $sql = "DELETE FROM competency_gap WHERE gap_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $gap_id);
        
        if ($stmt->execute()) {
            header("Location: Competencymanagement.php#gap-tab");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

function updateEmployeeCompetency($employee_id, $competency_id, $level_achieved, $proficiency_score) {
    global $conn;
    
    $current_level = $level_achieved;
    $target_level = $level_achieved;
    
    $check_sql = "SELECT * FROM employee_competency WHERE fk_employee_id = ? AND fk_competency_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    
    if ($check_stmt) {
        $check_stmt->bind_param("ii", $employee_id, $competency_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            $update_sql = "UPDATE employee_competency SET current_level = ?, target_level = ?, last_assessed_date = CURDATE() 
                          WHERE fk_employee_id = ? AND fk_competency_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            if ($update_stmt) {
                $update_stmt->bind_param("iiii", $current_level, $target_level, $employee_id, $competency_id);
                $update_stmt->execute();
                $update_stmt->close();
            }
        } else {
            $insert_sql = "INSERT INTO employee_competency (fk_employee_id, fk_competency_id, current_level, target_level, development_priority, asses_by, last_assessed_date) 
                          VALUES (?, ?, ?, ?, 'Medium', 'System', CURDATE())";
            $insert_stmt = $conn->prepare($insert_sql);
            if ($insert_stmt) {
                $insert_stmt->bind_param("iiii", $employee_id, $competency_id, $current_level, $target_level);
                $insert_stmt->execute();
                $insert_stmt->close();
            }
        }
        $check_stmt->close();
    }
}

// Fetch data
$employees = $conn->query("SELECT * FROM employee WHERE employment_status = 'Active'");
$competencies = $conn->query("SELECT * FROM competency WHERE is_active = 1");
$positions = $conn->query("SELECT * FROM position");
$categories = $conn->query("SELECT * FROM competency_category WHERE is_active = 1");
$gaps = $conn->query("SELECT * FROM competency_gap");

$assessments = $conn->query("
    SELECT ca.*, e.first_name, e.last_name, c.competency_name 
    FROM competency_assessment ca 
    JOIN employee e ON ca.fk_employee_id = e.employee_id 
    JOIN competency c ON ca.fk_competency_id = c.competency_id 
    ORDER BY ca.assessment_date DESC
");

$employee_competencies = $conn->query("
    SELECT ec.*, e.first_name, e.last_name, c.competency_name 
    FROM employee_competency ec 
    JOIN employee e ON ec.fk_employee_id = e.employee_id 
    JOIN competency c ON ec.fk_competency_id = c.competency_id 
    ORDER BY ec.last_assessed_date DESC
");

$position_competencies = $conn->query("
    SELECT pc.*, p.title as position_title, c.competency_name 
    FROM position_competency pc 
    JOIN position p ON pc.fk_position_id = p.position_id 
    JOIN competency c ON pc.fk_competency_id = c.competency_id 
    ORDER BY pc.created_date DESC
");

$development_plans = $conn->query("
    SELECT dp.*, e.first_name, e.last_name, cg.fk_competency_id 
    FROM development_plan dp 
    JOIN employee e ON dp.fk_employee_id = e.employee_id 
    LEFT JOIN competency_gap cg ON dp.fk_gap_id = cg.gap_id 
    ORDER BY dp.created_date DESC
");

// Fetch competency gaps
$competency_gaps = $conn->query("
    SELECT cg.*, e.first_name, e.last_name, c.competency_name 
    FROM competency_gap cg 
    JOIN employee e ON cg.fk_employee_id = e.employee_id 
    JOIN competency c ON cg.fk_competency_id = c.competency_id 
    ORDER BY cg.priority_level DESC, cg.gap_score DESC
");

// Get competency details for editing
$edit_competency = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_stmt = $conn->prepare("SELECT * FROM competency WHERE competency_id = ?");
    $edit_stmt->bind_param("i", $edit_id);
    $edit_stmt->execute();
    $edit_result = $edit_stmt->get_result();
    $edit_competency = $edit_result->fetch_assoc();
}

// Get competency details for viewing
$view_competency = null;
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];
    $view_stmt = $conn->prepare("SELECT c.*, cc.category_name FROM competency c LEFT JOIN competency_category cc ON c.competency_category = cc.category_id WHERE c.competency_id = ?");
    $view_stmt->bind_param("i", $view_id);
    $view_stmt->execute();
    $view_result = $view_stmt->get_result();
    $view_competency = $view_result->fetch_assoc();
}

// Get assessment details for editing
$edit_assessment = null;
if (isset($_GET['edit_assessment_id'])) {
    $edit_assessment_id = $_GET['edit_assessment_id'];
    $edit_assessment_stmt = $conn->prepare("SELECT * FROM competency_assessment WHERE assessment_id = ?");
    $edit_assessment_stmt->bind_param("i", $edit_assessment_id);
    $edit_assessment_stmt->execute();
    $edit_assessment_result = $edit_assessment_stmt->get_result();
    $edit_assessment = $edit_assessment_result->fetch_assoc();
}

// Get assessment details for viewing
$view_assessment = null;
if (isset($_GET['view_assessment_id'])) {
    $view_assessment_id = $_GET['view_assessment_id'];
    $view_assessment_stmt = $conn->prepare("
        SELECT ca.*, e.first_name, e.last_name, c.competency_name 
        FROM competency_assessment ca 
        JOIN employee e ON ca.fk_employee_id = e.employee_id 
        JOIN competency c ON ca.fk_competency_id = c.competency_id 
        WHERE ca.assessment_id = ?
    ");
    $view_assessment_stmt->bind_param("i", $view_assessment_id);
    $view_assessment_stmt->execute();
    $view_assessment_result = $view_assessment_stmt->get_result();
    $view_assessment = $view_assessment_result->fetch_assoc();
}

// Get employee competency details for editing
$edit_employee_competency = null;
if (isset($_GET['edit_emp_competency_id'])) {
    $edit_emp_competency_id = $_GET['edit_emp_competency_id'];
    $edit_employee_competency_stmt = $conn->prepare("SELECT * FROM employee_competency WHERE emp_competency_id = ?");
    $edit_employee_competency_stmt->bind_param("i", $edit_emp_competency_id);
    $edit_employee_competency_stmt->execute();
    $edit_employee_competency_result = $edit_employee_competency_stmt->get_result();
    $edit_employee_competency = $edit_employee_competency_result->fetch_assoc();
}

// Get employee competency details for viewing
$view_employee_competency = null;
if (isset($_GET['view_emp_competency_id'])) {
    $view_emp_competency_id = $_GET['view_emp_competency_id'];
    $view_employee_competency_stmt = $conn->prepare("
        SELECT ec.*, e.first_name, e.last_name, c.competency_name 
        FROM employee_competency ec 
        JOIN employee e ON ec.fk_employee_id = e.employee_id 
        JOIN competency c ON ec.fk_competency_id = c.competency_id 
        WHERE ec.emp_competency_id = ?
    ");
    $view_employee_competency_stmt->bind_param("i", $view_emp_competency_id);
    $view_employee_competency_stmt->execute();
    $view_employee_competency_result = $view_employee_competency_stmt->get_result();
    $view_employee_competency = $view_employee_competency_result->fetch_assoc();
}

// Get position competency details for editing
$edit_position_competency = null;
if (isset($_GET['edit_position_competency_id'])) {
    $edit_position_competency_id = $_GET['edit_position_competency_id'];
    $edit_position_competency_stmt = $conn->prepare("SELECT * FROM position_competency WHERE position_competency_id = ?");
    $edit_position_competency_stmt->bind_param("i", $edit_position_competency_id);
    $edit_position_competency_stmt->execute();
    $edit_position_competency_result = $edit_position_competency_stmt->get_result();
    $edit_position_competency = $edit_position_competency_result->fetch_assoc();
}

// Get position competency details for viewing
$view_position_competency = null;
if (isset($_GET['view_position_competency_id'])) {
    $view_position_competency_id = $_GET['view_position_competency_id'];
    $view_position_competency_stmt = $conn->prepare("
        SELECT pc.*, p.title as position_title, c.competency_name 
        FROM position_competency pc 
        JOIN position p ON pc.fk_position_id = p.position_id 
        JOIN competency c ON pc.fk_competency_id = c.competency_id 
        WHERE pc.position_competency_id = ?
    ");
    $view_position_competency_stmt->bind_param("i", $view_position_competency_id);
    $view_position_competency_stmt->execute();
    $view_position_competency_result = $view_position_competency_stmt->get_result();
    $view_position_competency = $view_position_competency_result->fetch_assoc();
}

// Get competency category details for editing
$edit_category = null;
if (isset($_GET['edit_category_id'])) {
    $edit_category_id = $_GET['edit_category_id'];
    $edit_category_stmt = $conn->prepare("SELECT * FROM competency_category WHERE category_id = ?");
    $edit_category_stmt->bind_param("i", $edit_category_id);
    $edit_category_stmt->execute();
    $edit_category_result = $edit_category_stmt->get_result();
    $edit_category = $edit_category_result->fetch_assoc();
}

// Get competency category details for viewing
$view_category = null;
if (isset($_GET['view_category_id'])) {
    $view_category_id = $_GET['view_category_id'];
    $view_category_stmt = $conn->prepare("SELECT * FROM competency_category WHERE category_id = ?");
    $view_category_stmt->bind_param("i", $view_category_id);
    $view_category_stmt->execute();
    $view_category_result = $view_category_stmt->get_result();
    $view_category = $view_category_result->fetch_assoc();
}

// Get competency gap details for editing
$edit_gap = null;
if (isset($_GET['edit_gap_id'])) {
    $edit_gap_id = $_GET['edit_gap_id'];
    $edit_gap_stmt = $conn->prepare("SELECT * FROM competency_gap WHERE gap_id = ?");
    $edit_gap_stmt->bind_param("i", $edit_gap_id);
    $edit_gap_stmt->execute();
    $edit_gap_result = $edit_gap_stmt->get_result();
    $edit_gap = $edit_gap_result->fetch_assoc();
}

// Get competency gap details for viewing
$view_gap = null;
if (isset($_GET['view_gap_id'])) {
    $view_gap_id = $_GET['view_gap_id'];
    $view_gap_stmt = $conn->prepare("
        SELECT cg.*, e.first_name, e.last_name, c.competency_name 
        FROM competency_gap cg 
        JOIN employee e ON cg.fk_employee_id = e.employee_id 
        JOIN competency c ON cg.fk_competency_id = c.competency_id 
        WHERE cg.gap_id = ?
    ");
    $view_gap_stmt->bind_param("i", $view_gap_id);
    $view_gap_stmt->execute();
    $view_gap_result = $view_gap_stmt->get_result();
    $view_gap = $view_gap_result->fetch_assoc();
}

// Get development plan details for editing
$edit_development_plan = null;
if (isset($_GET['edit_development_plan_id'])) {
    $edit_development_plan_id = $_GET['edit_development_plan_id'];
    $edit_development_plan_stmt = $conn->prepare("SELECT * FROM development_plan WHERE dev_plan_id = ?");
    $edit_development_plan_stmt->bind_param("i", $edit_development_plan_id);
    $edit_development_plan_stmt->execute();
    $edit_development_plan_result = $edit_development_plan_stmt->get_result();
    $edit_development_plan = $edit_development_plan_result->fetch_assoc();
}

// Get development plan details for viewing
$view_development_plan = null;
if (isset($_GET['view_development_plan_id'])) {
    $view_development_plan_id = $_GET['view_development_plan_id'];
    $view_development_plan_stmt = $conn->prepare("
        SELECT dp.*, e.first_name, e.last_name, cg.fk_competency_id 
        FROM development_plan dp 
        JOIN employee e ON dp.fk_employee_id = e.employee_id 
        LEFT JOIN competency_gap cg ON dp.fk_gap_id = cg.gap_id 
        WHERE dp.dev_plan_id = ?
    ");
    $view_development_plan_stmt->bind_param("i", $view_development_plan_id);
    $view_development_plan_stmt->execute();
    $view_development_plan_result = $view_development_plan_stmt->get_result();
    $view_development_plan = $view_development_plan_result->fetch_assoc();
}

// Simple proficiency levels options
$proficiency_options = [
    "Basic",
    "Intermediate", 
    "Advanced",
    "Expert",
    "Beginner",
    "Proficient",
    "Master"
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Competency Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.select2').select2();
            
            // Check URL hash and activate corresponding tab
            var hash = window.location.hash;
            if (hash) {
                $('.nav-tab').removeClass('text-blue-600 border-blue-600').addClass('text-gray-600 border-transparent');
                $('.tab-content').addClass('hidden');
                
                var tabId = hash.substring(1); // Remove # from hash
                $('[data-tab="' + tabId + '"]').removeClass('text-gray-600 border-transparent').addClass('text-blue-600 border-blue-600');
                $('#' + tabId).removeClass('hidden');
            } else {
                // Initialize first tab as active
                $('.nav-tab').first().addClass('text-blue-600 border-blue-600');
                $('.nav-tab').first().removeClass('text-gray-600 border-transparent');
                $('.tab-content').first().removeClass('hidden');
            }
            
            $('.nav-tab').click(function() {
                var tabId = $(this).data('tab');
                
                // Remove active classes from all tabs
                $('.nav-tab').removeClass('text-blue-600 border-blue-600');
                $('.nav-tab').addClass('text-gray-600 border-transparent');
                
                // Add active classes to clicked tab
                $(this).removeClass('text-gray-600 border-transparent');
                $(this).addClass('text-blue-600 border-blue-600');
                
                // Hide all tab content
                $('.tab-content').addClass('hidden');
                // Show selected tab content
                $('#' + tabId).removeClass('hidden');
                
                // Update URL hash
                window.location.hash = tabId;
            });

            // Search functionality for competencies
            $('#searchCompetency').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#competencyTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Search functionality for assessments
            $('#searchAssessment').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#assessmentTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Search functionality for employee competencies
            $('#searchEmployeeCompetency').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#employeeCompetencyTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Search functionality for position competencies
            $('#searchPositionCompetency').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#positionCompetencyTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Search functionality for categories
            $('#searchCategory').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#categoryTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Search functionality for competency gaps
            $('#searchGap').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#gapTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // Search functionality for development plans
            $('#searchDevelopmentPlan').on('input', function() {
                var searchText = $(this).val().toLowerCase();
                $('#developmentPlanTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });

            // REMOVED AUTO GAP SCORE CALCULATION - MANUAL INPUT ONLY
        });
        
        function confirmDelete(message) {
            return confirm(message);
        }
        
        function openEditModal(competencyId) {
            window.location.href = 'Competencymanagement.php?edit_id=' + competencyId + '#competency-tab';
        }
        
        function openViewModal(competencyId) {
            window.location.href = 'Competencymanagement.php?view_id=' + competencyId + '#competency-tab';
        }
        
        function openAssessmentViewModal(assessmentId) {
            window.location.href = 'Competencymanagement.php?view_assessment_id=' + assessmentId + '#assessment-tab';
        }

        function openAssessmentEditModal(assessmentId) {
            window.location.href = 'Competencymanagement.php?edit_assessment_id=' + assessmentId + '#assessment-tab';
        }

        function openEmployeeCompetencyViewModal(empCompetencyId) {
            window.location.href = 'Competencymanagement.php?view_emp_competency_id=' + empCompetencyId + '#employee-competency-tab';
        }

        function openEmployeeCompetencyEditModal(empCompetencyId) {
            window.location.href = 'Competencymanagement.php?edit_emp_competency_id=' + empCompetencyId + '#employee-competency-tab';
        }

        function openPositionCompetencyViewModal(positionCompetencyId) {
            window.location.href = 'Competencymanagement.php?view_position_competency_id=' + positionCompetencyId + '#position-competency-tab';
        }

        function openPositionCompetencyEditModal(positionCompetencyId) {
            window.location.href = 'Competencymanagement.php?edit_position_competency_id=' + positionCompetencyId + '#position-competency-tab';
        }

        function openCategoryViewModal(categoryId) {
            window.location.href = 'Competencymanagement.php?view_category_id=' + categoryId + '#category-tab';
        }

        function openCategoryEditModal(categoryId) {
            window.location.href = 'Competencymanagement.php?edit_category_id=' + categoryId + '#category-tab';
        }

        function openGapViewModal(gapId) {
            window.location.href = 'Competencymanagement.php?view_gap_id=' + gapId + '#gap-tab';
        }

        function openGapEditModal(gapId) {
            window.location.href = 'Competencymanagement.php?edit_gap_id=' + gapId + '#gap-tab';
        }

        function openDevelopmentPlanViewModal(planId) {
            window.location.href = 'Competencymanagement.php?view_development_plan_id=' + planId + '#development-plan-tab';
        }

        function openDevelopmentPlanEditModal(planId) {
            window.location.href = 'Competencymanagement.php?edit_development_plan_id=' + planId + '#development-plan-tab';
        }
        
        function closeModal() {
            // Redirect back to the current tab
            var currentTab = window.location.hash;
            if (!currentTab) {
                currentTab = '#competency-tab';
            }
            window.location.href = 'Competencymanagement.php' + currentTab;
        }

        function clearSearch(tableId) {
            if (tableId === 'competency') {
                $('#searchCompetency').val('');
                $('#competencyTable tbody tr').show();
            } else if (tableId === 'assessment') {
                $('#searchAssessment').val('');
                $('#assessmentTable tbody tr').show();
            } else if (tableId === 'employee-competency') {
                $('#searchEmployeeCompetency').val('');
                $('#employeeCompetencyTable tbody tr').show();
            } else if (tableId === 'position-competency') {
                $('#searchPositionCompetency').val('');
                $('#positionCompetencyTable tbody tr').show();
            } else if (tableId === 'category') {
                $('#searchCategory').val('');
                $('#categoryTable tbody tr').show();
            } else if (tableId === 'gap') {
                $('#searchGap').val('');
                $('#gapTable tbody tr').show();
            } else if (tableId === 'development-plan') {
                $('#searchDevelopmentPlan').val('');
                $('#developmentPlanTable tbody tr').show();
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col">
        <!-- Top Navigation -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-6 py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold text-gray-800">Competency Management</h1>
                </div>
            </div>

            <!-- Sub Navigation Tabs -->
            <div class="bg-white border-b">
                <div class="px-6">
                    <div class="flex space-x-8">
                        <button class="nav-tab px-1 py-3 text-blue-600 border-b-2 border-blue-600 font-medium transition-colors duration-200" data-tab="competency-tab">
                            Competencies
                        </button>
                        <button class="nav-tab px-1 py-3 text-gray-600 border-b-2 border-transparent font-medium hover:text-blue-600 transition-colors duration-200" data-tab="assessment-tab">
                            Assessments
                        </button>
                        <button class="nav-tab px-1 py-3 text-gray-600 border-b-2 border-transparent font-medium hover:text-blue-600 transition-colors duration-200" data-tab="employee-competency-tab">
                            Employee Competencies
                        </button>
                        <button class="nav-tab px-1 py-3 text-gray-600 border-b-2 border-transparent font-medium hover:text-blue-600 transition-colors duration-200" data-tab="position-competency-tab">
                            Position Competencies
                        </button>
                        <button class="nav-tab px-1 py-3 text-gray-600 border-b-2 border-transparent font-medium hover:text-blue-600 transition-colors duration-200" data-tab="category-tab">
                            Categories
                        </button>
                        <button class="nav-tab px-1 py-3 text-gray-600 border-b-2 border-transparent font-medium hover:text-blue-600 transition-colors duration-200" data-tab="gap-tab">
                            Competency Gaps
                        </button>
                        <button class="nav-tab px-1 py-3 text-gray-600 border-b-2 border-transparent font-medium hover:text-blue-600 transition-colors duration-200" data-tab="development-plan-tab">
                            Development Plans
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-auto p-6">
            <!-- Competency Tab -->
            <div id="competency-tab" class="tab-content">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_competency ? 'Edit Competency' : 'Add New Competency'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_competency): ?>
                            <input type="hidden" name="competency_id" value="<?php echo $edit_competency['competency_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competency Name</label>
                            <input type="text" name="competency_name" required 
                                   value="<?php echo $edit_competency ? $edit_competency['competency_name'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $edit_competency ? $edit_competency['description'] : ''; ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category</label>
                            <select name="competency_category" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Category</option>
                                <?php 
                                $categories_options = $conn->query("SELECT * FROM competency_category WHERE is_active = 1");
                                while($cat = $categories_options->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['category_id']; ?>" 
                                        <?php echo ($edit_competency && $edit_competency['competency_category'] == $cat['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['category_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Type</label>
                            <select name="competency_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Hard Skill" <?php echo ($edit_competency && $edit_competency['competency_type'] == 'Hard Skill') ? 'selected' : ''; ?>>Hard Skill</option>
                                <option value="Soft Skill" <?php echo ($edit_competency && $edit_competency['competency_type'] == 'Soft Skill') ? 'selected' : ''; ?>>Soft Skill</option>
                                <option value="Technical" <?php echo ($edit_competency && $edit_competency['competency_type'] == 'Technical') ? 'selected' : ''; ?>>Technical</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Proficiency Level</label>
                            <select name="proficiency_levels" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Proficiency Level</option>
                                <?php foreach($proficiency_options as $option): ?>
                                    <option value="<?php echo $option; ?>" 
                                        <?php echo ($edit_competency && $edit_competency['proficiency_levels'] == $option) ? 'selected' : ''; ?>>
                                        <?php echo $option; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_core_competency" 
                                   <?php echo ($edit_competency && $edit_competency['is_core_competency'] == 1) ? 'checked' : ''; ?>
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label class="ml-2 text-sm font-medium text-gray-700">Core Competency</label>
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_competency): ?>
                                <button type="submit" name="update_competency" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Competency
                                </button>
                                <a href="Competencymanagement.php#competency-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_competency" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Competency
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Competency List</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchCompetency" placeholder="Search competencies..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('competency')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="competencyTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proficiency Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Core</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $competencies_list = $conn->query("SELECT * FROM competency WHERE is_active = 1");
                                while($comp = $competencies_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $comp['competency_name']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate" title="<?php echo $comp['description']; ?>">
                                            <?php echo $comp['description']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $comp['competency_type']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $comp['proficiency_levels']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $comp['is_core_competency'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $comp['is_core_competency'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_id=<?php echo $comp['competency_id']; ?>#competency-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_id=<?php echo $comp['competency_id']; ?>#competency-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this competency?')">
                                            <input type="hidden" name="competency_id" value="<?php echo $comp['competency_id']; ?>">
                                            <button type="submit" name="delete_competency" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $competencies_list->num_rows; ?> competencies
                    </div>
                </div>
            </div>

            <!-- View Competency Modal -->
            <?php if ($view_competency): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Competency</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Competency Name</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_competency['competency_name']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_competency['description']; ?></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Category</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_competency['category_name']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Type</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_competency['competency_type']; ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Proficiency Level</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?php echo $view_competency['proficiency_levels']; ?>
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Core Competency</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $view_competency['is_core_competency'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                        <?php echo $view_competency['is_core_competency'] ? 'Yes' : 'No'; ?>
                                    </span>
                                </p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created By</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_competency['created_by']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_competency['created_date']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Assessment Tab -->
            <div id="assessment-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_assessment ? 'Edit Assessment' : 'Add New Assessment'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_assessment): ?>
                            <input type="hidden" name="assessment_id" value="<?php echo $edit_assessment['assessment_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Employee</option>
                                <?php 
                                $employees_assess = $conn->query("SELECT * FROM employee WHERE employment_status = 'Active'");
                                while($emp = $employees_assess->fetch_assoc()): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>" <?php echo ($edit_assessment && $edit_assessment['fk_employee_id'] == $emp['employee_id']) ? 'selected' : ''; ?>>
                                        <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competency</label>
                            <select name="competency_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Competency</option>
                                <?php 
                                $competencies2 = $conn->query("SELECT * FROM competency WHERE is_active = 1");
                                while($comp = $competencies2->fetch_assoc()): ?>
                                    <option value="<?php echo $comp['competency_id']; ?>" <?php echo ($edit_assessment && $edit_assessment['fk_competency_id'] == $comp['competency_id']) ? 'selected' : ''; ?>><?php echo $comp['competency_name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assessment Type</label>
                            <select name="assessment_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Performance Review" <?php echo ($edit_assessment && $edit_assessment['assessment_type'] == 'Performance Review') ? 'selected' : ''; ?>>Performance Review</option>
                                <option value="Self Assessment" <?php echo ($edit_assessment && $edit_assessment['assessment_type'] == 'Self Assessment') ? 'selected' : ''; ?>>Self Assessment</option>
                                <option value="Manager Assessment" <?php echo ($edit_assessment && $edit_assessment['assessment_type'] == 'Manager Assessment') ? 'selected' : ''; ?>>Manager Assessment</option>
                                <option value="Peer Review" <?php echo ($edit_assessment && $edit_assessment['assessment_type'] == 'Peer Review') ? 'selected' : ''; ?>>Peer Review</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assessment Date</label>
                            <input type="date" name="assessment_date" required value="<?php echo $edit_assessment ? $edit_assessment['assessment_date'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Proficiency Score (1-5)</label>
                            <input type="number" name="proficiency_score" min="1" max="5" required value="<?php echo $edit_assessment ? $edit_assessment['proficiency_score'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Level Achieved (1-5)</label>
                            <input type="number" name="level_achieved" min="1" max="5" required value="<?php echo $edit_assessment ? $edit_assessment['level_achieved'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assessment Method</label>
                            <input type="text" name="assessment_method" required value="<?php echo $edit_assessment ? $edit_assessment['assessment_method'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Active" <?php echo ($edit_assessment && $edit_assessment['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Completed" <?php echo ($edit_assessment && $edit_assessment['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Pending" <?php echo ($edit_assessment && $edit_assessment['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Feedback</label>
                            <textarea name="feedback" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $edit_assessment ? $edit_assessment['feedback'] : ''; ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Recommendations</label>
                            <textarea name="recommendations" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $edit_assessment ? $edit_assessment['recommendations'] : ''; ?></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Next Assessment Due</label>
                            <input type="date" name="next_assessment_due" value="<?php echo $edit_assessment ? $edit_assessment['next_assessment_due'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_assessment): ?>
                                <button type="submit" name="update_assessment" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Assessment
                                </button>
                                <a href="Competencymanagement.php#assessment-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_assessment" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Assessment
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Assessment History</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchAssessment" placeholder="Search assessments..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('assessment')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="assessmentTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Next Due</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $assessments_list = $conn->query("
                                    SELECT ca.*, e.first_name, e.last_name, c.competency_name 
                                    FROM competency_assessment ca 
                                    JOIN employee e ON ca.fk_employee_id = e.employee_id 
                                    JOIN competency c ON ca.fk_competency_id = c.competency_id 
                                    ORDER BY ca.assessment_date DESC
                                ");
                                while($assess = $assessments_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $assess['first_name'] . ' ' . $assess['last_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $assess['competency_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $assess['assessment_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($assess['proficiency_score'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($assess['proficiency_score'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $assess['proficiency_score']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($assess['level_achieved'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($assess['level_achieved'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $assess['level_achieved']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $assess['assessment_method']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $assess['status'] == 'Active' ? 'bg-green-100 text-green-800' : ($assess['status'] == 'Completed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                            <?php echo $assess['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $assess['next_assessment_due']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_assessment_id=<?php echo $assess['assessment_id']; ?>#assessment-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_assessment_id=<?php echo $assess['assessment_id']; ?>#assessment-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this assessment?')">
                                            <input type="hidden" name="assessment_id" value="<?php echo $assess['assessment_id']; ?>">
                                            <button type="submit" name="delete_assessment" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $assessments_list->num_rows; ?> assessments
                    </div>
                </div>
            </div>

            <!-- View Assessment Modal -->
            <?php if ($view_assessment): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Assessment</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['first_name'] . ' ' . $view_assessment['last_name']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Competency</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['competency_name']; ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Assessment Type</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['assessment_type']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Assessment Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['assessment_date']; ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Proficiency Score</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($view_assessment['proficiency_score'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($view_assessment['proficiency_score'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $view_assessment['proficiency_score']; ?>/5
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Level Achieved</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($view_assessment['level_achieved'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($view_assessment['level_achieved'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $view_assessment['level_achieved']; ?>/5
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assessment Method</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['assessment_method']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Feedback</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['feedback'] ?: 'No feedback provided'; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Recommendations</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['recommendations'] ?: 'No recommendations provided'; ?></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Next Assessment Due</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_assessment['next_assessment_due'] ?: 'Not set'; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $view_assessment['status'] == 'Active' ? 'bg-green-100 text-green-800' : ($view_assessment['status'] == 'Completed' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800'); ?>">
                                            <?php echo $view_assessment['status']; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Employee Competency Tab -->
            <div id="employee-competency-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_employee_competency ? 'Edit Employee Competency' : 'Add Employee Competency'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_employee_competency): ?>
                            <input type="hidden" name="emp_competency_id" value="<?php echo $edit_employee_competency['emp_competency_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="emp_employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Employee</option>
                                <?php 
                                $employees3 = $conn->query("SELECT * FROM employee WHERE employment_status = 'Active'");
                                while($emp = $employees3->fetch_assoc()): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>" <?php echo ($edit_employee_competency && $edit_employee_competency['fk_employee_id'] == $emp['employee_id']) ? 'selected' : ''; ?>>
                                        <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competency</label>
                            <?php if ($edit_employee_competency): ?>
                                <!-- For editing - use emp_competency_id_edit -->
                                <select name="emp_competency_id_edit" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                    <option value="">Select Competency</option>
                                    <?php 
                                    $competencies3 = $conn->query("SELECT * FROM competency WHERE is_active = 1");
                                    while($comp = $competencies3->fetch_assoc()): ?>
                                        <option value="<?php echo $comp['competency_id']; ?>" <?php echo ($edit_employee_competency && $edit_employee_competency['fk_competency_id'] == $comp['competency_id']) ? 'selected' : ''; ?>><?php echo $comp['competency_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php else: ?>
                                <!-- For adding - use emp_competency_id -->
                                <select name="emp_competency_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                    <option value="">Select Competency</option>
                                    <?php 
                                    $competencies3 = $conn->query("SELECT * FROM competency WHERE is_active = 1");
                                    while($comp = $competencies3->fetch_assoc()): ?>
                                        <option value="<?php echo $comp['competency_id']; ?>"><?php echo $comp['competency_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Level (1-5)</label>
                            <input type="number" name="current_level" min="1" max="5" required value="<?php echo $edit_employee_competency ? $edit_employee_competency['current_level'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Level (1-5)</label>
                            <input type="number" name="target_level" min="1" max="5" required value="<?php echo $edit_employee_competency ? $edit_employee_competency['target_level'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Development Priority</label>
                            <select name="development_priority" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Low" <?php echo ($edit_employee_competency && $edit_employee_competency['development_priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                                <option value="Medium" <?php echo ($edit_employee_competency && $edit_employee_competency['development_priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="High" <?php echo ($edit_employee_competency && $edit_employee_competency['development_priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Assessed By</label>
                            <input type="text" name="asses_by" required value="<?php echo $edit_employee_competency ? $edit_employee_competency['asses_by'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Last Assessed Date</label>
                            <input type="date" name="last_assessed_date" required value="<?php echo $edit_employee_competency ? $edit_employee_competency['last_assessed_date'] : ''; ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_employee_competency): ?>
                                <button type="submit" name="update_employee_competency" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Employee Competency
                                </button>
                                <a href="Competencymanagement.php#employee-competency-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_employee_competency" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Employee Competency
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Employee Competencies</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchEmployeeCompetency" placeholder="Search employee competencies..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('employee-competency')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="employeeCompetencyTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assessed By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Assessed</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $employee_competencies_list = $conn->query("
                                    SELECT ec.*, e.first_name, e.last_name, c.competency_name 
                                    FROM employee_competency ec 
                                    JOIN employee e ON ec.fk_employee_id = e.employee_id 
                                    JOIN competency c ON ec.fk_competency_id = c.competency_id 
                                    ORDER BY ec.last_assessed_date DESC
                                ");
                                while($ec = $employee_competencies_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $ec['first_name'] . ' ' . $ec['last_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $ec['competency_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php 
                                            if($ec['current_level'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($ec['current_level'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $ec['current_level']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo $ec['target_level']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php 
                                            if($ec['development_priority'] == 'High') echo 'bg-red-100 text-red-800';
                                            elseif($ec['development_priority'] == 'Medium') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo $ec['development_priority']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $ec['asses_by']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $ec['last_assessed_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_emp_competency_id=<?php echo $ec['emp_competency_id']; ?>#employee-competency-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_emp_competency_id=<?php echo $ec['emp_competency_id']; ?>#employee-competency-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this employee competency?')">
                                            <input type="hidden" name="emp_competency_id" value="<?php echo $ec['emp_competency_id']; ?>">
                                            <button type="submit" name="delete_employee_competency" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $employee_competencies_list->num_rows; ?> employee competencies
                    </div>
                </div>
            </div> 

            <!-- View Employee Competency Modal -->
            <?php if ($view_employee_competency): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Employee Competency</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_employee_competency['first_name'] . ' ' . $view_employee_competency['last_name']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Competency</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_employee_competency['competency_name']; ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Current Level</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php 
                                            if($view_employee_competency['current_level'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($view_employee_competency['current_level'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $view_employee_competency['current_level']; ?>/5
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Level</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <?php echo $view_employee_competency['target_level']; ?>/5
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Development Priority</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php 
                                            if($view_employee_competency['development_priority'] == 'High') echo 'bg-red-100 text-red-800';
                                            elseif($view_employee_competency['development_priority'] == 'Medium') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo $view_employee_competency['development_priority']; ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Assessed By</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_employee_competency['asses_by']; ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Last Assessed Date</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_employee_competency['last_assessed_date']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Position Competency Tab -->
            <div id="position-competency-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_position_competency ? 'Edit Position Competency' : 'Add Position Competency'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_position_competency): ?>
                            <input type="hidden" name="position_competency_id" value="<?php echo $edit_position_competency['position_competency_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Position</label>
                            <select name="position_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Position</option>
                                <?php 
                                $positions2 = $conn->query("SELECT * FROM position");
                                while($pos = $positions2->fetch_assoc()): ?>
                                    <option value="<?php echo $pos['position_id']; ?>" 
                                        <?php echo ($edit_position_competency && $edit_position_competency['fk_position_id'] == $pos['position_id']) ? 'selected' : ''; ?>>
                                        <?php echo $pos['title']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competency</label>
                            <select name="pos_competency_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Competency</option>
                                <?php 
                                $competencies4 = $conn->query("SELECT * FROM competency WHERE is_active = 1");
                                while($comp = $competencies4->fetch_assoc()): ?>
                                    <option value="<?php echo $comp['competency_id']; ?>" 
                                        <?php echo ($edit_position_competency && $edit_position_competency['fk_competency_id'] == $comp['competency_id']) ? 'selected' : ''; ?>>
                                        <?php echo $comp['competency_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Required Level (1-5)</label>
                            <input type="number" name="required_level" min="1" max="5" required 
                                   value="<?php echo $edit_position_competency ? $edit_position_competency['required_level'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Importance Weight (0-100)</label>
                            <input type="number" name="importance_weight" min="0" max="100" required 
                                   value="<?php echo $edit_position_competency ? $edit_position_competency['importance_weight'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Minimum Score (1-5)</label>
                            <input type="number" name="minimum_score" min="1" max="5" required 
                                   value="<?php echo $edit_position_competency ? $edit_position_competency['minimum_score'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="flex items-center">
                            <input type="checkbox" name="is_mandatory" id="is_mandatory" 
                                   <?php echo ($edit_position_competency && $edit_position_competency['is_mandatory'] == 1) ? 'checked' : ''; ?>
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label for="is_mandatory" class="ml-2 text-sm font-medium text-gray-700">Mandatory Competency</label>
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_position_competency): ?>
                                <button type="submit" name="update_position_competency" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Position Competency
                                </button>
                                <a href="Competencymanagement.php#position-competency-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_position_competency" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Position Competency
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Position Competencies</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchPositionCompetency" placeholder="Search position competencies..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('position-competency')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="positionCompetencyTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mandatory</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $position_competencies_list = $conn->query("
                                    SELECT pc.*, p.title as position_title, c.competency_name 
                                    FROM position_competency pc 
                                    JOIN position p ON pc.fk_position_id = p.position_id 
                                    JOIN competency c ON pc.fk_competency_id = c.competency_id 
                                    ORDER BY pc.created_date DESC
                                ");
                                while($pc = $position_competencies_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $pc['position_title']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $pc['competency_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $pc['required_level']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $pc['importance_weight']; ?>%</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $pc['is_mandatory'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $pc['is_mandatory'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <?php echo $pc['minimum_score']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_position_competency_id=<?php echo $pc['position_competency_id']; ?>#position-competency-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_position_competency_id=<?php echo $pc['position_competency_id']; ?>#position-competency-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this position competency?')">
                                            <input type="hidden" name="position_competency_id" value="<?php echo $pc['position_competency_id']; ?>">
                                            <button type="submit" name="delete_position_competency" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $position_competencies_list->num_rows; ?> position competencies
                    </div>
                </div>
            </div>

            <!-- View Position Competency Modal -->
            <?php if ($view_position_competency): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Position Competency</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Position</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_position_competency['position_title']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Competency</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_position_competency['competency_name']; ?></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Required Level</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $view_position_competency['required_level']; ?>/5
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Importance Weight</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_position_competency['importance_weight']; ?>%</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Minimum Score</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <?php echo $view_position_competency['minimum_score']; ?>/5
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Mandatory</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $view_position_competency['is_mandatory'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'; ?>">
                                            <?php echo $view_position_competency['is_mandatory'] ? 'Yes' : 'No'; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Category Tab -->
            <div id="category-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_category ? 'Edit Competency Category' : 'Add Competency Category'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_category): ?>
                            <input type="hidden" name="category_id" value="<?php echo $edit_category['category_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category Name</label>
                            <input type="text" name="category_name" required 
                                   value="<?php echo $edit_category ? $edit_category['category_name'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Category Weight (0-100)</label>
                            <input type="number" name="category_weight" min="0" max="100" required 
                                   value="<?php echo $edit_category ? $edit_category['category_weight'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="category_description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $edit_category ? $edit_category['description'] : ''; ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_category): ?>
                                <button type="submit" name="update_competency_category" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Category
                                </button>
                                <a href="Competencymanagement.php#category-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_competency_category" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Category
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Competency Categories</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchCategory" placeholder="Search categories..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('category')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="categoryTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Weight</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $categories_list = $conn->query("SELECT * FROM competency_category WHERE is_active = 1");
                                while($cat = $categories_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $cat['category_name']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div class="max-w-xs truncate" title="<?php echo $cat['description']; ?>">
                                            <?php echo $cat['description']; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $cat['category_weight']; ?>%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_category_id=<?php echo $cat['category_id']; ?>#category-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_category_id=<?php echo $cat['category_id']; ?>#category-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this category?')">
                                            <input type="hidden" name="category_id" value="<?php echo $cat['category_id']; ?>">
                                            <button type="submit" name="delete_competency_category" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $categories_list->num_rows; ?> categories
                    </div>
                </div>
            </div>

            <!-- View Category Modal -->
            <?php if ($view_category): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Competency Category</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category Name</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_category['category_name']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_category['description']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category Weight</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_category['category_weight']; ?>%</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_category['created_date']; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Competency Gap Tab -->
            <div id="gap-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_gap ? 'Edit Competency Gap' : 'Add Competency Gap'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_gap): ?>
                            <input type="hidden" name="gap_id" value="<?php echo $edit_gap['gap_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="gap_employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Employee</option>
                                <?php 
                                $employees_gap = $conn->query("SELECT * FROM employee WHERE employment_status = 'Active'");
                                while($emp = $employees_gap->fetch_assoc()): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>" <?php echo ($edit_gap && $edit_gap['fk_employee_id'] == $emp['employee_id']) ? 'selected' : ''; ?>>
                                        <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competency</label>
                            <select name="gap_competency_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Competency</option>
                                <?php 
                                $competencies_gap = $conn->query("SELECT * FROM competency WHERE is_active = 1");
                                while($comp = $competencies_gap->fetch_assoc()): ?>
                                    <option value="<?php echo $comp['competency_id']; ?>" <?php echo ($edit_gap && $edit_gap['fk_competency_id'] == $comp['competency_id']) ? 'selected' : ''; ?>>
                                        <?php echo $comp['competency_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Current Level (1-5)</label>
                            <input type="number" name="current_level" min="1" max="5" required 
                                   value="<?php echo $edit_gap ? $edit_gap['current_level'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Required Level (1-5)</label>
                            <input type="number" name="required_level" min="1" max="5" required 
                                   value="<?php echo $edit_gap ? $edit_gap['required_level'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Gap Score (0-4)</label>
                            <input type="number" name="gap_score" min="0" max="4" required
                                   value="<?php echo $edit_gap ? $edit_gap['gap_score'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Enter the gap score manually (0 = no gap, 4 = largest gap)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                            <select name="priority_level" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Low" <?php echo ($edit_gap && $edit_gap['priority_level'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                                <option value="Medium" <?php echo ($edit_gap && $edit_gap['priority_level'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                <option value="High" <?php echo ($edit_gap && $edit_gap['priority_level'] == 'High') ? 'selected' : ''; ?>>High</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Completion Date</label>
                            <input type="date" name="target_compensation_date" 
                                   value="<?php echo $edit_gap ? $edit_gap['target_compensation_date'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="gap_status" required class="mt-1 block w-full rounded-md border-gray-300 shadowSm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Open" <?php echo ($edit_gap && $edit_gap['status'] == 'Open') ? 'selected' : ''; ?>>Open</option>
                                <option value="In Progress" <?php echo ($edit_gap && $edit_gap['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Closed" <?php echo ($edit_gap && $edit_gap['status'] == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                                <option value="On Hold" <?php echo ($edit_gap && $edit_gap['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Action Plan</label>
                            <textarea name="action_plan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" rows="3"><?php echo $edit_gap ? $edit_gap['action_plan'] : ''; ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_gap): ?>
                                <button type="submit" name="update_competency_gap" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Competency Gap
                                </button>
                                <a href="Competencymanagement.php#gap-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_competency_gap" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Competency Gap
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Competency Gaps</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchGap" placeholder="Search competency gaps..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('gap')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="gapTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Competency</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Required Level</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gap Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $gaps_list = $conn->query("
                                    SELECT cg.*, e.first_name, e.last_name, c.competency_name 
                                    FROM competency_gap cg 
                                    JOIN employee e ON cg.fk_employee_id = e.employee_id 
                                    JOIN competency c ON cg.fk_competency_id = c.competency_id 
                                    ORDER BY cg.priority_level DESC, cg.gap_score DESC
                                ");
                                while($gap = $gaps_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $gap['first_name'] . ' ' . $gap['last_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $gap['competency_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($gap['current_level'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($gap['current_level'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $gap['current_level']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $gap['required_level']; ?>/5
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($gap['gap_score'] >= 3) echo 'bg-red-100 text-red-800';
                                            elseif($gap['gap_score'] >= 2) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo $gap['gap_score']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($gap['priority_level'] == 'High') echo 'bg-red-100 text-red-800';
                                            elseif($gap['priority_level'] == 'Medium') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo $gap['priority_level']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($gap['status'] == 'Closed') echo 'bg-green-100 text-green-800';
                                            elseif($gap['status'] == 'In Progress') echo 'bg-blue-100 text-blue-800';
                                            elseif($gap['status'] == 'On Hold') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo $gap['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $gap['target_compensation_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_gap_id=<?php echo $gap['gap_id']; ?>#gap-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_gap_id=<?php echo $gap['gap_id']; ?>#gap-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this competency gap?')">
                                            <input type="hidden" name="gap_id" value="<?php echo $gap['gap_id']; ?>">
                                            <button type="submit" name="delete_competency_gap" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $gaps_list->num_rows; ?> competency gaps
                    </div>
                </div>
            </div>

            <!-- View Competency Gap Modal -->
            <?php if ($view_gap): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Competency Gap</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_gap['first_name'] . ' ' . $view_gap['last_name']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Competency</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_gap['competency_name']; ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Current Level</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($view_gap['current_level'] >= 4) echo 'bg-green-100 text-green-800';
                                            elseif($view_gap['current_level'] >= 3) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-red-100 text-red-800';
                                            ?>">
                                            <?php echo $view_gap['current_level']; ?>/5
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Required Level</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <?php echo $view_gap['required_level']; ?>/5
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Gap Score</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            <?php 
                                            if($view_gap['gap_score'] >= 3) echo 'bg-red-100 text-red-800';
                                            elseif($view_gap['gap_score'] >= 2) echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo $view_gap['gap_score']; ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Priority Level</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($view_gap['priority_level'] == 'High') echo 'bg-red-100 text-red-800';
                                            elseif($view_gap['priority_level'] == 'Medium') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-green-100 text-green-800';
                                            ?>">
                                            <?php echo $view_gap['priority_level']; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($view_gap['status'] == 'Closed') echo 'bg-green-100 text-green-800';
                                            elseif($view_gap['status'] == 'In Progress') echo 'bg-blue-100 text-blue-800';
                                            elseif($view_gap['status'] == 'On Hold') echo 'bg-yellow-100 text-yellow-800';
                                            else echo 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo $view_gap['status']; ?>
                                        </span>
                                    </p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Completion Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_gap['target_compensation_date'] ?: 'Not set'; ?></p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Action Plan</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_gap['action_plan'] ?: 'No action plan provided'; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_gap['created_date']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Development Plan Tab -->
            <div id="development-plan-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4"><?php echo $edit_development_plan ? 'Edit Development Plan' : 'Add Development Plan'; ?></h2>
                    <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php if ($edit_development_plan): ?>
                            <input type="hidden" name="plan_id" value="<?php echo $edit_development_plan['dev_plan_id']; ?>">
                        <?php endif; ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Employee</label>
                            <select name="dev_employee_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Employee</option>
                                <?php 
                                $employees5 = $conn->query("SELECT * FROM employee WHERE employment_status = 'Active'");
                                while($emp = $employees5->fetch_assoc()): ?>
                                    <option value="<?php echo $emp['employee_id']; ?>" <?php echo ($edit_development_plan && $edit_development_plan['fk_employee_id'] == $emp['employee_id']) ? 'selected' : ''; ?>>
                                        <?php echo $emp['first_name'] . ' ' . $emp['last_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Competency Gap</label>
                            <select name="gap_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 select2">
                                <option value="">Select Gap (Optional)</option>
                                <?php 
                                $gaps2 = $conn->query("SELECT * FROM competency_gap");
                                while($gap = $gaps2->fetch_assoc()): ?>
                                    <option value="<?php echo $gap['gap_id']; ?>" <?php echo ($edit_development_plan && $edit_development_plan['fk_gap_id'] == $gap['gap_id']) ? 'selected' : ''; ?>>Gap ID: <?php echo $gap['gap_id']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                            <input type="text" name="plan_name" required 
                                   value="<?php echo $edit_development_plan ? $edit_development_plan['plan_name'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select name="dev_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Active" <?php echo ($edit_development_plan && $edit_development_plan['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                <option value="Completed" <?php echo ($edit_development_plan && $edit_development_plan['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="On Hold" <?php echo ($edit_development_plan && $edit_development_plan['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                <option value="Cancelled" <?php echo ($edit_development_plan && $edit_development_plan['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" required 
                                   value="<?php echo $edit_development_plan ? $edit_development_plan['start_date'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Completion Date</label>
                            <input type="date" name="target_completion_date" required 
                                   value="<?php echo $edit_development_plan ? $edit_development_plan['target_completion_date'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Actual Completion Date</label>
                            <input type="date" name="actual_completion_date" 
                                   value="<?php echo $edit_development_plan ? $edit_development_plan['actual_completion_date'] : ''; ?>"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Progress Status</label>
                            <select name="progress_status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Not Started" <?php echo ($edit_development_plan && $edit_development_plan['progress_status'] == 'Not Started') ? 'selected' : ''; ?>>Not Started</option>
                                <option value="In Progress" <?php echo ($edit_development_plan && $edit_development_plan['progress_status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                                <option value="Completed" <?php echo ($edit_development_plan && $edit_development_plan['progress_status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                <option value="Delayed" <?php echo ($edit_development_plan && $edit_development_plan['progress_status'] == 'Delayed') ? 'selected' : ''; ?>>Delayed</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="dev_description" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $edit_development_plan ? $edit_development_plan['description'] : ''; ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Development Methods</label>
                            <textarea name="development_methods" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo $edit_development_plan ? $edit_development_plan['development_methods'] : ''; ?></textarea>
                        </div>
                        <div class="md:col-span-2">
                            <?php if ($edit_development_plan): ?>
                                <button type="submit" name="update_development_plan" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                    Update Development Plan
                                </button>
                                <a href="Competencymanagement.php#development-plan-tab" class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 ml-2">
                                    Cancel
                                </a>
                            <?php else: ?>
                                <button type="submit" name="add_development_plan" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                    Add Development Plan
                                </button>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold">Development Plans</h2>
                        <div class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" id="searchDevelopmentPlan" placeholder="Search development plans..." 
                                       class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 w-64">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <button onclick="clearSearch('development-plan')" class="bg-gray-500 text-white px-3 py-2 rounded-lg hover:bg-gray-600 text-sm">
                                Clear
                            </button>
                        </div>
                    </div>
                    
                    <div class="overflow-y-auto border border-gray-200 rounded-lg max-h-96">
                        <table id="developmentPlanTable" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Target Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $development_plans_list = $conn->query("
                                    SELECT dp.*, e.first_name, e.last_name, cg.fk_competency_id 
                                    FROM development_plan dp 
                                    JOIN employee e ON dp.fk_employee_id = e.employee_id 
                                    LEFT JOIN competency_gap cg ON dp.fk_gap_id = cg.gap_id 
                                    ORDER BY dp.created_date DESC
                                ");
                                while($dp = $development_plans_list->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $dp['first_name'] . ' ' . $dp['last_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $dp['plan_name']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $dp['start_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $dp['target_completion_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($dp['status'] == 'Active') echo 'bg-green-100 text-green-800';
                                            elseif($dp['status'] == 'Completed') echo 'bg-blue-100 text-blue-800';
                                            else echo 'bg-yellow-100 text-yellow-800';
                                            ?>">
                                            <?php echo $dp['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($dp['progress_status'] == 'Completed') echo 'bg-green-100 text-green-800';
                                            elseif($dp['progress_status'] == 'In Progress') echo 'bg-blue-100 text-blue-800';
                                            elseif($dp['progress_status'] == 'Delayed') echo 'bg-red-100 text-red-800';
                                            else echo 'bg-gray-100 text-gray-800';
                                            ?>">
                                            <?php echo $dp['progress_status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="Competencymanagement.php?view_development_plan_id=<?php echo $dp['dev_plan_id']; ?>#development-plan-tab" class="text-blue-600 hover:text-blue-900 mr-3">View</a>
                                        <a href="Competencymanagement.php?edit_development_plan_id=<?php echo $dp['dev_plan_id']; ?>#development-plan-tab" class="text-green-600 hover:text-green-900 mr-3">Edit</a>
                                        <form method="POST" style="display: inline;" onsubmit="return confirmDelete('Are you sure you want to delete this development plan?')">
                                            <input type="hidden" name="plan_id" value="<?php echo $dp['dev_plan_id']; ?>">
                                            <button type="submit" name="delete_development_plan" class="text-red-600 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Showing <?php echo $development_plans_list->num_rows; ?> development plans
                    </div>
                </div>
            </div>

            <!-- View Development Plan Modal -->
            <?php if ($view_development_plan): ?>
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center pb-3 border-b">
                            <h3 class="text-xl font-semibold text-gray-900">View Development Plan</h3>
                            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Employee</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['first_name'] . ' ' . $view_development_plan['last_name']; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Plan Name</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['plan_name']; ?></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['start_date']; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Completion Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['target_completion_date']; ?></p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Actual Completion Date</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['actual_completion_date'] ?: 'Not completed'; ?></p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php 
                                            if($view_development_plan['status'] == 'Active') echo 'bg-green-100 text-green-800';
                                            elseif($view_development_plan['status'] == 'Completed') echo 'bg-blue-100 text-blue-800';
                                            else echo 'bg-yellow-100 text-yellow-800';
                                            ?>">
                                            <?php echo $view_development_plan['status']; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Progress Status</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php 
                                        if($view_development_plan['progress_status'] == 'Completed') echo 'bg-green-100 text-green-800';
                                        elseif($view_development_plan['progress_status'] == 'In Progress') echo 'bg-blue-100 text-blue-800';
                                        elseif($view_development_plan['progress_status'] == 'Delayed') echo 'bg-red-100 text-red-800';
                                        else echo 'bg-gray-100 text-gray-800';
                                        ?>">
                                        <?php echo $view_development_plan['progress_status']; ?>
                                    </span>
                                </p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Description</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['description'] ?: 'No description provided'; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Development Methods</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['development_methods'] ?: 'No development methods specified'; ?></p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Created Date</label>
                                <p class="mt-1 text-sm text-gray-900 bg-gray-50 p-2 rounded"><?php echo $view_development_plan['created_date']; ?></p>
                            </div>
                        </div>
                        
                        <div class="flex justify-end mt-6">
                            <button onclick="closeModal()" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>