-- First, let's insert a single user and capture the ID
INSERT INTO `users` (`fullname`, `email`, `phone`, `password`, `registration_date`) VALUES
('Neha Sharma', 'neha.sharma@gmail.com', '9876543212', '$2y$10$hashed_password6', CURRENT_TIMESTAMP);

-- Get the ID of the inserted user
SET @user_id = LAST_INSERT_ID();

-- Insert educational details for this user
INSERT INTO `educational_details` (`user_id`, `university_name`, `enrollment_number`, `graduation_year`, `verification_status`) VALUES
(@user_id, 'Uka Tarsadia University', '202207100510073', 2026, 'pending');

-- Insert professional status for this user
INSERT INTO `professional_status` (`user_id`, `current_status`, `company_name`, `position`, `start_date`, `is_current`) VALUES
(@user_id, 'student', NULL, NULL, NULL, 1);

-- Insert skills for this user
INSERT INTO `skills` (`user_id`, `proficiency_level`, `language_specialization`, `tools`, `technologies`) VALUES
(@user_id, 'intermediate', 'Python, JavaScript', 'PyCharm, VS Code', 'Flask, React, MongoDB');

-- Insert a project for this user
INSERT INTO `projects` (`user_id`, `title`, `description`, `technologies_used`) VALUES
(@user_id, 'Library Management System', 'Digital library management solution', 'Python, SQLite');

-- Insert career goals for this user
INSERT INTO `career_goals` (`user_id`, `description`, `target_date`, `status`, `goal_type`) VALUES
(@user_id, 'Complete Full Stack Development certification', '2025-06-30', 'not_started', 'career');

-- Insert certification for this user
INSERT INTO `certifications` (`user_id`, `certificate_path`, `upload_date`) VALUES
(@user_id, 'assets/certificates/202207100510073/python_cert.pdf', CURRENT_TIMESTAMP);

-- Now let's insert a second user and repeat the process
INSERT INTO `users` (`fullname`, `email`, `phone`, `password`, `registration_date`) VALUES
('Vikram Patel', 'vikram.patel@gmail.com', '8765432108', '$2y$10$hashed_password7', CURRENT_TIMESTAMP);

-- Get the ID of the second inserted user
SET @user_id = LAST_INSERT_ID();

-- Insert educational details for the second user
INSERT INTO `educational_details` (`user_id`, `university_name`, `enrollment_number`, `graduation_year`, `verification_status`) VALUES
(@user_id, 'Uka Tarsadia University', '201904100940070', 2019, 'verified');

-- Insert professional status for the second user
INSERT INTO `professional_status` (`user_id`, `current_status`, `company_name`, `position`, `start_date`, `is_current`) VALUES
(@user_id, 'employed', 'Wipro', 'Frontend Developer', '2019-07-10', 1);

-- Insert skills for the second user
INSERT INTO `skills` (`user_id`, `proficiency_level`, `language_specialization`, `tools`, `technologies`) VALUES
(@user_id, 'advanced', 'HTML, CSS, JavaScript', 'VS Code, Figma', 'React, Vue.js, Tailwind CSS');

-- Insert a project for the second user
INSERT INTO `projects` (`user_id`, `title`, `description`, `technologies_used`) VALUES
(@user_id, 'Portfolio Website', 'Personal portfolio website', 'HTML, CSS, JavaScript');

-- Insert career goals for the second user
INSERT INTO `career_goals` (`user_id`, `description`, `target_date`, `status`, `goal_type`) VALUES
(@user_id, 'Become a UI/UX Designer', '2024-12-31', 'in_progress', 'career');

-- Insert certification for the second user
INSERT INTO `certifications` (`user_id`, `certificate_path`, `upload_date`) VALUES
(@user_id, 'assets/certificates/201904100940070/frontend_cert.pdf', CURRENT_TIMESTAMP);