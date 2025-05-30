<?php
    include 'db/db.php';
    if(session_status() == PHP_SESSION_NONE) {
       session_start();
    }
    if (isset($_SESSION['admin_name']))
    {
        if(isset($_GET['delete']))
        {
            $id = $_GET['delete'];
            $sql = "DELETE FROM property WHERE property_id = $id";
            $result = mysqli_query($con, $sql);
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
        /* Minimal Client Management Styles */
.content {
    padding: 20px;
    background: #f8f9fa;
}

.main {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    overflow: hidden;
}

.h1 {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: #303030;
    color: white;
}

.h1 h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.btnn {
    display: flex;
    gap: 10px;
}

.btn {
    background: #15bb31;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.btn:hover {
    background: #128a27;
    transform: translateY(-1px);
}

.btn i {
    font-size: 14px;
}

.message {
    padding: 12px 20px;
    margin: 0 20px;
    border-radius: 4px;
    font-weight: 500;
}

.message.success {
    background: #e6ffed;
    color: #2d8a4a;
}

.message.error {
    background: #ffebee;
    color: #c62828;
}

/* Minimal Table Styling - No Lines */
.info {
    padding: 20px;
    overflow-x: auto;
}

.info table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
    margin-bottom: 15px;
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

.info tr:hover td {
    background: #f8f9fa;
}


.edit-button {
    color: #4285f4;
}

.delete-button {
    color: #ea4335;
}

.edit-button:hover {
    background: rgba(66, 133, 244, 0.1);
}

.delete-button:hover {
    background: rgba(234, 67, 53, 0.1);
}




.close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
}

.modal h2 {
    margin-top: 0;
    color: #303030;
    font-size: 1.5rem;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #495057;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 10px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: 0.9rem;
}

.readonly-field {
    background: #f8f9fa;
    color: #6c757d;
}

.action-buttons {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    margin-top: 20px;
}

.save-button, .cancel-button {
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s ease;
}

.save-button {
    background: #15bb31;
    color: white;
}

.save-button:hover {
    background: #128a27;
}

.info img {
    border-radius: 4px;
    object-fit: cover;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: transform 0.3s ease;
}

.info img:hover {
    transform: scale(1.2);
    z-index: 20;
    position: relative;
    border: 3px solid #00ff00;
    box-shadow: 0 8px 16px rgba(0,0,0,0.15);
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
                    <div><h1 style="font-size:25px; font-weight:bold;">Apartment Description</h1></div>
                <div class="btnn">
                    <a href="add_property.php"><button class="btn"><i class="fa fa-plus"></i>Add Apartment</button></a>
                </div>
                </div>
                <div class="info">
                    <table cellpadding="10px">
                        <tr>
                            <th>No.</th>
                            <th>Apartment Name</th>
                            <th>Address</th>
                            <th>Monthly Price</th>
                            <th>Status</th>
                            <th>Bedrooms</th>
                            <th>Bathrooms</th>
                            <th>Balcony</th>
                            <th>Area</th>
                            <th>Description</th>
                            <th>Image 1</th>
                            <th>Image 2</th>
                            <th>Image 3</th>
                            <th>Image 4</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                        $i=1;
                        $result = mysqli_query($con,"select * from property");
                        while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['bedroom']; ?></td>
                            <td><?php echo $row['bathroom']; ?></td>
                            <td><?php echo $row['balcony']; ?></td>
                            <td><?php echo $row['area']; ?>sqft</td>
                            <td><?php echo $row['description']; ?></td>
                            <td><img src="uploads/<?php echo $row['img1']; ?>" height="50px" width="50px"></td>
                            <td><img src="uploads/<?php echo $row['img2']; ?>" height="50px" width="50px"></td>
                            <td><img src="uploads/<?php echo $row['img3']; ?>" height="50px" width="50px"></td>
                            <td><img src="uploads/<?php echo $row['img4']; ?>" height="50px" width="50px"></td>
                            <td><a href='edit_property.php?edit=<?php echo $row['property_id']; ?>'
                            <i class="fa-regular fa-pen-to-square" style="color:blue"></i></a>
                            <a href='property.php?delete=<?php echo $row['property_id']; ?>' onclick="return confirm('Are you sure you want to delete this Apartment?');"><i class="fa-regular fa-trash-can" style="color:red"></i></a></td>
                         </tr>
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