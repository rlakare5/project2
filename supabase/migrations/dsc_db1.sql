-- Create the database
CREATE DATABASE IF NOT EXISTS dsc_db1;
USE dsc_db1;

-- Students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prn VARCHAR(20) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    contact_no VARCHAR(20),
    department ENUM('CSE', 'CY', 'AIML', 'ALDS') NOT NULL,
    year ENUM('FY', 'SY', 'TY', 'FINAL') NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255),
    linkedin_url VARCHAR(255),
    github_url VARCHAR(255),
    leetcode_url VARCHAR(255),
    other_url VARCHAR(255),
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- HODs table
CREATE TABLE hods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    department ENUM('CSE', 'CY', 'AIML', 'ALDS') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Deans table
CREATE TABLE deans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    speaker VARCHAR(100),
    max_participants INT,
    image VARCHAR(255),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Event participants table
CREATE TABLE event_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    student_id INT NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    attendance BOOLEAN DEFAULT 0,
    feedback TEXT,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY (event_id, student_id)
);

-- Opportunities table
CREATE TABLE opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    type ENUM('internship', 'certification', 'project', 'other') NOT NULL,
    link VARCHAR(255) NOT NULL,
    department ENUM('all', 'CSE', 'CY', 'AIML', 'ALDS') NOT NULL,
    created_by INT NOT NULL,
    created_role ENUM('hod', 'dean', 'admin') NOT NULL,
    expiry_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Certifications table
CREATE TABLE certifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    title VARCHAR(100) NOT NULL,
    issuer VARCHAR(100) NOT NULL,
    issue_date DATE NOT NULL,
    certificate_image VARCHAR(255) NOT NULL,
    verification_link VARCHAR(255),
    points INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT,
    approved_role ENUM('hod', 'dean', 'admin'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Student points table
CREATE TABLE student_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    points INT NOT NULL,
    description TEXT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    department ENUM('all', 'CSE', 'CY', 'AIML', 'ALDS') NOT NULL,
    year ENUM('all', 'FY', 'SY', 'TY', 'FINAL') NOT NULL,
    created_by INT NOT NULL,
    created_role ENUM('hod', 'dean', 'admin') NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Student notification mapping
CREATE TABLE student_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    notification_id INT NOT NULL,
    student_id INT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (notification_id) REFERENCES notifications(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY (notification_id, student_id)
);

-- Team members table
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position VARCHAR(100) NOT NULL,
    bio TEXT,
    image VARCHAR(255),
    role ENUM('core', 'lead', 'member') NOT NULL,
    skills VARCHAR(255),
    linkedin VARCHAR(255),
    github VARCHAR(255),
    twitter VARCHAR(255),
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Contact messages table
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data: Admin
INSERT INTO admins (username, name, email, password) 
VALUES ('admin', 'System Administrator', 'admin@sanjivani.edu.in', '$2y$10$8QFtY1PYhZ.piGH9Y2LHVeRqbk5/3Iz5E9ZmQzqGcPZKSTC8lxg1e'); -- Password: admin123

-- Sample data: Dean
INSERT INTO deans (username, name, email, password) 
VALUES ('dean', 'Dean of Engineering', 'dean@sanjivani.edu.in', '$2y$10$YIUk3G4yqTu/Xz43ZNZmTubKVDH8b.ZaRZ8bQIt1XUZSeyfVVRgaC'); -- Password: dean123

-- Sample data: HODs
INSERT INTO hods (username, name, email, password, department) VALUES 
('hodcse', 'HOD Computer Science', 'hodcse@sanjivani.edu.in', '$2y$10$7J/Kt4qwT6TQBtolTk0xE.gPX1DVNZ3fYBue7r5wKmtG.rftf5Ivi', 'CSE'), -- Password: hod123
('hodcy', 'HOD Cyber Security', 'hodcy@sanjivani.edu.in', '$2y$10$7J/Kt4qwT6TQBtolTk0xE.gPX1DVNZ3fYBue7r5wKmtG.rftf5Ivi', 'CY'), -- Password: hod123
('hodaiml', 'HOD AI & ML', 'hodaiml@sanjivani.edu.in', '$2y$10$7J/Kt4qwT6TQBtolTk0xE.gPX1DVNZ3fYBue7r5wKmtG.rftf5Ivi', 'AIML'), -- Password: hod123
('hodalds', 'HOD AI & Data Science', 'hodalds@sanjivani.edu.in', '$2y$10$7J/Kt4qwT6TQBtolTk0xE.gPX1DVNZ3fYBue7r5wKmtG.rftf5Ivi', 'ALDS'); -- Password: hod123

-- Sample data: Students
INSERT INTO students (prn, first_name, middle_name, last_name, email, contact_no, department, year, password) VALUES
('PRN001', 'John', 'A', 'Doe', 'john.doe@sanjivani.edu.in', '9876543210', 'CSE', 'TY', '$2y$10$HT6V0XBAfQdF1C1XJxHx5eMz9l5ccPACWNnUkjcTH2al2JTH2Ioj.'), -- Password: John@123
('PRN002', 'Jane', 'B', 'Smith', 'jane.smith@sanjivani.edu.in', '9876543211', 'CSE', 'TY', '$2y$10$XhIviLAzUfhzM5dUvI2eG.Q8zCvbm9IWOiAM2sG8LlAZ.1dR0PsNS'), -- Password: Jane@123
('PRN003', 'Amit', 'C', 'Patel', 'amit.patel@sanjivani.edu.in', '9876543212', 'AIML', 'SY', '$2y$10$wHs0mCHXdTOh6tF2EvDcdeqA4ZyRwq3QX10xnnfeBE8KPZvIhdLyi'), -- Password: Amit@123
('PRN004', 'Priya', 'D', 'Sharma', 'priya.sharma@sanjivani.edu.in', '9876543213', 'AIML', 'SY', '$2y$10$EBiAP4GE79yCUe8.FKgOceYW1Z3jHM8fLfQyW6YblzJZ2eE2FIhvW'), -- Password: Priya@123
('PRN005', 'Raj', 'E', 'Kumar', 'raj.kumar@sanjivani.edu.in', '9876543214', 'CY', 'FY', '$2y$10$TYmxCU.UfjLNwqRvUDO1g.nIisMPMnSQ0FOeR.AuxoAqlGw03eeRO'), -- Password: Raj@123
('PRN006', 'Neha', 'F', 'Verma', 'neha.verma@sanjivani.edu.in', '9876543215', 'CY', 'FY', '$2y$10$WEhX0HrA9gGjnzKbpgS6IunouSyO.0lcvRFYKTfmSHl6eqsQCzRsO'), -- Password: Neha@123
('PRN007', 'Vikram', 'G', 'Singh', 'vikram.singh@sanjivani.edu.in', '9876543216', 'ALDS', 'FINAL', '$2y$10$4B1mLMH7aCg7AiUbHHt98uBXBPbDAFsxwLtBTaNTLQ35Nj99UNm36'), -- Password: Vikram@123
('PRN008', 'Ananya', 'H', 'Joshi', 'ananya.joshi@sanjivani.edu.in', '9876543217', 'ALDS', 'FINAL', '$2y$10$nU1mzXfpd4VWm2eDWkB0xeYG5HX1t81jvpnQpW8kVUjFlE7p0zFky'); -- Password: Ananya@123

-- Sample data: Team members
INSERT INTO team_members (name, position, bio, role, skills, linkedin, github, twitter) VALUES
('Ravi Kumar', 'DSC Lead', 'A passionate tech enthusiast with a love for building community projects.', 'core', 'Leadership, Web Development, Public Speaking', 'https://linkedin.com', 'https://github.com', 'https://twitter.com'),
('Anjali Desai', 'Technical Lead', 'Full-stack developer with experience in React and Node.js.', 'core', 'React, Node.js, MongoDB, Express', 'https://linkedin.com', 'https://github.com', null),
('Suresh Patel', 'Design Lead', 'UI/UX designer passionate about creating beautiful user experiences.', 'lead', 'Figma, Adobe XD, UI/UX, Illustration', 'https://linkedin.com', null, 'https://twitter.com'),
('Meera Shah', 'Android Lead', 'Android developer with a focus on Kotlin and Jetpack Compose.', 'lead', 'Android, Kotlin, Java, Firebase', 'https://linkedin.com', 'https://github.com', null),
('Rahul Gupta', 'ML Lead', 'Machine Learning enthusiast specializing in computer vision.', 'lead', 'Python, TensorFlow, PyTorch, OpenCV', 'https://linkedin.com', 'https://github.com', null),
('Neha Sharma', 'Web Lead', 'Web developer with expertise in modern JavaScript frameworks.', 'lead', 'JavaScript, React, Vue, SCSS', 'https://linkedin.com', 'https://github.com', 'https://twitter.com'),
('Kiran Patel', 'Member', 'Computer Science student with interest in cloud computing.', 'member', 'AWS, Google Cloud, Docker, Kubernetes', 'https://linkedin.com', 'https://github.com', null),
('Deepak Verma', 'Member', 'Open source contributor and competitive programmer.', 'member', 'C++, Data Structures, Algorithms', 'https://linkedin.com', 'https://github.com', null),
('Priya Singh', 'Member', 'IoT enthusiast working on smart home solutions.', 'member', 'Arduino, Raspberry Pi, MQTT, C', 'https://linkedin.com', 'https://github.com', null),
('Arun Joshi', 'Member', 'Backend developer specializing in microservices.', 'member', 'Java, Spring Boot, Microservices', 'https://linkedin.com', 'https://github.com', null);

-- Sample data: Events
INSERT INTO events (title, description, event_date, event_time, location, speaker, max_participants, created_by) VALUES
('Web Development Workshop', 'Learn the basics of web development with HTML, CSS, and JavaScript. This hands-on workshop will help you build your first website from scratch.', '2023-10-15', '10:00:00', 'Computer Lab 1', 'Prof. Sharma', 30, 1),
('Introduction to Machine Learning', 'This workshop covers the fundamentals of machine learning, including supervised and unsupervised learning, with practical examples using Python.', '2023-10-20', '14:00:00', 'Seminar Hall', 'Dr. Patel', 50, 1),
('Competitive Programming Contest', 'Test your coding skills in this competitive programming contest. Solve algorithmic problems and compete with your peers.', '2023-11-05', '09:00:00', 'Computer Lab 2', NULL, 100, 1),
('Cloud Computing Seminar', 'Learn about cloud computing technologies, including AWS, Azure, and Google Cloud Platform.', '2023-11-15', '11:00:00', 'Auditorium', 'Mr. Rajesh Kumar (AWS)', 200, 1),
('Hackathon: Solve for Community', 'A 24-hour hackathon to build solutions for local community problems using technology.', '2023-12-01', '09:00:00', 'Main Building', NULL, 150, 1);

-- Sample data: Opportunities
INSERT INTO opportunities (title, description, type, link, department, created_by, created_role, expiry_date) VALUES
('Google Summer of Code 2023', 'Apply for Google Summer of Code to work with open source organizations.', 'internship', 'https://summerofcode.withgoogle.com/', 'all', 1, 'admin', '2023-12-31'),
('AWS Certified Solutions Architect', 'Get certified as an AWS Solutions Architect to boost your cloud computing career.', 'certification', 'https://aws.amazon.com/certification/certified-solutions-architect-associate/', 'CSE', 1, 'hod', '2023-11-30'),
('TensorFlow Developer Certificate', 'Demonstrate your proficiency in using TensorFlow to solve deep learning problems.', 'certification', 'https://www.tensorflow.org/certificate', 'AIML', 2, 'hod', '2023-12-15'),
('Smart City Project', 'Contribute to developing smart solutions for urban infrastructure using IoT.', 'project', 'https://example.com/smart-city', 'all', 1, 'dean', '2023-12-20'),
('Microsoft Learn Student Ambassadors', 'Join the Microsoft Learn Student Ambassadors program to develop leadership skills.', 'other', 'https://studentambassadors.microsoft.com/', 'all', 1, 'admin', '2023-11-25');

-- Sample data: Notifications
INSERT INTO notifications (title, message, department, year, created_by, created_role) VALUES
('Important: Registration Deadline', 'Registration for the Web Development Workshop closes tomorrow. Don\'t miss out!', 'all', 'all', 1, 'admin'),
('New Certification Opportunity', 'AWS is offering a 50% discount on certification exams for students until December 31.', 'CSE', 'all', 1, 'hod'),
('Hackathon Teams Formation', 'Form your teams for the upcoming hackathon. Maximum team size is 4 members.', 'all', 'all', 1, 'dean'),
('Guest Lecture Announcement', 'A guest lecture on Cybersecurity will be held on November 10 at 2 PM in the Seminar Hall.', 'CY', 'all', 2, 'hod'),
('Internship Opportunity', 'TCS is accepting applications for winter internships. Last date to apply is November 15.', 'all', 'FINAL', 1, 'admin');

-- Create view for student points summary
CREATE VIEW student_points_summary AS
SELECT 
    s.id,
    s.prn,
    CONCAT(s.first_name, ' ', s.last_name) AS name,
    s.department,
    s.year,
    SUM(sp.points) AS total_points
FROM 
    students s
LEFT JOIN 
    student_points sp ON s.id = sp.student_id
GROUP BY 
    s.id, s.prn, name, s.department, s.year
ORDER BY 
    total_points DESC;

-- Create view for department-wise leaderboard
CREATE VIEW department_leaderboard AS
SELECT 
    department,
    year,
    id,
    prn,
    name,
    total_points,
    RANK() OVER (PARTITION BY department, year ORDER BY total_points DESC) AS rank_in_class
FROM 
    student_points_summary;

-- Create view for university-wide leaderboard
CREATE VIEW university_leaderboard AS
SELECT 
    id,
    prn,
    name,
    department,
    year,
    total_points,
    RANK() OVER (ORDER BY total_points DESC) AS university_rank
FROM 
    student_points_summary;