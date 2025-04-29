-- Test data for Alumni Network Database

-- Insert test users
INSERT INTO users (fullname, email, phone, dob, password, registration_date) VALUES
('John Smith', 'john.smith@example.com', '9876543210', '1995-05-15', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-01-15'),
('Sarah Johnson', 'sarah.j@example.com', '8765432109', '1996-08-22', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-02-20'),
('Michael Brown', 'michael.b@example.com', '7654321098', '1994-11-30', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-03-10'),
('Emily Davis', 'emily.d@example.com', '6543210987', '1997-03-12', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-04-05'),
('David Wilson', 'david.w@example.com', '5432109876', '1995-07-18', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-05-22'),
('Jennifer Lee', 'jennifer.l@example.com', '4321098765', '1996-01-25', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-06-15'),
('Robert Taylor', 'robert.t@example.com', '3210987654', '1994-09-08', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-07-30'),
('Lisa Anderson', 'lisa.a@example.com', '2109876543', '1997-12-14', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-08-12'),
('James Martinez', 'james.m@example.com', '1098765432', '1995-04-20', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-09-25'),
('Patricia White', 'patricia.w@example.com', '9876543211', '1996-10-05', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-10-18');

-- Insert educational details
INSERT INTO educational_details (user_id, university_name, enrollment_number, graduation_year, verification_status, verification_date) VALUES
(LAST_INSERT_ID()-9, 'Uka Tarsadia University', '2023051001001', 2026, 'verified', '2023-11-01'),
(LAST_INSERT_ID()-8, 'Uka Tarsadia University', '2023051001002', 2026, 'pending', NULL),
(LAST_INSERT_ID()-7, 'Uka Tarsadia University', '2023051001003', 2026, 'verified', '2023-11-05'),
(LAST_INSERT_ID()-6, 'Uka Tarsadia University', '2023051001004', 2026, 'rejected', '2023-11-10'),
(LAST_INSERT_ID()-5, 'Uka Tarsadia University', '2023051001005', 2026, 'verified', '2023-11-15'),
(LAST_INSERT_ID()-4, 'Uka Tarsadia University', '2023051001006', 2026, 'pending', NULL),
(LAST_INSERT_ID()-3, 'Uka Tarsadia University', '2023051001007', 2026, 'verified', '2023-11-20'),
(LAST_INSERT_ID()-2, 'Uka Tarsadia University', '2023051001008', 2026, 'pending', NULL),
(LAST_INSERT_ID()-1, 'Uka Tarsadia University', '2023051001009', 2026, 'verified', '2023-11-25'),
(LAST_INSERT_ID(), 'Uka Tarsadia University', '2023051001010', 2026, 'pending', NULL);

-- Insert professional status
INSERT INTO professional_status (user_id, current_status, company_name, position) VALUES
(LAST_INSERT_ID()-9, 'employed', 'Tech Solutions Inc.', 'Software Developer'),
(LAST_INSERT_ID()-8, 'seeking', NULL, NULL),
(LAST_INSERT_ID()-7, 'employed', 'Digital Innovations', 'Web Developer'),
(LAST_INSERT_ID()-6, 'student', NULL, NULL),
(LAST_INSERT_ID()-5, 'employed', 'Global Tech', 'Frontend Developer'),
(LAST_INSERT_ID()-4, 'seeking', NULL, NULL),
(LAST_INSERT_ID()-3, 'employed', 'Future Systems', 'Full Stack Developer'),
(LAST_INSERT_ID()-2, 'student', NULL, NULL),
(LAST_INSERT_ID()-1, 'employed', 'Smart Solutions', 'Backend Developer'),
(LAST_INSERT_ID(), 'seeking', NULL, NULL);

-- Insert skills
INSERT INTO skills (user_id, language_specialization, tools, technologies, proficiency_level) VALUES
(LAST_INSERT_ID()-9, 'Java', 'Eclipse, Maven', 'Spring Boot, Hibernate', 'advanced'),
(LAST_INSERT_ID()-9, 'Python', 'PyCharm, pip', 'Django, Flask', 'intermediate'),
(LAST_INSERT_ID()-8, 'JavaScript', 'VS Code, npm', 'React, Node.js', 'advanced'),
(LAST_INSERT_ID()-8, 'HTML/CSS', 'VS Code', 'Bootstrap, SASS', 'expert'),
(LAST_INSERT_ID()-7, 'PHP', 'VS Code, Composer', 'Laravel, MySQL', 'advanced'),
(LAST_INSERT_ID()-7, 'JavaScript', 'VS Code, npm', 'Vue.js, Express', 'intermediate'),
(LAST_INSERT_ID()-6, 'Python', 'PyCharm, pip', 'TensorFlow, PyTorch', 'beginner'),
(LAST_INSERT_ID()-6, 'R', 'RStudio', 'ggplot2, dplyr', 'intermediate'),
(LAST_INSERT_ID()-5, 'JavaScript', 'VS Code, npm', 'React, Redux', 'expert'),
(LAST_INSERT_ID()-5, 'TypeScript', 'VS Code, npm', 'Angular, RxJS', 'advanced'),
(LAST_INSERT_ID()-4, 'Java', 'IntelliJ IDEA, Maven', 'Spring, JPA', 'intermediate'),
(LAST_INSERT_ID()-4, 'Kotlin', 'Android Studio', 'Android SDK', 'beginner'),
(LAST_INSERT_ID()-3, 'PHP', 'VS Code, Composer', 'Laravel, MySQL', 'expert'),
(LAST_INSERT_ID()-3, 'JavaScript', 'VS Code, npm', 'Node.js, Express', 'advanced'),
(LAST_INSERT_ID()-2, 'Python', 'PyCharm, pip', 'Django, PostgreSQL', 'intermediate'),
(LAST_INSERT_ID()-2, 'JavaScript', 'VS Code, npm', 'React, Firebase', 'beginner'),
(LAST_INSERT_ID()-1, 'Java', 'Eclipse, Maven', 'Spring Boot, MongoDB', 'advanced'),
(LAST_INSERT_ID()-1, 'Python', 'PyCharm, pip', 'FastAPI, SQLAlchemy', 'intermediate'),
(LAST_INSERT_ID(), 'JavaScript', 'VS Code, npm', 'Next.js, GraphQL', 'intermediate'),
(LAST_INSERT_ID(), 'TypeScript', 'VS Code, npm', 'React, TypeORM', 'beginner');

-- Insert projects
INSERT INTO projects (user_id, title, description, technologies_used) VALUES
(LAST_INSERT_ID()-9, 'E-commerce Platform', 'A full-featured e-commerce platform with payment integration', 'Java, Spring Boot, MySQL, React'),
(LAST_INSERT_ID()-9, 'Task Management App', 'A collaborative task management application', 'Python, Django, PostgreSQL, Vue.js'),
(LAST_INSERT_ID()-8, 'Portfolio Website', 'Personal portfolio website with animations', 'HTML, CSS, JavaScript, GSAP'),
(LAST_INSERT_ID()-8, 'Weather Dashboard', 'Real-time weather dashboard with API integration', 'JavaScript, React, OpenWeather API'),
(LAST_INSERT_ID()-7, 'Content Management System', 'CMS for managing blog content', 'PHP, Laravel, MySQL, Bootstrap'),
(LAST_INSERT_ID()-7, 'RESTful API', 'API for mobile application backend', 'Node.js, Express, MongoDB'),
(LAST_INSERT_ID()-6, 'Machine Learning Model', 'Image classification model using TensorFlow', 'Python, TensorFlow, NumPy, Pandas'),
(LAST_INSERT_ID()-6, 'Data Visualization Dashboard', 'Dashboard for visualizing sales data', 'R, Shiny, ggplot2, dplyr'),
(LAST_INSERT_ID()-5, 'Social Media Clone', 'A simplified clone of a social media platform', 'React, Redux, Firebase, Material UI'),
(LAST_INSERT_ID()-5, 'E-learning Platform', 'Online learning platform with video streaming', 'Angular, Node.js, MongoDB, AWS'),
(LAST_INSERT_ID()-4, 'Inventory Management System', 'System for tracking inventory and sales', 'Java, Spring, MySQL, Thymeleaf'),
(LAST_INSERT_ID()-4, 'Mobile App', 'Cross-platform mobile application', 'Kotlin, Android SDK, Firebase'),
(LAST_INSERT_ID()-3, 'Booking System', 'Online booking system for services', 'PHP, Laravel, MySQL, Vue.js'),
(LAST_INSERT_ID()-3, 'Real-time Chat Application', 'Chat application with real-time messaging', 'Node.js, Socket.io, Express, MongoDB'),
(LAST_INSERT_ID()-2, 'Blog Platform', 'Multi-user blog platform', 'Python, Django, PostgreSQL, Bootstrap'),
(LAST_INSERT_ID()-2, 'Fitness Tracker', 'Mobile app for tracking fitness goals', 'React Native, Firebase, Redux'),
(LAST_INSERT_ID()-1, 'Microservices Architecture', 'Distributed system using microservices', 'Java, Spring Cloud, Docker, Kubernetes'),
(LAST_INSERT_ID()-1, 'API Gateway', 'API gateway for microservices', 'Python, FastAPI, Redis, Nginx'),
(LAST_INSERT_ID(), 'Next.js E-commerce', 'Modern e-commerce platform with Next.js', 'Next.js, GraphQL, PostgreSQL, Tailwind CSS'),
(LAST_INSERT_ID(), 'Task Management App', 'Task management application with TypeScript', 'TypeScript, React, TypeORM, PostgreSQL'); 