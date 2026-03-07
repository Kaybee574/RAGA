<?php
require_once 'DatabaseConnection.php';
require_once 'session_helper.php';

if (!isLoggedIn()) {
    header("Location: SignIn.html");
    exit();
}

$buyer_id = $_SESSION['student_number']; // Standardized to student_number for database consistency

$cart_total = 0;
$query = "
    SELECT SUM(p.price * ci.quantity) as total 
    FROM cart_items ci
    JOIN products p ON ci.product_id = p.id
    WHERE ci.buyer_student_number = '$buyer_id'
";
$result = mysqli_query($conn, $query);
if ($result && $row = mysqli_fetch_assoc($result)) {
    $cart_total = $row['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout · Raga</title>
    <link rel="stylesheet" href="mainCss.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
        }

        .page-header {
            text-align: center;
            color: #ffaa50;
            font-size: 2.5rem;
            margin: 30px 0;
        }

        .checkout-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: rgb(235, 235, 235);
            padding: 30px;
            border-radius: 12px;
            border: 1px solid black;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .order-summary {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #ddd;
        }

        .btn-submit {
            width: 100%;
            padding: 12px 20px;
            background-color: #ffaa50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            font-weight: bold;
            line-height: 1.2;
            display: inline-block;
        }

        .btn-submit:hover {
            background-color: #e68f3c;
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>
    <h1 class="page-header">Secure Checkout</h1>

    <div class="checkout-container">
        
        <div class="order-summary">
            <h3>Total to Pay: R<?php echo number_format($cart_total, 2); ?></h3>
        </div>

        <div id="error-message" style="color:red; margin-bottom:15px; display:none; padding:10px; border:1px solid red; border-radius:5px; background:#fff5f5;"></div>

        <form id="payment-form" action="process_checkout.php" method="POST">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input type="text" id="fullName" name="fullName" value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Shipping Address (Res name & Room number)</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" id="cardNumber" name="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19" required>
            </div>

            <div style="display:flex; gap:10px;">
                <div class="form-group" style="flex:1;">
                    <label for="expiry">Expiry Date</label>
                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div class="form-group" style="flex:1;">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                </div>
            </div>

            <button type="submit" class="btn-submit">Confirm Order & Pay</button>
        </form>
        <div style="text-align:center; margin-top:20px;">
            <a href="Cart.php" style="color:#666; text-decoration:none;">Cancel and return to Cart</a>
        </div>
    </div>

    <script>
        document.getElementById('payment-form').onsubmit = function(e) {
            const errorDiv = document.getElementById('error-message');
            errorDiv.style.display = 'none';
            let errors = [];

            const card = document.getElementById('cardNumber').value.replace(/\s+/g, '');
            const expiry = document.getElementById('expiry').value;
            const cvv = document.getElementById('cvv').value;

            // Simple 16-digit card validation
            if (!/^\d{16}$/.test(card)) {
                errors.push("Card number must be 16 digits.");
            }

            // Expiry MM/YY validation
            if (!/^\d{2}\/\d{2}$/.test(expiry)) {
                errors.push("Expiry must be in MM/YY format.");
            } else {
                const parts = expiry.split('/');
                const month = parseInt(parts[0], 10);
                const year = parseInt(parts[1], 10) + 2000;
                const now = new Date();
                const expiryDate = new Date(year, month - 1, 1);
                
                if (month < 1 || month > 12) {
                    errors.push("Invalid month in expiry date.");
                } else if (expiryDate < new Date(now.getFullYear(), now.getMonth(), 1)) {
                    errors.push("Card has expired.");
                }
            }

            // CVV validation
            if (!/^\d{3}$/.test(cvv)) {
                errors.push("CVV must be 3 digits.");
            }

            if (errors.length > 0) {
                e.preventDefault();
                errorDiv.innerText = errors.join("\n");
                errorDiv.style.display = 'block';
                window.scrollTo(0, 0);
                return false;
            }
        };

        // Auto-format card number with spaces
        document.getElementById('cardNumber').addEventListener('input', function (e) {
            let target = e.target;
            let position = target.selectionEnd;
            let length = target.value.length;
            
            target.value = target.value.replace(/[^\d]/g, '').replace(/(.{4})/g, '$1 ').trim();
            
            if (target.value.length !== length) {
                if (position % 5 === 0) position++;
                target.setSelectionRange(position, position);
            }
        });

        // Auto-format expiry with slash
        document.getElementById('expiry').addEventListener('input', function (e) {
            let target = e.target;
            if (target.value.length === 2 && !target.value.includes('/')) {
                target.value = target.value + '/';
            }
        });
    </script>

</body>
</html>
