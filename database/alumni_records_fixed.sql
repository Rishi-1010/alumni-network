-- First, let's check if the professional_status table has a start_date column
-- If not, we'll need to modify our approach

-- Let's insert users with explicit IDs
INSERT INTO `users` (`user_id`, `fullname`, `email`, `phone`, `password`, `registration_date`) VALUES
(1001, 'Neha Sharma', 'neha.sharma@gmail.com', '9876543212', '$2y$10$hashed_password6', CURRENT_TIMESTAMP),
(1002, 'Vikram Patel', 'vikram.patel@gmail.com', '8765432108', '$2y$10$hashed_password7', CURRENT_TIMESTAMP),
(1003, 'Ananya Reddy', 'ananya.reddy@gmail.com', '7654321097', '$2y$10$hashed_password8', CURRENT_TIMESTAMP),
(1004, 'Rahul Gupta', 'rahul.gupta@gmail.com', '6543210986', '$2y$10$hashed_password9', CURRENT_TIMESTAMP),
(1005, 'Sneha Joshi', 'sneha.joshi@gmail.com', '9876543213', '$2y$10$hashed_password10', CURRENT_TIMESTAMP),
(1006, 'Arjun Singh', 'arjun.singh@gmail.com', '8765432107', '$2y$10$hashed_password11', CURRENT_TIMESTAMP),
(1007, 'Priyanka Mehta', 'priyanka.mehta@gmail.com', '7654321096', '$2y$10$hashed_password12', CURRENT_TIMESTAMP);

-- Insert educational details
INSERT INTO `educational_details` (`user_id`, `university_name`, `enrollment_number`, `graduation_year`, `verification_status`) VALUES
(1001, 'Uka Tarsadia University', '202207100510073', 2026, 'pending'),
(1002, 'Uka Tarsadia University', '201904100940070', 2019, 'verified'),
(1003, 'Uka Tarsadia University', '06BCA70', 2009, 'verified'),
(1004, 'Uka Tarsadia University', '12BCA47', 2015, 'verified'),
(1005, 'Uka Tarsadia University', '09MCA48', 2011, 'verified'),
(1006, 'Uka Tarsadia University', '202107100510074', 2025, 'pending'),
(1007, 'Uka Tarsadia University', '201804100940071', 2018, 'verified');

-- Insert professional status (without start_date if it doesn't exist)
INSERT INTO `professional_status` (`user_id`, `current_status`, `company_name`, `position`, `is_current`) VALUES
(1001, 'student', NULL, NULL, 1),
(1002, 'employed', 'Wipro', 'Frontend Developer', 1),
(1003, 'employed', 'Accenture', 'Project Manager', 1),
(1004, 'employed', 'Amazon', 'Data Scientist', 1),
(1005, 'employed', 'IBM', 'Cloud Architect', 1),
(1006, 'student', NULL, NULL, 1),
(1007, 'employed', 'Tech Mahindra', 'DevOps Engineer', 1);

-- Insert skills
INSERT INTO `skills` (`user_id`, `proficiency_level`, `language_specialization`, `tools`, `technologies`) VALUES
(1001, 'intermediate', 'Python, JavaScript', 'PyCharm, VS Code', 'Flask, React, MongoDB'),
(1002, 'advanced', 'HTML, CSS, JavaScript', 'VS Code, Figma', 'React, Vue.js, Tailwind CSS'),
(1003, 'expert', 'Java, Python, SQL', 'Jira, Confluence', 'Agile, Scrum, PMP'),
(1004, 'expert', 'Python, R, SQL', 'Jupyter, Tableau', 'Machine Learning, Deep Learning, NLP'),
(1005, 'advanced', 'Java, Python, Go', 'AWS Console, Terraform', 'Cloud Computing, Serverless, Kubernetes'),
(1006, 'intermediate', 'Java, Python', 'Eclipse, PyCharm', 'Spring Boot, Django'),
(1007, 'advanced', 'Python, Shell Scripting', 'Jenkins, GitLab', 'CI/CD, Docker, Kubernetes');

-- Insert projects
INSERT INTO `projects` (`user_id`, `title`, `description`, `technologies_used`) VALUES
(1001, 'Library Management System', 'Digital library management solution', 'Python, SQLite'),
(1002, 'Portfolio Website', 'Personal portfolio website', 'HTML, CSS, JavaScript'),
(1003, 'ERP Implementation', 'Enterprise resource planning system', 'Java, Oracle'),
(1004, 'Customer Churn Prediction', 'ML model to predict customer churn', 'Python, Scikit-learn'),
(1005, 'Cloud Infrastructure Setup', 'Multi-cloud infrastructure deployment', 'Terraform, AWS, Azure'),
(1006, 'Mobile App Development', 'Cross-platform mobile application', 'React Native, Firebase'),
(1007, 'Automated Testing Pipeline', 'CI/CD pipeline for automated testing', 'Jenkins, Docker, Selenium');

-- Insert career goals
INSERT INTO `career_goals` (`user_id`, `description`, `target_date`, `status`, `goal_type`) VALUES
(1001, 'Complete Full Stack Development certification', '2025-06-30', 'not_started', 'career'),
(1002, 'Become a UI/UX Designer', '2024-12-31', 'in_progress', 'career'),
(1003, 'Obtain PMP certification', '2024-09-30', 'in_progress', 'career'),
(1004, 'Publish research paper in AI', '2024-12-31', 'not_started', 'career'),
(1005, 'Become AWS Solutions Architect', '2024-10-31', 'in_progress', 'career'),
(1006, 'Start a tech blog', '2025-03-31', 'not_started', 'career'),
(1007, 'Lead DevOps team', '2024-12-31', 'in_progress', 'career');

-- Insert certifications
INSERT INTO `certifications` (`user_id`, `certificate_path`, `upload_date`) VALUES
(1001, 'assets/certificates/202207100510073/python_cert.pdf', CURRENT_TIMESTAMP),
(1002, 'assets/certificates/201904100940070/frontend_cert.pdf', CURRENT_TIMESTAMP),
(1003, 'assets/certificates/06BCA70/pmp_cert.pdf', CURRENT_TIMESTAMP),
(1004, 'assets/certificates/12BCA47/data_science_cert.pdf', CURRENT_TIMESTAMP),
(1005, 'assets/certificates/09MCA48/cloud_cert.pdf', CURRENT_TIMESTAMP),
(1006, 'assets/certificates/202107100510074/java_cert.pdf', CURRENT_TIMESTAMP),
(1007, 'assets/certificates/201804100940071/devops_cert.pdf', CURRENT_TIMESTAMP); 