<?php
session_start();
include 'includes/config.php';

// Check if user is logged in and has appropriate role
if(!isLoggedIn() || $_SESSION['user_role'] === 'student') {
    redirect('login.php');
}

$role = $_SESSION['user_role'];
$user_id = $_SESSION['user_id'];
$department = isset($_SESSION['department']) ? $_SESSION['department'] : 'all';

// Get statistics based on role
$stats = [];

// Total students
if($role === 'hod') {
    $query = "SELECT COUNT(*) as count FROM students WHERE department = '$department'";
} else {
    $query = "SELECT COUNT(*) as count FROM students";
}
$result = mysqli_query($conn, $query);
$stats['total_students'] = mysqli_fetch_assoc($result)['count'];

// Total events
$query = "SELECT COUNT(*) as count FROM events";
$result = mysqli_query($conn, $query);
$stats['total_events'] = mysqli_fetch_assoc($result)['count'];

// Total certifications
if($role === 'hod') {
    $query = "SELECT COUNT(*) as count FROM certifications c 
              JOIN students s ON c.student_id = s.id 
              WHERE s.department = '$department'";
} else {
    $query = "SELECT COUNT(*) as count FROM certifications";
}
$result = mysqli_query($conn, $query);
$stats['total_certifications'] = mysqli_fetch_assoc($result)['count'];

// Total opportunities
$query = "SELECT COUNT(*) as count FROM opportunities";
$result = mysqli_query($conn, $query);
$stats['total_opportunities'] = mysqli_fetch_assoc($result)['count'];

// Handle form submissions
if($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add notification
    if(isset($_POST['add_notification'])) {
        $title = sanitize($_POST['title']);
        $message = sanitize($_POST['message']);
        $target_dept = sanitize($_POST['department']);
        $target_year = sanitize($_POST['year']);
        
        $query = "INSERT INTO notifications (title, message, department, year, created_by, created_role) 
                  VALUES ('$title', '$message', '$target_dept', '$target_year', $user_id, '$role')";
                  
        if(mysqli_query($conn, $query)) {
            // Get notification ID
            $notification_id = mysqli_insert_id($conn);
            
            // Add to student notifications
            $student_query = "SELECT id FROM students WHERE 1=1";
            
            if($target_dept !== 'all') {
                $student_query .= " AND department = '$target_dept'";
            }
            
            if($target_year !== 'all') {
                $student_query .= " AND year = '$target_year'";
            }
            
            $student_result = mysqli_query($conn, $student_query);
            
            while($student = mysqli_fetch_assoc($student_result)) {
                $student_id = $student['id'];
                $insert_query = "INSERT INTO student_notifications (notification_id, student_id) 
                                VALUES ($notification_id, $student_id)";
                mysqli_query($conn, $insert_query);
            }
            
            setAlert('success', 'Notification sent successfully.');
        } else {
            setAlert('error', 'Error sending notification.');
        }
    }
    
    // Edit notification
    if(isset($_POST['edit_notification'])) {
        $id = (int)$_POST['notification_id'];
        $title = sanitize($_POST['title']);
        $message = sanitize($_POST['message']);
        
        $query = "UPDATE notifications SET title = '$title', message = '$message' WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Notification updated successfully.');
        } else {
            setAlert('error', 'Error updating notification.');
        }
    }
    
    // Delete notification
    if(isset($_POST['delete_notification'])) {
        $id = (int)$_POST['notification_id'];
        
        $query = "DELETE FROM notifications WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Notification deleted successfully.');
        } else {
            setAlert('error', 'Error deleting notification.');
        }
    }
    
    // Add opportunity
    if(isset($_POST['add_opportunity'])) {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $type = sanitize($_POST['type']);
        $link = sanitize($_POST['link']);
        $target_dept = sanitize($_POST['department']);
        $expiry_date = sanitize($_POST['expiry_date']);
        
        $query = "INSERT INTO opportunities (title, description, type, link, department, created_by, created_role, expiry_date) 
                  VALUES ('$title', '$description', '$type', '$link', '$target_dept', $user_id, '$role', '$expiry_date')";
                  
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Opportunity added successfully.');
        } else {
            setAlert('error', 'Error adding opportunity.');
        }
    }
    
    // Edit opportunity
    if(isset($_POST['edit_opportunity'])) {
        $id = (int)$_POST['opportunity_id'];
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $type = sanitize($_POST['type']);
        $link = sanitize($_POST['link']);
        $target_dept = sanitize($_POST['department']);
        $expiry_date = sanitize($_POST['expiry_date']);
        
        $query = "UPDATE opportunities SET title = '$title', description = '$description', type = '$type', 
                  link = '$link', department = '$target_dept', expiry_date = '$expiry_date' WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Opportunity updated successfully.');
        } else {
            setAlert('error', 'Error updating opportunity.');
        }
    }
    
    // Delete opportunity
    if(isset($_POST['delete_opportunity'])) {
        $id = (int)$_POST['opportunity_id'];
        
        $query = "DELETE FROM opportunities WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Opportunity deleted successfully.');
        } else {
            setAlert('error', 'Error deleting opportunity.');
        }
    }
    
    // Add event
    if(isset($_POST['add_event']) && $role === 'admin') {
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $event_date = sanitize($_POST['event_date']);
        $event_time = sanitize($_POST['event_time']);
        $location = sanitize($_POST['location']);
        $speaker = sanitize($_POST['speaker']);
        
        $query = "INSERT INTO events (title, description, event_date, event_time, location, speaker) 
                  VALUES ('$title', '$description', '$event_date', '$event_time', '$location', '$speaker')";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Event added successfully.');
        } else {
            setAlert('error', 'Error adding event.');
        }
    }
    
    // Edit event
    if(isset($_POST['edit_event']) && $role === 'admin') {
        $id = (int)$_POST['event_id'];
        $title = sanitize($_POST['title']);
        $description = sanitize($_POST['description']);
        $event_date = sanitize($_POST['event_date']);
        $event_time = sanitize($_POST['event_time']);
        $location = sanitize($_POST['location']);
        $speaker = sanitize($_POST['speaker']);
        
        $query = "UPDATE events SET title = '$title', description = '$description', event_date = '$event_date', 
                  event_time = '$event_time', location = '$location', speaker = '$speaker' WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Event updated successfully.');
        } else {
            setAlert('error', 'Error updating event.');
        }
    }
    
    // Delete event
    if(isset($_POST['delete_event']) && $role === 'admin') {
        $id = (int)$_POST['event_id'];
        
        $query = "DELETE FROM events WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Event deleted successfully.');
        } else {
            setAlert('error', 'Error deleting event.');
        }
    }
    
    // Add team member
    if(isset($_POST['add_team_member']) && $role === 'admin') {
        $name = sanitize($_POST['name']);
        $position = sanitize($_POST['position']);
        $bio = sanitize($_POST['bio']);
        $team_role = sanitize($_POST['team_role']);
        $department = sanitize($_POST['department']);
        $skills = sanitize($_POST['skills']);
        $linkedin = sanitize($_POST['linkedin']);
        $github = sanitize($_POST['github']);
        $twitter = sanitize($_POST['twitter']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        
        $query = "INSERT INTO team_members (name, position, bio, role, department, skills, linkedin, github, twitter, email, phone) 
                  VALUES ('$name', '$position', '$bio', '$team_role', '$department', '$skills', '$linkedin', '$github', '$twitter', '$email', '$phone')";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Team member added successfully.');
        } else {
            setAlert('error', 'Error adding team member.');
        }
    }
    
    // Edit team member
    if(isset($_POST['edit_team_member']) && $role === 'admin') {
        $id = (int)$_POST['member_id'];
        $name = sanitize($_POST['name']);
        $position = sanitize($_POST['position']);
        $bio = sanitize($_POST['bio']);
        $team_role = sanitize($_POST['team_role']);
        $department = sanitize($_POST['department']);
        $skills = sanitize($_POST['skills']);
        $linkedin = sanitize($_POST['linkedin']);
        $github = sanitize($_POST['github']);
        $twitter = sanitize($_POST['twitter']);
        $email = sanitize($_POST['email']);
        $phone = sanitize($_POST['phone']);
        
        $query = "UPDATE team_members SET name = '$name', position = '$position', bio = '$bio', 
                  role = '$team_role', department = '$department', skills = '$skills', linkedin = '$linkedin', github = '$github', 
                  twitter = '$twitter', email = '$email', phone = '$phone' WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Team member updated successfully.');
        } else {
            setAlert('error', 'Error updating team member.');
        }
    }
    
    // Delete team member
    if(isset($_POST['delete_team_member']) && $role === 'admin') {
        $id = (int)$_POST['member_id'];
        
        $query = "DELETE FROM team_members WHERE id = $id";
        
        if(mysqli_query($conn, $query)) {
            setAlert('success', 'Team member deleted successfully.');
        } else {
            setAlert('error', 'Error deleting team member.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="dashboard">
        <div class="sidebar">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-details">
                    <h3><?php echo $_SESSION['admin_name']; ?></h3>
                    <p><?php echo ucfirst($role); ?></p>
                    <?php if($role === 'hod'): ?>
                    <p><?php echo $departments[$department]; ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="#overview" class="active"><i class="fas fa-tachometer-alt"></i> Overview</a>
                <a href="#students"><i class="fas fa-user-graduate"></i> Student Performance</a>
                <a href="#notifications"><i class="fas fa-bell"></i> Notifications</a>
                <a href="#opportunities"><i class="fas fa-briefcase"></i> Opportunities</a>
                <?php if($role === 'admin'): ?>
                <a href="#events"><i class="fas fa-calendar-alt"></i> Events</a>
                <a href="#team"><i class="fas fa-users"></i> Team Management</a>
                <a href="#contact_messages"><i class="fas fa-envelope-open-text"></i> Contact Messages</a>

                <?php endif; ?>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </nav>
        </div>
        
        <div class="main-content">
            <?php displayAlert(); ?>
            
            <section id="overview" class="dashboard-section">
                <h2>Dashboard Overview</h2>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['total_students']; ?></h3>
                            <p>Total Students</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['total_events']; ?></h3>
                            <p>Total Events</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['total_certifications']; ?></h3>
                            <p>Total Certifications</p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div class="stat-details">
                            <h3><?php echo $stats['total_opportunities']; ?></h3>
                            <p>Total Opportunities</p>
                        </div>
                    </div>
                </div>
                
                <div class="recent-activity">
                    <h3>Recent Activity</h3>
                    
                    <div class="activity-list">
                        <?php
                        // Get recent activities
                        $query = "SELECT 'event' as type, e.title, e.event_date as date FROM events e
                                  UNION ALL
                                  SELECT 'certification' as type, c.title, c.created_at as date FROM certifications c
                                  UNION ALL
                                  SELECT 'notification' as type, n.title, n.created_at as date FROM notifications n
                                  UNION ALL
                                  SELECT 'opportunity' as type, o.title, o.created_at as date FROM opportunities o
                                  ORDER BY date DESC LIMIT 5";
                        $result = mysqli_query($conn, $query);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while($row = mysqli_fetch_assoc($result)) {
                                echo '<div class="activity-item">';
                                
                                switch($row['type']) {
                                    case 'event':
                                        echo '<i class="fas fa-calendar-alt"></i>';
                                        echo '<div class="activity-details">';
                                        echo '<p>New event added: <strong>'.$row['title'].'</strong></p>';
                                        echo '<span>'.date('M d, Y', strtotime($row['date'])).'</span>';
                                        echo '</div>';
                                        break;
                                    case 'certification':
                                        echo '<i class="fas fa-certificate"></i>';
                                        echo '<div class="activity-details">';
                                        echo '<p>New certification: <strong>'.$row['title'].'</strong></p>';
                                        echo '<span>'.date('M d, Y', strtotime($row['date'])).'</span>';
                                        echo '</div>';
                                        break;
                                    case 'notification':
                                        echo '<i class="fas fa-bell"></i>';
                                        echo '<div class="activity-details">';
                                        echo '<p>New notification: <strong>'.$row['title'].'</strong></p>';
                                        echo '<span>'.date('M d, Y', strtotime($row['date'])).'</span>';
                                        echo '</div>';
                                        break;
                                    case 'opportunity':
                                        echo '<i class="fas fa-briefcase"></i>';
                                        echo '<div class="activity-details">';
                                        echo '<p>New opportunity: <strong>'.$row['title'].'</strong></p>';
                                        echo '<span>'.date('M d, Y', strtotime($row['date'])).'</span>';
                                        echo '</div>';
                                        break;
                                }
                                
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="no-data">No recent activity.</p>';
                        }
                        ?>
                    </div>
                </div>
            </section>
            
            <section id="students" class="dashboard-section">
                <h2>Student Performance</h2>
                
                <div class="filter-controls">
                    <div class="filter-group">
                        <label for="filter-department">Department:</label>
                        <select id="filter-department">
                            <?php if($role !== 'hod'): ?>
                            <option value="all">All Departments</option>
                            <?php endif; ?>
                            
                            <?php
                            foreach($departments as $key => $value) {
                                if($role === 'hod' && $key !== $department) {
                                    continue;
                                }
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label for="filter-year">Year:</label>
                        <select id="filter-year">
                            <option value="all">All Years</option>
                            <?php
                            foreach($years as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <button type="button" id="filter-button" class="btn btn-primary">Apply Filter</button>
                </div>
                
                <div class="tabs">
                    <button class="tab-button active" data-tab="leaderboard">Leaderboard</button>
                    <button class="tab-button" data-tab="student-list">Student List</button>
                    <button class="tab-button" data-tab="certifications">Certifications</button>
                </div>
                
                <div class="tab-content">
                    <div class="tab-pane active" id="leaderboard">
                        <div class="leaderboard-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>PRN</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Year</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody id="leaderboard-data">
                                    <?php
                                    // Get leaderboard data
                                    if($role === 'hod') {
                                        $query = "SELECT * FROM university_leaderboard WHERE department = '$department' ORDER BY university_rank LIMIT 20";
                                    } else {
                                        $query = "SELECT * FROM university_leaderboard ORDER BY university_rank LIMIT 20";
                                    }
                                    
                                    $result = mysqli_query($conn, $query);
                                    
                                    if(mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['university_rank'].'</td>';
                                            echo '<td>'.$row['prn'].'</td>';
                                            echo '<td>'.$row['name'].'</td>';
                                            echo '<td>'.$row['department'].'</td>';
                                            echo '<td>'.$row['year'].'</td>';
                                            echo '<td>'.$row['total_points'].'</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="no-data">No leaderboard data available.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="student-list">
                        <div class="search-box">
                            <input type="text" id="student-search" placeholder="Search by name or PRN...">
                            <i class="fas fa-search"></i>
                        </div>
                        
                        <div class="student-list-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>PRN</th>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Year</th>
                                        <th>Points</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="student-list-data">
                                    <?php
                                    // Get student list
                                    if($role === 'hod') {
                                        $query = "SELECT s.*, COALESCE(SUM(sp.points), 0) as total_points 
                                                  FROM students s 
                                                  LEFT JOIN student_points sp ON s.id = sp.student_id 
                                                  WHERE s.department = '$department' 
                                                  GROUP BY s.id 
                                                  ORDER BY s.last_name, s.first_name";
                                    } else {
                                        $query = "SELECT s.*, COALESCE(SUM(sp.points), 0) as total_points 
                                                  FROM students s 
                                                  LEFT JOIN student_points sp ON s.id = sp.student_id 
                                                  GROUP BY s.id 
                                                  ORDER BY s.department, s.year, s.last_name, s.first_name";
                                    }
                                    
                                    $result = mysqli_query($conn, $query);
                                    
                                    if(mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['prn'].'</td>';
                                            echo '<td>'.$row['first_name'].' '.$row['last_name'].'</td>';
                                            echo '<td>'.$row['department'].'</td>';
                                            echo '<td>'.$row['year'].'</td>';
                                            echo '<td>'.$row['total_points'].'</td>';
                                            echo '<td>'.($row['is_active'] ? '<span class="status-active">Active</span>' : '<span class="status-inactive">Inactive</span>').'</td>';
                                            echo '<td><a href="view_student.php?id='.$row['id'].'" class="btn btn-small">View</a></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="no-data">No students found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="tab-pane" id="certifications">
                        <div class="certifications-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Title</th>
                                        <th>Issuer</th>
                                        <th>Issue Date</th>
                                        <th>Points</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="certifications-data">
                                    <?php
                                    // Get certifications
                                    if($role === 'hod') {
                                        $query = "SELECT c.*, s.first_name, s.last_name, s.prn, s.department, s.year 
                                                  FROM certifications c 
                                                  JOIN students s ON c.student_id = s.id 
                                                  WHERE s.department = '$department' 
                                                  ORDER BY c.created_at DESC";
                                    } else {
                                        $query = "SELECT c.*, s.first_name, s.last_name, s.prn, s.department, s.year 
                                                  FROM certifications c 
                                                  JOIN students s ON c.student_id = s.id 
                                                  ORDER BY c.created_at DESC";
                                    }
                                    
                                    $result = mysqli_query($conn, $query);
                                    
                                    if(mysqli_num_rows($result) > 0) {
                                        while($row = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>'.$row['first_name'].' '.$row['last_name'].' ('.$row['prn'].')</td>';
                                            echo '<td>'.$row['title'].'</td>';
                                            echo '<td>'.$row['issuer'].'</td>';
                                            echo '<td>'.date('M d, Y', strtotime($row['issue_date'])).'</td>';
                                            echo '<td>'.$row['points'].'</td>';
                                            
                                            switch($row['status']) {
                                                case 'pending':
                                                    echo '<td><span class="status-pending">Pending</span></td>';
                                                    break;
                                                case 'approved':
                                                    echo '<td><span class="status-active">Approved</span></td>';
                                                    break;
                                                case 'rejected':
                                                    echo '<td><span class="status-inactive">Rejected</span></td>';
                                                    break;
                                            }
                                            
                                            echo '<td><a href="view_certification.php?id='.$row['id'].'" class="btn btn-small">View</a></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="no-data">No certifications found.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
            
            <section id="notifications" class="dashboard-section">
                <h2>Notifications</h2>
                
                <div class="card">
                    <h3>Send New Notification</h3>
                    
                    <form method="POST" action="#notifications">
                        <div class="form-group">
                            <label for="notification-title">Title</label>
                            <input type="text" id="notification-title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="notification-message">Message</label>
                            <textarea id="notification-message" name="message" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="notification-department">Department</label>
                                <select id="notification-department" name="department">
                                    <option value="all">All Departments</option>
                                    <?php
                                    foreach($departments as $key => $value) {
                                        if($role === 'hod' && $key !== $department) {
                                            continue;
                                        }
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="notification-year">Year</label>
                                <select id="notification-year" name="year">
                                    <option value="all">All Years</option>
                                    <?php
                                    foreach($years as $key => $value) {
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_notification" class="btn btn-primary">Send Notification</button>
                        </div>
                    </form>
                </div>
                
                <div class="card mt-4">
                    <h3>Recent Notifications</h3>
                    
                    <div class="notifications-list">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Target</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get recent notifications
                                if($role === 'hod') {
                                    $query = "SELECT n.*, 
                                              CASE 
                                                  WHEN n.created_role = 'hod' THEN (SELECT name FROM hods WHERE id = n.created_by)
                                                  WHEN n.created_role = 'dean' THEN (SELECT name FROM deans WHERE id = n.created_by)
                                                  WHEN n.created_role = 'admin' THEN (SELECT name FROM admins WHERE id = n.created_by)
                                              END as creator_name
                                              FROM notifications n 
                                              WHERE n.created_by = $user_id AND n.created_role = '$role'
                                              ORDER BY n.created_at DESC LIMIT 10";
                                } else {
                                    $query = "SELECT n.*, 
                                              CASE 
                                                  WHEN n.created_role = 'hod' THEN (SELECT name FROM hods WHERE id = n.created_by)
                                                  WHEN n.created_role = 'dean' THEN (SELECT name FROM deans WHERE id = n.created_by)
                                                  WHEN n.created_role = 'admin' THEN (SELECT name FROM admins WHERE id = n.created_by)
                                              END as creator_name
                                              FROM notifications n 
                                              ORDER BY n.created_at DESC LIMIT 10";
                                }
                                
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>';
                                        echo '<strong>'.$row['title'].'</strong><br>';
                                        echo '<small>'.substr($row['message'], 0, 100).'...</small>';
                                        echo '</td>';
                                        echo '<td>'.($row['department'] === 'all' ? 'All Departments' : $departments[$row['department']]).', '.($row['year'] === 'all' ? 'All Years' : $years[$row['year']]).'</td>';
                                        echo '<td>'.date('M d, Y', strtotime($row['created_at'])).'</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-small btn-secondary" onclick="editNotification('.$row['id'].', \''.$row['title'].'\', \''.$row['message'].'\')">Edit</button> ';
                                        echo '<button class="btn btn-small btn-danger" onclick="deleteNotification('.$row['id'].')">Delete</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="4" class="no-data">No notifications found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            
            <section id="opportunities" class="dashboard-section">
                <h2>Opportunities</h2>
                
                <div class="card">
                    <h3>Add New Opportunity</h3>
                    
                    <form method="POST" action="#opportunities">
                        <div class="form-group">
                            <label for="opportunity-title">Title</label>
                            <input type="text" id="opportunity-title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="opportunity-description">Description</label>
                            <textarea id="opportunity-description" name="description" rows="5" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="opportunity-type">Type</label>
                                <select id="opportunity-type" name="type" required>
                                    <option value="internship">Internship</option>
                                    <option value="certification">Certification</option>
                                    <option value="project">Project</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="opportunity-department">Department</label>
                                <select id="opportunity-department" name="department">
                                    <option value="all">All Departments</option>
                                    <?php
                                    foreach($departments as $key => $value) {
                                        if($role === 'hod' && $key !== $department) {
                                            continue;
                                        }
                                        echo '<option value="'.$key.'">'.$value.'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="opportunity-link">Link</label>
                                <input type="url" id="opportunity-link" name="link" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="opportunity-expiry">Expiry Date</label>
                                <input type="date" id="opportunity-expiry" name="expiry_date" required>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_opportunity" class="btn btn-primary">Add Opportunity</button>
                        </div>
                    </form>
                </div>
                
                <div class="card mt-4">
                    <h3>Recent Opportunities</h3>
                    
                    <div class="opportunities-list">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Department</th>
                                    <th>Expiry Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get recent opportunities
                                if($role === 'hod') {
                                    $query = "SELECT * FROM opportunities 
                                              WHERE created_by = $user_id AND created_role = '$role'
                                              ORDER BY created_at DESC LIMIT 10";
                                } else {
                                    $query = "SELECT * FROM opportunities 
                                              ORDER BY created_at DESC LIMIT 10";
                                }
                                
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>'.$row['title'].'</td>';
                                        echo '<td>'.ucfirst($row['type']).'</td>';
                                        echo '<td>'.($row['department'] === 'all' ? 'All Departments' : $departments[$row['department']]).'</td>';
                                        echo '<td>'.date('M d, Y', strtotime($row['expiry_date'])).'</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-small" onclick="viewOpportunity('.$row['id'].')">View</button> ';
                                        
                                        if($role === 'admin' || ($role === $row['created_role'] && $user_id === $row['created_by'])) {
                                            echo '<button class="btn btn-small btn-secondary" onclick="editOpportunity('.$row['id'].')">Edit</button> ';
                                            echo '<button class="btn btn-small btn-danger" onclick="deleteOpportunity('.$row['id'].')">Delete</button>';
                                        }
                                        
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="no-data">No opportunities found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            
            <?php if($role === 'admin'): ?>
            <section id="events" class="dashboard-section">
                <h2>Events Management</h2>
                
                <div class="card">
                    <h3>Add New Event</h3>
                    
                    <form method="POST" action="#events">
                        <div class="form-group">
                            <label for="event-title">Title</label>
                            <input type="text" id="event-title" name="title" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="event-description">Description</label>
                            <textarea id="event-description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="event-date">Event Date</label>
                                <input type="date" id="event-date" name="event_date" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="event-time">Event Time</label>
                                <input type="time" id="event-time" name="event_time" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="event-location">Location</label>
                                <input type="text" id="event-location" name="location" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="event-speaker">Speaker (Optional)</label>
                                <input type="text" id="event-speaker" name="speaker">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_event" class="btn btn-primary">Add Event</button>
                        </div>
                    </form>
                </div>
                
                <div class="card mt-4">
                    <h3>Events List</h3>
                    
                    <div class="events-list">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Participants</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get events
                                $query = "SELECT e.*, COUNT(ep.id) as participant_count 
                                          FROM events e 
                                          LEFT JOIN event_participants ep ON e.id = ep.event_id 
                                          GROUP BY e.id 
                                          ORDER BY e.event_date DESC";
                                
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>'.$row['title'].'</td>';
                                        echo '<td>'.date('M d, Y', strtotime($row['event_date'])).'</td>';
                                        echo '<td>'.$row['location'].'</td>';
                                        echo '<td>'.$row['participant_count'].'</td>';
                                        echo '<td>';
                                        echo '<button class="btn btn-small" onclick="viewEvent('.$row['id'].')">View</button> ';
                                        echo '<button class="btn btn-small btn-secondary" onclick="editEvent('.$row['id'].')">Edit</button> ';
                                        echo '<button class="btn btn-small btn-danger" onclick="deleteEvent('.$row['id'].')">Delete</button>';
                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="5" class="no-data">No events found.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            
            <section id="team" class="dashboard-section">
                <h2>Team Management</h2>
                
                <div class="card">
                    <h3>Add Team Member</h3>
                    
                    <form method="POST" action="#team">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="member-name">Name</label>
                                <input type="text" id="member-name" name="name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="member-position">Position</label>
                                <input type="text" id="member-position" name="position" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="member-bio">Bio</label>
                            <textarea id="member-bio" name="bio" rows="3"></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="member-role">Role</label>
                                <select id="member-role" name="team_role" required>
                                    <option value="dean">Dean</option>
                                    <option value="hod">HOD</option>
                                    <option value="president">President</option>
                                    <option value="vice_president">Vice President</option>
                                    <option value="technical_head">Technical Head</option>
                                    <option value="non_technical_head">Non-Technical Head</option>
                                    <option value="management_head">Management Head</option>
                                    <option value="photography_head">Photography Head</option>
                                    <option value="social_media_manager">Social Media Manager</option>
                                    <option value="domain_lead">Domain Lead</option>
                                    <option value="accountant">Accountant</option>
                                    <option value="student">Student</option>
                                    <option value="member">Member</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="member-department">Department</label>
                                <select id="member-department" name="department">
                                    <option value="">Select Department</option>
                                    <option value="CSE">Computer Science Engineering</option>
                                    <option value="CY">Cyber Security</option>
                                    <option value="AIML">AI & Machine Learning</option>
                                    <option value="ALDS">Applied Data Science</option>
                                    <option value="management">Management</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="member-skills">Skills (comma separated)</label>
                            <input type="text" id="member-skills" name="skills">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="member-email">Email</label>
                                <input type="email" id="member-email" name="email">
                            </div>
                            
                            <div class="form-group">
                                <label for="member-phone">Phone</label>
                                <input type="tel" id="member-phone" name="phone">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="member-linkedin">LinkedIn URL</label>
                                <input type="url" id="member-linkedin" name="linkedin">
                            </div>
                            
                            <div class="form-group">
                                <label for="member-github">GitHub URL</label>
                                <input type="url" id="member-github" name="github">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="member-twitter">Twitter URL</label>
                                <input type="url" id="member-twitter" name="twitter">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" name="add_team_member" class="btn btn-primary">Add Team Member</button>
                        </div>
                    </form>
                </div>
                
                <div class="card mt-4">
                    <h3>Team Structure</h3>
                    
                    <div class="team-hierarchy">
                        <!-- Leadership -->
                        <div class="team-category">
                            <h4>Leadership</h4>
                            <div class="team-members-grid">
                                <?php
                                $leadership_roles = ['dean', 'hod', 'president', 'vice_president'];
                                $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $leadership_roles)."') ORDER BY FIELD(role, 'dean', 'hod', 'president', 'vice_president')";
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<div class="team-member-card leadership">';
                                        echo '<div class="member-info">';
                                        echo '<h5>'.$row['name'].'</h5>';
                                        echo '<p class="role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                                        echo '<p class="position">'.$row['position'].'</p>';
                                        if($row['department']) echo '<p class="department">'.$row['department'].'</p>';
                                        echo '<div class="member-actions">';
                                        echo '<button class="btn btn-small btn-secondary" onclick="editTeamMember('.$row['id'].')">Edit</button> ';
                                        echo '<button class="btn btn-small btn-danger" onclick="deleteTeamMember('.$row['id'].')">Delete</button>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="no-data">No leadership members found.</p>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Department Heads -->
                        <div class="team-category">
                            <h4>Department Heads</h4>
                            <div class="team-members-grid">
                                <?php
                                $head_roles = ['technical_head', 'non_technical_head', 'management_head', 'photography_head', 'social_media_manager'];
                                $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $head_roles)."') ORDER BY role";
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<div class="team-member-card heads">';
                                        echo '<div class="member-info">';
                                        echo '<h5>'.$row['name'].'</h5>';
                                        echo '<p class="role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                                        echo '<p class="position">'.$row['position'].'</p>';
                                        if($row['department']) echo '<p class="department">'.$row['department'].'</p>';
                                        echo '<div class="member-actions">';
                                        echo '<button class="btn btn-small btn-secondary" onclick="editTeamMember('.$row['id'].')">Edit</button> ';
                                        echo '<button class="btn btn-small btn-danger" onclick="deleteTeamMember('.$row['id'].')">Delete</button>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="no-data">No department heads found.</p>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Domain Leads & Others -->
                        <div class="team-category">
                            <h4>Domain Leads & Support</h4>
                            <div class="team-members-grid">
                                <?php
                                $other_roles = ['domain_lead', 'accountant'];
                                $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $other_roles)."') ORDER BY role";
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<div class="team-member-card others">';
                                        echo '<div class="member-info">';
                                        echo '<h5>'.$row['name'].'</h5>';
                                        echo '<p class="role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                                        echo '<p class="position">'.$row['position'].'</p>';
                                        if($row['department']) echo '<p class="department">'.$row['department'].'</p>';
                                        echo '<div class="member-actions">';
                                        echo '<button class="btn btn-small btn-secondary" onclick="editTeamMember('.$row['id'].')">Edit</button> ';
                                        echo '<button class="btn btn-small btn-danger" onclick="deleteTeamMember('.$row['id'].')">Delete</button>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="no-data">No domain leads or support staff found.</p>';
                                }
                                ?>
                            </div>
                        </div>
                        
                        <!-- Students & Members -->
                        <div class="team-category">
                            <h4>Students & Members</h4>
                            <div class="team-members-grid">
                                <?php
                                $member_roles = ['student', 'member'];
                                $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $member_roles)."') ORDER BY role, name";
                                $result = mysqli_query($conn, $query);
                                
                                if(mysqli_num_rows($result) > 0) {
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<div class="team-member-card members">';
                                        echo '<div class="member-info">';
                                        echo '<h5>'.$row['name'].'</h5>';
                                        echo '<p class="role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                                        echo '<p class="position">'.$row['position'].'</p>';
                                        if($row['department']) echo '<p class="department">'.$row['department'].'</p>';
                                        echo '<div class="member-actions">';
                                        echo '<button class="btn btn-small btn-secondary" onclick="editTeamMember('.$row['id'].')">Edit</button> ';
                                        echo '<button class="btn btn-small btn-danger" onclick="deleteTeamMember('.$row['id'].')">Delete</button>';
                                        echo '</div>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="no-data">No students or members found.</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section id="contact_messages" class="dashboard-section">
                <h2>Contact Messages</h2>
                <?php $query = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

                                 <div class="recent-activity">
<table border="1" cellpadding="10" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Subject</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if(mysqli_num_rows($result) > 0):
                    $count = 1;
                    while($row = mysqli_fetch_assoc($result)):
                ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['message'])) ?></td>
                    <td><?= $row['is_read'] ? 'Read' : 'Unread' ?></td>
                    <td><?= $row['created_at'] ?></td>
                    <td>
                        <a href="contact_reply.php?id=<?= $row['id'] ?>">Reply</a>
                    </td>
                </tr>
                <?php
                    endwhile;
                else:
                ?>
                <tr>
                    <td colspan="9" align="center">No contact messages found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

                            </div>

            </section>
                

            <?php endif; ?>
        </div>
    </div>

    
    <!-- Modal for Edit/View Operations -->
    <div id="crudModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-body">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Hidden forms for delete operations -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" id="deleteId" name="">
        <input type="hidden" id="deleteAction" name="">
    </form>
    
    <script>
        // Tab switching
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and panes
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-pane').forEach(pane => pane.classList.remove('active'));
                
                // Add active class to clicked button and corresponding pane
                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
            });
        });
        
        // Sidebar navigation
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                if(this.getAttribute('href').startsWith('#')) {
                    e.preventDefault();
                    
                    // Remove active class from all links
                    document.querySelectorAll('.sidebar-nav a').forEach(navLink => navLink.classList.remove('active'));
                    
                    // Add active class to clicked link
                    this.classList.add('active');
                    
                    // Show corresponding section
                    const sectionId = this.getAttribute('href').substring(1);
                    document.querySelectorAll('.dashboard-section').forEach(section => {
                        section.style.display = section.id === sectionId ? 'block' : 'none';
                    });
                    
                    // Scroll to top
                    window.scrollTo(0, 0);
                }
            });
        });
        
        // Student search
        if(document.getElementById('student-search')) {
            document.getElementById('student-search').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('#student-list-data tr');
                
                rows.forEach(row => {
                    const prn = row.cells[0]?.textContent.toLowerCase() || '';
                    const name = row.cells[1]?.textContent.toLowerCase() || '';
                    
                    if(prn.includes(searchTerm) || name.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
        
        // Set minimum date for opportunity expiry
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const todayStr = `${yyyy}-${mm}-${dd}`;
        
        if(document.getElementById('opportunity-expiry')) {
            document.getElementById('opportunity-expiry').min = todayStr;
        }
        if(document.getElementById('event-date')) {
            document.getElementById('event-date').min = todayStr;
        }

        // Modal functionality
        const modal = document.getElementById('crudModal');
        const closeBtn = document.querySelector('.close');
        const modalBody = document.getElementById('modal-body');

        // Close modal
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }

        // CRUD Functions for Notifications
        function editNotification(id, title, message) {
            modalBody.innerHTML = `
                <h3>Edit Notification</h3>
                <form method="POST" action="#notifications">
                    <input type="hidden" name="notification_id" value="${id}">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="${title}" required>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="message" rows="5" required>${message}</textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="edit_notification" class="btn btn-primary">Update</button>
                        <button type="button" onclick="modal.style.display='none'" class="btn btn-secondary">Cancel</button>
                    </div>
                </form>
            `;
            modal.style.display = 'block';
        }

        function deleteNotification(id) {
            if(confirm('Are you sure you want to delete this notification?')) {
                const form = document.getElementById('deleteForm');
                form.innerHTML = `
                    <input type="hidden" name="notification_id" value="${id}">
                    <input type="hidden" name="delete_notification" value="1">
                `;
                form.submit();
            }
        }

        // CRUD Functions for Opportunities
        function viewOpportunity(id) {
            fetch('get_opportunity.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        modalBody.innerHTML = `
                            <h3>${data.opportunity.title}</h3>
                            <p><strong>Type:</strong> ${data.opportunity.type}</p>
                            <p><strong>Department:</strong> ${data.opportunity.department}</p>
                            <p><strong>Description:</strong></p>
                            <p>${data.opportunity.description}</p>
                            <p><strong>Link:</strong> <a href="${data.opportunity.link}" target="_blank">${data.opportunity.link}</a></p>
                            <p><strong>Expiry Date:</strong> ${data.opportunity.expiry_date}</p>
                        `;
                        modal.style.display = 'block';
                    }
                });
        }

        function editOpportunity(id) {
            fetch('get_opportunity.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const opp = data.opportunity;
                        modalBody.innerHTML = `
                            <h3>Edit Opportunity</h3>
                            <form method="POST" action="#opportunities">
                                <input type="hidden" name="opportunity_id" value="${id}">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" value="${opp.title}" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" rows="5" required>${opp.description}</textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select name="type" required>
                                            <option value="internship" ${opp.type === 'internship' ? 'selected' : ''}>Internship</option>
                                            <option value="certification" ${opp.type === 'certification' ? 'selected' : ''}>Certification</option>
                                            <option value="project" ${opp.type === 'project' ? 'selected' : ''}>Project</option>
                                            <option value="other" ${opp.type === 'other' ? 'selected' : ''}>Other</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department">
                                            <option value="all" ${opp.department === 'all' ? 'selected' : ''}>All Departments</option>
                                            <option value="CSE" ${opp.department === 'CSE' ? 'selected' : ''}>CSE</option>
                                            <option value="CY" ${opp.department === 'CY' ? 'selected' : ''}>CY</option>
                                            <option value="AIML" ${opp.department === 'AIML' ? 'selected' : ''}>AIML</option>
                                            <option value="ALDS" ${opp.department === 'ALDS' ? 'selected' : ''}>ALDS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Link</label>
                                        <input type="url" name="link" value="${opp.link}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Expiry Date</label>
                                        <input type="date" name="expiry_date" value="${opp.expiry_date}" required>
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" name="edit_opportunity" class="btn btn-primary">Update</button>
                                    <button type="button" onclick="modal.style.display='none'" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                        `;
                        modal.style.display = 'block';
                    }
                });
        }

        function deleteOpportunity(id) {
            if(confirm('Are you sure you want to delete this opportunity?')) {
                const form = document.getElementById('deleteForm');
                form.innerHTML = `
                    <input type="hidden" name="opportunity_id" value="${id}">
                    <input type="hidden" name="delete_opportunity" value="1">
                `;
                form.submit();
            }
        }

        // CRUD Functions for Events
        function viewEvent(id) {
            fetch('get_event.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const event = data.event;
                        modalBody.innerHTML = `
                            <h3>${event.title}</h3>
                            <p><strong>Date:</strong> ${event.event_date}</p>
                            <p><strong>Time:</strong> ${event.event_time}</p>
                            <p><strong>Location:</strong> ${event.location}</p>
                            <p><strong>Speaker:</strong> ${event.speaker || 'N/A'}</p>
                            <p><strong>Description:</strong></p>
                            <p>${event.description}</p>
                        `;
                        modal.style.display = 'block';
                    }
                });
        }

        function editEvent(id) {
            fetch('get_event.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const event = data.event;
                        modalBody.innerHTML = `
                            <h3>Edit Event</h3>
                            <form method="POST" action="#events">
                                <input type="hidden" name="event_id" value="${id}">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="title" value="${event.title}" required>
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea name="description" rows="4" required>${event.description}</textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Event Date</label>
                                        <input type="date" name="event_date" value="${event.event_date}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Event Time</label>
                                        <input type="time" name="event_time" value="${event.event_time}" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Location</label>
                                        <input type="text" name="location" value="${event.location}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Speaker</label>
                                        <input type="text" name="speaker" value="${event.speaker || ''}">
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" name="edit_event" class="btn btn-primary">Update</button>
                                    <button type="button" onclick="modal.style.display='none'" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                        `;
                        modal.style.display = 'block';
                    }
                });
        }

        function deleteEvent(id) {
            if(confirm('Are you sure you want to delete this event?')) {
                const form = document.getElementById('deleteForm');
                form.innerHTML = `
                    <input type="hidden" name="event_id" value="${id}">
                    <input type="hidden" name="delete_event" value="1">
                `;
                form.submit();
            }
        }

        // CRUD Functions for Team Members
        function editTeamMember(id) {
            fetch('get_team_member.php?id=' + id)
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const member = data.member;
                        modalBody.innerHTML = `
                            <h3>Edit Team Member</h3>
                            <form method="POST" action="#team">
                                <input type="hidden" name="member_id" value="${id}">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <input type="text" name="name" value="${member.name}" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Position</label>
                                        <input type="text" name="position" value="${member.position}" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Bio</label>
                                    <textarea name="bio" rows="3">${member.bio || ''}</textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Role</label>
                                        <select name="team_role" required>
                                            <option value="dean" ${member.role === 'dean' ? 'selected' : ''}>Dean</option>
                                            <option value="hod" ${member.role === 'hod' ? 'selected' : ''}>HOD</option>
                                            <option value="president" ${member.role === 'president' ? 'selected' : ''}>President</option>
                                            <option value="vice_president" ${member.role === 'vice_president' ? 'selected' : ''}>Vice President</option>
                                            <option value="technical_head" ${member.role === 'technical_head' ? 'selected' : ''}>Technical Head</option>
                                            <option value="non_technical_head" ${member.role === 'non_technical_head' ? 'selected' : ''}>Non-Technical Head</option>
                                            <option value="management_head" ${member.role === 'management_head' ? 'selected' : ''}>Management Head</option>
                                            <option value="photography_head" ${member.role === 'photography_head' ? 'selected' : ''}>Photography Head</option>
                                            <option value="social_media_manager" ${member.role === 'social_media_manager' ? 'selected' : ''}>Social Media Manager</option>
                                            <option value="domain_lead" ${member.role === 'domain_lead' ? 'selected' : ''}>Domain Lead</option>
                                            <option value="accountant" ${member.role === 'accountant' ? 'selected' : ''}>Accountant</option>
                                            <option value="student" ${member.role === 'student' ? 'selected' : ''}>Student</option>
                                            <option value="member" ${member.role === 'member' ? 'selected' : ''}>Member</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select name="department">
                                            <option value="">Select Department</option>
                                            <option value="CSE" ${member.department === 'CSE' ? 'selected' : ''}>Computer Science Engineering</option>
                                            <option value="CY" ${member.department === 'CY' ? 'selected' : ''}>Cyber Security</option>
                                            <option value="AIML" ${member.department === 'AIML' ? 'selected' : ''}>AI & Machine Learning</option>
                                            <option value="ALDS" ${member.department === 'ALDS' ? 'selected' : ''}>Applied Data Science</option>
                                            <option value="management" ${member.department === 'management' ? 'selected' : ''}>Management</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Skills</label>
                                    <input type="text" name="skills" value="${member.skills || ''}">
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" name="email" value="${member.email || ''}">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone</label>
                                        <input type="tel" name="phone" value="${member.phone || ''}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>LinkedIn</label>
                                        <input type="url" name="linkedin" value="${member.linkedin || ''}">
                                    </div>
                                    <div class="form-group">
                                        <label>GitHub</label>
                                        <input type="url" name="github" value="${member.github || ''}">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Twitter</label>
                                        <input type="url" name="twitter" value="${member.twitter || ''}">
                                    </div>
                                </div>
                                <div class="form-actions">
                                    <button type="submit" name="edit_team_member" class="btn btn-primary">Update</button>
                                    <button type="button" onclick="modal.style.display='none'" class="btn btn-secondary">Cancel</button>
                                </div>
                            </form>
                        `;
                        modal.style.display = 'block';
                    }
                });
        }

        function deleteTeamMember(id) {
            if(confirm('Are you sure you want to delete this team member?')) {
                const form = document.getElementById('deleteForm');
                form.innerHTML = `
                    <input type="hidden" name="member_id" value="${id}">
                    <input type="hidden" name="delete_team_member" value="1">
                `;
                form.submit();
            }
        }
    </script>
</body>
</html>