<?php
include 'db/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['admin_name'])) {

    // Ensure required columns exist in the 'payment' table
    $required_columns = [
        'tenant_id' => 'INT NOT NULL',
        'amount' => 'DECIMAL(10,2) NOT NULL',
        'payment_date' => 'DATE NOT NULL',
        'payment_status' => 'VARCHAR(50) NOT NULL DEFAULT "Paid"',
        'receipt_no' => 'VARCHAR(20) UNIQUE'
    ];

    $existing_columns = [];
    $res = mysqli_query($con, "SHOW COLUMNS FROM payment");
    while ($row = mysqli_fetch_assoc($res)) {
        $existing_columns[] = $row['Field'];
    }

    foreach ($required_columns as $col => $type) {
        if (!in_array($col, $existing_columns)) {
            $alter_sql = "ALTER TABLE payment ADD $col $type";
            mysqli_query($con, $alter_sql);
        }
    }

    // Delete payment
    if (isset($_GET['pid'])) {
        $id = $_GET['pid'];
        $sql = "DELETE FROM payment WHERE payment_id = $id";
        $result = mysqli_query($con, $sql);
        if ($result) {
            $_SESSION['message'] = "Payment deleted successfully";
            header("location: payments.php");
            exit();
        }
    }

    // Add or update payment
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $tenant_id = mysqli_real_escape_string($con, $_POST['tenant_id']);
        $amount = mysqli_real_escape_string($con, $_POST['amount']);
        $payment_date = mysqli_real_escape_string($con, $_POST['payment_date']);
        $payment_status = mysqli_real_escape_string($con, $_POST['payment_status']);

        if (isset($_POST['add_payment'])) {
            // Generate unique receipt number
            $receipt_no = 'RCPT-' . random_int(1000, 9999);
            $sql = "INSERT INTO payment (tenant_id, amount, payment_date, payment_status, receipt_no) 
                    VALUES ('$tenant_id', '$amount', '$payment_date', '$payment_status', '$receipt_no')";
        } elseif (isset($_POST['update_payment'])) {
            $payment_id = mysqli_real_escape_string($con, $_POST['payment_id']);
            $sql = "UPDATE payment SET 
                        tenant_id = '$tenant_id',
                        amount = '$amount',
                        payment_date = '$payment_date',
                        payment_status = '$payment_status'
                    WHERE payment_id = '$payment_id'";
        }

        if (isset($sql)) {
            $result = mysqli_query($con, $sql);
            if ($result) {
                $_SESSION['message'] = isset($_POST['add_payment']) 
                    ? "Payment added successfully" 
                    : "Payment updated successfully";
                header("location: payments.php");
                exit();
            }
        }
    }

    // Search functionality
    $search_query = "";
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search_term = mysqli_real_escape_string($con, $_GET['search']);
        $search_query = " AND (payment.receipt_no LIKE '%$search_term%' OR tenants.fullname LIKE '%$search_term%')";
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
    <title>Apartment Rental - Payments</title>
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
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
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
        .form-group input, .form-group select, .form-group textarea {
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
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .paid {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .pending {
            background-color: #fcf8e3;
            color: #8a6d3b;
        }
        .cancelled {
            background-color: #f2dede;
            color: #a94442;
        }
        .view-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            margin-right: 5px;
        }
        .print-button {
            background-color: #607d8b;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .receipt-details {
            margin-bottom: 20px;
        }
        .receipt-details div {
            margin-bottom: 10px;
        }
        .receipt-details label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            text-align: center;
            padding-top: 5px;
        }
        .search-container {
            margin-top: 15px;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
        }
        .search-container input {
            padding: 8px;
            width: 300px;
            margin-left: 400px;
        }
        .search-container button {
            padding: 8px 15px;
            cursor: pointer;
        }
        .search-container button:hover {
            background-color: #303030;
            color: white;
            
        }
        .btnclr {
            background-color: #f44336;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 4px;
        }
        .btnclr:hover {
            background-color: #303030;
            text-decoration: none;
            color: white;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            #viewModal, #viewModal * {
                visibility: visible;
            }
            #viewModal {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 20px;
            }
            .action-buttons {
                display: none;
            }
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
    justify-content: space-between;
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
                    <h1 style="font-size:25px; font-weight:bold;">Payment Management</h1>
                    <div class="btnn">
                        <button onclick="openAddModal()" class="btn">
                            <i class="fa fa-plus"></i> Add Payment
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
                
                <!-- Search Form -->
                <div class="search-container">
                    <form method="GET" action="payments.php">
                        <input type="text" name="search" placeholder="Search by receipt no. or client name" 
                               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                        <button type="submit"><i class="fa fa-search"></i> Search</button>
                        <?php
                         if(isset($_GET['search']) && !empty($_GET['search'])): 
                         
                         ?>
                            
                            <a href="payments.php" class="btnclr" style="margin-left: 10px;">Clear Search</a>
                        <?php endif; ?>
                    </form>
                </div>
                
                <div class="info">
                    <table cellpadding="10px">
                        <tr>
                            <th>No.</th>
                            <th>Receipt No.</th>
                            <th>Client Name</th>
                            <th>Apartment</th>
                            <th>Total Payment</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        <?php 
                        $i=1;
                        $result = mysqli_query($con, "SELECT payment.*, tenants.fullname, tenants.property_id, property.name as property_name 
                            FROM payment 
                            JOIN tenants ON payment.tenant_id = tenants.tenant_id
                            JOIN property ON tenants.property_id = property.property_id
                            WHERE 1=1 $search_query
                            ORDER BY payment_date DESC");
                        while($row = mysqli_fetch_assoc($result)) { 
                            $status_class = strtolower($row['payment_status']);
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo $row['receipt_no']; ?></td>
                            <td><?php echo $row['fullname']; ?></td>
                            <td><?php echo $row['property_name']; ?></td>
                            <td>₱<?php echo number_format($row['amount'], 2); ?></td>
                            <td><?php echo date('M j, Y', strtotime($row['payment_date'])); ?></td>
                            <td><span class="status-badge <?php echo $status_class; ?>"><?php echo $row['payment_status']; ?></span></td>
                            <td>
                                <button onclick="openViewModal(
                                    '<?php echo $row['receipt_no']; ?>',
                                    '<?php echo $row['fullname']; ?>',
                                    '<?php echo $row['property_name']; ?>',
                                    '<?php echo number_format($row['amount'], 2); ?>',
                                    '<?php echo date('M j, Y', strtotime($row['payment_date'])); ?>',
                                    '<?php echo $row['payment_status']; ?>'
                                )" class="view-button" style="text-decoration: none; background: none; border: none; color: #15bb31;">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button onclick="openEditModal(
                                    '<?php echo $row['payment_id']; ?>',
                                    '<?php echo $row['tenant_id']; ?>',
                                    '<?php echo $row['amount']; ?>',
                                    '<?php echo $row['payment_date']; ?>',
                                    '<?php echo $row['payment_status']; ?>'
                                )" class="edit-button">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <a href="payments.php?pid=<?php echo $row['payment_id']; ?>" onclick="return confirm('Are you sure you want to delete this payment?')" class="delete-button">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </td>
                         </tr>
                         <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Payment Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Payment</h2>
            <form action="payments.php" method="POST">
                <div class="form-group">
                    <label for="tenant_id">Client:</label>
                    <select name="tenant_id" id="tenant_id" required onchange="getTenantRent()">
                        <option value="">Select Client</option>
                        <?php
                        $tenants = mysqli_query($con, "SELECT t.tenant_id, t.fullname, t.total_rent, p.name 
                            FROM tenants t 
                            JOIN property p ON t.property_id = p.property_id
                            WHERE NOT EXISTS (
                                SELECT 1 FROM payment 
                                WHERE payment.tenant_id = t.tenant_id 
                                AND payment.payment_status = 'Paid'
                            )
                            ORDER BY t.fullname");
                        while($tenant = mysqli_fetch_assoc($tenants)) {
                            echo "<option value='".$tenant['tenant_id']."' data-rent='".$tenant['total_rent']."'>".$tenant['fullname']." (".$tenant['name'].")</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Total Payment (₱):</label>
                    <input type="number" step="0.01" name="amount" id="amount" required readonly>
                </div>
                <div class="form-group">
                    <label for="payment_date">Payment Date:</label>
                    <input type="date" name="payment_date" id="payment_date" required>
                </div>
                <div class="form-group">
                    <label for="payment_status">Payment Status:</label>
                    <select name="payment_status" id="payment_status" required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button type="submit" name="add_payment" class="save-button">Save</button>
                    <button type="button" onclick="closeAddModal()" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Edit Payment Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Payment</h2>
            <form action="payments.php" method="POST">
                <input type="hidden" name="payment_id" id="edit_payment_id">
                <div class="form-group">
                    <label for="edit_tenant_id">Client:</label>
                    <select name="tenant_id" id="edit_tenant_id" required onchange="getTenantRent('edit')">
                        <option value="">Select Client</option>
                        <?php
                        $tenants = mysqli_query($con, "SELECT t.tenant_id, t.fullname, t.total_rent, p.name 
                            FROM tenants t 
                            JOIN property p ON t.property_id = p.property_id
                            ORDER BY t.fullname");
                        while($tenant = mysqli_fetch_assoc($tenants)) {
                            echo "<option value='".$tenant['tenant_id']."' data-rent='".$tenant['total_rent']."'>".$tenant['fullname']." (".$tenant['name'].")</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit_amount">Total Payment (₱):</label>
                    <input type="number" step="0.01" name="amount" id="edit_amount" required>
                </div>
                <div class="form-group">
                    <label for="edit_payment_date">Payment Date:</label>
                    <input type="date" name="payment_date" id="edit_payment_date" required>
                </div>
                <div class="form-group">
                    <label for="edit_payment_status">Payment Status:</label>
                    <select name="payment_status" id="edit_payment_status" required>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="action-buttons">
                    <button type="submit" name="update_payment" class="save-button">Update</button>
                    <button type="button" onclick="closeEditModal()" class="cancel-button">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- View Payment Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <!-- <span class="close" onclick="closeViewModal()">&times;</span> -->
            <div class="receipt-header">
                <h2>Payment Receipt</h2>
                <h3>APARTMENT RENTAL SYSTEM</h3>
                <p>Payment Details</p>
            </div>
            <div class="receipt-details">
                <div>
                    <label>Receipt No.:</label>
                    <span id="view_receipt_no"></span>
                </div>
                <div>
                    <label>Client Name:</label>
                    <span id="view_tenant_name"></span>
                </div>
                <div>
                    <label>Apartment:</label>
                    <span id="view_property"></span>
                </div>
                <div>
                    <label>Total Payment:</label>
                    <span id="view_amount"></span>
                </div>
                <div>
                    <label>Payment Method:</label>
                    <span id="view_paymethod"></span>
                </div>
                <div>
                    <label>Payment Date:</label>
                    <span id="view_payment_date"></span>
                </div>
                <div>
                    <label>Status:</label>
                    <span id="view_payment_status"></span>
                </div>
            </div>
            <div class="signature-area">
                
                <div class="signature-line">
                    <p>Management Signature</p>
                </div>
            </div>
            <div class="action-buttons">
                <button onclick="printReceipt()" class="print-button">
                    <i class="fa fa-print"></i> Print Receipt
                </button>
                <button onclick="closeViewModal()" class="cancel-button">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>
    
    <script>
        // Get tenant's rent amount when client is selected
        function getTenantRent(formType = 'add') {
            const prefix = formType === 'edit' ? 'edit_' : '';
            const tenantSelect = document.getElementById(`${prefix}tenant_id`);
            const selectedOption = tenantSelect.options[tenantSelect.selectedIndex];
            const rentAmount = selectedOption.getAttribute('data-rent') || '0';
            
            document.getElementById(`${prefix}amount`).value = parseFloat(rentAmount).toFixed(2);
        }

        // Add Payment Modal Functions
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
            // Set default payment date to today
            document.getElementById('payment_date').valueAsDate = new Date();
        }
        
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        // Edit Payment Modal Functions
        function openEditModal(payment_id, tenant_id, amount, payment_date, payment_status) {
            document.getElementById('edit_payment_id').value = payment_id;
            document.getElementById('edit_tenant_id').value = tenant_id;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_payment_date').value = payment_date;
            document.getElementById('edit_payment_status').value = payment_status;
            
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // View Payment Modal Functions
        function openViewModal(receipt_no, tenant_name, property, amount, payment_date, payment_status) {
            document.getElementById('view_receipt_no').textContent = receipt_no;
            document.getElementById('view_tenant_name').textContent = tenant_name;
            document.getElementById('view_property').textContent = property;
            document.getElementById('view_amount').textContent = '₱' + amount;
            document.getElementById('view_paymethod').textContent = 'Cash';
            document.getElementById('view_payment_date').textContent = payment_date;
            document.getElementById('view_payment_status').textContent = payment_status;
            
            document.getElementById('viewModal').style.display = 'block';
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }
        
        function printReceipt() {
            window.print();
        }
        
        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target == document.getElementById('addModal')) {
                closeAddModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('viewModal')) {
                closeViewModal();
            }
        }
    </script>
</body>
</html>
<?php
} else {
    header("location: login.php");
}