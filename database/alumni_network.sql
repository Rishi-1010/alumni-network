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
    graduation_year INT NOT NULL,
    verification_status VARCHAR(50),
    verification_date DATE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Professional status
CREATE TABLE professional_status (
    prof_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    current_status ENUM('employed', 'seeking', 'student', 'other') NOT NULL,
    company_name VARCHAR(200),
    position VARCHAR(100),
    start_date DATE,
    is_current BOOLEAN DEFAULT TRUE,
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

-- Skills table
CREATE TABLE skills (
    skill_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    skill_name VARCHAR(100) NOT NULL,
    proficiency_level ENUM('beginner', 'intermediate', 'advanced', 'expert'),
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
