-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 04, 2025 at 06:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hr2_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `benefits`
--

CREATE TABLE `benefits` (
  `benefit_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `benefit_type` varchar(50) NOT NULL,
  `provider` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_pool`
--

CREATE TABLE `candidate_pool` (
  `candidate_id` int(11) NOT NULL,
  `fk_plan_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `potential_score` int(11) NOT NULL,
  `remarks` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_pool`
--

INSERT INTO `candidate_pool` (`candidate_id`, `fk_plan_id`, `fk_employee_id`, `potential_score`, `remarks`) VALUES
(1, 1, 11, 99, '100'),
(2, 1, 10, 100, '99');

-- --------------------------------------------------------

--
-- Table structure for table `competency`
--

CREATE TABLE `competency` (
  `competency_id` int(11) NOT NULL,
  `competency_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `is_core_competency` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(100) NOT NULL,
  `competency_category` int(11) NOT NULL,
  `competency_type` varchar(50) NOT NULL,
  `proficiency_levels` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency`
--

INSERT INTO `competency` (`competency_id`, `competency_name`, `description`, `is_core_competency`, `is_active`, `created_date`, `created_by`, `competency_category`, `competency_type`, `proficiency_levels`) VALUES
(2, 'Technical Skills', 'Ability to apply technical knowledge and expertise', 1, 1, '2025-09-07 15:05:48', 'system', 1, 'Soft Skill', 'Intermediate'),
(3, 'Communication', 'Effective verbal and written communication', 1, 1, '2025-09-07 15:05:48', 'system', 2, 'Soft Skill', 'Master'),
(4, 'Leadership', 'Ability to guide and influence a team', 0, 1, '2025-09-07 15:05:48', 'system', 1, 'Hard Skill', 'Intermediate'),
(5, 'Human rights ', 'asdasdasdasdadasdasdasdasda asda asda dasdas asd asd asd asd asd asd asd asd asd as asd asdas as d', 1, 0, '2025-09-29 17:06:03', 'Admin', 1, 'Technical', 'Expert'),
(6, 'Human rights ', 'Human rights ', 1, 1, '2025-09-29 18:31:13', 'Admin', 2, 'Hard Skill', 'Beginner'),
(7, 'Edit skill', 'basic edit skill', 1, 1, '2025-09-29 18:42:08', 'Admin', 2, 'Soft Skill', 'beginner'),
(8, 'pat gayming ', 'malupet maglaro \r\n', 1, 1, '2025-09-29 18:42:40', 'Admin', 2, 'Hard Skill', 'Expert'),
(9, 'basa gaymings', 'sana totoo na ito', 1, 0, '2025-09-29 18:43:12', 'Admin', 1, 'Soft Skill', 'Advanced'),
(10, 'shearwin gay ming sss', 'ashisss', 1, 0, '2025-09-29 18:52:49', 'Admin', 1, 'Hard Skill', 'Basic'),
(11, 'shearwin gay ming ', 'ashi', 1, 0, '2025-09-29 19:21:02', 'Admin', 2, 'Hard Skill', 'beginners '),
(12, 'Patrick dela cruz sssssssss', 'cajetasssssss', 1, 0, '2025-09-30 12:31:25', 'Admin', 3, 'Soft Skill', 'Master');

-- --------------------------------------------------------

--
-- Table structure for table `competency_assessment`
--

CREATE TABLE `competency_assessment` (
  `assessment_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_competency_id` int(11) NOT NULL,
  `assessment_type` varchar(50) NOT NULL,
  `assessment_date` date NOT NULL,
  `proficiency_score` int(11) NOT NULL,
  `level_achieved` int(11) NOT NULL,
  `assessment_method` varchar(100) NOT NULL,
  `feedback` text NOT NULL,
  `recommendations` text NOT NULL,
  `next_assessment_due` date NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_assessment`
--

INSERT INTO `competency_assessment` (`assessment_id`, `fk_employee_id`, `fk_competency_id`, `assessment_type`, `assessment_date`, `proficiency_score`, `level_achieved`, `assessment_method`, `feedback`, `recommendations`, `next_assessment_due`, `status`) VALUES
(318, 10, 2, 'Self Assessment', '2025-10-15', 4, 5, 'shitt', 'asdasdds', 'asdasdas', '2025-10-01', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `competency_category`
--

CREATE TABLE `competency_category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category_weight` decimal(10,0) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_category`
--

INSERT INTO `competency_category` (`category_id`, `category_name`, `description`, `category_weight`, `is_active`, `created_date`) VALUES
(1, 'Core Skills', 'Basic company-wide skills', 100, 1, '2025-09-03 05:25:40'),
(2, 'Tech Skills ', 'Basic Tech skills', 50, 0, '2025-09-29 18:29:51'),
(3, 'Tech Skills', 'Basic tech skills', 100, 1, '2025-09-30 23:10:48'),
(4, 'basketball ssss', 'magaling dapat sdsadsda ', 99, 1, '2025-10-02 14:18:36'),
(5, 'shitt', 'shittt', 100, 1, '2025-10-02 18:44:42');

-- --------------------------------------------------------

--
-- Table structure for table `competency_gap`
--

CREATE TABLE `competency_gap` (
  `gap_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_competency_id` int(11) NOT NULL,
  `current_level` int(11) NOT NULL,
  `required_level` int(11) NOT NULL,
  `gap_score` decimal(10,0) NOT NULL,
  `priority_level` enum('Low','Medium','High') NOT NULL,
  `action_plan` text NOT NULL,
  `target_compensation_date` date NOT NULL,
  `status` enum('Open','In Progress','Resolved','Closed') NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_gap`
--

INSERT INTO `competency_gap` (`gap_id`, `fk_employee_id`, `fk_competency_id`, `current_level`, `required_level`, `gap_score`, `priority_level`, `action_plan`, `target_compensation_date`, `status`, `created_date`) VALUES
(6, 10, 3, 4, 4, 2, 'Medium', 'asdasd', '2025-10-16', 'In Progress', '2025-10-02 18:45:01');

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `course_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `duration` int(11) NOT NULL,
  `delivery_mode` varchar(100) NOT NULL DEFAULT '''online'', ''in-person'''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`course_id`, `title`, `description`, `category`, `duration`, `delivery_mode`) VALUES
(1, 'Leadership Basics', 'Introductory course on leadership skills for new managers', 'Management', 20, 'Online'),
(2, 'Advanced Data Analytics', 'Covers machine learning, AI, and advanced statistical methods', 'Data Science', 40, 'Hybrid'),
(3, 'Workplace Communication', 'Develop effective communication skills for professional settings', 'Soft Skills', 15, 'online'),
(4, 'Project Management Essentials', 'Learn project planning, execution, and monitoring', 'Management', 25, 'in-person'),
(5, 'Time Management', 'Strategies and tools to boost productivity and efficiency', 'Personal Development', 12, 'Online'),
(6, 'Emotional Intelligence', 'Training to improve interpersonal and leadership skills', 'Soft Skills', 18, 'Hybrid'),
(7, 'Leadership Training', '', 'Technical', 10, 'online'),
(8, 'teach skills', '', 'General', 0, 'online'),
(14, 'Communication Skills', 'Effective workplace communication', 'Soft Skills', 8, 'online'),
(15, 'Leadership Fundamentals', 'Basic leadership principles', 'Leadership', 12, 'hybrid'),
(16, 'Project Management', 'Manage projects effectively', 'Management', 10, 'online'),
(17, 'PHP Basics', 'Introduction to PHP programming', 'Technical', 20, 'online'),
(18, 'Database Design', 'MySQL database management', 'Technical', 15, 'online'),
(19, 'HTML & CSS', 'Web development fundamentals', 'Technical', 15, 'online'),
(20, 'JavaScript', 'Frontend programming', 'Technical', 20, 'online'),
(21, 'PHP & MySQL', 'Backend development', 'Technical', 25, 'online'),
(22, 'Leadership Basics', 'Introduction to leadership', 'Management', 10, 'hybrid');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `department_id` int(11) NOT NULL,
  `department_code` varchar(20) NOT NULL,
  `department_name` varchar(100) NOT NULL,
  `fk_hr_staff_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`department_id`, `department_code`, `department_name`, `fk_hr_staff_id`) VALUES
(8, 'HR1', 'HR11', 1),
(17, 'HR2', 'HR12', 1),
(18, 'HR3', 'HR13', 1),
(19, 'HR4', 'HR14', 1),
(20, 'HR5', 'HR15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `development_plan`
--

CREATE TABLE `development_plan` (
  `dev_plan_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_gap_id` int(11) NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `start_date` date NOT NULL,
  `target_completion_date` date NOT NULL,
  `actual_completion_date` date NOT NULL,
  `development_methods` text NOT NULL,
  `status` enum('Planned','Ongoing','Completed','Cancelled') NOT NULL DEFAULT 'Planned',
  `progress_status` enum('Not Started','In Progress','Completed','Delayed') NOT NULL DEFAULT 'Not Started',
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `development_plan`
--

INSERT INTO `development_plan` (`dev_plan_id`, `fk_employee_id`, `fk_gap_id`, `plan_name`, `description`, `start_date`, `target_completion_date`, `actual_completion_date`, `development_methods`, `status`, `progress_status`, `created_date`) VALUES
(12, 10, 6, 'Social exp', 'asdasd', '2025-10-24', '2025-10-21', '2025-10-21', 'shitttttt', 'Completed', 'In Progress', '2025-10-02 18:45:22');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `hire_date` date NOT NULL,
  `employment_status` enum('Active','Inactive','Terminated') NOT NULL DEFAULT 'Active',
  `fk_department_id` int(11) NOT NULL,
  `fk_position_id` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `first_name`, `last_name`, `email`, `phone_number`, `hire_date`, `employment_status`, `fk_department_id`, `fk_position_id`, `created_date`, `address`) VALUES
(10, 'John', 'Patrick Dela Cruz', 'cajetapatrick1@gmail.com1', '09128884380', '2025-10-08', 'Active', 8, 1, '2025-10-02 18:43:21', 'manilaaa'),
(11, 'Anasel', 'Portes', 'portes@gmail.com', '0912312321', '2025-10-16', 'Active', 8, 1, '2025-10-02 18:52:43', 'hello po'),
(12, 'Sean Allen ', 'Salopaso', 'sean@gmail.com', '09317949540', '2025-10-04', 'Active', 18, 1, '2025-10-03 13:48:46', 'manila'),
(19, 'Josh Henrick ', 'Catchillar', 'catchillar@gmail.com', '09458963896', '2025-10-04', 'Active', 17, 1, '2025-10-04 13:55:10', 'Manila ');

-- --------------------------------------------------------

--
-- Table structure for table `employee_competency`
--

CREATE TABLE `employee_competency` (
  `emp_competency_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_competency_id` int(11) NOT NULL,
  `current_level` int(11) NOT NULL,
  `target_level` int(11) NOT NULL,
  `development_priority` varchar(20) NOT NULL,
  `asses_by` varchar(100) NOT NULL,
  `last_assessed_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_competency`
--

INSERT INTO `employee_competency` (`emp_competency_id`, `fk_employee_id`, `fk_competency_id`, `current_level`, `target_level`, `development_priority`, `asses_by`, `last_assessed_date`) VALUES
(20, 10, 2, 5, 5, 'Medium', 'System', '2025-10-03'),
(21, 10, 6, 2, 4, 'High', 'HR ', '2025-10-24'),
(22, 11, 3, 1, 1, 'Medium', 'Managers', '2025-10-16');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_course_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `rating` int(11) NOT NULL,
  `date_given` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `fk_employee_id`, `fk_course_id`, `comment`, `rating`, `date_given`) VALUES
(3, 1, 1, 'Great introduction to leadership, very engaging!', 5, '2025-09-05 14:30:00'),
(4, 1, 2, 'Challenging but useful, especially the hands-on exercises.', 5, '2025-09-29 02:22:13'),
(5, 1, 3, 'Not started yet, but looking forward to it.', 5, '2025-09-29 02:22:08'),
(6, 1, 4, 'Excellent coverage of project management fundamentals.', 5, '2025-08-25 16:45:00'),
(7, 1, 5, 'Good course but could use more real-world examples.', 5, '2025-09-29 02:22:03'),
(8, 6, 2, 'mahal mo ba si patrick ????', 5, '2025-09-27 22:49:42'),
(9, 7, 6, 'ang pogi ni josh dapat ipromote siya agad para masolo ko na', 5, '2025-09-22 03:29:51'),
(10, 4, 0, 'All round', 5, '2025-09-23 19:04:39'),
(11, 4, 5, 'Not all but almost', 4, '2025-09-23 19:05:26'),
(12, 4, 7, 'Good for leadership', 3, '2025-09-23 20:58:03'),
(13, 4, 2, 'wehhh sana legit gumana', 4, '2025-09-27 03:38:30'),
(15, 6, 6, 'clingy ang sweet sa akin kapag may needs sweetttttssss', 4, '2025-09-27 23:46:29'),
(18, 10, 2, 'dsadadasdasd', 5, '2025-10-03 02:46:39'),
(19, 10, 0, 'asdasdasdd', 5, '2025-10-03 02:46:41'),
(20, 10, 0, 'asdasdasasasd', 5, '2025-10-03 02:46:44'),
(21, 10, 1, 'asdasdasdas', 5, '2025-10-03 02:46:48');

-- --------------------------------------------------------

--
-- Table structure for table `hr_staff`
--

CREATE TABLE `hr_staff` (
  `hr_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_staff`
--

INSERT INTO `hr_staff` (`hr_id`, `name`, `email`, `department`) VALUES
(1, 'Admin HR', 'admin@example.com', 'Human Resources');

-- --------------------------------------------------------

--
-- Table structure for table `learning_path`
--

CREATE TABLE `learning_path` (
  `path_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `duration_total` int(11) NOT NULL,
  `fk_hr_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_path`
--

INSERT INTO `learning_path` (`path_id`, `title`, `description`, `duration_total`, `fk_hr_id`) VALUES
(1, 'Leadership Basics', 'Introductory course for new managers', 20, 1),
(5, 'Tech support', 'pogi at masarap', 0, 1),
(6, 'Skill support', 'all a round', 0, 1),
(13, 'Course', 'pogi', 20, 1),
(14, 'Management Training', 'Comprehensive management skills development', 30, 1),
(15, 'Technical Development', 'IT and technical skills enhancement', 35, 1),
(16, 'Professional Skills', 'Essential workplace competencies', 20, 1),
(17, 'Web Development Path', 'Become a full-stack developer sw', 60, 1),
(18, 'Management Training', 'Leadership and management skills', 40, 1);

-- --------------------------------------------------------

--
-- Table structure for table `learning_path_courses`
--

CREATE TABLE `learning_path_courses` (
  `link_id` int(11) NOT NULL,
  `fk_path_id` int(11) NOT NULL,
  `fk_course_id` int(11) NOT NULL,
  `course_order` int(11) DEFAULT 1,
  `is_required` tinyint(1) DEFAULT 1,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_path_courses`
--

INSERT INTO `learning_path_courses` (`link_id`, `fk_path_id`, `fk_course_id`, `course_order`, `is_required`, `created_date`) VALUES
(33, 17, 19, 1, 1, '2025-09-28 13:48:48'),
(34, 17, 20, 2, 1, '2025-09-28 13:48:48'),
(39, 5, 5, 2, 1, '2025-09-28 15:37:41'),
(43, 17, 14, 3, 1, '2025-09-28 18:58:01'),
(44, 17, 2, 4, 1, '2025-09-28 18:58:24'),
(47, 13, 2, 1, 1, '2025-09-28 20:19:03'),
(48, 13, 18, 2, 1, '2025-09-28 20:19:07'),
(49, 13, 20, 3, 1, '2025-09-28 20:19:10'),
(50, 13, 17, 4, 1, '2025-09-28 20:19:19');

-- --------------------------------------------------------

--
-- Table structure for table `learning_report`
--

CREATE TABLE `learning_report` (
  `report_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `summary` text NOT NULL,
  `generated_date` datetime NOT NULL,
  `total_courses_completed` int(11) NOT NULL,
  `overall_rating` decimal(3,2) DEFAULT NULL,
  `average_completion_rate` decimal(5,2) DEFAULT NULL,
  `report_type` enum('Monthly','Quarterly','Annual','Custom') DEFAULT 'Monthly'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `learning_report`
--

INSERT INTO `learning_report` (`report_id`, `fk_employee_id`, `summary`, `generated_date`, `total_courses_completed`, `overall_rating`, `average_completion_rate`, `report_type`) VALUES
(12, 10, 'Learning Report for Dela Cruz  John Patrick . Completed 3 out of 3 courses. Average completion rate: 100%. Overall feedback rating: 0/5.', '2025-10-03 02:46:14', 3, 0.00, 100.00, 'Monthly'),
(13, 10, 'Learning Report for Dela Cruz  John Patrick . Completed 3 out of 3 courses. Average completion rate: 100%. Overall feedback rating: 5/5.', '2025-10-03 02:46:53', 3, 5.00, 100.00, 'Monthly');

-- --------------------------------------------------------

--
-- Table structure for table `leave_request`
--

CREATE TABLE `leave_request` (
  `leave_request_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_request`
--

INSERT INTO `leave_request` (`leave_request_id`, `fk_employee_id`, `type`, `start_date`, `end_date`, `status`) VALUES
(1, 10, 'sick', '2025-10-13', '2025-10-13', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `payroll`
--

CREATE TABLE `payroll` (
  `payroll_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `salary` decimal(10,0) NOT NULL,
  `pay_date` date NOT NULL,
  `deductions` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `position`
--

CREATE TABLE `position` (
  `position_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `required_skills` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position`
--

INSERT INTO `position` (`position_id`, `title`, `required_skills`) VALUES
(1, 'Software Engineer', 'Programming, Problem Solvings'),
(2, 'GOOD SKILL', 'hell yeah');

-- --------------------------------------------------------

--
-- Table structure for table `position_competency`
--

CREATE TABLE `position_competency` (
  `position_competency_id` int(11) NOT NULL,
  `fk_position_id` int(11) NOT NULL,
  `fk_competency_id` int(11) NOT NULL,
  `required_level` int(11) NOT NULL,
  `importance_weight` decimal(10,0) NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `minimum_score` int(11) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `position_competency`
--

INSERT INTO `position_competency` (`position_competency_id`, `fk_position_id`, `fk_competency_id`, `required_level`, `importance_weight`, `is_mandatory`, `minimum_score`, `created_date`) VALUES
(3, 1, 7, 4, 54, 1, 4, '2025-09-30 14:09:33'),
(5, 1, 6, 5, 100, 1, 5, '2025-09-30 15:45:43'),
(7, 1, 9, 3, 100, 1, 3, '2025-09-30 15:46:24');

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `progress_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_course_id` int(11) NOT NULL,
  `status` varchar(20) NOT NULL,
  `completion_percent` decimal(10,0) NOT NULL,
  `date_started` datetime DEFAULT NULL,
  `date_updated` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`progress_id`, `fk_employee_id`, `fk_course_id`, `status`, `completion_percent`, `date_started`, `date_updated`) VALUES
(31, 10, 18, 'Completed', 100, '2025-10-03 02:45:58', '2025-10-03 02:45:58'),
(32, 10, 1, 'Completed', 100, '2025-10-03 02:46:04', '2025-10-03 02:46:04'),
(33, 10, 17, 'Completed', 100, '2025-10-03 02:46:10', '2025-10-03 02:46:10'),
(34, 11, 2, 'Not Started', 0, '2025-10-04 20:15:13', '2025-10-04 20:15:13');

-- --------------------------------------------------------

--
-- Table structure for table `succession_plan`
--

CREATE TABLE `succession_plan` (
  `plan_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `position_target` varchar(100) NOT NULL,
  `readiness_level` varchar(50) NOT NULL,
  `last_reviewed` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `succession_plan`
--

INSERT INTO `succession_plan` (`plan_id`, `fk_employee_id`, `position_target`, `readiness_level`, `last_reviewed`) VALUES
(1, 10, 'HR ', '1-2 Years', '2025-10-12');

-- --------------------------------------------------------

--
-- Table structure for table `training`
--

CREATE TABLE `training` (
  `training_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `date` date NOT NULL,
  `trainer` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training`
--

INSERT INTO `training` (`training_id`, `title`, `date`, `trainer`) VALUES
(1, 'Leadership Development', '2025-02-15', 'John Smith'),
(2, 'Digital Marketing Fundamentals', '2025-03-05', 'Maria Garcia'),
(3, 'Project Management', '2025-04-10', 'Robert Johnson');

-- --------------------------------------------------------

--
-- Table structure for table `training_analytics`
--

CREATE TABLE `training_analytics` (
  `analytic_id` int(11) NOT NULL,
  `fk_program_id` int(11) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_category` varchar(50) NOT NULL,
  `dimension_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_analytics`
--

INSERT INTO `training_analytics` (`analytic_id`, `fk_program_id`, `metric_name`, `metric_category`, `dimension_type`) VALUES
(1, 9, 'goodsss', 'hell yeahh', 'asd123');

-- --------------------------------------------------------

--
-- Table structure for table `training_attendance`
--

CREATE TABLE `training_attendance` (
  `attendance_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `attendance_status` varchar(20) NOT NULL,
  `attendance_date` date NOT NULL,
  `time_in` time NOT NULL,
  `time_out` time NOT NULL,
  `hours_attended` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_attendance`
--

INSERT INTO `training_attendance` (`attendance_id`, `fk_employee_id`, `attendance_status`, `attendance_date`, `time_in`, `time_out`, `hours_attended`) VALUES
(3, 10, 'Present', '2025-10-03', '20:13:00', '08:15:00', 13),
(6, 11, 'Present', '2025-10-03', '08:18:00', '11:14:00', 3),
(7, 10, 'Present', '2025-10-09', '09:26:00', '09:30:00', 0),
(8, 12, 'Present', '2025-10-18', '11:11:00', '16:06:00', 6);

-- --------------------------------------------------------

--
-- Table structure for table `training_calendar`
--

CREATE TABLE `training_calendar` (
  `calendar_id` int(11) NOT NULL,
  `event_start` datetime NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_title` varchar(100) NOT NULL,
  `location` int(11) NOT NULL,
  `fk_session_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_calendar`
--

INSERT INTO `training_calendar` (`calendar_id`, `event_start`, `event_name`, `event_title`, `location`, `fk_session_id`) VALUES
(4, '2025-10-23 21:58:00', 'ratbu12', 'burat12', 69, 2),
(6, '2025-10-25 09:19:00', 'AHHH', 'HAAA', 69, 2);

-- --------------------------------------------------------

--
-- Table structure for table `training_completion`
--

CREATE TABLE `training_completion` (
  `completion_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `completion_status` varchar(20) NOT NULL,
  `completion_date` date NOT NULL,
  `certificate_issued` tinyint(1) NOT NULL,
  `final_grade` varchar(10) NOT NULL,
  `assessment_score` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_completion`
--

INSERT INTO `training_completion` (`completion_id`, `fk_employee_id`, `completion_status`, `completion_date`, `certificate_issued`, `final_grade`, `assessment_score`) VALUES
(417, 11, '2', '2025-10-23', 1, '100', 100),
(419, 10, '2', '2025-10-03', 1, '100', 100),
(420, 12, '2', '2025-10-03', 1, '100', 100);

-- --------------------------------------------------------

--
-- Table structure for table `training_program`
--

CREATE TABLE `training_program` (
  `program_id` int(11) NOT NULL,
  `program_name` varchar(100) NOT NULL,
  `program_description` text NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `duration_hours` int(11) NOT NULL,
  `skill_level` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_program`
--

INSERT INTO `training_program` (`program_id`, `program_name`, `program_description`, `fk_employee_id`, `duration_hours`, `skill_level`) VALUES
(6, '123', '123', 10, 14, 'Beginner'),
(7, 'dasd', '1231231231231313231 213 123 123123 12312', 11, 23, 'Intermediate'),
(9, 'Hello', 'Hellooo', 11, 23, 'Intermediate');

-- --------------------------------------------------------

--
-- Table structure for table `training_registration`
--

CREATE TABLE `training_registration` (
  `registration_id` int(11) NOT NULL,
  `fk_employee_id` int(11) NOT NULL,
  `fk_training_id` int(11) NOT NULL,
  `registration_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_registration`
--

INSERT INTO `training_registration` (`registration_id`, `fk_employee_id`, `fk_training_id`, `registration_date`) VALUES
(1, 10, 2, '2025-10-14');

-- --------------------------------------------------------

--
-- Table structure for table `training_session`
--

CREATE TABLE `training_session` (
  `session_id` int(11) NOT NULL,
  `session_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `venue` varchar(100) NOT NULL,
  `fk_program_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `training_session`
--

INSERT INTO `training_session` (`session_id`, `session_name`, `start_date`, `end_date`, `venue`, `fk_program_id`) VALUES
(2, 'DNA DAY', '2025-10-28', '2025-10-30', 'nova burattt', 9);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `benefits`
--
ALTER TABLE `benefits`
  ADD PRIMARY KEY (`benefit_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `candidate_pool`
--
ALTER TABLE `candidate_pool`
  ADD PRIMARY KEY (`candidate_id`),
  ADD KEY `candidate_pool_ibfk_1` (`fk_employee_id`),
  ADD KEY `fk_plan_id` (`fk_plan_id`);

--
-- Indexes for table `competency`
--
ALTER TABLE `competency`
  ADD PRIMARY KEY (`competency_id`);

--
-- Indexes for table `competency_assessment`
--
ALTER TABLE `competency_assessment`
  ADD PRIMARY KEY (`assessment_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`),
  ADD KEY `fk_competency_id` (`fk_competency_id`);

--
-- Indexes for table `competency_category`
--
ALTER TABLE `competency_category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `competency_gap`
--
ALTER TABLE `competency_gap`
  ADD PRIMARY KEY (`gap_id`),
  ADD KEY `competency_gap_ibfk_1` (`fk_employee_id`),
  ADD KEY `competency_gap_ibfk_2` (`fk_competency_id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`department_id`),
  ADD KEY `fk_hr_staff_id` (`fk_hr_staff_id`);

--
-- Indexes for table `development_plan`
--
ALTER TABLE `development_plan`
  ADD PRIMARY KEY (`dev_plan_id`),
  ADD KEY `development_plan_ibfk_1` (`fk_employee_id`),
  ADD KEY `development_plan_ibfk_2` (`fk_gap_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_department_id` (`fk_department_id`),
  ADD KEY `fk_position_id` (`fk_position_id`);

--
-- Indexes for table `employee_competency`
--
ALTER TABLE `employee_competency`
  ADD PRIMARY KEY (`emp_competency_id`),
  ADD KEY `employee_competency_ibfk_1` (`fk_employee_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `hr_staff`
--
ALTER TABLE `hr_staff`
  ADD PRIMARY KEY (`hr_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `learning_path`
--
ALTER TABLE `learning_path`
  ADD PRIMARY KEY (`path_id`),
  ADD KEY `learning_path_ibfk_1` (`fk_hr_id`);

--
-- Indexes for table `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  ADD PRIMARY KEY (`link_id`),
  ADD KEY `learning_path_courses_ibfk_1` (`fk_path_id`),
  ADD KEY `learning_path_courses_ibfk_2` (`fk_course_id`);

--
-- Indexes for table `learning_report`
--
ALTER TABLE `learning_report`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `idx_employee_date` (`fk_employee_id`,`generated_date`);

--
-- Indexes for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD PRIMARY KEY (`leave_request_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `payroll`
--
ALTER TABLE `payroll`
  ADD PRIMARY KEY (`payroll_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `position`
--
ALTER TABLE `position`
  ADD PRIMARY KEY (`position_id`);

--
-- Indexes for table `position_competency`
--
ALTER TABLE `position_competency`
  ADD PRIMARY KEY (`position_competency_id`),
  ADD KEY `fk_competency_id` (`fk_competency_id`),
  ADD KEY `fk_position_id` (`fk_position_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`progress_id`),
  ADD KEY `employee_id` (`fk_employee_id`),
  ADD KEY `fk_course_id` (`fk_course_id`);

--
-- Indexes for table `succession_plan`
--
ALTER TABLE `succession_plan`
  ADD PRIMARY KEY (`plan_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `training`
--
ALTER TABLE `training`
  ADD PRIMARY KEY (`training_id`);

--
-- Indexes for table `training_analytics`
--
ALTER TABLE `training_analytics`
  ADD PRIMARY KEY (`analytic_id`),
  ADD KEY `fk_program_id` (`fk_program_id`);

--
-- Indexes for table `training_attendance`
--
ALTER TABLE `training_attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `training_calendar`
--
ALTER TABLE `training_calendar`
  ADD PRIMARY KEY (`calendar_id`),
  ADD KEY `fk_session_id` (`fk_session_id`);

--
-- Indexes for table `training_completion`
--
ALTER TABLE `training_completion`
  ADD PRIMARY KEY (`completion_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `training_program`
--
ALTER TABLE `training_program`
  ADD PRIMARY KEY (`program_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`);

--
-- Indexes for table `training_registration`
--
ALTER TABLE `training_registration`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `fk_employee_id` (`fk_employee_id`),
  ADD KEY `fk_training_id` (`fk_training_id`);

--
-- Indexes for table `training_session`
--
ALTER TABLE `training_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk_program_id` (`fk_program_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `benefits`
--
ALTER TABLE `benefits`
  MODIFY `benefit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidate_pool`
--
ALTER TABLE `candidate_pool`
  MODIFY `candidate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `competency`
--
ALTER TABLE `competency`
  MODIFY `competency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `competency_assessment`
--
ALTER TABLE `competency_assessment`
  MODIFY `assessment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=319;

--
-- AUTO_INCREMENT for table `competency_category`
--
ALTER TABLE `competency_category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `competency_gap`
--
ALTER TABLE `competency_gap`
  MODIFY `gap_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `development_plan`
--
ALTER TABLE `development_plan`
  MODIFY `dev_plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `employee_competency`
--
ALTER TABLE `employee_competency`
  MODIFY `emp_competency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `hr_staff`
--
ALTER TABLE `hr_staff`
  MODIFY `hr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `learning_path`
--
ALTER TABLE `learning_path`
  MODIFY `path_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  MODIFY `link_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `learning_report`
--
ALTER TABLE `learning_report`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `leave_request`
--
ALTER TABLE `leave_request`
  MODIFY `leave_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll`
--
ALTER TABLE `payroll`
  MODIFY `payroll_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `position`
--
ALTER TABLE `position`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `position_competency`
--
ALTER TABLE `position_competency`
  MODIFY `position_competency_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `progress_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `succession_plan`
--
ALTER TABLE `succession_plan`
  MODIFY `plan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `training`
--
ALTER TABLE `training`
  MODIFY `training_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `training_analytics`
--
ALTER TABLE `training_analytics`
  MODIFY `analytic_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `training_attendance`
--
ALTER TABLE `training_attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `training_calendar`
--
ALTER TABLE `training_calendar`
  MODIFY `calendar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `training_completion`
--
ALTER TABLE `training_completion`
  MODIFY `completion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT for table `training_program`
--
ALTER TABLE `training_program`
  MODIFY `program_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `training_registration`
--
ALTER TABLE `training_registration`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `training_session`
--
ALTER TABLE `training_session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `benefits`
--
ALTER TABLE `benefits`
  ADD CONSTRAINT `benefits_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidate_pool`
--
ALTER TABLE `candidate_pool`
  ADD CONSTRAINT `candidate_pool_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `candidate_pool_ibfk_2` FOREIGN KEY (`fk_plan_id`) REFERENCES `succession_plan` (`plan_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `competency_assessment`
--
ALTER TABLE `competency_assessment`
  ADD CONSTRAINT `competency_assessment_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `competency_assessment_ibfk_2` FOREIGN KEY (`fk_competency_id`) REFERENCES `competency` (`competency_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `competency_gap`
--
ALTER TABLE `competency_gap`
  ADD CONSTRAINT `competency_gap_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `competency_gap_ibfk_2` FOREIGN KEY (`fk_competency_id`) REFERENCES `competency` (`competency_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `department_ibfk_1` FOREIGN KEY (`fk_hr_staff_id`) REFERENCES `hr_staff` (`hr_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `development_plan`
--
ALTER TABLE `development_plan`
  ADD CONSTRAINT `development_plan_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `development_plan_ibfk_2` FOREIGN KEY (`fk_gap_id`) REFERENCES `competency_gap` (`gap_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_ibfk_1` FOREIGN KEY (`fk_department_id`) REFERENCES `department` (`department_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `employee_ibfk_2` FOREIGN KEY (`fk_position_id`) REFERENCES `position` (`position_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_competency`
--
ALTER TABLE `employee_competency`
  ADD CONSTRAINT `employee_competency_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `learning_path`
--
ALTER TABLE `learning_path`
  ADD CONSTRAINT `learning_path_ibfk_1` FOREIGN KEY (`fk_hr_id`) REFERENCES `hr_staff` (`hr_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `learning_path_courses`
--
ALTER TABLE `learning_path_courses`
  ADD CONSTRAINT `learning_path_courses_ibfk_1` FOREIGN KEY (`fk_path_id`) REFERENCES `learning_path` (`path_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `learning_path_courses_ibfk_2` FOREIGN KEY (`fk_course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `learning_report`
--
ALTER TABLE `learning_report`
  ADD CONSTRAINT `fk_learning_report_employee` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `learning_report_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `leave_request`
--
ALTER TABLE `leave_request`
  ADD CONSTRAINT `leave_request_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payroll`
--
ALTER TABLE `payroll`
  ADD CONSTRAINT `payroll_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `position_competency`
--
ALTER TABLE `position_competency`
  ADD CONSTRAINT `position_competency_ibfk_1` FOREIGN KEY (`fk_competency_id`) REFERENCES `competency` (`competency_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `position_competency_ibfk_2` FOREIGN KEY (`fk_position_id`) REFERENCES `position` (`position_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`fk_course_id`) REFERENCES `course` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `succession_plan`
--
ALTER TABLE `succession_plan`
  ADD CONSTRAINT `succession_plan_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_analytics`
--
ALTER TABLE `training_analytics`
  ADD CONSTRAINT `training_analytics_ibfk_1` FOREIGN KEY (`fk_program_id`) REFERENCES `training_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_attendance`
--
ALTER TABLE `training_attendance`
  ADD CONSTRAINT `training_attendance_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_calendar`
--
ALTER TABLE `training_calendar`
  ADD CONSTRAINT `training_calendar_ibfk_1` FOREIGN KEY (`fk_session_id`) REFERENCES `training_session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_completion`
--
ALTER TABLE `training_completion`
  ADD CONSTRAINT `training_completion_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_program`
--
ALTER TABLE `training_program`
  ADD CONSTRAINT `training_program_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_registration`
--
ALTER TABLE `training_registration`
  ADD CONSTRAINT `training_registration_ibfk_1` FOREIGN KEY (`fk_employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `training_registration_ibfk_2` FOREIGN KEY (`fk_training_id`) REFERENCES `training` (`training_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `training_session`
--
ALTER TABLE `training_session`
  ADD CONSTRAINT `training_session_ibfk_1` FOREIGN KEY (`fk_program_id`) REFERENCES `training_program` (`program_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
