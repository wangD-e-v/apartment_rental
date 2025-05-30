<?php
session_start();
include "db/db.php";

// Check if user is already logged in
if(isset($_SESSION['admin_name'])) {
    header("Location: index.php");
    exit();
}

$error = '';
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password']; // Don't escape passwords
    
    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($con, "SELECT admin_id, username, password FROM admin_reg WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) == 1) {
        $admin = mysqli_fetch_assoc($result);
        
        // Verify password (use password_verify if passwords are hashed)
        if($password === $admin['password']) { // Replace with password_verify if using hashed passwords
            $_SESSION['admin_name'] = $admin['username'];
            $_SESSION['admin_id'] = $admin['admin_id'];
            
            // Update last login time
            $update = mysqli_prepare($con, "UPDATE admin_reg SET last_logged_in = NOW() WHERE admin_id = ?");
            mysqli_stmt_bind_param($update, "i", $admin['admin_id']);
            mysqli_stmt_execute($update);
            
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid username or password!";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>STAYSPOT - Login</title>
    <style>
        .error-message {
            color: #ff3333;
            text-align: center;
            margin-bottom: 15px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-box">
            <h2>Login</h2>
            
            <?php if(!empty($error)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="" method="post" autocomplete="off">
                <div class="input-box">
                    <i class="fa fa-user"></i>
                    <input type="text" name="username" placeholder="Username" required maxlength="50">
                </div>
                <div class="input-box">
                    <i class="fa fa-lock"></i>
                    <input type="password" name="password" placeholder="Password" required maxlength="20">
                </div>
                <input type="submit" value="Login" name="login" class="login-btn">
            </form>
        </div>
    </div>
</body>

</html>