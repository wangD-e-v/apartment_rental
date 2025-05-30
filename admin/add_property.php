<?php
    include 'db/db.php';
    if(session_status() == PHP_SESSION_NONE) {
       session_start();
    }
    if (isset($_SESSION['admin_name']))
    {
        if(isset($_POST['submit']))
        {
            $name = $_POST['name'];
            $address = $_POST['address'];
            $price = $_POST['price'];
            $status = $_POST['status'];
            $bedrooms = $_POST['bedroom'];
            $bathrooms = $_POST['bathroom'];
            $balcony = $_POST['balcony'];
            $area = $_POST['area'];
            $description = $_POST['description'];
            
            // First check if apartment with same name and address already exists
            $check_sql = "SELECT * FROM property WHERE name = '$name' OR address = '$address'";
            $check_result = mysqli_query($con, $check_sql);
            
            if(mysqli_num_rows($check_result) > 0) {
                // Apartment or Address exists
                echo "<script>
                    alert('Apartment Name or Apartment Address is Already Exist!');
                    window.history.back();
                </script>";
                exit();
            }
            
            // Process file uploads only if no duplicate found
            $image_1 = $_FILES['img1']['name'];
            $image_2 = $_FILES['img2']['name'];
            $image_3 = $_FILES['img3']['name'];
            $image_4 = $_FILES['img4']['name'];

            $tempname = $_FILES["img1"]["tmp_name"];
            $target_dir = "uploads/".$image_1;
            move_uploaded_file($tempname, $target_dir);

            $tempname = $_FILES["img2"]["tmp_name"];
            $target_dir = "uploads/".$image_2;
            move_uploaded_file($tempname, $target_dir);

            $tempname = $_FILES["img3"]["tmp_name"];
            $target_dir = "uploads/".$image_3;
            move_uploaded_file($tempname, $target_dir);

            $tempname = $_FILES["img4"]["tmp_name"];
            $target_dir = "uploads/".$image_4;
            move_uploaded_file($tempname, $target_dir);

            //insert property details to database
            $sql = "insert into property (name, address, price, status, bedroom, bathroom, balcony, area, description, img1, img2, img3, img4) values
            ('$name', '$address', '$price', '$status', '$bedrooms', '$bathrooms', '$balcony', '$area', '$description', '$image_1', '$image_2', '$image_3', '$image_4')";
            $result = mysqli_query($con, $sql);
            if ($result) {
                header("location: property.php");
            } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($con);
            }
        }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
    <title>Apartment Rental - Apartment</title>
    <script>
        // Preserve form data when going back
        document.addEventListener('DOMContentLoaded', function() {
            if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_BACK_FORWARD) {
                // Form data will be preserved automatically by browser
            }
        });
    </script>

    <style>
        .btn {
    background: #15bb31;
    color: white;
    border: none;
    padding: auto;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}


.info th {
    text-align: left;
    background: #f1f3f5;
    color: #495057;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.info td {
    background: white;
    color: #495057;
    font-size: 0.9rem;
    vertical-align: middle;
    transition: all 0.2s ease;
}

.readonly-field {
    background: #f8f9fa;
    color: #6c757d;
}
.main {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
    justify-content: center;
    align-items: center;

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
            <div class="main">
                <div class="h1">
                    <div><h1>Add Apartment</h1></div>
                </div>
                <div class="info">
                    <table cellpadding="10px" class="input">
                    <form method="POST" enctype="multipart/form-data">
                        <tr>
                            <th>Apartment Name:</th>
                            <td><input type="text" name="name" placeholder="Enter Apartment Name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td><input type="text" name="address" placeholder="Enter Address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Monthly Price:</th>
                            <td><input type="number" name="price" placeholder="Enter Monthly Price" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <select name="status" required>
                                    <option value="available" <?php echo (isset($_POST['status']) && $_POST['status'] == 'available') ? 'selected' : ''; ?>>available</option>
                                    <option value="rented" <?php echo (isset($_POST['status']) && $_POST['status'] == 'rented') ? 'selected' : ''; ?>>rented</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Bedrooms:</th>
                            <td><input type="number" name="bedroom" placeholder="Enter Bedrooms Number" value="<?php echo isset($_POST['bedroom']) ? htmlspecialchars($_POST['bedroom']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Bathrooms:</th>
                            <td><input type="number" name="bathroom" placeholder="Enter Bathrooms Number" value="<?php echo isset($_POST['bathroom']) ? htmlspecialchars($_POST['bathroom']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Balcony:</th>
                            <td><input type="number" name="balcony" placeholder="Enter Balcony Number" value="<?php echo isset($_POST['balcony']) ? htmlspecialchars($_POST['balcony']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Area (sqft):</th>
                            <td><input type="number" name="area" placeholder="Enter Area in sqft" value="<?php echo isset($_POST['area']) ? htmlspecialchars($_POST['area']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td><input type="text" name="description" placeholder="Enter Description" value="<?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?>" required></td>
                        </tr>
                        <tr>
                            <th>House:</th>
                            <td><input type="file" name="img1" required></td>
                        </tr>
                        <tr>
                            <th>Bedroom:</th>
                            <td><input type="file" name="img2" required></td>
                        </tr>
                        <tr>
                            <th>Kitchen:</th>
                            <td><input type="file" name="img3" required></td>
                        </tr>
                        <tr>
                            <th>Bathroom:</th>
                            <td><input type="file" name="img4" required></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;">
                                <div style="display: flex; justify-content: center; gap: 20px;">
                                    <input type="submit" value="Add Apartment" name="submit" class="btn">
                                    <a href="property.php" class="btn" style="background-color:maroon";>Cancel</a>
                                </div>
                            </td>
                        </tr>
                    </form>
                    </table>
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