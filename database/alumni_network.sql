- Create the database
CREATE DATABASE IF NOT EXISTS alumni_network;
USE alumni_network;

-- Users table for basic authentication and profile
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    dob DATE NOT NULL, -- Added Date of Birth column
    password VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP,
    certificate_id VARCHAR(255),
    certificate_path VARCHAR(255)
);

-- Educational details
CREATE TABLE IF NOT EXISTS educational_details (
    edu_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    university_name VARCHAR(255) NOT NULL,
    enrollment_number VARCHAR(15) NOT NULL UNIQUE,
    graduation_year INT DEFAULT NULL,
    verification_status VARCHAR(50),
    verification_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Professional status
CREATE TABLE professional_status (
    prof_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    current_status ENUM('employed', 'seeking', 'student', 'freelancer', 'other') NOT NULL,
    company_name VARCHAR(200),
    position VARCHAR(100),
    start_date DATE,
    is_current BOOLEAN DEFAULT TRUE,
    freelance_title VARCHAR(200),
    platforms TEXT,
    expertise_areas TEXT,
    experience_years INT,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);


-- Admin users
CREATE TABLE admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    last_login TIMESTAMP
);


-- Projects table
CREATE TABLE projects (
    project_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    technologies_used TEXT,
    start_date DATE,
    end_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Skills table (updated version)
CREATE TABLE skills (
    skill_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    language_specialization JSON,
    tools JSON,
    technologies JSON,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Career goals table
CREATE TABLE career_goals (
    goal_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    goal_type ENUM('learning', 'career', 'other'),
    description TEXT NOT NULL,
    target_date DATE,
    status ENUM('planned', 'in_progress', 'achieved', 'cancelled') DEFAULT 'planned',
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Certifications table
CREATE TABLE certifications (
    certificate_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    certificate_path VARCHAR(255),
    upload_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    event_type ENUM('physical', 'virtual') NOT NULL,
    max_attendees INT NOT NULL,
    registration_deadline DATETIME NOT NULL,
    status ENUM('upcoming', 'ongoing', 'past', 'cancelled') NOT NULL DEFAULT 'upcoming',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
); 