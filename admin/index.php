<?php
    include 'db/db.php';
    if(session_status() == PHP_SESSION_NONE) {
       session_start();
    }
    if (isset($_SESSION['admin_name']))
    {

        $vacant_query = "SELECT 
                            (COUNT(CASE WHEN status = 'available' THEN 1 END) / COUNT(*)) * 100 as vacancy_rate 
                          FROM property";
        $vacant_result = mysqli_query($con, $vacant_query);
        $vacant_data = mysqli_fetch_assoc($vacant_result);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>Apartment Rental - Admin Panel</title>

    <style>
        /* Add these new styles for the rules card */
        .rules-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            /* padding: 25px;
            margin: 20px 0; */
            margin-top: 30px;
            width: 100%;
            max-width: 800px;
            color: maroon;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .rules-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                rgba(255, 0, 150, 0.1) 0%,
                rgba(0, 204, 255, 0.1) 50%,
                rgba(0, 255, 161, 0.1) 100%
            );
            transform: rotate(45deg);
            z-index: -1;
            animation: gradientShift 8s ease infinite;
        }
        
        @keyframes gradientShift {
            0% { transform: rotate(45deg) translateX(-10%); }
            50% { transform: rotate(45deg) translateX(10%); }
            100% { transform: rotate(45deg) translateX(-10%); }
        }
        
        .rules-card h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            text-align: center;
            color: #fff;
            text-shadow: 0 0 10px rgba(255,255,255,0.3);
        }
        
        .rules-card p {
            font-size: 1.2rem;
            line-height: 1.6;
            text-align: center;
            margin-bottom: 10px;
        }
        
        .warning-icon {
            display: block;
            text-align: center;
            font-size: 3rem;
            margin-top:10px;
            margin-bottom: 15px;
            color: #ff6b6b;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        
        .rules-section {
            width: 100;
            display: flex;
            justify-content: center;
            padding: 5px; 
        }

        </style>
</head>

<body>
    <div class="side-menu">
        <div class="brand-name">
            <h2 style="color:white;";><i style="color:#DA2C32; margin-right: 8px; font-size:40px"; class="fas fa-house"></i>Apartment Rental</h2>
           
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
            <div class="main" style="border-radius: 20px;">
                <div class="h1">
                    <h1>Welcome Back Administrator!</h1>
                </div>
                <div class="cards">
                    
                    <a href="property.php" style="color: white;"><div class="card" style="background: #007bff; ">
                        <div class="box">
                            <?php $sql = "SELECT COUNT(*) as total_property FROM property"; 
                                  $result = $con->query($sql); 
                                  if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                            ?>
                            <h1><?php echo $row["total_property"]; ?></h1>
                            <?php
                               } 
                            ?>
                            <h3>Apartments</h3>
                        </div>
                        <div class="icon-case">
                            <i class="fa-solid fa-house"></i>
                        </div>
                    </div></a>


                    <a href="tenants.php" style="color: white;"><div class="card" style="background: #ffc107;">
                        <div class="box">
                            <?php $sql = "SELECT COUNT(*) as total_tenant FROM tenants"; 
                                  $result = $con->query($sql); 
                                  if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                            ?>
                            <h1><?php echo $row["total_tenant"]; ?></h1>
                            <?php
                               } 
                            ?>
                            <h3>Clients</h3>
                        </div>
                        <div class="icon-case">
                            <i class="fa fa-user-friends "></i>
                        </div>

                    </div></a>


                    <a href="property.php" style="color: white;"><div class="card" style="background: rgb(168, 65, 68);">
                        <div class="box">
                            <h1 class="stats-value"><?php echo number_format($vacant_data['vacancy_rate'] ?? 0, 2); ?>%</h1>
                            
                            <h3>Vacancy</h3>
                        </div>
                        <div class="icon-case">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div></a>


                    
                    <a href="payments.php" style="color: white;"><div class="card" style="background: #28a745;">
                        <div class="box">
                            <?php $sql = "SELECT sum(amount) as payment FROM payment"; 
                                  $result = $con->query($sql); 
                                  if ($result->num_rows > 0) {
                                    $row = $result->fetch_assoc();
                            ?>
                            <?php
                               }
                            ?>
                            
                            <h3>Payment</h3>
                        </div>
                        <div class="icon-case">
                            <i class="fas fa-credit-card"></i>
                        </div>
                    </div></a>
                    
                </div>
            </div>
            <!-- New Rules and Regulation Card -->
                <div class="rules-section">
                    <div class="rules-card">
                        <div class="warning-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h2 style="color:red">RULES & REGULATIONS</h2>
                        <p><strong>Important Notice:</strong> We do not accept apartment reservations.</p>
                        <p>All rentals require immediate payment confirmation to secure the unit.</p>
                        <p>Please ensure all transactions are completed through cash.</p>
                    </div>
                </div>
        </div>
    </div>
</body>
</html>

<?php
    }
    else{
        header("location: login.php");
    }
?>