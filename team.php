<?php
session_start();
include 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/team.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="team-hero">
            <div class="container">
                <div class="section-header">
                    <h1>Our Team</h1>
                    <p>By Students, for Students</p>
                    <div class="underline"></div>
                </div>
            </div>
        </section>
        
        <!-- Leadership -->
        <section class="team-section leadership">
            <div class="container">
                <h2>Leadership</h2>
                
                <div class="team-grid">
                    <?php
                    // Get leadership members
                    $leadership_roles = ['dean', 'hod', 'president', 'vice_president'];
                    $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $leadership_roles)."') ORDER BY FIELD(role, 'dean', 'hod', 'president', 'vice_president')";
                    $result = mysqli_query($conn, $query);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="team-member leadership" data-id="'.$row['id'].'">';
                            echo '<div class="member-image">';
                            echo '<img src="'.(!empty($row['image']) ? 'uploads/team/'.$row['image'] : 'images/default-avatar.png').'" alt="'.$row['name'].'">';
                            echo '</div>';
                            echo '<div class="member-info">';
                            echo '<h3>'.$row['name'].'</h3>';
                            echo '<p class="member-role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                            echo '<p class="member-position">'.$row['position'].'</p>';
                            if($row['department']) echo '<p class="member-department">'.$row['department'].'</p>';
                            echo '<div class="member-social">';
                            
                            if(!empty($row['linkedin'])) {
                                echo '<a href="'.$row['linkedin'].'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                            }
                            
                            if(!empty($row['github'])) {
                                echo '<a href="'.$row['github'].'" target="_blank"><i class="fab fa-github"></i></a>';
                            }
                            
                            if(!empty($row['twitter'])) {
                                echo '<a href="'.$row['twitter'].'" target="_blank"><i class="fab fa-twitter"></i></a>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-members">No leadership members available.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
        
        <!-- Department Heads -->
        <section class="team-section heads">
            <div class="container">
                <h2>Department Heads</h2>
                
                <div class="team-grid">
                    <?php
                    // Get department heads
                    $head_roles = ['technical_head', 'non_technical_head', 'management_head', 'photography_head', 'social_media_manager'];
                    $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $head_roles)."') ORDER BY role";
                    $result = mysqli_query($conn, $query);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="team-member heads" data-id="'.$row['id'].'">';
                            echo '<div class="member-image">';
                            echo '<img src="'.(!empty($row['image']) ? 'uploads/team/'.$row['image'] : 'images/default-avatar.png').'" alt="'.$row['name'].'">';
                            echo '</div>';
                            echo '<div class="member-info">';
                            echo '<h3>'.$row['name'].'</h3>';
                            echo '<p class="member-role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                            echo '<p class="member-position">'.$row['position'].'</p>';
                            if($row['department']) echo '<p class="member-department">'.$row['department'].'</p>';
                            echo '<div class="member-social">';
                            
                            if(!empty($row['linkedin'])) {
                                echo '<a href="'.$row['linkedin'].'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                            }
                            
                            if(!empty($row['github'])) {
                                echo '<a href="'.$row['github'].'" target="_blank"><i class="fab fa-github"></i></a>';
                            }
                            
                            if(!empty($row['twitter'])) {
                                echo '<a href="'.$row['twitter'].'" target="_blank"><i class="fab fa-twitter"></i></a>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-members">No department heads available.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
        
        <!-- Domain Leads & Support -->
        <section class="team-section others">
            <div class="container">
                <h2>Domain Leads & Support</h2>
                
                <div class="team-grid">
                    <?php
                    // Get domain leads and support staff
                    $other_roles = ['domain_lead', 'accountant'];
                    $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $other_roles)."') ORDER BY role";
                    $result = mysqli_query($conn, $query);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="team-member others" data-id="'.$row['id'].'">';
                            echo '<div class="member-image">';
                            echo '<img src="'.(!empty($row['image']) ? 'uploads/team/'.$row['image'] : 'images/default-avatar.png').'" alt="'.$row['name'].'">';
                            echo '</div>';
                            echo '<div class="member-info">';
                            echo '<h3>'.$row['name'].'</h3>';
                            echo '<p class="member-role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                            echo '<p class="member-position">'.$row['position'].'</p>';
                            if($row['department']) echo '<p class="member-department">'.$row['department'].'</p>';
                            echo '<div class="member-social">';
                            
                            if(!empty($row['linkedin'])) {
                                echo '<a href="'.$row['linkedin'].'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                            }
                            
                            if(!empty($row['github'])) {
                                echo '<a href="'.$row['github'].'" target="_blank"><i class="fab fa-github"></i></a>';
                            }
                            
                            if(!empty($row['twitter'])) {
                                echo '<a href="'.$row['twitter'].'" target="_blank"><i class="fab fa-twitter"></i></a>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-members">No domain leads or support staff available.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
        
        <!-- Students & Members -->
        <section class="team-section members">
            <div class="container">
                <h2>Students & Members</h2>
                
                <div class="team-grid">
                    <?php
                    // Get students and members
                    $member_roles = ['student', 'member'];
                    $query = "SELECT * FROM team_members WHERE role IN ('".implode("','", $member_roles)."') ORDER BY role, name";
                    $result = mysqli_query($conn, $query);
                    
                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)) {
                            echo '<div class="team-member members" data-id="'.$row['id'].'">';
                            echo '<div class="member-image">';
                            echo '<img src="'.(!empty($row['image']) ? 'uploads/team/'.$row['image'] : 'images/default-avatar.png').'" alt="'.$row['name'].'">';
                            echo '</div>';
                            echo '<div class="member-info">';
                            echo '<h3>'.$row['name'].'</h3>';
                            echo '<p class="member-role">'.ucwords(str_replace('_', ' ', $row['role'])).'</p>';
                            echo '<p class="member-position">'.$row['position'].'</p>';
                            if($row['department']) echo '<p class="member-department">'.$row['department'].'</p>';
                            echo '<div class="member-social">';
                            
                            if(!empty($row['linkedin'])) {
                                echo '<a href="'.$row['linkedin'].'" target="_blank"><i class="fab fa-linkedin-in"></i></a>';
                            }
                            
                            if(!empty($row['github'])) {
                                echo '<a href="'.$row['github'].'" target="_blank"><i class="fab fa-github"></i></a>';
                            }
                            
                            if(!empty($row['twitter'])) {
                                echo '<a href="'.$row['twitter'].'" target="_blank"><i class="fab fa-twitter"></i></a>';
                            }
                            
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p class="no-members">No students or members available.</p>';
                    }
                    ?>
                </div>
            </div>
        </section>
    </main>

    <!-- Member Profile Modal -->
    <div id="member-modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="member-profile">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Member profile modal
        const modal = document.getElementById('member-modal');
        const closeBtn = document.querySelector('.close');
        const memberProfile = document.getElementById('member-profile');
        const teamMembers = document.querySelectorAll('.team-member');
        
        // Open modal when clicking on a team member
        teamMembers.forEach(member => {
            member.addEventListener('click', function() {
                const memberId = this.dataset.id;
                
                // Fetch member details via AJAX
                fetch('get_member.php?id=' + memberId)
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            memberProfile.innerHTML = `
                                <div class="profile-header">
                                    <div class="profile-image">
                                        <img src="${data.member.image ? 'uploads/team/' + data.member.image : 'images/default-avatar.png'}" alt="${data.member.name}">
                                    </div>
                                    <div class="profile-info">
                                        <h2>${data.member.name}</h2>
                                        <p class="profile-position">${data.member.position}</p>
                                        <div class="profile-social">
                                            ${data.member.linkedin ? `<a href="${data.member.linkedin}" target="_blank"><i class="fab fa-linkedin-in"></i></a>` : ''}
                                            ${data.member.github ? `<a href="${data.member.github}" target="_blank"><i class="fab fa-github"></i></a>` : ''}
                                            ${data.member.twitter ? `<a href="${data.member.twitter}" target="_blank"><i class="fab fa-twitter"></i></a>` : ''}
                                        </div>
                                    </div>
                                </div>
                                <div class="profile-content">
                                    <div class="profile-bio">
                                        <h3>About</h3>
                                        <p>${data.member.bio || 'No bio available.'}</p>
                                    </div>
                                    ${data.member.skills ? `
                                    <div class="profile-skills">
                                        <h3>Skills</h3>
                                        <div class="skills-list">
                                            ${data.member.skills.split(',').map(skill => `<span class="skill-tag">${skill.trim()}</span>`).join('')}
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                            `;
                            
                            modal.style.display = 'block';
                        } else {
                            alert('Failed to load member profile.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while loading the profile.');
                    });
            });
        });
        
        // Close modal
        closeBtn.onclick = function() {
            modal.style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>