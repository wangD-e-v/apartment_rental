<?php
    include 'db/db.php';
    if(session_status() == PHP_SESSION_NONE) {
       session_start();
    }
    if (isset($_SESSION['admin_name']))
    {
        if(isset($_POST['update']))
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
            $image_1 = $_FILES['img1']['name'];
            $image_2 = $_FILES['img2']['name'];
            $image_3 = $_FILES['img3']['name'];
            $image_4 = $_FILES['img4']['name'];

            $id = $_GET['edit'];
            $sql = "UPDATE property SET 
                    name='$name', 
                    address='$address',  
                    price='$price', 
                    status='$status', 
                    bedroom='$bedrooms', 
                    bathroom='$bathrooms', 
                    balcony='$balcony', 
                    area='$area', 
                    description='$description'";

            $images = ['img1', 'img2', 'img3', 'img4'];
            foreach ($images as $img) {
                if (!empty($_FILES[$img]['name'])) {
                    $image_name = $_FILES[$img]['name'];
                    $tempname = $_FILES[$img]["tmp_name"];
                    $target_dir = "uploads/" . $image_name;
                    move_uploaded_file($tempname, $target_dir);
                    $sql .= ", $img='$image_name'";
                }
            }

            // Complete the SQL query with the WHERE clause
            $sql .= " WHERE property_id = $id";

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
            <li><a href="profile.php" class="active"><i class="fas fa-user-shield-shield"></i><span>Profile</span></a></li>
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
                            <!-- <a href="manage-account.php"><i class="fa fa-cog"></i>Manage Account</a> -->
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
                    <div><h1>Edit Apartment</h1></div>
                </div>
                <div class="info">
                    <table cellpadding="10px" class="input">
                    <?php 
                    $id = $_GET['edit'];
                    $result = mysqli_query($con,"select * from property where property_id = $id");
                    while($row = mysqli_fetch_assoc($result)) { ?>
                    <form method="POST" enctype="multipart/form-data">
                        <tr>
                            <th>Apartment Name:</th>
                            <td><input type="text" name="name" value="<?php echo $row['name']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td><input type="text" name="address" value="<?php echo $row['address']; ?>" ></td>
                        </tr>
                        
                        <tr>
                            <th>Monthly Price:</th>
                            <td><input type="number" name="price" value="<?php echo $row['price']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <select name="status" >
                                    <option value="available" <?php if ($row['status'] == 'available') echo 'selected'; ?>>available</option>
                                    <option value="rented" <?php if ($row['status'] == 'rented') echo 'selected'; ?>>rented</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Bedrooms:</th>
                            <td><input type="number" name="bedroom" value="<?php echo $row['bedroom']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>Bathrooms:</th>
                            <td><input type="number" name="bathroom" value="<?php echo $row['bathroom']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>Balcony:</th>
                            <td><input type="number" name="balcony" value="<?php echo $row['balcony']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>Area (sqft):</th>
                            <td><input type="number" name="area" value="<?php echo $row['area']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td><input type="text" name="description" value="<?php echo $row['description']; ?>" ></td>
                        </tr>
                        <tr>
                            <th>House:</th>
                            <td><input type="file" name="img1" >
                            <img src="uploads/<?php echo $row['img1']; ?>" height="150px" width="250px"></td>
                        </tr>
                        <tr>
                            <th>Bedroom:</th>
                            <td><input type="file" name="img2" >
                            <img src="uploads/<?php echo $row['img2']; ?>" height="150px" width="250px"></td>
                        </tr>
                        <tr>
                            <th>Kitchen:</th>
                            <td><input type="file" name="img3" >
                            <img src="uploads/<?php echo $row['img3']; ?>" height="150px" width="250px"></td>
                        </tr>
                        <tr>
                            <th>Bathroom:</th>
                            <td><input type="file" name="img4" >
                            <img src="uploads/<?php echo $row['img4']; ?>" height="150px" width="250px"></td>
                        </tr>
                        <tr>
    <td colspan="2" style="text-align: center;">
        <div style="display: flex; justify-content: center; gap: 20px;">
            <input type="submit" value="Update Apartment" name="update" class="btn">
            
            <a href="property.php" class="btn" style="color:white; background-color:maroon";>Cancel</a>
        </div>
    </td>
</tr>

                    </form>
                    <?php } ?>
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