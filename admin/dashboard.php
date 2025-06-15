<?php
session_start();
include '../includes/config.php';

// Check if user is logged in and is admin
if(!isLoggedIn() || $_SESSION['user_role'] !== 'admin') {
    redirect('../login.php');
}

// Get statistics
$stats = [];

// Total students
$query = "SELECT COUNT(*) as count FROM students";
$result = mysqli_query($conn, $query);
$stats['total_students'] = mysqli_fetch_assoc($result)['count'];

// Total events
$query = "SELECT COUNT(*) as count FROM events";
$result = mysqli_query($conn, $query);
$stats['total_events'] = mysqli_fetch_assoc($result)['count'];

// Total certifications
$query = "SELECT COUNT(*) as count FROM certifications";
$result = mysqli_query($conn, $query);
$stats['total_certifications'] = mysqli_fetch_assoc($result)['count'];

// Total opportunities
$query = "SELECT COUNT(*) as count FROM opportunities";
$result = mysqli_query($conn, $query);
$stats['total_opportunities'] = mysqli_fetch_assoc($result)['count'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <img src="../images/dsc-logo.svg" alt="DSC Logo">
                <h2>Admin Panel</h2>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="students.php">
                    <i class="fas fa-user-graduate"></i>
                    <span>Students</span>
                </a>
                <a href="events.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Events</span>
                </a>
                <a href="opportunities.php">
                    <i class="fas fa-briefcase"></i>
                    <span>Opportunities</span>
                </a>
                <a href="certifications.php">
                    <i class="fas fa-certificate"></i>
                    <span>Certifications</span>
                </a>
                <a href="notifications.php">
                    <i class="fas fa-bell"></i>
                    <span>Notifications</span>
                </a>
                <a href="team.php">
                    <i class="fas fa-users"></i>
                    <span>Team</span>
                </a>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="admin-main">
            <header class="admin-header">
                <div class="header-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
                
                <div class="header-profile">
                    <span>Welcome, <?php echo $_SESSION['admin_name']; ?></span>
                    <img src="../images/default-avatar.png" alt="Admin">
                </div>
            </header>

            <div class="admin-content">
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--primary-light);">
                            <i class="fas fa-user-graduate" style="color: var(--primary);"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_students']; ?></h3>
                            <p>Total Students</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--secondary-light);">
                            <i class="fas fa-calendar-alt" style="color: var(--secondary);"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_events']; ?></h3>
                            <p>Total Events</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--accent-light);">
                            <i class="fas fa-certificate" style="color: var(--accent-dark);"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_certifications']; ?></h3>
                            <p>Total Certifications</p>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon" style="background-color: var(--danger-light);">
                            <i class="fas fa-briefcase" style="color: var(--danger);"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $stats['total_opportunities']; ?></h3>
                            <p>Total Opportunities</p>
                        </div>
                    </div>
                </div>

                <div class="dashboard-sections">
                    <!-- Recent Events -->
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h2>Recent Events</h2>
                            <a href="events.php" class="btn btn-small">View All</a>
                        </div>
                        <div class="section-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Participants</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT e.*, COUNT(ep.id) as participant_count 
                                              FROM events e 
                                              LEFT JOIN event_participants ep ON e.id = ep.event_id 
                                              GROUP BY e.id 
                                              ORDER BY e.event_date DESC 
                                              LIMIT 5";
                                    $result = mysqli_query($conn, $query);
                                    
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>'.$row['title'].'</td>';
                                        echo '<td>'.date('M d, Y', strtotime($row['event_date'])).'</td>';
                                        echo '<td>'.$row['location'].'</td>';
                                        echo '<td>'.$row['participant_count'].'</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Recent Certifications -->
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h2>Recent Certifications</h2>
                            <a href="certifications.php" class="btn btn-small">View All</a>
                        </div>
                        <div class="section-content">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Certificate</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT c.*, s.first_name, s.last_name 
                                              FROM certifications c 
                                              JOIN students s ON c.student_id = s.id 
                                              ORDER BY c.created_at DESC 
                                              LIMIT 5";
                                    $result = mysqli_query($conn, $query);
                                    
                                    while($row = mysqli_fetch_assoc($result)) {
                                        echo '<tr>';
                                        echo '<td>'.$row['first_name'].' '.$row['last_name'].'</td>';
                                        echo '<td>'.$row['title'].'</td>';
                                        echo '<td><span class="status-'.$row['status'].'">'.$row['status'].'</span></td>';
                                        echo '<td>'.date('M d, Y', strtotime($row['created_at'])).'</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../js/admin.js"></script>
</body>
</html>