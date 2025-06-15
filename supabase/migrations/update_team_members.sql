
-- Update team_members table to include new organizational roles and fields
ALTER TABLE team_members 
MODIFY COLUMN role ENUM('dean', 'hod', 'president', 'vice_president', 'technical_head', 'non_technical_head', 'management_head', 'photography_head', 'social_media_manager', 'domain_lead', 'accountant', 'student', 'member', 'core', 'lead') NOT NULL;

ALTER TABLE team_members 
ADD COLUMN department VARCHAR(50) NULL AFTER role,
ADD COLUMN phone VARCHAR(20) NULL AFTER email;

-- Sample data for the new organizational structure
INSERT INTO team_members (name, position, bio, role, department, skills, linkedin, github, twitter, email, phone) VALUES
('Mr. Rohit Ravindra Lakare', 'President', 'Visionary leader dedicated to fostering innovation and excellence in the developer community.', 'president', 'CSE', 'Leadership, Strategic Planning, Team Management', 'https://linkedin.com/in/rohit-lakare', 'https://github.com/rohitlakare', 'https://twitter.com/rohitlakare', 'rohit.lakare@example.com', '+91-9876543210'),

('Dr. Sarah Johnson', 'Dean of Engineering', 'Experienced academic leader with 20+ years in computer science education.', 'dean', 'CSE', 'Academic Leadership, Research, Curriculum Development', 'https://linkedin.com/in/sarah-johnson', null, null, 'dean.sarah@university.edu', '+91-9876543211'),

('Prof. Amit Sharma', 'Head of Department', 'HOD of Computer Science with expertise in AI and Machine Learning.', 'hod', 'CSE', 'AI/ML, Department Management, Research', 'https://linkedin.com/in/amit-sharma', null, null, 'hod.amit@university.edu', '+91-9876543212'),

('Priya Patel', 'Vice President', 'Dynamic student leader focused on community engagement and technical excellence.', 'vice_president', 'CY', 'Leadership, Cybersecurity, Event Management', 'https://linkedin.com/in/priya-patel', 'https://github.com/priyapatel', null, 'priya.patel@student.edu', '+91-9876543213'),

('Vikram Singh', 'Technical Head', 'Senior developer passionate about emerging technologies and mentorship.', 'technical_head', 'CSE', 'Full Stack Development, DevOps, Mentoring', 'https://linkedin.com/in/vikram-singh', 'https://github.com/vikramsingh', 'https://twitter.com/vikramtech', 'vikram.singh@example.com', '+91-9876543214'),

('Anita Desai', 'Non-Technical Head', 'Expert in community outreach, event planning, and organizational management.', 'non_technical_head', 'management', 'Event Management, Public Relations, Marketing', 'https://linkedin.com/in/anita-desai', null, 'https://twitter.com/anitadesai', 'anita.desai@example.com', '+91-9876543215'),

('Rajesh Kumar', 'Management Head', 'Operations specialist with focus on team coordination and project management.', 'management_head', 'management', 'Project Management, Operations, Team Coordination', 'https://linkedin.com/in/rajesh-kumar', null, null, 'rajesh.kumar@example.com', '+91-9876543216'),

('Sneha Reddy', 'Photography Head', 'Creative visual storyteller capturing moments and memories for the community.', 'photography_head', 'CSE', 'Photography, Video Editing, Creative Design', 'https://linkedin.com/in/sneha-reddy', null, 'https://twitter.com/snehaphoto', 'sneha.reddy@example.com', '+91-9876543217'),

('Arjun Iyer', 'Social Media Manager', 'Digital marketing enthusiast managing online presence and community engagement.', 'social_media_manager', 'CY', 'Social Media Marketing, Content Creation, Analytics', 'https://linkedin.com/in/arjun-iyer', null, 'https://twitter.com/arjunsocial', 'arjun.iyer@example.com', '+91-9876543218'),

('Kavya Nair', 'Domain Lead - Web Development', 'Full-stack developer leading web development initiatives and workshops.', 'domain_lead', 'CSE', 'React, Node.js, MongoDB, JavaScript', 'https://linkedin.com/in/kavya-nair', 'https://github.com/kavyanair', null, 'kavya.nair@student.edu', '+91-9876543219'),

('Ravi Gupta', 'Accountant', 'Financial expert managing budgets, expenses, and financial planning for events.', 'accountant', 'management', 'Financial Management, Budgeting, Accounting', 'https://linkedin.com/in/ravi-gupta', null, null, 'ravi.gupta@example.com', '+91-9876543220'),

('Aditi Sharma', 'Student Member', 'Enthusiastic computer science student passionate about web development.', 'student', 'CSE', 'HTML, CSS, JavaScript, Python', 'https://linkedin.com/in/aditi-sharma', 'https://github.com/aditisharma', null, 'aditi.sharma@student.edu', '+91-9876543221'),

('Karan Malhotra', 'Student Member', 'AI/ML enthusiast working on innovative projects and research.', 'student', 'AIML', 'Python, TensorFlow, Machine Learning, Data Science', 'https://linkedin.com/in/karan-malhotra', 'https://github.com/karanmalhotra', null, 'karan.malhotra@student.edu', '+91-9876543222'),

('Deepika Singh', 'Active Member', 'Community contributor focusing on open source projects and documentation.', 'member', 'CY', 'Documentation, Open Source, Community Support', 'https://linkedin.com/in/deepika-singh', 'https://github.com/deepikasingh', null, 'deepika.singh@student.edu', '+91-9876543223'),

('Nikhil Joshi', 'Active Member', 'Event volunteer and workshop organizer with passion for teaching.', 'member', 'ALDS', 'Data Analysis, Workshop Organization, Teaching', 'https://linkedin.com/in/nikhil-joshi', 'https://github.com/nikhiljoshi', null, 'nikhil.joshi@student.edu', '+91-9876543224'),

('Pooja Agarwal', 'Active Member', 'Design enthusiast contributing to UI/UX projects and branding initiatives.', 'member', 'CSE', 'UI/UX Design, Figma, Adobe Creative Suite', 'https://linkedin.com/in/pooja-agarwal', null, 'https://twitter.com/poojadesign', 'pooja.agarwal@student.edu', '+91-9876543225'),

('Harsh Verma', 'Active Member', 'Mobile app developer working on Android and cross-platform solutions.', 'member', 'CSE', 'Android Development, Flutter, Kotlin, Java', 'https://linkedin.com/in/harsh-verma', 'https://github.com/harshverma', null, 'harsh.verma@student.edu', '+91-9876543226');
