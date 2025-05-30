<?php
    include 'db/db.php';
    if(session_status() == PHP_SESSION_NONE) {
       session_start();
    }
    
    // Check if last_logged_in column exists, if not create it
    $check_column = mysqli_query($con, "SHOW COLUMNS FROM admin_reg LIKE 'last_logged_in'");
    if(mysqli_num_rows($check_column) == 0) {
        mysqli_query($con, "ALTER TABLE admin_reg ADD COLUMN last_logged_in TIMESTAMP NULL DEFAULT NULL");
    }

    if (isset($_SESSION['admin_name'])) {
        // Update last login time for current session
        $update_login = "UPDATE admin_reg SET last_logged_in = CURRENT_TIMESTAMP WHERE username = '".$_SESSION['admin_name']."'";
        mysqli_query($con, $update_login);
        
        // Get admin details
        $admin_query = "SELECT * FROM admin_reg WHERE username = '".$_SESSION['admin_name']."'";
        $admin_result = mysqli_query($con, $admin_query);
        $admin_data = mysqli_fetch_assoc($admin_result);
        
        // Get total properties count
        $property_query = "SELECT COUNT(property_id) as total_properties FROM property";
        $property_result = mysqli_query($con, $property_query);
        $property_data = mysqli_fetch_assoc($property_result);
        
        // Get active tenants count
        $tenants_query = "SELECT COUNT(tenant_id) as active_tenants FROM tenants";
        $tenants_result = mysqli_query($con, $tenants_query);
        $tenants_data = mysqli_fetch_assoc($tenants_result);
        
        // Get monthly revenue (only paid payments)
        $revenue_query = "SELECT SUM(amount) as monthly_revenue FROM payment WHERE payment_status = 'paid'";
        $revenue_result = mysqli_query($con, $revenue_query);
        $revenue_data = mysqli_fetch_assoc($revenue_result);
        
        // Get vacancy rate (available properties / total properties)
        $vacant_query = "SELECT 
                            (COUNT(CASE WHEN status = 'available' THEN 1 END) / COUNT(*)) * 100 as vacancy_rate 
                          FROM property";
        $vacant_result = mysqli_query($con, $vacant_query);
        $vacant_data = mysqli_fetch_assoc($vacant_result);
        
        // Get recent activities (last 3 properties added)
        $activities_query = "SELECT name, property_id FROM property ORDER BY property_id DESC LIMIT 3";
        $activities_result = mysqli_query($con, $activities_query);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>Apartment Rental - Profile</title>
    <style>
        /* Main Layout */
        body {
            background: #f4f4f4;
            display: flex;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .content {
            flex: 1;
            padding: 20px;
            background: rgba(255, 255, 255, 0);
        }
        
        /* Profile Container */
        .profile-container {
            display: flex;
            gap: 30px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .profile-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            flex: 1;
            min-width: 300px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #f0f0f0;
            margin-right: 20px;
        }
        
        .profile-info h2 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }
        
        .profile-info p {
            margin: 5px 0 0;
            color: #777;
        }
        
        .profile-details {
            margin-top: 20px;
        }
        
        .detail-row {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-label {
            flex: 1;
            color: #666;
            font-weight: 500;
        }
        
        .detail-value {
            flex: 2;
            color: #333;
        }
        
        .profile-actions {
            margin-top: 30px;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-edit {
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s;
            font-size: 14px;
        }
        
        .btn-edit {
            background: #DA2C32;
        }
        
        .btn-edit:hover {
            background: #b82227;
        }
        
     
        
        /* Stats Cards */
        .stats-container {
            flex: 0.8;
            min-width: 250px;
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            color: white;
            margin-right: 20px;
        }
        
        .stats-title {
            font-size: 16px;
            margin-bottom: 10px;
            color: rgba(255,255,255,0.8);
        }
        
        .stats-value {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            width: 80%;
            max-width: 500px;
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #777;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #555;
        }
        
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            border: none;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-primary {
            background: #DA2C32;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
            }
            
            .stats-container {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="side-menu">
        <div class="brand-name">
            <h2 style="color:white;"><i style="color:#DA2C32; margin-right: 8px; font-size:40px" class="fas fa-house"></i>Apartment Rental</h2>
        </div>
        
        <ul>
            <li><a href="index.php"><i class="fa-solid fa-chess-board"></i><span>Dashboard</span></a></li>
            <li><a href="property.php"><i class="fa-solid fa-house"></i><span>Apartment</span></a></li>
            <li><a href="tenants.php"><i class="fa fa-user-friends"></i><span>Clients</span></a></li>
            <li><a href="payments.php"><i class="fas fa-credit-card"></i><span>Payments</span></a></li>
            <li><a href="profile.php" class="active"><i class="fas fa-user-shield"></i><span>Profile</span></a></li>
        </ul>
        <div class="footer" style="position: absolute; bottom: 0; width: 100%; text-align: center; color: white;">
            <p>&copy; 2025 Apartment Rental. All rights reserved.</p>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="header">
            <div class="nav">
                <div class="account">
                    <div class="dropdown">
                        <button class="dropbtn"><i class="fa fa-user-shield"></i> Account <i class="fa fa-caret-down"></i></button>
                        <div class="dropdown-content">
                            <a href="profile.php"><i class="fa fa-user-shield"></i>Profile</a>
                            <a href="logout.php"><i class="fa fa-sign-out-alt"></i>Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="main">
                <div class="h1">
                    <div><h1>Admin Profile</h1></div>
                </div>
                
                <div class="profile-container">
                    <div class="profile-card">
                        <div class="profile-header">
                            <?php if(!empty($admin_data['profile_pic'])): ?>
                                <img src="uploads/<?php echo $admin_data['profile_pic']; ?>" class="profile-avatar" alt="Admin Avatar">
                            <?php else: ?>
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['admin_name']); ?>&background=DA2C32&color=fff" class="profile-avatar" alt="Admin Avatar">
                            <?php endif; ?>
                            <div class="profile-info">
                                <h2><?php echo $_SESSION['admin_name']; ?></h2>
                                <p>Administrator</p>
                            </div>
                        </div>
                        
                        <div class="profile-details">
                            <div class="detail-row">
                                <div class="detail-label">Email</div>
                                <div class="detail-value"><?php echo !empty($admin_data['email']) ? $admin_data['email'] : 'N/A'; ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value"><?php echo !empty($admin_data['phone']) ? $admin_data['phone'] : 'N/A'; ?></div>
                            </div>
                            <div class="detail-row">
                                <div class="detail-label">Last Login</div>
                                <div class="detail-value"><?php echo !empty($admin_data['last_logged_in']) ? date("F j, Y, g:i a", strtotime($admin_data['last_logged_in'])) : 'N/A'; ?></div>
                            </div>
                        </div>
                        
                        <div class="profile-actions">
                            <button class="btn-edit" onclick="openEditModal()"><i class="fas fa-edit"></i> Edit Profile</button>
                        </div>
                    </div>
                    
                    <div class="stats-container">
                        <div class="stats-card" style="background: #007bff;">
                            <h3 class="stats-title">Total Apartment</h3>
                            <p class="stats-value"><?php echo $property_data['total_properties']; ?></p>
                        </div>
                        <div class="stats-card" style="background: #28a745;">
                            <h3 class="stats-title">Active Clients</h3>
                            <p class="stats-value"><?php echo $tenants_data['active_tenants']; ?></p>
                        </div>
                        <div class="stats-card" style="background: #ffc107;">
                            <h3 class="stats-title">Monthly Revenue</h3>
                            <p class="stats-value">₱<?php echo number_format($revenue_data['monthly_revenue'] ?? 0, 2); ?></p>
                        </div>
                        <div class="stats-card" style="background: rgb(168, 65, 68);">
                            <h3 class="stats-title">Vacancy Rate</h3>
                            <p class="stats-value"><?php echo number_format($vacant_data['vacancy_rate'] ?? 0, 2); ?>%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Modal -->
    <div id="editProfileModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Profile</h2>
                <button class="close-btn" onclick="closeModal('editProfileModal')">&times;</button>
            </div>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data" id="profileForm">
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo htmlspecialchars($admin_data['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($admin_data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($admin_data['phone'] ?? ''); ?>">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editProfileModal')">Close</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        // Modal functions
        function openEditModal() {
            document.getElementById('editProfileModal').style.display = 'block';
        }
        
        
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.className === 'modal') {
                event.target.style.display = 'none';
            }
        }
        
        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('profile_pic');
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                
                if (!validTypes.includes(file.type)) {
                    alert('Only JPG, PNG, and GIF images are allowed');
                    e.preventDefault();
                    return false;
                }
                
                if (file.size > maxSize) {
                    alert('File size must be less than 2MB');
                    e.preventDefault();
                    return false;
                }
            }
            return true;
        });
        
    </script>
</body>

</html>
<?php
    } else {
        header("location: login.php");
    }
?>