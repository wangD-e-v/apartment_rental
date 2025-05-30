<?php
include 'db/db.php';
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Create tenants table if not exists
$createTableQuery = "CREATE TABLE IF NOT EXISTS tenants (
    tenant_id INT(30) AUTO_INCREMENT PRIMARY KEY,
    property_id INT(30) NOT NULL,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    number VARCHAR(20) NOT NULL,
    address VARCHAR(255) NOT NULL,
    rent_months INT(11) NOT NULL,
    date_from DATE NOT NULL,
    date_to DATE NOT NULL,
    total_rent DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (property_id) REFERENCES property(property_id)
)";

mysqli_query($con, $createTableQuery);

if (isset($_SESSION['admin_name'])) {
    // Handle delete operation
    if(isset($_GET['tid'])) {
        $id = $_GET['tid'];
        $sql = "DELETE FROM tenants WHERE tenant_id = $id";
        $result = mysqli_query($con, $sql);
        if($result) {
            $_SESSION['message'] = "Client deleted successfully";
            header("location: tenants.php");
            exit();
        }
    }
    
    // Handle add and edit operations
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isset($_POST['add_tenant'])) {
            // Add new tenant
            $property_id = mysqli_real_escape_string($con, $_POST['property_id']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $number = mysqli_real_escape_string($con, $_POST['number']);
            $address = mysqli_real_escape_string($con, $_POST['address']);
            $date_from = mysqli_real_escape_string($con, $_POST['date_from']);
            $date_to = mysqli_real_escape_string($con, $_POST['date_to']);
            
            // Calculate rent months
            $start = new DateTime($date_from);
            $end = new DateTime($date_to);
            $interval = $start->diff($end);
            $rent_months = ($interval->y * 12) + $interval->m;
            
            // Get property price and calculate total rent
            $priceQuery = mysqli_query($con, "SELECT price FROM property WHERE property_id = '$property_id'");
            $property = mysqli_fetch_assoc($priceQuery);
            $total_rent = $property['price'] * $rent_months;
            
            $sql = "INSERT INTO tenants (property_id, fullname, email, number, address, rent_months, date_from, date_to, total_rent) 
                    VALUES ('$property_id', '$fullname', '$email', '$number', '$address', '$rent_months', '$date_from', '$date_to', '$total_rent')";
            $result = mysqli_query($con, $sql);
            
            if($result) {
                // Update property status to rented
                mysqli_query($con, "UPDATE property SET status = 'rented' WHERE property_id = '$property_id'");
                
                $_SESSION['message'] = "Client added successfully";
                header("location: tenants.php");
                exit();
            }
        } elseif(isset($_POST['update_tenant'])) {
            // Update existing tenant
            $tenant_id = mysqli_real_escape_string($con, $_POST['tenant_id']);
            $property_id = mysqli_real_escape_string($con, $_POST['property_id']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $number = mysqli_real_escape_string($con, $_POST['number']);
            $address = mysqli_real_escape_string($con, $_POST['address']);
            $date_from = mysqli_real_escape_string($con, $_POST['date_from']);
            $date_to = mysqli_real_escape_string($con, $_POST['date_to']);
            
            // Calculate rent months
            $start = new DateTime($date_from);
            $end = new DateTime($date_to);
            $interval = $start->diff($end);
            $rent_months = ($interval->y * 12) + $interval->m;
            
            // Get property price and calculate total rent
            $priceQuery = mysqli_query($con, "SELECT price FROM property WHERE property_id = '$property_id'");
            $property = mysqli_fetch_assoc($priceQuery);
            $total_rent = $property['price'] * $rent_months;
            
            $sql = "UPDATE tenants SET 
                    property_id = '$property_id',
                    fullname = '$fullname',
                    email = '$email',
                    number = '$number',
                    address = '$address',
                    rent_months = '$rent_months',
                    date_from = '$date_from',
                    date_to = '$date_to',
                    total_rent = '$total_rent'
                    WHERE tenant_id = '$tenant_id'";
            $result = mysqli_query($con, $sql);
            
            if($result) {
                $_SESSION['message'] = "Client updated successfully";
                header("location: tenants.php");
                exit();
            }
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
    <title>Apartment Rental - Clients</title>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            margin-top:5px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .action-buttons {
            margin-top: 20px;
        }
        .action-buttons button {
            padding: 8px 15px;
            margin-right: 10px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
        }
        .readonly-field {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
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
}

.info th {
    text-align: left;
    padding: 12px 16px;
    background: #f1f3f5;
    color: #495057;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info td {
    padding: 16px;
    background: white;
    color: #495057;
    font-size: 0.9rem;
    vertical-align: middle;
    transition: all 0.2s ease;
}

.info tr:hover td {
    background: #f8f9fa;
}

/* Action Buttons */
.edit-button, .delete-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.2s ease;
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

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 100;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.modal-content {
    background: white;
    margin: 5% auto;
    padding: 25px;
    width: 50%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    position: relative;
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
    justify-content: flex-end;
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

.cancel-button {
    background: #f1f3f5;
    color: #495057;
}

.cancel-button:hover {
    background: #dee2e6;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .modal-content {
        width: 90%;
        margin: 10% auto;
    }
    
    .h1 {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .info table {
        display: block;
        overflow-x: auto;
        white-space: nowrap;
    }
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
                    <h1 style="font-size:25px; font-weight:bold;">Client Management</h1>
                    <div class="btnn">
                    <button onclick="openAddModal()" class="btn">
                        <i class="fa fa-plus"></i> Add Client
                    </button>
                    </div>
                </div>
                
                <?php if(isset($_SESSION['message'])): ?>
                    <div class="message <?php echo strpos($_SESSION['message'], 'successfully') !== false ? 'success' : 'error'; ?>">
                        <?php 
                            echo $_SESSION['message']; 
                            unset($_SESSION['message']);
                        ?>
                    </div>
                <?php endif; ?>
                
                <div class="info">
                    <table cellpadding="10px">
                        <tr>
                            <th>No.</th>
                            <th>Client Fullname</th>
                            <th>Apartment Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Address</th>
                            <th>Rent Month/s</th>
                            <th>Date From</th>
                            <th>Date To</th>
                            <th>Total Rent</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                        $i=1;
                        $result = mysqli_query($con, "SELECT tenants.*, property.name FROM tenants JOIN property ON tenants.property_id = property.property_id");
                        while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['number']; ?></td>
                            <td><?php echo $row['address']; ?></td>
                            <td><?php echo $row['rent_months']; ?></td>
                            <td><?php echo $row['date_from']; ?></td>
                            <td><?php echo $row['date_to']; ?></td>
                            <td>₱<?php echo number_format($row['total_rent'], 2); ?></td>
                            <td>
                                <button onclick="openEditModal(
                                    '<?php echo $row['tenant_id']; ?>',
                                    '<?php echo $row['property_id']; ?>',
                                    '<?php echo addslashes($row['fullname']); ?>',
                                    '<?php echo $row['email']; ?>',
                                    '<?php echo $row['number']; ?>',
                                    '<?php echo addslashes($row['address']); ?>',
                                    '<?php echo $row['date_from']; ?>',
                                    '<?php echo $row['date_to']; ?>'
                                )" class="edit-button" style="color:blue; border: none; padding: auto; text-decoration: none; display: inline-block;">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a href="tenants.php?tid=<?php echo $row['tenant_id']; ?>" onclick="return confirm('Are you sure you want to delete this Client?')" class="delete-button" style="color: red; border: none; text-decoration: none; display: inline-block;">
                                    <i class="fa-regular fa-trash-can"></i>
                                </a>
                            </td>
                         </tr>
                         <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Tenant Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2  style="margin-bottom:0px;">Add Client</h2>
            <form action="tenants.php" method="POST">
                <div class="form-group">
                    <label for="property_id">Apartment Name:</label>
                    <select name="property_id" id="property_id" required onchange="updatePropertyDetails()">
                        <option value="">Select Apartment</option>
                        <?php
                        $properties = mysqli_query($con, "SELECT * FROM property WHERE status = 'available'");
                        while($property = mysqli_fetch_assoc($properties)) {
                            echo "<option value='".$property['property_id']."' data-price='".$property['price']."'>".$property['name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="monthly_rent">Monthly Rent (₱):</label>
                    <input type="text" id="monthly_rent" class="readonly-field" readonly>
                </div>
                <div class="form-group">
                    <label for="fullname">Client Fullname:</label>
                    <input type="text" name="fullname" id="fullname" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="number">Phone Number:</label>
                    <input type="text" name="number" id="number" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" name="address" id="address" required>
                </div>
                <div class="form-group">
                    <label for="date_from">Date From:</label>
                    <input type="date" name="date_from" id="date_from" required onchange="calculateRent()">
                </div>
                <div class="form-group">
                    <label for="date_to">Date To:</label>
                    <input type="date" name="date_to" id="date_to" required onchange="calculateRent()">
                </div>
                <div class="form-group">
                    <label for="rent_months_display">Rent Duration (Months):</label>
                    <input type="text" id="rent_months_display" class="readonly-field" readonly>
                    <input type="hidden" name="rent_months" id="rent_months">
                </div>
                <div class="form-group">
                    <label for="total_rent_display">Total Rent (₱):</label>
                    <input type="text" id="total_rent_display" class="readonly-field" readonly>
                    <input type="hidden" name="total_rent" id="total_rent">
                </div>
                <div class="action-buttons">
                    <button type="submit" name="add_tenant" class="save-button">Save</button>
                    <button type="button" onclick="closeAddModal()" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Tenant Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Client</h2>
            <form action="tenants.php" method="POST">
                <input type="hidden" name="tenant_id" id="edit_tenant_id">
                <div class="form-group">
                    <label for="edit_property_id">Apartment Name:</label>
                    <select name="property_id" id="edit_property_id" required onchange="updatePropertyDetails('edit')">
                        <option value="">Select Apartment</option>
                        <?php
                        $properties = mysqli_query($con, "SELECT * FROM property");
                        while($property = mysqli_fetch_assoc($properties)) {
                            echo "<option value='".$property['property_id']."' data-price='".$property['price']."'>".$property['name']."</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_monthly_rent">Monthly Rent (₱):</label>
                    <input type="text" id="edit_monthly_rent" class="readonly-field" readonly>
                </div>
                <div class="form-group">
                    <label for="edit_fullname">Client Fullname:</label>
                    <input type="text" name="fullname" id="edit_fullname" required>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" name="email" id="edit_email" required>
                </div>
                <div class="form-group">
                    <label for="edit_number">Phone Number:</label>
                    <input type="text" name="number" id="edit_number" required>
                </div>
                <div class="form-group">
                    <label for="edit_address">Address:</label>
                    <input type="text" name="address" id="edit_address" required>
                </div>
                <div class="form-group">
                    <label for="edit_date_from">Date From:</label>
                    <input type="date" name="date_from" id="edit_date_from" required onchange="calculateRent('edit')">
                </div>
                <div class="form-group">
                    <label for="edit_date_to">Date To:</label>
                    <input type="date" name="date_to" id="edit_date_to" required onchange="calculateRent('edit')">
                </div>
                <div class="form-group">
                    <label for="edit_rent_months_display">Rent Duration (Months):</label>
                    <input type="text" id="edit_rent_months_display" class="readonly-field" readonly>
                    <input type="hidden" name="rent_months" id="edit_rent_months">
                </div>
                <div class="form-group">
                    <label for="edit_total_rent_display">Total Rent (₱):</label>
                    <input type="text" id="edit_total_rent_display" class="readonly-field" readonly>
                    <input type="hidden" name="total_rent" id="edit_total_rent">
                </div>
                <div class="action-buttons">
                    <button type="submit" name="update_tenant" class="save-button">Update</button>
                    <button type="button" onclick="closeEditModal()" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Add Tenant Modal Functions
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        // Edit Tenant Modal Functions
        function openEditModal(tenant_id, property_id, fullname, email, number, address, date_from, date_to) {
            document.getElementById('edit_tenant_id').value = tenant_id;
            document.getElementById('edit_property_id').value = property_id;
            document.getElementById('edit_fullname').value = fullname;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_number').value = number;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_date_from').value = date_from;
            document.getElementById('edit_date_to').value = date_to;
            
            // Update property details for edit form
            updatePropertyDetails('edit');
            calculateRent('edit');
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Update property details when property is selected
        function updatePropertyDetails(formType = 'add') {
            const prefix = formType === 'edit' ? 'edit_' : '';
            const propertySelect = document.getElementById(`${prefix}property_id`);
            const selectedOption = propertySelect.options[propertySelect.selectedIndex];
            const monthlyRent = selectedOption.getAttribute('data-price') || '0';
            
            document.getElementById(`${prefix}monthly_rent`).value = '₱' + parseFloat(monthlyRent).toFixed(2);
        }
        
        // Calculate rent based on dates
        function calculateRent(formType = 'add') {
            const prefix = formType === 'edit' ? 'edit_' : '';
            const dateFrom = document.getElementById(`${prefix}date_from`).value;
            const dateTo = document.getElementById(`${prefix}date_to`).value;
            const propertySelect = document.getElementById(`${prefix}property_id`);
            
            if (!dateFrom || !dateTo || !propertySelect.value) return;
            
            const start = new Date(dateFrom);
            const end = new Date(dateTo);
            
            if (start >= end) {
                alert('End date must be after start date');
                return;
            }
            
            // Calculate months difference
            const months = (end.getFullYear() - start.getFullYear()) * 12;
            const totalMonths = months + (end.getMonth() - start.getMonth());
            
            // Get monthly rent
            const selectedOption = propertySelect.options[propertySelect.selectedIndex];
            const monthlyRent = parseFloat(selectedOption.getAttribute('data-price')) || 0;
            const totalRent = monthlyRent * totalMonths;
            
            // Update display fields
            document.getElementById(`${prefix}rent_months_display`).value = totalMonths;
            document.getElementById(`${prefix}rent_months`).value = totalMonths;
            document.getElementById(`${prefix}total_rent_display`).value = '₱' + totalRent.toFixed(2);
            document.getElementById(`${prefix}total_rent`).value = totalRent;
        }
        
        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target == document.getElementById('addModal')) {
                closeAddModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html>
<?php
} else {
    header("location: login.php");
}
?>