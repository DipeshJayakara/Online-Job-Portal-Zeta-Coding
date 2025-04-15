CREATE DATABASE Online_job_Portal;
USE Online_job_Portal;;


-- ✅ Users Table (For Job Seekers, Employers, and Admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL, -- Hashed password
    phone VARCHAR(15) NULL,
    address TEXT NULL,
    role ENUM('seeker', 'provider', 'admin') NOT NULL,
    resume VARCHAR(255) NULL, -- Resume file path
    degree VARCHAR(255) NULL,
    university VARCHAR(255) NULL,
    passing_year YEAR NULL,
    achievements TEXT NULL,
    certificate VARCHAR(255) NULL, -- Certificate file path
    verified TINYINT(1) NOT NULL DEFAULT 0, -- Email verification status
    reset_token VARCHAR(100) NULL,
    reset_expiry DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ✅ Jobs Table (Posted by Employers)
CREATE TABLE jobs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL, -- Employer (User ID)
    company_name VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    salary DECIMAL(10,2) NOT NULL CHECK (salary >= 0), -- Salary stored in decimal
    job_type ENUM('Full-Time', 'Part-Time', 'Internship') NOT NULL DEFAULT 'Full-Time',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ✅ Applications Table (Job Applications)
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    applicant_id INT NOT NULL,
    ats_score DECIMAL(5,2) NOT NULL DEFAULT 0.00, -- ATS Score
    status ENUM('Pending', 'Accepted', 'Rejected') DEFAULT 'Pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE,
    FOREIGN KEY (applicant_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ✅ ATS Keywords Table (For Resume Screening)
CREATE TABLE ats_keywords (
    id INT AUTO_INCREMENT PRIMARY KEY,
    keyword VARCHAR(50) UNIQUE NOT NULL
);

-- ✅ ATS Scores Table (Tracking Resume Scores)
CREATE TABLE ats_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    seeker_id INT NOT NULL,
    job_id INT NOT NULL,
    score DECIMAL(5,2) NOT NULL, 
    feedback TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seeker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
);

-- ✅ Admins Table (Platform Administrators)
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL 
);

-- ✅ Sample Data Insertions
INSERT INTO users (name, email, password, role) 
VALUES 
('John Doe', 'john@example.com', '$2y$10$EXAMPLEHASH1', 'seeker'),
('Jane Smith', 'jane@example.com', '$2y$10$EXAMPLEHASH2', 'provider');

INSERT INTO jobs (provider_id, company_name, title, description, location, salary, job_type) 
VALUES 
(2, 'Tech Corp', 'Software Engineer', 'Develop web applications.', 'New York', 60000.00, 'Full-Time'),
(2, 'Health Inc.', 'Data Analyst', 'Analyze healthcare data.', 'Los Angeles', 50000.00, 'Full-Time');

INSERT INTO admin (username, password) 
VALUES 
('admin1', '$2y$10$EXAMPLEHASH3');

INSERT INTO ats_keywords (keyword) 
VALUES 
('JavaScript'), ('PHP'), ('MySQL'), ('Python');

ALTER TABLE users ADD COLUMN skills JSON NULL;
ALTER TABLE users ADD COLUMN additional_education TEXT NULL;
ALTER TABLE users
MODIFY COLUMN skills JSON;
ALTER TABLE jobs ADD COLUMN verified TINYINT(1) NOT NULL DEFAULT 0;

