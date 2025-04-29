-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 13, 2025 at 10:52 AM
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
-- Database: `alumni_network`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--
-- Error reading structure for table alumni_network.admin: #1932 - Table &#039;alumni_network.admin&#039; doesn&#039;t exist in engine
-- Error reading data for table alumni_network.admin: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `alumni_network`.`admin`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `last_login`) VALUES
(2, 'admin', '$2y$10$6FSD18C2dSO9JG/AQa61ju6BRg7bNno/Cy1AJWGB7DXrDndfhjmXm', '2025-04-13 06:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `career_goals`
--

CREATE TABLE `career_goals` (
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `target_date` date NOT NULL,
  `status` enum('not_started','in_progress','completed') NOT NULL,
  `goal_type` varchar(255) NOT NULL DEFAULT 'career'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `career_goals`
--

INSERT INTO `career_goals` (`goal_id`, `user_id`, `description`, `target_date`, `status`, `goal_type`) VALUES
(67, 115, 'awdawdawwdawd', '0000-00-00', 'not_started', 'career');

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `certificate_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `certificate_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certifications`
--

INSERT INTO `certifications` (`certificate_id`, `user_id`, `certificate_path`, `upload_date`) VALUES
(10, 115, 'assets/certificates/06BCA069/certificate1.pdf', '2025-04-13 08:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `educational_details`
--

CREATE TABLE `educational_details` (
  `edu_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `university_name` varchar(255) NOT NULL,
  `enrollment_number` varchar(255) DEFAULT NULL,
  `graduation_year` year(4) DEFAULT NULL,
  `verification_status` enum('pending','verified','rejected') DEFAULT 'pending',
  `verification_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `educational_details`
--

INSERT INTO `educational_details` (`edu_id`, `user_id`, `university_name`, `enrollment_number`, `graduation_year`, `verification_status`, `verification_date`) VALUES
(20, 24, 'Uka Tarsadia University', '02MCA054', NULL, 'verified', NULL),
(86, 115, 'Uka Tarsadia University', '06BCA069', NULL, 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `professional_status`
--

CREATE TABLE `professional_status` (
  `prof_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `current_status` enum('employed','seeking','student','other') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `professional_status`
--

INSERT INTO `professional_status` (`prof_id`, `user_id`, `current_status`, `company_name`, `position`, `start_date`, `is_current`) VALUES
(19, 24, 'employed', 'SRIMCA', 'Assistant Professor', NULL, 1),
(82, 115, 'seeking', '', '', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `project_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `technologies_used` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`project_id`, `user_id`, `title`, `description`, `technologies_used`, `start_date`, `end_date`) VALUES
(21, 24, 'HSPITAL MANAGEMENT SYSTEM', 'XYZ', 'PHP', '2025-03-03', '2025-03-18'),
(84, 115, 'Priyanshu\'s Project', 'awdawd', 'awdaw', '2025-03-30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `skill_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(255) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','advanced','expert') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`skill_id`, `user_id`, `skill_name`, `proficiency_level`) VALUES
(20, 24, 'PYTHON', 'beginner'),
(83, 115, 'FrontEnd', 'advanced');

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--
-- Error reading structure for table alumni_network.universities: #1932 - Table &#039;alumni_network.universities&#039; doesn&#039;t exist in engine
-- Error reading data for table alumni_network.universities: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `alumni_network`.`universities`&#039; at line 1

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `certificate_id` varchar(255) DEFAULT NULL,
  `certificate_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `email`, `phone`, `password`, `registration_date`, `last_login`, `certificate_id`, `certificate_path`) VALUES
(24, 'Jitendra Upadhyay', 'g2bupadhyay@gmail.com', '9909812837', '$2y$10$9G7ByLRHo2b82LU7.BAtfuT7DWRV5/dFrEBN1zD1G5pznU/t1d5gy', '2025-03-30 11:57:18', NULL, NULL, NULL),
(115, 'Rishi bardoliya', 'rishi@gmail.com', '6969696969', '$2y$10$UFvXdK6KiD0gmixFmz4BzeNB2BHcd3rccqNSZBbz.wx0FJj.WGYkC', '2025-04-13 08:47:30', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_portfolio`
--
-- Error reading structure for table alumni_network.user_portfolio: #1932 - Table &#039;alumni_network.user_portfolio&#039; doesn&#039;t exist in engine
-- Error reading data for table alumni_network.user_portfolio: #1064 - You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near &#039;FROM `alumni_network`.`user_portfolio`&#039; at line 1

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `career_goals`
--
ALTER TABLE `career_goals`
  ADD PRIMARY KEY (`goal_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`certificate_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `educational_details`
--
ALTER TABLE `educational_details`
  ADD PRIMARY KEY (`edu_id`),
  ADD UNIQUE KEY `enrollment_number` (`enrollment_number`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `professional_status`
--
ALTER TABLE `professional_status`
  ADD PRIMARY KEY (`prof_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`project_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`skill_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `career_goals`
--
ALTER TABLE `career_goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `certificate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `educational_details`
--
ALTER TABLE `educational_details`
  MODIFY `edu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `professional_status`
--
ALTER TABLE `professional_status`
  MODIFY `prof_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `project_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `career_goals`
--
ALTER TABLE `career_goals`
  ADD CONSTRAINT `career_goals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `educational_details`
--
ALTER TABLE `educational_details`
  ADD CONSTRAINT `educational_details_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `professional_status`
--
ALTER TABLE `professional_status`
  ADD CONSTRAINT `professional_status_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `skills`
--
ALTER TABLE `skills`
  ADD CONSTRAINT `skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
