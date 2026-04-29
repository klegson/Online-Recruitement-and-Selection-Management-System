-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 28, 2026 at 01:22 AM
-- Server version: 8.0.43
-- PHP Version: 8.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `deped_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `applicationId` int NOT NULL,
  `applicationCode` varchar(20) DEFAULT NULL,
  `userId` int DEFAULT NULL,
  `fullName` varchar(255) DEFAULT NULL,
  `contactNumber` varchar(20) DEFAULT NULL,
  `religion` varchar(100) DEFAULT NULL,
  `ethnicity` varchar(100) DEFAULT NULL,
  `isPersonWithDisability` tinyint(1) DEFAULT '0',
  `isSoloParent` tinyint(1) DEFAULT '0',
  `jobId` int DEFAULT NULL,
  `status` enum('Pending','Qualified','Disqualified') NOT NULL DEFAULT 'Pending',
  `updatedBy` int DEFAULT NULL,
  `hrNotes` text,
  `appliedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`applicationId`, `applicationCode`, `userId`, `fullName`, `contactNumber`, `religion`, `ethnicity`, `isPersonWithDisability`, `isSoloParent`, `jobId`, `status`, `updatedBy`, `hrNotes`, `appliedAt`) VALUES
(14, 'APP20264145', 1, 'Lebron James', '09652365652', 'Roman Catholic', 'Filipino', 1, 1, 261, 'Qualified', 2, '', '2026-04-23 01:17:13'),
(16, 'APP20263742', 7, 'Bronny James', '09625301194', 'Roman Catholic', 'Filipino', 1, 1, 261, 'Qualified', 2, '', '2026-04-24 14:02:55'),
(17, 'APP20262712', 6, 'Bam Adebayo', '09625301194', 'Roman Catholic', 'Filipino', 1, 1, 261, 'Qualified', 2, '', '2026-04-24 14:03:41'),
(18, 'APP20269190', 8, 'John Basq', '03256325535', 'Roman Catholic', 'Filipino', 1, 1, 261, 'Qualified', 2, '', '2026-04-24 14:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `application_files`
--

CREATE TABLE `application_files` (
  `id` int NOT NULL,
  `applicationId` int DEFAULT NULL,
  `documentTypeId` int DEFAULT NULL,
  `filePath` varchar(255) NOT NULL,
  `uploadedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `application_files`
--

INSERT INTO `application_files` (`id`, `applicationId`, `documentTypeId`, `filePath`, `uploadedAt`) VALUES
(13, 14, 2, '../uploads/1776907033_Week 9_REYES.pdf', '2026-04-23 01:17:13'),
(14, 16, 2, '../uploads/1777039375_Week 9_REYES.pdf', '2026-04-24 14:02:55'),
(15, 17, 2, '../uploads/1777039421_Week 6_REYES.pdf', '2026-04-24 14:03:41'),
(16, 18, 2, '../uploads/1777039465_Week 4_REYES.pdf', '2026-04-24 14:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `logId` int NOT NULL,
  `userId` int DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text,
  `ipAddress` varchar(45) DEFAULT NULL,
  `userAgent` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int NOT NULL,
  `dept_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `dept_name`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'Administrative Division - Asset Management Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(2, 'Administrative Division - General Services Unit', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(3, 'Administrative Division - Payroll Services Unit', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(4, 'Administrative Division - Records Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(5, 'Administrative Division - Personnel Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(6, 'Administrative Division - Cash Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(7, 'Curriculum and Learning Management Division - Learning Resource Management Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(8, 'Education Support Services Division - Health and Nutrition', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(9, 'Education Support Services Division - Programs and Projects', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(10, 'Education Support Services Division - Facilities', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(11, 'Finance Division - Budget Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(12, 'Finance Division - Accounting Section', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(13, 'Human Resource Development Division - NEAP', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(14, 'Office of the Regional Director - Procurement Unit', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(15, 'Office of the Regional Director - Information and Communications Technology Unit', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(16, 'Office of the Regional Director - Public Affairs Unit', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(17, 'Office of the Regional Director - Legal Unit', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(18, 'Administrative Division', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(19, 'Curriculum and Learning Management Division', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(20, 'Education Support Services Division', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(21, 'Finance Division', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(22, 'Office of the Regional Director', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`id`, `name`) VALUES
(2, 'Application Letter');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `jobId` int NOT NULL,
  `position` enum('Accountant I','Accountant II','Accountant III','Accountant IV','Accounting Analyst','Accounting Clerk II','Administrative Assistant VI','Administrative Aide I','Administrative Aide II','Administrative Aide III','Administrative Aide IV','Administrative Aide V','Administrative Aide VI','Administrative Assistant I','Administrative Assistant II','Administrative assistant III','Administrative Assistant V','Administrative Officer I','Administrative Officer II','Administrative Officer III','Administrative Officer IV','Administrative Officer V','Agriculturist I','Agriculturist II','Aquacultural Technician II','Aquaculturist I','Architect II','Architect III','Artist-Illustrator II','Assistant Regional Director','Assistant Schools Division Superintendent','Assistant School Principal 1','Assistant School Principal II','Assistant School Principal III','Assistant Secretary','Assistant Special School Principal','Assistant Teacher''s Camp Superintendent','Attorney V','Attorney I','Attorney II','Attorney III','Attorney IV','Bookkeeper','Budget Officer I','Bureau Director III','Bureau Director IV','Cashier I','Cashier II','Chief Accountant','Chief Administrative Officer','Chief Education Supervisor','Chief Health Program Officer','Cinematographer I','Clerk II','Clerk III','College Librarian I','College Librarian II','Communications Equipment Operator I','Communications Equipment Operator II','Communications Equipment Operator III','Communications Equipment Operator IV','Computer File Librarian II','Computer Maintenance Technologist I','Computer Maintenance Technologist III','Computer Programmer II','Computer Programmer III','Construction and Maintenance Man','Cook I','Copy Reader','Coxswain','Crafts Education Demonstrator I','Crafts Education Demonstrator II','Creative Arts Specialist I','Creative Arts Specialist II','Dental Aide','Dentist I','Dentist II','Dentist III','Department Legislative Liason Specialist','Disbursing Officer I','Disbursing Officer II','Dormitory Manager I','Dormitory Manager II','Draftsman I','Draftsman II','Driver I','Education Program Specialist I','Education Program Specialist II','Education Program Supervisor','Education Research Assistant II','Electronics and Communications Equipment Technician','Engineer II','Engineer III','Engineer IV','Engineer V','Farm Worker I','Fiscal Clerk I','Fiscal Examiner I','Fisherman','Guesthouse Caretaker','Guidance Coordinator 1','Guidance Coordinator II','Guidance Coordinator III','Guidance Counselor I','Guidance Counselor II','Guidance Counselor III','Guidance Services Specialist I','Guidance Services Specialist II','Handicraft Worker I','Handicraft Worker II','Head Teacher I','Head Teacher II','Head Teacher III','Head Teacher IV','Head Teacher V','Head Teacher VI','Health Education and Promotion Officer II','Health Education and Promotion Officer III','Heavy Equipment Operator I','Houseparent I','Human Resource Management Officer II','Human Resource Management Officer I','Information Systems Analyst II','Information Systems Analyst III','Information Systems Researcher III','Information Technology Officer','Information Technology Officer I','Information Technology Officer II','Internal Auditing Assistant','Internal Auditor I','Internal Auditor II','Internal Auditor III','Internal Auditor IV','Internal Auditor V','Laboratory Technician I','Legal Aide','Legal Assistant I','Legal Assistant II','Librarian I','Librarian II','Librarian III','Light Equipment Operator','Lineman I','Marine Engineman I','Master Fisherman','Master Teacher I','Master Teacher II','Master Teacher III','Master Teacher IV','Mechanic I','Mechanic II','Mechanical Plant Operator II','Mechanical Plant Operator I','Medical Officer II','Medical Officer III','Medical Officer IV','Metal Worker I','Nurse I','Nurse II','Nurse Maid I','Nursing Attendant I','Nutritionist-Dietitian I','Nutritionist-Dietitian II','Nutritionist-Dietitian III','Photoengraver II','Planning Officer I','Planning Officer II','Planning Officer III','Planning Officer IV','Planning Officer V','Printing Foreman','Project Development Assistant','Project Development Officer I','Project Development Officer II','Project Development Officer III','Project Development Officer IV','Project Development Officer V','Project Evaluation Officer IV','Proofreader II','Public Schools District Supervisor','Publication Production Supervisor','Pyschologist I','Records Officer II','Regional Director','Registrar I','Registrar II','Reproduction Machine Operator I','School Farm Demonstrator','School Farming Coordinator I','School Farming Coordinator II','School Farming Coordinator III','School Librarian I','School Librarian II','School Librarian III','School Principal I','School Principal II','School Principal III','School Principal IV','Schools Division Superintendent','Science Research Assistant','Science Research Specialist II','Science Research Technician I','Science Research Technician II','Science Research Technician III','Science Research Technician IV','Secretary','Security Guard I','Security Guard II','Security Guard III','Security Officer II','Security Officer IV','Senior Administrative Assistant I','Senior Administrative Assistant II','Senior Administrative Assistant III','Senior Administrative Assistant V','Senior Bookkeeper','Senior Education Program Specialist','Senior Science Research Specialist','Social Welfare Officer I','Special Investigator III','Special Invetigator II','Special School Principal I','Special School Principal II','Statistician Aide','Statistician I','Statistician II','Statistician III','Supervising Administrative Officer','Supervising Education Program Specialist','Supervising Health Program Officer','Supply Officer I','Supply Officer II','Teacher Credentials Evaluator I','Teacher Credentials Evaluator II','Teacher I','Teacher II','Teacher III','Teacher''s Camp Superintendent','Teaching-Aids Specialist','Telegram Carrier','Typesetter II','Undersecretary','Utility Foreman','Utility Worker I','Vocational Instruction Supervisor I','Vocational Instruction Supervisor II','Vocational Instruction Supervisor III','Vocational Placement Coordinator I','Vocational School Administrator I','Vocational School Administrator II','Warehouseman III','Watchman I','Watchman II') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `pos_id` int DEFAULT NULL,
  `plantillaItemNo` varchar(50) DEFAULT NULL,
  `description` text,
  `department` enum('Administrative Division - Asset Management Section','Administrative Division - General Services Unit','Administrative Division - Payroll Services Unit','Administrative Division - Records Section','Administrative Division - Personnel Section','Administrative Division - Cash Section','Curriculum and Learning Management Division - Learning Resource Management Section','Education Support Services Division - Health and Nutrition','Education Support Services Division - Programs and Projects','Education Support Services Division - Facilities','Finance Division - Budget Section','Finance Division - Accounting Section','Human Resource Development Division - NEAP','Office of the Regional Director - Procurement Unit','Office of the Regional Director - Information and Communications Technology Unit','Office of the Regional Director - Public Affairs Unit','Office of the Regional Director - Legal Unit','Administrative Division','Curriculum and Learning Management Division','Education Support Services Division','Finance Division','Office of the Regional Director') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `dept_id` int DEFAULT NULL,
  `salaryGrade` varchar(10) DEFAULT NULL,
  `monthlySalary` decimal(10,2) DEFAULT NULL,
  `requirements` text,
  `deadline` date DEFAULT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `jobStatus` enum('Open','Closed') NOT NULL DEFAULT 'Open',
  `postedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`jobId`, `position`, `pos_id`, `plantillaItemNo`, `description`, `department`, `dept_id`, `salaryGrade`, `monthlySalary`, `requirements`, `deadline`, `createdAt`, `updatedAt`, `jobStatus`, `postedAt`) VALUES
(261, 'Accountant I', NULL, '0111-456485', 'testd', 'Finance Division - Accounting Section', NULL, 'SG-12', 23000.00, NULL, '2026-04-30', '2026-04-13 03:00:50', '2026-04-13 03:00:50', 'Open', '2026-04-13 03:00:50');

-- --------------------------------------------------------

--
-- Table structure for table `job_requirements`
--

CREATE TABLE `job_requirements` (
  `id` int NOT NULL,
  `jobId` int DEFAULT NULL,
  `documentTypeId` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `job_requirements`
--

INSERT INTO `job_requirements` (`id`, `jobId`, `documentTypeId`) VALUES
(18, 261, 2);

-- --------------------------------------------------------

--
-- Table structure for table `job_statuses`
--

CREATE TABLE `job_statuses` (
  `id` int NOT NULL,
  `status_name` varchar(50) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `job_statuses`
--

INSERT INTO `job_statuses` (`id`, `status_name`, `description`, `is_active`, `created_at`) VALUES
(1, 'Open', 'Job is open for applications', 1, '2026-03-31 02:09:08'),
(2, 'Closed', 'Job is no longer accepting applications', 1, '2026-03-31 02:09:08'),
(3, 'On Hold', 'Job applications are temporarily suspended', 1, '2026-03-31 02:09:08'),
(4, 'Filled', 'Position has been filled', 1, '2026-03-31 02:09:08');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `status` enum('Success','Failed') NOT NULL,
  `failure_reason` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `id` int NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `position_name`, `created_at`, `updated_at`, `is_active`) VALUES
(1, 'Accountant I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(2, 'Accountant II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(3, 'Accountant III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(4, 'Accountant IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(5, 'Accounting Analyst', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(6, 'Accounting Clerk II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(7, 'Administrative Assistant VI', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(8, 'Administrative Aide I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(9, 'Administrative Aide II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(10, 'Administrative Aide III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(11, 'Administrative Aide IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(12, 'Administrative Aide V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(13, 'Administrative Aide VI', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(14, 'Administrative Assistant I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(15, 'Administrative Assistant II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(16, 'Administrative assistant III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(17, 'Administrative Assistant V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(18, 'Administrative Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(19, 'Administrative Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(20, 'Administrative Officer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(21, 'Administrative Officer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(22, 'Administrative Officer V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(23, 'Agriculturist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(24, 'Agriculturist II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(25, 'Aquacultural Technician II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(26, 'Aquaculturist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(27, 'Architect II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(28, 'Architect III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(29, 'Artist-Illustrator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(30, 'Assistant Regional Director', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(31, 'Assistant Schools Division Superintendent', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(32, 'Assistant School Principal 1', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(33, 'Assistant School Principal II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(34, 'Assistant School Principal III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(35, 'Assistant Secretary', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(36, 'Assistant Special School Principal', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(37, 'Assistant Teacher\'s Camp Superintendent', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(38, 'Attorney V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(39, 'Attorney I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(40, 'Attorney II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(41, 'Attorney III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(42, 'Attorney IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(43, 'Bookkeeper', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(44, 'Budget Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(45, 'Bureau Director III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(46, 'Bureau Director IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(47, 'Cashier I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(48, 'Cashier II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(49, 'Chief Accountant', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(50, 'Chief Administrative Officer', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(51, 'Chief Education Supervisor', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(52, 'Chief Health Program Officer', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(53, 'Cinematographer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(54, 'Clerk II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(55, 'Clerk III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(56, 'College Librarian I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(57, 'College Librarian II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(58, 'Communications Equipment Operator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(59, 'Communications Equipment Operator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(60, 'Communications Equipment Operator III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(61, 'Communications Equipment Operator IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(62, 'Computer File Librarian II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(63, 'Computer Maintenance Technologist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(64, 'Computer Maintenance Technologist III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(65, 'Computer Programmer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(66, 'Computer Programmer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(67, 'Construction and Maintenance Man', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(68, 'Cook I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(69, 'Copy Reader', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(70, 'Coxswain', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(71, 'Crafts Education Demonstrator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(72, 'Crafts Education Demonstrator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(73, 'Creative Arts Specialist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(74, 'Creative Arts Specialist II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(75, 'Dental Aide', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(76, 'Dentist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(77, 'Dentist II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(78, 'Dentist III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(79, 'Department Legislative Liason Specialist', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(80, 'Disbursing Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(81, 'Disbursing Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(82, 'Dormitory Manager I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(83, 'Dormitory Manager II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(84, 'Draftsman I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(85, 'Draftsman II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(86, 'Driver I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(87, 'Education Program Specialist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(88, 'Education Program Specialist II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(89, 'Education Program Supervisor', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(90, 'Education Research Assistant II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(91, 'Electronics and Communications Equipment Technician', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(92, 'Engineer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(93, 'Engineer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(94, 'Engineer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(95, 'Engineer V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(96, 'Farm Worker I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(97, 'Fiscal Clerk I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(98, 'Fiscal Examiner I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(99, 'Fisherman', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(100, 'Guesthouse Caretaker', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(101, 'Guidance Coordinator 1', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(102, 'Guidance Coordinator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(103, 'Guidance Coordinator III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(104, 'Guidance Counselor I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(105, 'Guidance Counselor II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(106, 'Guidance Counselor III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(107, 'Guidance Services Specialist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(108, 'Guidance Services Specialist II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(109, 'Handicraft Worker I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(110, 'Handicraft Worker II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(111, 'Head Teacher I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(112, 'Head Teacher II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(113, 'Head Teacher III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(114, 'Head Teacher IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(115, 'Head Teacher V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(116, 'Head Teacher VI', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(117, 'Health Education and Promotion Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(118, 'Health Education and Promotion Officer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(119, 'Heavy Equipment Operator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(120, 'Houseparent I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(121, 'Human Resource Management Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(122, 'Human Resource Management Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(123, 'Information Systems Analyst II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(124, 'Information Systems Analyst III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(125, 'Information Systems Researcher III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(126, 'Information Technology Officer', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(127, 'Information Technology Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(128, 'Information Technology Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(129, 'Internal Auditing Assistant', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(130, 'Internal Auditor I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(131, 'Internal Auditor II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(132, 'Internal Auditor III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(133, 'Internal Auditor IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(134, 'Internal Auditor V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(135, 'Laboratory Technician I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(136, 'Legal Aide', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(137, 'Legal Assistant I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(138, 'Legal Assistant II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(139, 'Librarian I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(140, 'Librarian II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(141, 'Librarian III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(142, 'Light Equipment Operator', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(143, 'Lineman I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(144, 'Marine Engineman I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(145, 'Master Fisherman', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(146, 'Master Teacher I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(147, 'Master Teacher II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(148, 'Master Teacher III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(149, 'Master Teacher IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(150, 'Mechanic I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(151, 'Mechanic II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(152, 'Mechanical Plant Operator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(153, 'Mechanical Plant Operator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(154, 'Medical Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(155, 'Medical Officer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(156, 'Medical Officer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(157, 'Metal Worker I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(158, 'Nurse I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(159, 'Nurse II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(160, 'Nurse Maid I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(161, 'Nursing Attendant I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(162, 'Nutritionist-Dietitian I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(163, 'Nutritionist-Dietitian II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(164, 'Nutritionist-Dietitian III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(165, 'Photoengraver II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(166, 'Planning Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(167, 'Planning Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(168, 'Planning Officer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(169, 'Planning Officer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(170, 'Planning Officer V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(171, 'Printing Foreman', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(172, 'Project Development Assistant', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(173, 'Project Development Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(174, 'Project Development Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(175, 'Project Development Officer III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(176, 'Project Development Officer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(177, 'Project Development Officer V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(178, 'Project Evaluation Officer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(179, 'Proofreader II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(180, 'Public Schools District Supervisor', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(181, 'Publication Production Supervisor', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(182, 'Pyschologist I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(183, 'Records Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(184, 'Regional Director', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(185, 'Registrar I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(186, 'Registrar II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(187, 'Reproduction Machine Operator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(188, 'School Farm Demonstrator', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(189, 'School Farming Coordinator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(190, 'School Farming Coordinator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(191, 'School Farming Coordinator III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(192, 'School Librarian I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(193, 'School Librarian II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(194, 'School Librarian III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(195, 'School Principal I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(196, 'School Principal II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(197, 'School Principal III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(198, 'School Principal IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(199, 'Schools Division Superintendent', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(200, 'Science Research Assistant', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(201, 'Science Research Specialist II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(202, 'Science Research Technician I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(203, 'Science Research Technician II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(204, 'Science Research Technician III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(205, 'Science Research Technician IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(206, 'Secretary', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(207, 'Security Guard I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(208, 'Security Guard II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(209, 'Security Guard III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(210, 'Security Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(211, 'Security Officer IV', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(212, 'Senior Administrative Assistant I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(213, 'Senior Administrative Assistant II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(214, 'Senior Administrative Assistant III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(215, 'Senior Administrative Assistant V', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(216, 'Senior Bookkeeper', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(217, 'Senior Education Program Specialist', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(218, 'Senior Science Research Specialist', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(219, 'Social Welfare Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(220, 'Special Investigator III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(221, 'Special Invetigator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(222, 'Special School Principal I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(223, 'Special School Principal II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(224, 'Statistician Aide', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(225, 'Statistician I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(226, 'Statistician II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(227, 'Statistician III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(228, 'Supervising Administrative Officer', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(229, 'Supervising Education Program Specialist', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(230, 'Supervising Health Program Officer', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(231, 'Supply Officer I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(232, 'Supply Officer II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(233, 'Teacher Credentials Evaluator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(234, 'Teacher Credentials Evaluator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(235, 'Teacher I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(236, 'Teacher II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(237, 'Teacher III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(238, 'Teacher\'s Camp Superintendent', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(239, 'Teaching-Aids Specialist', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(240, 'Telegram Carrier', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(241, 'Typesetter II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(242, 'Undersecretary', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(243, 'Utility Foreman', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(244, 'Utility Worker I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(245, 'Vocational Instruction Supervisor I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(246, 'Vocational Instruction Supervisor II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(247, 'Vocational Instruction Supervisor III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(248, 'Vocational Placement Coordinator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(249, 'Vocational School Administrator I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(250, 'Vocational School Administrator II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(251, 'Warehouseman III', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(252, 'Watchman I', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1),
(253, 'Watchman II', '2026-03-31 01:52:54', '2026-03-31 01:52:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userId` int NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `middleName` varchar(100) NOT NULL,
  `lastName` varchar(100) DEFAULT NULL,
  `extension` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `userRole` enum('Applicant','HR_Staff','Admin','Board') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Applicant',
  `dateJoined` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `accountStatus` enum('Active','For Activation','Deleted') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'For Activation'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `firstName`, `middleName`, `lastName`, `extension`, `email`, `password`, `userRole`, `dateJoined`, `updatedAt`, `accountStatus`) VALUES
(1, 'Lebron', 'Del Prado', 'James', 'Jr.', 'leb@gmail.com', '$2y$12$5apoc6m/VUlJY6EIodTHC.f1WgMmKQMTmFeeVRCzkY/T0cgXbTFui', 'Applicant', '2026-02-15 13:29:58', '2026-03-09 02:24:14', 'For Activation'),
(2, 'Steph', 'Mustera', 'Curry', 'Jr.', 'steph@gmail.com', '$2y$12$lrVsmj2UZgul9en.TO2b8uTUYy/o9GTCGTTfjgIYSswezR1s48r/e', 'HR_Staff', '2026-02-15 13:48:52', '2026-03-09 02:24:03', 'For Activation'),
(3, 'Gabriel', 'Cano', 'Reyes', '', 'gabbb@gmail.com', '$2y$12$GWdMBlr84xkyPO.MsdzQQuzS3jZGo9nvZhCGQNS52aMG8BQC.hatC', 'Admin', '2026-02-15 13:49:32', '2026-03-09 02:23:35', 'For Activation'),
(6, 'Bam', 'Aquino', 'Adebayo', '', 'bam@gmail.com', '$2y$12$vZQW4Oe3VVh/AJlxVQ4OmOStg1wZOeu1ibPf7O4V4zVtTdS3CBSRS', 'Applicant', '2026-03-24 02:11:11', '2026-03-24 02:11:11', 'For Activation'),
(7, 'Bronny', 'Sen', 'James', '', 'gabrieldv412@gmail.com', '$2y$12$J11LR606C8fRPEVvI/bEg.xM5p5bDXwKxCi5RtrrRMGPcQoV2QvH6', 'Applicant', '2026-03-24 02:44:42', '2026-03-24 04:51:49', 'For Activation'),
(8, 'John', 'Castro', 'Basq', '', 'john@gmail.com', '$2y$12$BnEPEbXldG/QQDDfRTK02.BqtGYl3X88M0DVYar2a1hXlP58uAO5i', 'Applicant', '2026-04-13 02:53:07', '2026-04-13 02:53:07', 'For Activation'),
(9, 'boarding', 'B', 'House', '', 'boardinghouse@gmail.com', '$2y$12$bhBAsQuQO3eNavflMQTe1.N3A6ZfOYwOA8Lmng1wxj/6MUvfIpwAG', 'Board', '2026-04-23 01:31:04', '2026-04-23 01:32:21', 'For Activation'),
(10, 'asd', 'a', 'asd', '', 'asd@gmail.com', '$2y$12$EWUZrkkPdVKwYj5PVZIHNe0BCL86zbthogikZa9XywRa73YgOcF.W', 'Applicant', '2026-04-27 01:44:36', '2026-04-27 01:44:36', 'For Activation');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`applicationId`),
  ADD UNIQUE KEY `applicationCode` (`applicationCode`),
  ADD UNIQUE KEY `uc_applicationCode` (`applicationCode`),
  ADD KEY `userId` (`userId`),
  ADD KEY `jobId` (`jobId`),
  ADD KEY `fk_updated_by` (`updatedBy`);

--
-- Indexes for table `application_files`
--
ALTER TABLE `application_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applicationId` (`applicationId`),
  ADD KEY `documentTypeId` (`documentTypeId`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`logId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dept_name` (`dept_name`),
  ADD KEY `idx_dept_name` (`dept_name`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`jobId`),
  ADD UNIQUE KEY `plantillaItemNo` (`plantillaItemNo`),
  ADD KEY `pos_id` (`pos_id`),
  ADD KEY `idx_dept_pos` (`dept_id`,`pos_id`),
  ADD KEY `idx_status` (`jobStatus`),
  ADD KEY `idx_deadline` (`deadline`);

--
-- Indexes for table `job_requirements`
--
ALTER TABLE `job_requirements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobId` (`jobId`),
  ADD KEY `documentTypeId` (`documentTypeId`);

--
-- Indexes for table `job_statuses`
--
ALTER TABLE `job_statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `status_name` (`status_name`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_status` (`user_id`,`status`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_address` (`ip_address`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `position_name` (`position_name`),
  ADD KEY `idx_position_name` (`position_name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userId`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `applicationId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `application_files`
--
ALTER TABLE `application_files`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `logId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `jobId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=262;

--
-- AUTO_INCREMENT for table `job_requirements`
--
ALTER TABLE `job_requirements`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `job_statuses`
--
ALTER TABLE `job_statuses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userId` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`) ON DELETE CASCADE,
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`jobId`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_updated_by` FOREIGN KEY (`updatedBy`) REFERENCES `users` (`userId`) ON DELETE SET NULL;

--
-- Constraints for table `application_files`
--
ALTER TABLE `application_files`
  ADD CONSTRAINT `application_files_ibfk_1` FOREIGN KEY (`applicationId`) REFERENCES `applications` (`applicationId`) ON DELETE CASCADE,
  ADD CONSTRAINT `application_files_ibfk_2` FOREIGN KEY (`documentTypeId`) REFERENCES `document_types` (`id`);

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`userId`);

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`dept_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `jobs_ibfk_2` FOREIGN KEY (`pos_id`) REFERENCES `positions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `job_requirements`
--
ALTER TABLE `job_requirements`
  ADD CONSTRAINT `job_requirements_ibfk_1` FOREIGN KEY (`jobId`) REFERENCES `jobs` (`jobId`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_requirements_ibfk_2` FOREIGN KEY (`documentTypeId`) REFERENCES `document_types` (`id`);

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `login_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userId`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
