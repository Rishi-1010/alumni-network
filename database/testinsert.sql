-- Insert into users table first (since other tables reference user_id)
INSERT INTO `users` (`fullname`, `email`, `phone`, `password`, `registration_date`) VALUES
('Raj Patel', 'raj.patel@gmail.com', '9876543210', '$2y$10$hashed_password1', CURRENT_TIMESTAMP),
('Priya Shah', 'priya.shah@gmail.com', '8765432109', '$2y$10$hashed_password2', CURRENT_TIMESTAMP),
('Amit Kumar', 'amit.kumar@gmail.com', '7654321098', '$2y$10$hashed_password3', CURRENT_TIMESTAMP),
('Meera Singh', 'meera.singh@gmail.com', '6543210987', '$2y$10$hashed_password4', CURRENT_TIMESTAMP),
('Jay Mehta', 'jay.mehta@gmail.com', '9876543211', '$2y$10$hashed_password5', CURRENT_TIMESTAMP);

-- Insert into educational_details with mixed enrollment number patterns
INSERT INTO `educational_details` (`user_id`, `university_name`, `enrollment_number`, `graduation_year`, `verification_status`) VALUES
(1, 'Uka Tarsadia University', '202307100510072', 2027, 'pending'),
(2, 'Uka Tarsadia University', '201604100940069', 2020, 'verified'),
(3, 'Uka Tarsadia University', '05BCA69', 2008, 'verified'),
(4, 'Uka Tarsadia University', '11BCA46', 2014, 'verified'),
(5, 'Uka Tarsadia University', '08MCA47', 2010, 'verified');

-- Insert corresponding professional status
INSERT INTO `professional_status` (`user_id`, `current_status`, `company_name`, `position`, `start_date`, `is_current`) VALUES
(1, 'student', NULL, NULL, NULL, 1),
(2, 'employed', 'TCS', 'Software Engineer', '2020-06-15', 1),
(3, 'employed', 'Infosys', 'Senior Developer', '2008-07-01', 1),
(4, 'employed', 'Google', 'Product Manager', '2014-08-20', 1),
(5, 'employed', 'Microsoft', 'Technical Lead', '2010-06-30', 1);

-- Insert skills
INSERT INTO `skills` (`user_id`, `proficiency_level`, `language_specialization`, `tools`, `technologies`) VALUES
(24, 'advanced', 'Python, Java, PHP', 'VS Code, Eclipse, PHPStorm', 'Django, Spring Boot, Laravel'),
(115, 'intermediate', 'JavaScript, TypeScript', 'Visual Studio Code, Git', 'React, Node.js, Express.js'),
(1, 'expert', 'Java, Kotlin, Python', 'IntelliJ IDEA, Android Studio', 'Spring Boot, Android SDK, Firebase'),
(2, 'intermediate', 'JavaScript, Python, SQL', 'Docker, Jenkins, GitLab', 'MERN Stack, AWS, Redux'),
(3, 'advanced', 'C#, JavaScript, TypeScript', 'Visual Studio, Azure DevOps', '.NET Core, Angular, Azure Services'),
(4, 'expert', 'Python, R, SQL', 'Jupyter, RStudio, Tableau', 'TensorFlow, PyTorch, Scikit-learn'),
(5, 'advanced', 'Go, Python, JavaScript', 'Docker, Kubernetes, Terraform', 'Microservices, AWS, GCP');

-- Insert projects
INSERT INTO `projects` (`user_id`, `title`, `description`, `technologies_used`, `start_date`, `end_date`) VALUES
(1, 'Student Management System', 'College project for managing student records', 'Python, MySQL', '2023-01-15', '2023-04-30'),
(2, 'E-commerce Platform', 'Online shopping platform', 'Java, Spring Boot', '2020-08-01', '2021-02-28'),
(3, 'Social Media App', 'Mobile application for social networking', 'React Native, Node.js', '2019-03-15', '2019-12-31'),
(4, 'Healthcare Management System', 'Hospital management solution', 'Angular, MongoDB', '2018-06-01', '2019-01-31'),
(5, 'Cloud Migration Project', 'Enterprise system migration to cloud', 'AWS, Docker', '2017-09-01', '2018-03-31');

-- Insert career goals
INSERT INTO `career_goals` (`user_id`, `description`, `target_date`, `status`, `goal_type`) VALUES
(1, 'Complete AWS certification', '2024-12-31', 'in_progress', 'career'),
(2, 'Lead a team of 10 developers', '2024-06-30', 'not_started', 'career'),
(3, 'Start a tech consultancy', '2025-01-01', 'in_progress', 'career'),
(4, 'Complete MBA', '2024-08-31', 'in_progress', 'career'),
(5, 'Become a Solution Architect', '2024-12-31', 'in_progress', 'career');

-- Insert certifications
INSERT INTO `certifications` (`user_id`, `certificate_path`, `upload_date`) VALUES
(1, 'assets/certificates/202307100510072/python_cert.pdf', CURRENT_TIMESTAMP),
(2, 'assets/certificates/201604100940069/java_cert.pdf', CURRENT_TIMESTAMP),
(3, 'assets/certificates/05BCA69/fullstack_cert.pdf', CURRENT_TIMESTAMP),
(4, 'assets/certificates/11BCA46/pmp_cert.pdf', CURRENT_TIMESTAMP),
(5, 'assets/certificates/08MCA47/aws_cert.pdf', CURRENT_TIMESTAMP);