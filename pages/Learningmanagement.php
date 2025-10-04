<?php
// learning_management.php - Backend functionality for Learning Management System
session_start();
require_once '../config/db_learn.php'; // 

class LearningManagement {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // Get all employees
    public function getEmployees() {
        try {
            $query = "SELECT e.employee_id, CONCAT(e.first_name, ' ', e.last_name) as full_name, 
                             d.department_name, p.title as position_title
                      FROM employee e 
                      LEFT JOIN department d ON e.fk_department_id = d.department_id
                      LEFT JOIN position p ON e.fk_position_id = p.position_id
                      WHERE e.employment_status = 'Active'
                      ORDER BY e.first_name, e.last_name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting employees: " . $e->getMessage());
            return [];
        }
    }
    
    // Get all courses
    public function getCourses() {
        try {
            $query = "SELECT course_id, title, description, category, duration, delivery_mode 
                      FROM course 
                      ORDER BY title";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting courses: " . $e->getMessage());
            return [];
        }
    }
    
    // Get progress for specific employee
    public function getProgress($employee_id) {
        try {
            $query = "SELECT p.progress_id, p.fk_course_id as course_id, c.title as course_name, 
                             p.status, p.completion_percent, p.date_started, p.date_updated
                      FROM progress p
                      JOIN course c ON p.fk_course_id = c.course_id
                      WHERE p.fk_employee_id = ?
                      ORDER BY c.title";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$employee_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting progress: " . $e->getMessage());
            return [];
        }
    }
    
    // Add new course - UPDATED WITH DELIVERY_MODE
    public function addCourse($title, $description = '', $category = 'General', $duration = 0, $delivery_mode = 'online') {
        try {
            // Check if course already exists
            $check_query = "SELECT course_id FROM course WHERE LOWER(title) = LOWER(?)";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->execute([$title]);
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Course already exists'];
            }
            
            $query = "INSERT INTO course (title, description, category, duration, delivery_mode) 
                      VALUES (?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$title, $description, $category, $duration, $delivery_mode]);
            
            if ($result) {
                return ['success' => true, 'course_id' => $this->conn->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Failed to add course'];
        } catch(PDOException $e) {
            error_log("Error adding course: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Update course - UPDATED WITH DELIVERY_MODE
    public function updateCourse($course_id, $title, $description = '', $category = 'General', $duration = 0, $delivery_mode = 'online') {
        try {
            $query = "UPDATE course SET title = ?, description = ?, category = ?, duration = ?, delivery_mode = ? WHERE course_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$title, $description, $category, $duration, $delivery_mode, $course_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error updating course: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Delete course - FIXED VERSION
    public function deleteCourse($course_id) {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Delete related records first
            $queries = [
                "DELETE FROM progress WHERE fk_course_id = ?",
                "DELETE FROM feedback WHERE fk_course_id = ?",
                "DELETE FROM course WHERE course_id = ?"
            ];
            
            foreach ($queries as $query) {
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$course_id]);
            }
            
            // Commit transaction
            $this->conn->commit();
            
            return ['success' => true, 'message' => 'Course deleted successfully'];
            
        } catch(PDOException $e) {
            // Rollback on error
            $this->conn->rollBack();
            error_log("Error deleting course: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
    
    // Update progress
    public function updateProgress($employee_id, $course_id, $status) {
        try {
            // Determine completion percentage based on status
            $completion_percent = 0;
            switch($status) {
                case 'Completed':
                    $completion_percent = 100;
                    break;
                case 'In Progress':
                    $completion_percent = 50;
                    break;
                case 'Not Started':
                    $completion_percent = 0;
                    break;
            }
            
            // Check if progress record exists
            $check_query = "SELECT progress_id FROM progress WHERE fk_employee_id = ? AND fk_course_id = ?";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->execute([$employee_id, $course_id]);
            
            if ($check_stmt->rowCount() > 0) {
                // Update existing record
                $query = "UPDATE progress 
                          SET status = ?, completion_percent = ?, date_updated = NOW() 
                          WHERE fk_employee_id = ? AND fk_course_id = ?";
                
                $stmt = $this->conn->prepare($query);
                $result = $stmt->execute([$status, $completion_percent, $employee_id, $course_id]);
            } else {
                // Insert new record
                $query = "INSERT INTO progress (fk_employee_id, fk_course_id, status, completion_percent, date_started, date_updated) 
                          VALUES (?, ?, ?, ?, NOW(), NOW())";
                
                $stmt = $this->conn->prepare($query);
                $result = $stmt->execute([$employee_id, $course_id, $status, $completion_percent]);
            }
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error updating progress: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Add learning path
    public function addLearningPath($title, $description, $duration_total = 0, $hr_id = 1) {
        try {
            $query = "INSERT INTO learning_path (title, description, duration_total, fk_hr_id) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$title, $description, $duration_total, $hr_id]);
            
            return ['success' => $result, 'path_id' => $this->conn->lastInsertId()];
        } catch(PDOException $e) {
            error_log("Error adding learning path: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Update learning path
    public function updateLearningPath($path_id, $title, $description, $duration_total = 0) {
        try {
            $query = "UPDATE learning_path SET title = ?, description = ?, duration_total = ? WHERE path_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$title, $description, $duration_total, $path_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error updating learning path: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Delete learning path
    public function deleteLearningPath($path_id) {
        try {
            $this->conn->beginTransaction();
            
            // Delete related records first
            $queries = [
                "DELETE FROM learning_path_courses WHERE fk_path_id = ?",
                "DELETE FROM learning_path WHERE path_id = ?"
            ];
            
            foreach ($queries as $query) {
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$path_id]);
            }
            
            $this->conn->commit();
            return ['success' => true, 'message' => 'Learning path deleted successfully'];
            
        } catch(PDOException $e) {
            $this->conn->rollBack();
            error_log("Error deleting learning path: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Get learning paths
    public function getLearningPaths() {
        try {
            $query = "SELECT lp.path_id, lp.title, lp.description, lp.duration_total, 
                             h.name as hr_name,
                             COUNT(lpc.fk_course_id) as courses_count
                      FROM learning_path lp
                      LEFT JOIN hr_staff h ON lp.fk_hr_id = h.hr_id
                      LEFT JOIN learning_path_courses lpc ON lp.path_id = lpc.fk_path_id
                      GROUP BY lp.path_id
                      ORDER BY lp.title";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting learning paths: " . $e->getMessage());
            return [];
        }
    }
    
    // Get courses for a learning path
    public function getPathCourses($path_id) {
        try {
            $query = "SELECT c.course_id, c.title, c.description, c.category, c.duration, c.delivery_mode,
                             lpc.link_id, lpc.course_order, lpc.is_required
                      FROM learning_path_courses lpc
                      JOIN course c ON lpc.fk_course_id = c.course_id
                      WHERE lpc.fk_path_id = ?
                      ORDER BY lpc.course_order";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$path_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting path courses: " . $e->getMessage());
            return [];
        }
    }
    
    // Add course to learning path
    public function addCourseToPath($path_id, $course_id, $course_order = 1, $is_required = 1) {
        try {
            // Check if course is already in path
            $check_query = "SELECT link_id FROM learning_path_courses WHERE fk_path_id = ? AND fk_course_id = ?";
            $check_stmt = $this->conn->prepare($check_query);
            $check_stmt->execute([$path_id, $course_id]);
            
            if ($check_stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Course already exists in this learning path'];
            }
            
            $query = "INSERT INTO learning_path_courses (fk_path_id, fk_course_id, course_order, is_required) 
                      VALUES (?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$path_id, $course_id, $course_order, $is_required]);
            
            return ['success' => $result, 'link_id' => $this->conn->lastInsertId()];
        } catch(PDOException $e) {
            error_log("Error adding course to path: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Remove course from learning path
    public function removeCourseFromPath($link_id) {
        try {
            $query = "DELETE FROM learning_path_courses WHERE link_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$link_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error removing course from path: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Update course order in learning path
    public function updateCourseOrder($link_id, $course_order) {
        try {
            $query = "UPDATE learning_path_courses SET course_order = ? WHERE link_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$course_order, $link_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error updating course order: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Update course requirement status in learning path
    public function updateCourseRequirement($link_id, $is_required) {
        try {
            $query = "UPDATE learning_path_courses SET is_required = ? WHERE link_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$is_required, $link_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error updating course requirement: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Add feedback
    public function addFeedback($employee_id, $course_id, $comment, $rating) {
        try {
            $query = "INSERT INTO feedback (fk_employee_id, fk_course_id, comment, rating, date_given) 
                      VALUES (?, ?, ?, ?, NOW())";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$employee_id, $course_id, $comment, $rating]);
            
            return ['success' => $result, 'feedback_id' => $this->conn->lastInsertId()];
        } catch(PDOException $e) {
            error_log("Error adding feedback: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Update feedback
    public function updateFeedback($feedback_id, $comment, $rating) {
        try {
            $query = "UPDATE feedback SET comment = ?, rating = ?, date_given = NOW() WHERE feedback_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$comment, $rating, $feedback_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error updating feedback: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Delete feedback
    public function deleteFeedback($feedback_id) {
        try {
            $query = "DELETE FROM feedback WHERE feedback_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $result = $stmt->execute([$feedback_id]);
            
            return ['success' => $result];
        } catch(PDOException $e) {
            error_log("Error deleting feedback: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
    
    // Get feedback for employee
    public function getFeedback($employee_id) {
        try {
            $query = "SELECT f.feedback_id, f.fk_course_id as course_id, c.title as course_name, 
                             f.comment, f.rating, f.date_given
                      FROM feedback f
                      LEFT JOIN course c ON f.fk_course_id = c.course_id
                      WHERE f.fk_employee_id = ?
                      ORDER BY f.date_given DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$employee_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting feedback: " . $e->getMessage());
            return [];
        }
    }
    
    // Get learning reports for specific employee
    public function getLearningReports($employee_id = null) {
        try {
            if ($employee_id) {
                // Get reports for specific employee
                $query = "SELECT lr.report_id, lr.fk_employee_id, 
                                 CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                                 lr.summary, lr.generated_date, lr.total_courses_completed, 
                                 lr.overall_rating, lr.average_completion_rate, lr.report_type
                          FROM learning_report lr
                          LEFT JOIN employee e ON lr.fk_employee_id = e.employee_id
                          WHERE lr.fk_employee_id = ?
                          ORDER BY lr.generated_date DESC";
                
                $stmt = $this->conn->prepare($query);
                $stmt->execute([$employee_id]);
            } else {
                // Get all reports
                $query = "SELECT lr.report_id, lr.fk_employee_id, 
                                 CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                                 lr.summary, lr.generated_date, lr.total_courses_completed, 
                                 lr.overall_rating, lr.average_completion_rate, lr.report_type
                          FROM learning_report lr
                          LEFT JOIN employee e ON lr.fk_employee_id = e.employee_id
                          ORDER BY lr.generated_date DESC";
                
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error getting learning reports: " . $e->getMessage());
            return [];
        }
    }
    
    // Generate learning report for employee
    public function generateLearningReport($employee_id) {
        try {
            // Get employee details
            $employee_query = "SELECT CONCAT(first_name, ' ', last_name) as employee_name 
                              FROM employee WHERE employee_id = ?";
            $employee_stmt = $this->conn->prepare($employee_query);
            $employee_stmt->execute([$employee_id]);
            $employee = $employee_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get basic statistics
            $stats_query = "SELECT 
                               COUNT(*) as total_courses,
                               SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) as completed_courses,
                               SUM(CASE WHEN status = 'In Progress' THEN 1 ELSE 0 END) as in_progress_courses,
                               AVG(completion_percent) as avg_completion
                           FROM progress 
                           WHERE fk_employee_id = ?";
            
            $stats_stmt = $this->conn->prepare($stats_query);
            $stats_stmt->execute([$employee_id]);
            $stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);
            
            // Get average rating from feedback
            $rating_query = "SELECT AVG(rating) as avg_rating 
                            FROM feedback 
                            WHERE fk_employee_id = ?";
            
            $rating_stmt = $this->conn->prepare($rating_query);
            $rating_stmt->execute([$employee_id]);
            $rating_result = $rating_stmt->fetch(PDO::FETCH_ASSOC);
            
            $overall_rating = $rating_result['avg_rating'] ?? 0;
            
            // Create summary
            $summary = "Learning Report for {$employee['employee_name']}. Completed {$stats['completed_courses']} out of {$stats['total_courses']} courses. Average completion rate: " . round($stats['avg_completion'], 1) . "%. Overall feedback rating: " . round($overall_rating, 1) . "/5.";
            
            // Insert learning report
            $report_query = "INSERT INTO learning_report 
                            (fk_employee_id, summary, generated_date, total_courses_completed, overall_rating, average_completion_rate, report_type)
                            VALUES (?, ?, NOW(), ?, ?, ?, 'Monthly')";
            
            $report_stmt = $this->conn->prepare($report_query);
            $result = $report_stmt->execute([
                $employee_id, 
                $summary, 
                $stats['completed_courses'], 
                round($overall_rating, 1),
                round($stats['avg_completion'], 1)
            ]);
            
            return [
                'success' => $result,
                'stats' => $stats,
                'overall_rating' => round($overall_rating, 1),
                'summary' => $summary
            ];
            
        } catch(PDOException $e) {
            error_log("Error generating learning report: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }
}

// Handle AJAX requests - UPDATED VERSION
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    // Use the existing db_learn.php connection instead of creating new one
    try {
        $lms = new LearningManagement($conn);
        
        header('Content-Type: application/json');
        
        switch($_POST['action']) {
            case 'get_employees':
                echo json_encode($lms->getEmployees());
                break;
                
            case 'get_courses':
                echo json_encode($lms->getCourses());
                break;
                
            case 'get_progress':
                $employee_id = $_POST['employee_id'] ?? 1;
                echo json_encode($lms->getProgress($employee_id));
                break;
                
            case 'add_course':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? 'General';
                $duration = $_POST['duration'] ?? 0;
                $delivery_mode = $_POST['delivery_mode'] ?? 'online';
                $result = $lms->addCourse($title, $description, $category, $duration, $delivery_mode);
                echo json_encode($result);
                break;
                
            case 'update_course':
                $course_id = $_POST['course_id'] ?? '';
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $category = $_POST['category'] ?? 'General';
                $duration = $_POST['duration'] ?? 0;
                $delivery_mode = $_POST['delivery_mode'] ?? 'online';
                $result = $lms->updateCourse($course_id, $title, $description, $category, $duration, $delivery_mode);
                echo json_encode($result);
                break;
                
            case 'delete_course':
                $course_id = $_POST['course_id'] ?? '';
                $result = $lms->deleteCourse($course_id);
                echo json_encode($result);
                break;
                
            case 'update_progress':
                $employee_id = $_POST['employee_id'] ?? '';
                $course_id = $_POST['course_id'] ?? '';
                $status = $_POST['status'] ?? '';
                $result = $lms->updateProgress($employee_id, $course_id, $status);
                echo json_encode($result);
                break;
                
            case 'add_learning_path':
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $duration_total = $_POST['duration_total'] ?? 0;
                $result = $lms->addLearningPath($title, $description, $duration_total);
                echo json_encode($result);
                break;
                
            case 'update_learning_path':
                $path_id = $_POST['path_id'] ?? '';
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $duration_total = $_POST['duration_total'] ?? 0;
                $result = $lms->updateLearningPath($path_id, $title, $description, $duration_total);
                echo json_encode($result);
                break;
                
            case 'delete_learning_path':
                $path_id = $_POST['path_id'] ?? '';
                $result = $lms->deleteLearningPath($path_id);
                echo json_encode($result);
                break;
                
            case 'get_learning_paths':
                echo json_encode($lms->getLearningPaths());
                break;
                
            case 'get_path_courses':
                $path_id = $_POST['path_id'] ?? '';
                echo json_encode($lms->getPathCourses($path_id));
                break;
                
            case 'add_course_to_path':
                $path_id = $_POST['path_id'] ?? '';
                $course_id = $_POST['course_id'] ?? '';
                $course_order = $_POST['course_order'] ?? 1;
                $is_required = $_POST['is_required'] ?? 1;
                $result = $lms->addCourseToPath($path_id, $course_id, $course_order, $is_required);
                echo json_encode($result);
                break;
                
            case 'remove_course_from_path':
                $link_id = $_POST['link_id'] ?? '';
                $result = $lms->removeCourseFromPath($link_id);
                echo json_encode($result);
                break;
                
            case 'update_course_order':
                $link_id = $_POST['link_id'] ?? '';
                $course_order = $_POST['course_order'] ?? 1;
                $result = $lms->updateCourseOrder($link_id, $course_order);
                echo json_encode($result);
                break;
                
            case 'update_course_requirement':
                $link_id = $_POST['link_id'] ?? '';
                $is_required = $_POST['is_required'] ?? 1;
                $result = $lms->updateCourseRequirement($link_id, $is_required);
                echo json_encode($result);
                break;
                
            case 'add_feedback':
                $employee_id = $_POST['employee_id'] ?? '';
                $course_id = $_POST['course_id'] ?? null;
                $comment = $_POST['comment'] ?? '';
                $rating = $_POST['rating'] ?? 5;
                $result = $lms->addFeedback($employee_id, $course_id, $comment, $rating);
                echo json_encode($result);
                break;
                
            case 'update_feedback':
                $feedback_id = $_POST['feedback_id'] ?? '';
                $comment = $_POST['comment'] ?? '';
                $rating = $_POST['rating'] ?? 5;
                $result = $lms->updateFeedback($feedback_id, $comment, $rating);
                echo json_encode($result);
                break;
                
            case 'delete_feedback':
                $feedback_id = $_POST['feedback_id'] ?? '';
                $result = $lms->deleteFeedback($feedback_id);
                echo json_encode($result);
                break;
                
            case 'get_feedback':
                $employee_id = $_POST['employee_id'] ?? 1;
                echo json_encode($lms->getFeedback($employee_id));
                break;
                
            case 'get_learning_reports':
                $employee_id = $_POST['employee_id'] ?? null;
                echo json_encode($lms->getLearningReports($employee_id));
                break;
                
            case 'generate_report':
                $employee_id = $_POST['employee_id'] ?? 1;
                echo json_encode($lms->generateLearningReport($employee_id));
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        
    } catch(Exception $e) {
        echo json_encode(['success' => false, 'message' => 'System error: ' . $e->getMessage()]);
        error_log("LMS Error: " . $e->getMessage());
    }
    
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Learning Management</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
  <div class="flex min-h-screen">

  <!-- Sidebar (Left Panel) - FIXED PATH -->
  <?php include '../includes/sidebar.php'; ?>

  <!-- Main Content Area -->
  <div class="flex-1 flex flex-col">
 <!-- Top Navigation -->
<nav class="bg-white shadow-sm border-b">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-center justify-between h-16">
      <div class="flex items-center space-x-8">
        <h1 class="text-xl font-bold text-gray-900">Learning Management</h1>
        <div class="flex space-x-6">
          <a href="#progress" class="nav-link px-3 py-2 text-sm font-medium text-blue-600 border-b-2 border-blue-600">Progress</a>
          <a href="#reports" class="nav-link px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Reports</a>
          <a href="#paths" class="nav-link px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Paths</a>
          <a href="#feedback" class="nav-link px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900">Feedback</a>
        </div>
      </div>
      <div class="flex items-center">
        <!-- Enhanced Employee Select with Search -->
        <div class="relative w-64">
          <div class="relative">
            <input 
              type="text" 
              id="employeeSearch" 
              placeholder="Search employees..." 
              class="w-full border rounded-lg pl-10 pr-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <i class="fas fa-chevron-down absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
          </div>
          <div id="employeeDropdown" class="absolute z-10 w-full mt-1 bg-white border rounded-lg shadow-lg max-h-60 overflow-y-auto hidden">
            <select id="employeeSelect" class="hidden">
              <option value="">Select Employee</option>
            </select>
            <div id="employeeOptions" class="py-1"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</nav>

  <main class="max-w-7xl mx-auto px-4 py-6">
      <div class="space-y-8">
        <!-- Progress Tracking - DEFAULT VISIBLE -->
        <section id="progress" class="bg-white shadow rounded-lg">
          <div class="p-6 border-b">
            <div class="flex items-center mb-6">
              <h2 class="text-xl font-semibold">Progress Tracking</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-6">
              <div class="p-4 rounded-lg border bg-gray-50">
                <div class="text-sm text-gray-500 mb-1">Overall Progress</div>
                <div id="overallPercent" class="text-2xl font-bold">0%</div>
              </div>
              <div class="p-4 rounded-lg border bg-gray-50">
                <div class="text-sm text-gray-500 mb-1">Total Courses</div>
                <div id="totalCourses" class="text-2xl font-bold">0</div>
              </div>
              <div class="p-4 rounded-lg border bg-gray-50">
                <div class="text-sm text-gray-500 mb-1">Completed</div>
                <div id="completedCount" class="text-2xl font-bold text-green-600">0</div>
              </div>
              <div class="p-4 rounded-lg border bg-gray-50">
                <div class="text-sm text-gray-500 mb-1">In Progress</div>
                <div id="inprogressCount" class="text-2xl font-bold text-blue-600">0</div>
              </div>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-3">
              <div id="progressBar" class="bg-blue-600 h-3 rounded-full transition-all duration-300" style="width: 0%;"></div>
            </div>
            <p id="progressLabel" class="mt-2 text-sm text-gray-600">0% Complete</p>
          </div>

          <!-- Course Progress List - IMPROVED SECTION WITH DETAILS -->
          <div class="p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
              <i class="fas fa-list-check text-blue-500 mr-2"></i>
              Course Progress Details
            </h3>
            <div id="progressList" class="space-y-4 max-h-96 overflow-y-auto p-2">
              <div class="text-center py-8 text-gray-500">
                <i class="fas fa-user-search text-4xl mb-3"></i>
                <p>Select an employee to view their course progress</p>
              </div>
            </div>
          </div>
        </section>

        <!-- Learning Reports - HIDDEN BY DEFAULT -->
        <section id="reports" class="bg-white shadow rounded-lg p-6 hidden">
          <div class="flex items-center mb-6">
            <h2 class="text-xl font-semibold">Learning Reports</h2>
          </div>

          <!-- Course Management Section -->
          <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Add/Edit Course Form - UPDATED WITH DELIVERY MODE -->
            <div class="bg-gray-50 rounded-lg p-4">
              <h3 class="text-lg font-semibold mb-3 flex items-center">
                <i class="fas fa-book mr-2 text-blue-500"></i>
                Course Management
              </h3>
              <form id="courseForm" class="space-y-3">
                <input type="hidden" id="editCourseId" value="">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Course Title</label>
                  <input 
                    type="text" 
                    id="courseTitle" 
                    placeholder="e.g., Project Management" 
                    class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                  <textarea 
                    id="courseDescription" 
                    rows="2" 
                    placeholder="Course description..." 
                    class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                  ></textarea>
                </div>
                <div class="grid grid-cols-3 gap-3">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                    <input 
                      type="number" 
                      id="courseDuration" 
                      min="0"
                      step="0.5"
                      placeholder="0" 
                      class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select id="courseCategory" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="General">General</option>
                      <option value="Technical">Technical</option>
                      <option value="Soft Skills">Soft Skills</option>
                      <option value="Leadership">Leadership</option>
                      <option value="Management">Management</option>
                      <option value="Data Science">Data Science</option>
                      <option value="Personal Development">Personal Development</option>
                    </select>
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Mode</label>
                    <select id="courseDeliveryMode" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                      <option value="online">Online</option>
                      <option value="in-person">In-person</option>
                      <option value="hybrid">Hybrid</option>
                    </select>
                  </div>
                </div>
                <div class="flex gap-2 pt-2">
                  <button type="submit" class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Course
                  </button>
                  <button type="button" id="cancelEditCourse" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors hidden">
                    <i class="fas fa-times mr-2"></i>Cancel
                  </button>
                </div>
              </form>
            </div>

            <!-- Update Progress Form -->
            <div class="bg-blue-50 rounded-lg p-4">
              <h3 class="text-lg font-semibold mb-3 flex items-center">
                <i class="fas fa-tasks mr-2 text-blue-500"></i>
                Update Progress
              </h3>
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Select Course</label>
                  <select id="courseSelect" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select a course...</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                  <select id="statusSelect" class="w-full border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="Not Started">Not Started</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                  </select>
                </div>
                <button id="updateProgressBtn" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                  <i class="fas fa-sync-alt mr-2"></i>Update Progress
                </button>
              </div>
            </div>
          </div>

          <!-- Courses List -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-list mr-2 text-green-500"></i>
                Available Courses
              </h3>
              <span class="text-sm text-gray-500" id="coursesCount">0 courses</span>
            </div>
            <div id="coursesList" class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-96 overflow-y-auto p-2"></div>
          </div>

          <!-- Employee Progress Table -->
          <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
              <i class="fas fa-table mr-2 text-purple-500"></i>
              Employee Progress
            </h3>
            <div class="overflow-x-auto rounded-lg border">
              <div class="max-h-80 overflow-y-auto">
                <table class="w-full text-left text-sm">
                  <thead class="bg-gray-100 sticky top-0">
                    <tr>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Course</th>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Status</th>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Progress</th>
                    </tr>
                  </thead>
                  <tbody id="reportsBody" class="bg-white">
                    <tr>
                      <td colspan="3" class="p-4 text-center text-gray-500">Select an employee to view their progress</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- Learning Reports Section - NEW SECTION ADDED -->
          <div>
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg font-semibold flex items-center">
                <i class="fas fa-chart-bar mr-2 text-indigo-500"></i>
                Learning Reports
              </h3>
              <button id="generateReportBtn" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm">
                <i class="fas fa-plus mr-2"></i>Generate Report
              </button>
            </div>
            <div class="overflow-x-auto rounded-lg border">
              <div class="max-h-80 overflow-y-auto">
                <table class="w-full text-left text-sm">
                  <thead class="bg-gray-100 sticky top-0">
                    <tr>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Employee</th>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Summary</th>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Date Generated</th>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Courses Completed</th>
                      <th class="p-3 font-medium text-gray-700 bg-gray-100">Overall Rating</th>
                    </tr>
                  </thead>
                  <tbody id="learningReportsBody" class="bg-white">
                    <tr>
                      <td colspan="5" class="p-4 text-center text-gray-500">Select an employee to view their learning reports</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </section>

        <!-- Learning Path Creation - HIDDEN BY DEFAULT -->
        <section id="paths" class="bg-white shadow rounded-lg p-6 hidden">
          <div class="flex items-center mb-6">
            <h2 class="text-xl font-semibold">Learning Path Creation</h2>
          </div>
          
          <!-- Learning Path Form -->
          <form id="pathForm" class="space-y-4 mb-6">
            <input type="hidden" id="editPathId" value="">
            <div>
              <input 
                id="pathName" 
                type="text" 
                placeholder="Path Name" 
                class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-500" 
                required
              />
            </div>
            <div>
              <textarea 
                id="pathDesc" 
                rows="3" 
                placeholder="Description" 
                class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-500"
              ></textarea>
            </div>
            <div>
              <input 
                id="pathHours" 
                type="number" 
                min="0"
                step="0.5"
                placeholder="Duration in hours (e.g., 20)" 
                class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-purple-500" 
                required
              />
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>Create Path
              </button>
              <button type="button" id="cancelEditPath" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors hidden">
                <i class="fas fa-times mr-2"></i>Cancel
              </button>
            </div>
          </form>
          
          <!-- Path Courses Management -->
              <div id="pathCoursesSection" class="mb-6 hidden">
                <h3 class="text-lg font-semibold mb-4">Manage Courses in Path</h3>
                <div class="grid md:grid-cols-2 gap-4 mb-4">
                  <select id="pathCourseSelect" class="border rounded-lg p-2">
                    <option value="">Select a course...</option>
                  </select>
                  <button id="addCourseToPathBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Add Course</button>
                </div>
                
                <!-- Add View/Cancel buttons -->
                <div class="flex justify-between items-center mb-4">
                  <h4 class="text-md font-medium">Courses in this Path</h4>
                  <div class="flex gap-2">
                    <button id="viewPathCoursesBtn" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm">
                      <i class="fas fa-eye mr-2"></i>View All Courses
                    </button>
                    <button id="cancelManagePathBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm">
                      <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                  </div>
                </div>
                
                <div id="pathCoursesList" class="space-y-3 max-h-64 overflow-y-auto p-2 border rounded-lg"></div>
              </div>
          
          <!-- Learning Paths List WITH SCROLL -->
          <div id="pathsList" class="grid sm:grid-cols-2 gap-4 max-h-96 overflow-y-auto p-2"></div>
        </section>

        <!-- Feedback - HIDDEN BY DEFAULT -->
        <section id="feedback" class="bg-white shadow rounded-lg p-6 mb-24 hidden">
          <div class="flex items-center mb-6">
            <h2 class="text-xl font-semibold">Feedback</h2>
          </div>
          
          <!-- Feedback Form -->
          <form id="feedbackForm" class="space-y-4 mb-6">
            <input type="hidden" id="editFeedbackId" value="">
            <textarea 
              id="feedbackText" 
              class="w-full border rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-yellow-500" 
              rows="3" 
              placeholder="Write your feedback..."
              required
            ></textarea>
            <div class="flex flex-wrap items-center gap-3">
              <select id="feedbackCourse" class="border rounded-lg p-2">
                <option value="">General</option>
              </select>
              <select id="feedbackRating" class="border rounded-lg p-2">
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
              </select>
              <div class="flex gap-2">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                  <i class="fas fa-paper-plane mr-2"></i>Submit
                </button>
                <button type="button" id="cancelEditFeedback" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors hidden">
                  <i class="fas fa-times mr-2"></i>Cancel
                </button>
              </div>
            </div>
          </form>
          
          <!-- Feedback List WITH SCROLL -->
          <ul id="feedbackList" class="space-y-3 max-h-96 overflow-y-auto p-2"></ul>
        </section>
      </div>
    </main>
  </div>
</div>

<script>
    let currentEmployeeId = '';
    let courses = [];
    let progressData = [];
    let allEmployees = [];
    let learningPaths = [];
    let feedbackData = [];
    let learningReports = [];
    let currentPathId = '';
    let pathCourses = [];

    // Initialize the application
    document.addEventListener('DOMContentLoaded', function() {
      loadEmployees();
      loadCourses();
      loadLearningPaths();
      setupNavigation();
      setupEmployeeSearch();
      
     // Event listeners
      document.getElementById('updateProgressBtn').addEventListener('click', updateProgress);
      document.getElementById('courseForm').addEventListener('submit', handleCourseSubmit);
      document.getElementById('pathForm').addEventListener('submit', handlePathSubmit);
      document.getElementById('feedbackForm').addEventListener('submit', handleFeedbackSubmit);
      document.getElementById('cancelEditCourse').addEventListener('click', cancelEditCourse);
      document.getElementById('cancelEditPath').addEventListener('click', cancelEditPath);
      document.getElementById('cancelEditFeedback').addEventListener('click', cancelEditFeedback);
      document.getElementById('generateReportBtn').addEventListener('click', generateLearningReport);
      document.getElementById('addCourseToPathBtn').addEventListener('click', addCourseToPath);

      // NEW: Add event listeners for view and cancel path buttons
      document.getElementById('viewPathCoursesBtn').addEventListener('click', viewAllPathCourses);
      document.getElementById('cancelManagePathBtn').addEventListener('click', cancelManagePath);
    });

    // Setup employee search functionality
    function setupEmployeeSearch() {
      const searchInput = document.getElementById('employeeSearch');
      const dropdown = document.getElementById('employeeDropdown');
      const optionsContainer = document.getElementById('employeeOptions');

      // Show dropdown when focusing on search input
      searchInput.addEventListener('focus', function() {
        if (allEmployees.length > 0) {
          dropdown.classList.remove('hidden');
          filterEmployees('');
        }
      });

      // Filter employees based on search input
      searchInput.addEventListener('input', function() {
        filterEmployees(this.value);
      });

      // Hide dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.classList.add('hidden');
        }
      });

      // Handle employee selection from dropdown
      optionsContainer.addEventListener('click', function(e) {
        const option = e.target.closest('.employee-option');
        if (option) {
          const employeeId = option.getAttribute('data-id');
          const employeeName = option.getAttribute('data-name');
          const department = option.getAttribute('data-department');
          
          selectEmployee(employeeId, employeeName, department);
          dropdown.classList.add('hidden');
        }
      });
    }

    // Filter employees based on search term
    function filterEmployees(searchTerm) {
      const optionsContainer = document.getElementById('employeeOptions');
      const term = searchTerm.toLowerCase();
      
      optionsContainer.innerHTML = '';
      
      const filteredEmployees = allEmployees.filter(emp => 
        emp.full_name.toLowerCase().includes(term) || 
        (emp.department_name && emp.department_name.toLowerCase().includes(term)) ||
        (emp.position_title && emp.position_title.toLowerCase().includes(term))
      );
      
      if (filteredEmployees.length === 0) {
        optionsContainer.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No employees found</div>';
        return;
      }
      
      filteredEmployees.forEach(emp => {
        const option = document.createElement('div');
        option.className = 'employee-option px-4 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0';
        option.setAttribute('data-id', emp.employee_id);
        option.setAttribute('data-name', emp.full_name);
        option.setAttribute('data-department', emp.department_name || 'No department');
        
        option.innerHTML = `
          <div class="font-medium text-gray-900">${emp.full_name}</div>
          <div class="text-xs text-gray-500">${emp.department_name || 'No department'} • ${emp.position_title || 'No position'}</div>
        `;
        
        optionsContainer.appendChild(option);
      });
    }

    // Select an employee
    function selectEmployee(employeeId, employeeName, department) {
      currentEmployeeId = employeeId;
      const searchInput = document.getElementById('employeeSearch');
      searchInput.value = `${employeeName} (${department})`;
      
      // Load data for selected employee
      loadProgress();
      loadFeedback();
      loadLearningReports();
    }

    // Setup navigation
    function setupNavigation() {
      const navLinks = document.querySelectorAll('nav .nav-link');
      const sections = document.querySelectorAll('main section');

      navLinks.forEach(link => {
        link.addEventListener('click', function (e) {
          e.preventDefault();
          const targetId = this.getAttribute('href').substring(1);

          // Hide all sections
          sections.forEach(sec => sec.classList.add('hidden'));

          // Show target section
          const targetElement = document.getElementById(targetId);
          if (targetElement) {
            targetElement.classList.remove('hidden');
          }

          // Reset nav styles
          navLinks.forEach(l => {
            l.classList.remove('text-blue-600', 'border-b-2', 'border-blue-600');
            l.classList.add('text-gray-600');
          });

          // Activate clicked nav
          this.classList.remove('text-gray-600');
          this.classList.add('text-blue-600', 'border-b-2', 'border-blue-600');
        });
      });
    }

    // API call helper function
    async function apiCall(action, data = {}) {
      try {
        const formData = new FormData();
        formData.append('action', action);
        
        for (const [key, value] of Object.entries(data)) {
          formData.append(key, value);
        }
        
        const response = await fetch('Learningmanagement.php', {
          method: 'POST',
          body: formData
        });
        
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        
        return await response.json();
      } catch (error) {
        console.error('API call failed:', error);
        return [];
      }
    }

    // Load employees
    async function loadEmployees() {
      allEmployees = await apiCall('get_employees');
      
      // Update the hidden select (for backward compatibility)
      const select = document.getElementById('employeeSelect');
      select.innerHTML = '<option value="">Select Employee</option>';
      
      allEmployees.forEach(emp => {
        const option = document.createElement('option');
        option.value = emp.employee_id;
        option.textContent = emp.full_name;
        select.appendChild(option);
      });
    }

    // Load courses
    async function loadCourses() {
      courses = await apiCall('get_courses');
      const courseSelect = document.getElementById('courseSelect');
      const feedbackCourse = document.getElementById('feedbackCourse');
      const pathCourseSelect = document.getElementById('pathCourseSelect');
      
      courseSelect.innerHTML = '<option value="">Select a course...</option>';
      feedbackCourse.innerHTML = '<option value="">General</option>';
      pathCourseSelect.innerHTML = '<option value="">Select a course...</option>';
      
      courses.forEach(course => {
        const option1 = document.createElement('option');
        option1.value = course.course_id;
        option1.textContent = course.title;
        courseSelect.appendChild(option1);
        
        const option2 = document.createElement('option');
        option2.value = course.course_id;
        option2.textContent = course.title;
        feedbackCourse.appendChild(option2);

        const option3 = document.createElement('option');
        option3.value = course.course_id;
        option3.textContent = course.title;
        pathCourseSelect.appendChild(option3);
      });
      
      // Update courses list
      renderCoursesList();
    }

    // Render courses list - UPDATED WITH DELIVERY MODE
    function renderCoursesList() {
      const coursesList = document.getElementById('coursesList');
      const coursesCount = document.getElementById('coursesCount');
      
      coursesCount.textContent = `${courses.length} course${courses.length !== 1 ? 's' : ''}`;
      
      if (!courses || courses.length === 0) {
        coursesList.innerHTML = `
          <div class="col-span-3 text-center py-8 text-gray-500">
            <i class="fas fa-book text-4xl mb-3"></i>
            <p>No courses available</p>
            <p class="text-sm mt-2">Add a new course using the form above</p>
          </div>
        `;
        return;
      }
      
      coursesList.innerHTML = courses.map(course => `
        <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium text-gray-900 truncate">${course.title}</h4>
            <span class="text-sm text-gray-500 whitespace-nowrap">${course.duration || 0}h</span>
          </div>
          <p class="text-sm text-gray-600 mb-3 line-clamp-2">${course.description || 'No description available'}</p>
          <div class="flex justify-between items-center">
            <div class="flex gap-2">
              <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">${course.category || 'General'}</span>
              <span class="text-xs text-blue-500 bg-blue-100 px-2 py-1 rounded">${course.delivery_mode || 'online'}</span>
            </div>
            <div class="flex gap-2">
              <button onclick="editCourse(${course.course_id})" class="text-blue-600 hover:text-blue-800 text-sm" title="Edit course">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="deleteCourse(${course.course_id})" class="text-red-600 hover:text-red-800 text-sm" title="Delete course">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `).join('');
    }

    // Handle course form submission (create/update) - UPDATED WITH DELIVERY MODE
    async function handleCourseSubmit(e) {
      e.preventDefault();
      
      const courseId = document.getElementById('editCourseId').value;
      const courseTitle = document.getElementById('courseTitle').value.trim();
      const courseDescription = document.getElementById('courseDescription').value.trim();
      const courseDuration = document.getElementById('courseDuration').value;
      const courseCategory = document.getElementById('courseCategory').value;
      const courseDeliveryMode = document.getElementById('courseDeliveryMode').value;
      
      if (!courseTitle) {
        alert('Please enter a course title');
        return;
      }
      
      let result;
      if (courseId) {
        // Update existing course
        result = await apiCall('update_course', {
          course_id: courseId,
          title: courseTitle,
          description: courseDescription,
          category: courseCategory,
          duration: parseFloat(courseDuration) || 0,
          delivery_mode: courseDeliveryMode
        });
      } else {
        // Create new course
        result = await apiCall('add_course', {
          title: courseTitle,
          description: courseDescription,
          category: courseCategory,
          duration: parseFloat(courseDuration) || 0,
          delivery_mode: courseDeliveryMode
        });
      }
      
      if (result.success) {
        // Clear form
        document.getElementById('courseForm').reset();
        document.getElementById('editCourseId').value = '';
        document.getElementById('cancelEditCourse').classList.add('hidden');
        document.querySelector('#courseForm button[type="submit"]').innerHTML = '<i class="fas fa-plus mr-2"></i>Add Course';
        
        // Refresh lists
        await loadCourses();
        
        alert(`Course ${courseId ? 'updated' : 'added'} successfully!`);
      } else {
        alert(result.message || `Failed to ${courseId ? 'update' : 'add'} course`);
      }
    }

    // Edit course - UPDATED WITH DELIVERY MODE
    function editCourse(courseId) {
      const course = courses.find(c => c.course_id == courseId);
      if (!course) return;
      
      document.getElementById('editCourseId').value = course.course_id;
      document.getElementById('courseTitle').value = course.title;
      document.getElementById('courseDescription').value = course.description || '';
      document.getElementById('courseDuration').value = course.duration || 0;
      document.getElementById('courseCategory').value = course.category || 'General';
      document.getElementById('courseDeliveryMode').value = course.delivery_mode || 'online';
      
      document.getElementById('cancelEditCourse').classList.remove('hidden');
      document.querySelector('#courseForm button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Update Course';
      
      // Scroll to form
      document.getElementById('courseForm').scrollIntoView({ behavior: 'smooth' });
    }

    // Delete course - FIXED VERSION
    async function deleteCourse(courseId) {
      if (!confirm('Are you sure you want to delete this course? This will also delete all progress and feedback records associated with this course.')) {
        return;
      }
      
      try {
        const result = await apiCall('delete_course', { course_id: courseId });
        
        if (result.success) {
          await loadCourses();
          alert('Course deleted successfully!');
        } else {
          // Show specific error message
          alert('Error: ' + (result.message || 'Failed to delete course'));
          console.error('Delete course error:', result);
        }
      } catch (error) {
        alert('Error deleting course: ' + error.message);
        console.error('Delete course error:', error);
      }
    }

    // Cancel edit course
    function cancelEditCourse() {
      document.getElementById('courseForm').reset();
      document.getElementById('editCourseId').value = '';
      document.getElementById('cancelEditCourse').classList.add('hidden');
      document.querySelector('#courseForm button[type="submit"]').innerHTML = '<i class="fas fa-plus mr-2"></i>Add Course';
    }

    // Load progress
    async function loadProgress() {
      if (!currentEmployeeId) return;
      
      progressData = await apiCall('get_progress', { employee_id: currentEmployeeId });
      updateProgressUI();
    }

    // Update progress UI
    function updateProgressUI() {
      const progressList = document.getElementById('progressList');
      const reportsBody = document.getElementById('reportsBody');
      
      if (!progressData || progressData.length === 0) {
        progressList.innerHTML = `
          <div class="text-center py-8 text-gray-500">
            <i class="fas fa-book-open text-4xl mb-3"></i>
            <p>No course progress found for this employee</p>
          </div>
        `;
        reportsBody.innerHTML = `
          <tr>
            <td colspan="3" class="p-4 text-center text-gray-500">No progress data available</td>
          </tr>
        `;
        return;
      }
      
      // Calculate statistics
      const totalCourses = progressData.length;
      const completedCount = progressData.filter(p => p.status === 'Completed').length;
      const inProgressCount = progressData.filter(p => p.status === 'In Progress').length;
      const overallPercent = totalCourses > 0 ? Math.round((completedCount / totalCourses) * 100) : 0;
      
      // Update statistics
      document.getElementById('overallPercent').textContent = `${overallPercent}%`;
      document.getElementById('totalCourses').textContent = totalCourses;
      document.getElementById('completedCount').textContent = completedCount;
      document.getElementById('inprogressCount').textContent = inProgressCount;
      document.getElementById('progressBar').style.width = `${overallPercent}%`;
      document.getElementById('progressLabel').textContent = `${overallPercent}% Complete`;
      
      // Update progress list
      progressList.innerHTML = progressData.map(progress => `
        <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium text-gray-900">${progress.course_name}</h4>
            <span class="px-2 py-1 text-xs rounded-full ${
              progress.status === 'Completed' ? 'bg-green-100 text-green-800' :
              progress.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
              'bg-gray-100 text-gray-800'
            }">${progress.status}</span>
          </div>
          <div class="text-sm text-gray-600 mb-3">
            <div class="flex justify-between mb-1">
              <span>Started: ${progress.date_started ? new Date(progress.date_started).toLocaleDateString() : 'N/A'}</span>
              <span>Last Updated: ${progress.date_updated ? new Date(progress.date_updated).toLocaleDateString() : 'N/A'}</span>
            </div>
            <div class="flex justify-between items-center">
              <span>Completion: ${progress.completion_percent}%</span>
              <div class="w-24 bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: ${progress.completion_percent}%"></div>
              </div>
            </div>
          </div>
        </div>
      `).join('');
      
      // Update reports table
      reportsBody.innerHTML = progressData.map(progress => `
        <tr class="border-b hover:bg-gray-50">
          <td class="p-3 font-medium text-gray-900">${progress.course_name}</td>
          <td class="p-3">
            <span class="px-2 py-1 text-xs rounded-full ${
              progress.status === 'Completed' ? 'bg-green-100 text-green-800' :
              progress.status === 'In Progress' ? 'bg-blue-100 text-blue-800' :
              'bg-gray-100 text-gray-800'
            }">${progress.status}</span>
          </td>
          <td class="p-3 font-medium">${progress.completion_percent}%</td>
        </tr>
      `).join('');
    }

    // Update progress
    async function updateProgress() {
      if (!currentEmployeeId) {
        alert('Please select an employee first');
        return;
      }
      
      const courseSelect = document.getElementById('courseSelect');
      const statusSelect = document.getElementById('statusSelect');
      
      const courseId = courseSelect.value;
      const status = statusSelect.value;
      
      if (!courseId) {
        alert('Please select a course');
        return;
      }
      
      const result = await apiCall('update_progress', {
        employee_id: currentEmployeeId,
        course_id: courseId,
        status: status
      });
      
      if (result.success) {
        await loadProgress();
        alert('Progress updated successfully!');
      } else {
        alert('Failed to update progress');
      }
    }

    // Load learning paths
    async function loadLearningPaths() {
      learningPaths = await apiCall('get_learning_paths');
      renderLearningPaths();
    }

    // Render learning paths
    function renderLearningPaths() {
      const pathsList = document.getElementById('pathsList');
      
      if (!learningPaths || learningPaths.length === 0) {
        pathsList.innerHTML = `
          <div class="col-span-2 text-center py-8 text-gray-500">
            <i class="fas fa-road text-4xl mb-3"></i>
            <p>No learning paths created yet</p>
          </div>
        `;
        return;
      }
      
      pathsList.innerHTML = learningPaths.map(path => `
        <div class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium text-gray-900">${path.title}</h4>
            <span class="text-sm text-gray-500">${path.duration_total || 0}h</span>
          </div>
          <p class="text-sm text-gray-600 mb-3">${path.description || 'No description'}</p>
          <div class="flex justify-between items-center">
            <div class="text-xs text-gray-500">
              ${path.courses_count || 0} courses • Created by: ${path.hr_name || 'System'}
            </div>
            <div class="flex gap-2">
              <button onclick="editLearningPath(${path.path_id})" class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="managePathCourses(${path.path_id})" class="text-green-600 hover:text-green-800 text-sm">
                <i class="fas fa-book"></i>
              </button>
              <button onclick="deleteLearningPath(${path.path_id})" class="text-red-600 hover:text-red-800 text-sm">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `).join('');
    }

    // Handle path form submission (create/update)
    async function handlePathSubmit(e) {
      e.preventDefault();
      
      const pathId = document.getElementById('editPathId').value;
      const pathName = document.getElementById('pathName').value.trim();
      const pathDesc = document.getElementById('pathDesc').value.trim();
      const pathHours = document.getElementById('pathHours').value;
      
      if (!pathName) {
        alert('Please enter a path name');
        return;
      }
      
      let result;
      if (pathId) {
        // Update existing path
        result = await apiCall('update_learning_path', {
          path_id: pathId,
          title: pathName,
          description: pathDesc,
          duration_total: parseFloat(pathHours) || 0
        });
      } else {
        // Create new path
        result = await apiCall('add_learning_path', {
          title: pathName,
          description: pathDesc,
          duration_total: parseFloat(pathHours) || 0
        });
      }
      
      if (result.success) {
        // Clear form
        document.getElementById('pathForm').reset();
        document.getElementById('editPathId').value = '';
        document.getElementById('cancelEditPath').classList.add('hidden');
        document.querySelector('#pathForm button[type="submit"]').innerHTML = '<i class="fas fa-plus mr-2"></i>Create Path';
        
        // Refresh list
        await loadLearningPaths();
        
        alert(`Learning path ${pathId ? 'updated' : 'created'} successfully!`);
      } else {
        alert(`Failed to ${pathId ? 'update' : 'create'} learning path`);
      }
    }

    // Edit learning path
    function editLearningPath(pathId) {
      const path = learningPaths.find(p => p.path_id == pathId);
      if (!path) return;
      
      document.getElementById('editPathId').value = path.path_id;
      document.getElementById('pathName').value = path.title;
      document.getElementById('pathDesc').value = path.description || '';
      document.getElementById('pathHours').value = path.duration_total || 0;
      
      document.getElementById('cancelEditPath').classList.remove('hidden');
      document.querySelector('#pathForm button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Update Path';
      
      document.getElementById('pathCoursesSection').classList.add('hidden');
      currentPathId = '';
      
      document.getElementById('pathForm').scrollIntoView({ behavior: 'smooth' });
    }

    // Manage courses in learning path
    async function managePathCourses(pathId) {
      currentPathId = pathId;
      document.getElementById('pathCoursesSection').classList.remove('hidden');
      await loadPathCourses(pathId);
      
      // Reset view state
      const viewBtn = document.getElementById('viewPathCoursesBtn');
      const pathCoursesList = document.getElementById('pathCoursesList');
      viewBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>View All Courses';
      viewBtn.classList.remove('bg-yellow-600');
      viewBtn.classList.add('bg-green-600');
      pathCoursesList.classList.remove('max-h-screen');
      pathCoursesList.classList.add('max-h-64');
      
      document.getElementById('pathCoursesSection').scrollIntoView({ behavior: 'smooth' });
    }
    // Load courses for a learning path
    async function loadPathCourses(pathId) {
      pathCourses = await apiCall('get_path_courses', { path_id: pathId });
      renderPathCourses();
    }

    // Render courses in learning path
function renderPathCourses() {
  const pathCoursesList = document.getElementById('pathCoursesList');
  
  if (!pathCourses || pathCourses.length === 0) {
    pathCoursesList.innerHTML = `
      <div class="text-center py-8 text-gray-500">
        <i class="fas fa-book-open text-2xl mb-2"></i>
        <p>No courses in this path</p>
        <p class="text-sm mt-1">Add courses using the form above</p>
      </div>
    `;
    return;
  }
  
  pathCoursesList.innerHTML = pathCourses.map(course => `
    <div class="border rounded-lg p-3 hover:bg-gray-50 transition-colors">
      <div class="flex justify-between items-start mb-2">
        <div class="flex-1">
          <h5 class="font-medium text-gray-900">${course.title}</h5>
          <p class="text-sm text-gray-600 mt-1">${course.description || 'No description available'}</p>
          <div class="flex flex-wrap gap-2 mt-2">
            <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800">
              ${course.duration || 0}h
            </span>
            <span class="text-xs px-2 py-1 rounded bg-purple-100 text-purple-800">
              ${course.category || 'General'}
            </span>
            <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800">
              ${course.delivery_mode || 'online'}
            </span>
          </div>
        </div>
        <div class="flex gap-2 ml-4">
          <span class="text-xs px-2 py-1 rounded ${
            course.is_required ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800'
          }">
            ${course.is_required ? 'Required' : 'Optional'}
          </span>
        </div>
      </div>
      <div class="flex justify-between items-center text-sm text-gray-500 border-t pt-2">
        <div>
          <span class="font-medium">Order:</span> ${course.course_order || 1}
        </div>
        <button onclick="removeCourseFromPath(${course.link_id})" class="text-red-600 hover:text-red-800 text-sm flex items-center">
          <i class="fas fa-times mr-1"></i> Remove
        </button>
      </div>
    </div>
  `).join('');
}

    // Add course to learning path
    async function addCourseToPath() {
      if (!currentPathId) {
        alert('Please select a learning path first');
        return;
      }
      
      const courseId = document.getElementById('pathCourseSelect').value;
      
      if (!courseId) {
        alert('Please select a course');
        return;
      }
      
      const result = await apiCall('add_course_to_path', {
        path_id: currentPathId,
        course_id: courseId,
        course_order: (pathCourses.length + 1),
        is_required: 1
      });
      
      if (result.success) {
        document.getElementById('pathCourseSelect').value = '';
        await loadPathCourses(currentPathId);
        await loadLearningPaths();
        alert('Course added successfully!');
      } else {
        alert(result.message || 'Failed to add course');
      }
    }

    // Remove course from learning path
    async function removeCourseFromPath(linkId) {
      if (!confirm('Remove this course from the path?')) {
        return;
      }
      
      const result = await apiCall('remove_course_from_path', { link_id: linkId });
      
      if (result.success) {
        await loadPathCourses(currentPathId);
        await loadLearningPaths();
        alert('Course removed successfully!');
      } else {
        alert('Failed to remove course');
      }
    }

    // Delete learning path
    async function deleteLearningPath(pathId) {
      if (!confirm('Delete this learning path?')) {
        return;
      }
      
      const result = await apiCall('delete_learning_path', { path_id: pathId });
      
      if (result.success) {
        if (currentPathId === pathId) {
          document.getElementById('pathCoursesSection').classList.add('hidden');
          currentPathId = '';
        }
        await loadLearningPaths();
        alert('Learning path deleted successfully!');
      } else {
        alert('Failed to delete learning path');
      }
    }

    // Cancel edit path
    function cancelEditPath() {
      document.getElementById('pathForm').reset();
      document.getElementById('editPathId').value = '';
      document.getElementById('cancelEditPath').classList.add('hidden');
      document.querySelector('#pathForm button[type="submit"]').innerHTML = '<i class="fas fa-plus mr-2"></i>Create Path';
      document.getElementById('pathCoursesSection').classList.add('hidden');
      currentPathId = '';
      
      // Reset view button state
      const viewBtn = document.getElementById('viewPathCoursesBtn');
      const pathCoursesList = document.getElementById('pathCoursesList');
      viewBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>View All Courses';
      viewBtn.classList.remove('bg-yellow-600');
      viewBtn.classList.add('bg-green-600');
      pathCoursesList.classList.remove('max-h-screen');
      pathCoursesList.classList.add('max-h-64');
    }

    // Load feedback
    async function loadFeedback() {
      if (!currentEmployeeId) return;
      
      feedbackData = await apiCall('get_feedback', { employee_id: currentEmployeeId });
      renderFeedback();
    }

    // Render feedback
    function renderFeedback() {
      const feedbackList = document.getElementById('feedbackList');
      
      if (!feedbackData || feedbackData.length === 0) {
        feedbackList.innerHTML = `
          <li class="text-center py-4 text-gray-500">
            <i class="fas fa-comment-slash text-2xl mb-2"></i>
            <p>No feedback submitted</p>
          </li>
        `;
        return;
      }
      
      feedbackList.innerHTML = feedbackData.map(item => `
        <li class="border rounded-lg p-4 hover:bg-gray-50 transition-colors">
          <div class="flex justify-between items-start mb-2">
            <h4 class="font-medium text-gray-900">${item.course_name || 'General Feedback'}</h4>
            <div class="flex items-center">
              ${Array.from({length: 5}, (_, i) => 
                `<i class="fas fa-star ${i < item.rating ? 'text-yellow-400' : 'text-gray-300'}"></i>`
              ).join('')}
            </div>
          </div>
          <p class="text-gray-600 mb-2">${item.comment}</p>
          <div class="flex justify-between items-center">
            <div class="text-xs text-gray-500">
              ${new Date(item.date_given).toLocaleDateString()}
            </div>
            <div class="flex gap-2">
              <button onclick="editFeedback(${item.feedback_id})" class="text-blue-600 hover:text-blue-800 text-sm">
                <i class="fas fa-edit"></i>
              </button>
              <button onclick="deleteFeedback(${item.feedback_id})" class="text-red-600 hover:text-red-800 text-sm">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </li>
      `).join('');
    }

    // Handle feedback form submission (create/update)
    async function handleFeedbackSubmit(e) {
      e.preventDefault();
      
      if (!currentEmployeeId) {
        alert('Please select an employee first');
        return;
      }
      
      const feedbackId = document.getElementById('editFeedbackId').value;
      const feedbackText = document.getElementById('feedbackText').value.trim();
      const feedbackCourse = document.getElementById('feedbackCourse').value;
      const feedbackRating = document.getElementById('feedbackRating').value;
      
      if (!feedbackText) {
        alert('Please enter feedback');
        return;
      }
      
      let result;
      if (feedbackId) {
        // Update existing feedback
        result = await apiCall('update_feedback', {
          feedback_id: feedbackId,
          comment: feedbackText,
          rating: parseInt(feedbackRating)
        });
      } else {
        // Create new feedback
        result = await apiCall('add_feedback', {
          employee_id: currentEmployeeId,
          course_id: feedbackCourse || null,
          comment: feedbackText,
          rating: parseInt(feedbackRating)
        });
      }
      
      if (result.success) {
        // Clear form
        document.getElementById('feedbackForm').reset();
        document.getElementById('editFeedbackId').value = '';
        document.getElementById('cancelEditFeedback').classList.add('hidden');
        document.querySelector('#feedbackForm button[type="submit"]').innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit';
        
        // Refresh list
        await loadFeedback();
        
        alert(`Feedback ${feedbackId ? 'updated' : 'submitted'} successfully!`);
      } else {
        alert(`Failed to ${feedbackId ? 'update' : 'submit'} feedback`);
      }
    }

    // Edit feedback
    function editFeedback(feedbackId) {
      const feedback = feedbackData.find(f => f.feedback_id == feedbackId);
      if (!feedback) return;
      
      document.getElementById('editFeedbackId').value = feedback.feedback_id;
      document.getElementById('feedbackText').value = feedback.comment;
      document.getElementById('feedbackCourse').value = feedback.course_id || '';
      document.getElementById('feedbackRating').value = feedback.rating;
      
      document.getElementById('cancelEditFeedback').classList.remove('hidden');
      document.querySelector('#feedbackForm button[type="submit"]').innerHTML = '<i class="fas fa-save mr-2"></i>Update Feedback';
      
      // Scroll to form
      document.getElementById('feedbackForm').scrollIntoView({ behavior: 'smooth' });
    }

    // Delete feedback
    async function deleteFeedback(feedbackId) {
      if (!confirm('Delete this feedback?')) {
        return;
      }
      
      const result = await apiCall('delete_feedback', { feedback_id: feedbackId });
      
      if (result.success) {
        await loadFeedback();
        alert('Feedback deleted successfully!');
      } else {
        alert('Failed to delete feedback');
      }
    }

    // Cancel edit feedback
    function cancelEditFeedback() {
      document.getElementById('feedbackForm').reset();
      document.getElementById('editFeedbackId').value = '';
      document.getElementById('cancelEditFeedback').classList.add('hidden');
      document.querySelector('#feedbackForm button[type="submit"]').innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit';
    }

    // Load learning reports for current employee
    async function loadLearningReports() {
      if (!currentEmployeeId) {
        // If no employee selected, show empty state
        const reportsBody = document.getElementById('learningReportsBody');
        reportsBody.innerHTML = `
          <tr>
            <td colspan="5" class="p-4 text-center text-gray-500">Select an employee to view their learning reports</td>
          </tr>
        `;
        return;
      }
      
      learningReports = await apiCall('get_learning_reports', { employee_id: currentEmployeeId });
      renderLearningReports();
    }

    // Render learning reports
    function renderLearningReports() {
      const reportsBody = document.getElementById('learningReportsBody');
      
      if (!learningReports || learningReports.length === 0) {
        reportsBody.innerHTML = `
          <tr>
            <td colspan="5" class="p-4 text-center text-gray-500">No learning reports available for this employee</td>
          </tr>
        `;
        return;
      }
      
      reportsBody.innerHTML = learningReports.map(report => `
        <tr class="border-b hover:bg-gray-50">
          <td class="p-3 text-gray-900">${report.employee_name}</td>
          <td class="p-3 text-gray-700">${report.summary || 'No summary available'}</td>
          <td class="p-3 text-gray-700">${new Date(report.generated_date).toLocaleDateString()}</td>
          <td class="p-3 text-gray-700">${report.total_courses_completed || 0}</td>
          <td class="p-3">
            <span class="px-2 py-1 rounded text-xs font-medium ${
              report.overall_rating >= 4 ? 'bg-green-100 text-green-800' :
              report.overall_rating >= 3 ? 'bg-yellow-100 text-yellow-800' :
              'bg-red-100 text-red-800'
            }">${report.overall_rating || 0}/5</span>
          </td>
        </tr>
      `).join('');
    }

    // Generate learning report
    async function generateLearningReport() {
      if (!currentEmployeeId) {
        alert('Please select an employee first');
        return;
      }
      
      const result = await apiCall('generate_report', { 
        employee_id: currentEmployeeId 
      });
      
      if (result.success) {
        await loadLearningReports();
        alert('Learning report generated successfully!');
      } else {
        alert('Failed to generate learning report: ' + (result.message || 'Unknown error'));
      }
    }
          // View all courses in path (expanded view)
      function viewAllPathCourses() {
        const pathCoursesList = document.getElementById('pathCoursesList');
        pathCoursesList.classList.toggle('max-h-64');
        pathCoursesList.classList.toggle('max-h-screen');
        
        const viewBtn = document.getElementById('viewPathCoursesBtn');
        if (pathCoursesList.classList.contains('max-h-screen')) {
          viewBtn.innerHTML = '<i class="fas fa-eye-slash mr-2"></i>Collapse View';
          viewBtn.classList.remove('bg-green-600');
          viewBtn.classList.add('bg-yellow-600');
        } else {
          viewBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>View All Courses';
          viewBtn.classList.remove('bg-yellow-600');
          viewBtn.classList.add('bg-green-600');
        }
      }

      // Cancel managing path courses
      function cancelManagePath() {
        document.getElementById('pathCoursesSection').classList.add('hidden');
        currentPathId = '';
        
        // Reset view button state
        const viewBtn = document.getElementById('viewPathCoursesBtn');
        const pathCoursesList = document.getElementById('pathCoursesList');
        viewBtn.innerHTML = '<i class="fas fa-eye mr-2"></i>View All Courses';
        viewBtn.classList.remove('bg-yellow-600');
        viewBtn.classList.add('bg-green-600');
        pathCoursesList.classList.remove('max-h-screen');
        pathCoursesList.classList.add('max-h-64');
      }
  </script>
</body>
</html>