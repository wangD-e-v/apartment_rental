<?php 
include "header.php"; 

// Database connection is already included in header.php

// Fetch tenant ID from the URL
$tid = $_GET['tid'];

// Fetch the total rent for the tenant
$result = mysqli_query($con, "SELECT total_rent,property_id FROM tenant WHERE tenant_id = $tid");
$row = mysqli_fetch_assoc($result);
$price = $row['total_rent'];
$pid = $row['property_id'];

// Check if the form has been submitted
if (isset($_POST['payment'])) {
    // Retrieve form data
    $payment_method = $_POST['payment_method'];
    $card_name = $_POST['card_name'];
    $card_number = str_replace(' ', '', $_POST['card_number']); 
    $expiry_date = $_POST['expiry_date'];
    

    // Insert the payment record into the database
    $insert_query = "INSERT INTO payment (tenant_id, card_type, card_name, card_number, exp_date, total_rent)
        VALUES ('$tid', '$payment_method', '$card_name', '$card_number', '$expiry_date', '$price')
    ";

    // Execute the query and check for success
    if (mysqli_query($con, $insert_query)) {
        $result = mysqli_query($con, "update property set status = 'rented' where property_id='$pid'");
        header('location: success.php');
    } else {
        header('location: unsuccess.php');

    }
}
?>

<div class="container">
   <form action="" method="post" enctype="multipart/form-data" autocomplete="off"> 
        <div class="row">
            <div class="col">
                <h3 class="title">Payment</h3>
                <div class="inputBox">
                    <span>Cards accepted: 
                        <div>
                            <i class="fa-brands fa-cc-visa"></i> 
                            <i class="fa-brands fa-cc-mastercard"></i> 
                            <i class="fa-brands fa-cc-amazon-pay"></i> 
                            <i class="fa-brands fa-cc-paypal"></i> 
                            <i class="fa-brands fa-cc-diners-club"></i> 
                            <i class="fa-brands fa-cc-jcb"></i>
                        </div>
                    </span>
                    <select name="payment_method" required>
                        <option value="credit_card">Credit Card</option>
                        <option value="debit_card">Debit Card</option>
                        <option value="net_banking">Net Banking</option>
                    </select>
                </div>
                <div class="inputBox">
                    <span>Name on card:</span>
                    <input type="text" name="card_name" maxlength="50" placeholder="John Deo" required>
                </div>
                <div class="inputBox">
                    <span>Credit card number:</span>
                    <input type="text" id="card_number" name="card_number" maxlength="19" placeholder="1111 2222 3333 4444" required>
                </div>

                <div class="flex">
                    <div class="inputBox">
                        <span>Exp date:</span>
                        <input type="text" name="expiry_date" required maxlength="5" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])\/?([0-9]{2})">
                    </div>
                    <div class="inputBox">
                        <span>CVV:</span>
                        <input type="password" name="cvv" maxlength="3" placeholder="***" required>
                    </div>
                </div>

                <div class="total">
                    <h3 class="title">Total AMOUNT</h3>
                    <h3 class="title">₹<?php echo $price; ?></h3>
                </div>
            </div>
        </div>
        <input type="submit" name="payment" value="Proceed to Checkout" class="btn">
    </form>
</div>  

<?php include "footer.php"; ?>

<!-- <script>
document.querySelector('input[name="expiry_date"]').addEventListener('input', function(e) {
    let value = e.target.value;
    if (value.length === 2 && !value.includes('/')) {
        e.target.value = value + '/';
    }
    if (value.length === 3 && value.includes('/')) {
        e.target.value = value.slice(0, 2);
    }
});

document.querySelector('#card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove all non-digit characters
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value; // Group in blocks of 4 digits
    e.target.value = formattedValue;
}); 
</script> -->