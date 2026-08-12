<?php
session_start();
if (!isset($_SESSION['customer_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
require_once __DIR__ . '/../config/database.php';

$customer_id = $_SESSION['customer_id'];

if (!isset($_GET['booking_id'])) {
    header("Location: bookings.php");
    exit();
}

$booking_id = intval($_GET['booking_id']);

// Get booking details
$booking_query = "SELECT b.*, v.vehicle_name, v.brand, v.model, c.full_name AS customer_name, 
                  c.phone_1 AS customer_phone, c.email AS customer_email
                  FROM booking b 
                  LEFT JOIN vehicle v ON b.vehicle_id = v.vehicle_id 
                  LEFT JOIN customer c ON b.customer_id = c.customer_id 
                  WHERE b.booking_id = '$booking_id' AND b.customer_id = '$customer_id'";
$booking_result = mysqli_query($conn, $booking_query);

if (!$booking_result) {
    echo "Database error: " . mysqli_error($conn);
    exit();
}

$booking = mysqli_fetch_assoc($booking_result);

if (!$booking) {
    echo "Booking not found!";
    exit();
}

// Handle payment processing
if(isset($_POST['process_payment'])) {
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    $transaction_id = mysqli_real_escape_string($conn, $_POST['transaction_id']);
    $payment_type = ($payment_method == 'Cash' || $payment_method == 'Manual') ? 'Manual' : 'Digital';
    
    // Update booking with payment details
    $update_query = "UPDATE booking SET 
                    payment_status = 'Paid',
                    payment_method = '$payment_method',
                    payment_type = '$payment_type',
                    transaction_id = '$transaction_id'
                    WHERE booking_id = '$booking_id'";
    
    if(mysqli_query($conn, $update_query)) {
        header("Location: payment.php?booking_id=$booking_id&success=1");
        exit();
    } else {
        $error = "Payment processing failed. Please try again.";
    }
}

$active_page = 'Payment';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - RideRent Pro</title>
    <link rel="stylesheet" href="../assets/css/new-theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            @page {
                size: 80mm auto;
                margin: 0;
            }
            body * {
                visibility: hidden;
            }
            .payment-slip-container, .payment-slip-container * {
                visibility: visible;
            }
            .payment-slip-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 80mm;
                margin: 0 auto;
                padding: 5px;
                background: white;
                box-shadow: none;
            }
            .no-print { display: none !important; }
            .dashboard-container, .main-content, .sidebar, .page-header, .card-header {
                display: none !important;
            }
            .payment-slip {
                border: 2px solid #000;
                padding: 8px;
                margin: 0;
                background: #fff;
                font-family: 'Courier New', monospace;
                font-size: 10px;
            }
            .logo-section {
                text-align: center;
                margin-bottom: 8px;
            }
            .receipt-icon {
                font-size: 24px;
            }
            .payment-slip-header h2 {
                font-size: 14px;
                margin: 5px 0;
                font-weight: bold;
                text-transform: uppercase;
            }
            .tagline {
                font-size: 8px;
                margin: 2px 0;
                font-style: italic;
            }
            .divider {
                border-top: 1px dashed #000;
                margin: 8px 0;
            }
            .receipt-title {
                font-size: 10px;
                font-weight: bold;
                margin: 5px 0;
                text-transform: uppercase;
            }
            .booking-id {
                font-size: 8px;
                font-weight: bold;
            }
            .section-title {
                font-size: 9px;
                font-weight: bold;
                margin: 8px 0 5px 0;
                text-transform: uppercase;
                background: #f0f0f0;
                padding: 2px 5px;
            }
            .payment-slip-details {
                margin: 8px 0;
            }
            .payment-slip-details .row {
                display: flex;
                justify-content: space-between;
                margin: 3px 0;
                font-size: 9px;
            }
            .status-row {
                background: #f9f9f9;
                padding: 3px;
            }
            .status-badge {
                font-weight: bold;
                font-size: 8px;
            }
            .status-paid {
                color: #000;
                background: #e8f5e9;
                padding: 2px 5px;
            }
            .status-pending {
                color: #000;
                background: #fff3e0;
                padding: 2px 5px;
            }
            .payment-slip-total {
                border-top: 2px solid #000;
                border-bottom: 2px solid #000;
                padding: 8px 0;
                margin: 10px 0;
                font-size: 12px;
                font-weight: bold;
            }
            .total-label {
                font-size: 10px;
            }
            .total-amount {
                font-size: 14px;
            }
            .payment-slip-footer {
                margin-top: 10px;
                padding-top: 8px;
                text-align: center;
                font-size: 8px;
            }
            .thank-you {
                font-weight: bold;
                margin: 5px 0;
                font-size: 9px;
            }
            .contact-info {
                margin: 2px 0;
                font-size: 8px;
            }
            .web-info {
                margin: 3px 0;
                font-size: 8px;
            }
            .generated {
                margin: 5px 0;
                font-size: 7px;
                color: #666;
            }
            .terms {
                margin: 3px 0;
                font-size: 7px;
                font-style: italic;
            }
        }
        .payment-slip-container {
            max-width: 420px;
            margin: 0 auto;
        }
        .payment-slip {
            border: 3px double #333;
            padding: 25px;
            margin: 20px 0;
            background: #fff;
            font-family: 'Courier New', monospace;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .logo-section {
            text-align: center;
            margin-bottom: 15px;
        }
        .receipt-icon {
            font-size: 36px;
            display: block;
            margin-bottom: 5px;
        }
        .payment-slip-header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .payment-slip-header h2 {
            font-size: 22px;
            margin: 8px 0;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .tagline {
            font-size: 12px;
            margin: 5px 0;
            font-style: italic;
            color: #666;
        }
        .divider {
            border-top: 2px dashed #333;
            margin: 15px 0;
        }
        .receipt-title {
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .booking-id {
            font-size: 11px;
            font-weight: bold;
            color: #333;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin: 15px 0 8px 0;
            text-transform: uppercase;
            background: #f0f0f0;
            padding: 5px 10px;
            border-left: 4px solid #333;
        }
        .payment-slip-details {
            margin: 15px 0;
        }
        .payment-slip-details .row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 13px;
        }
        .status-row {
            background: #f9f9f9;
            padding: 8px;
            border-radius: 4px;
        }
        .status-badge {
            font-weight: bold;
            font-size: 11px;
            padding: 4px 8px;
            border-radius: 3px;
        }
        .status-paid {
            color: #2e7d32;
            background: #e8f5e9;
            border: 1px solid #2e7d32;
        }
        .status-pending {
            color: #e65100;
            background: #fff3e0;
            border: 1px solid #e65100;
        }
        .payment-slip-total {
            border-top: 3px double #333;
            border-bottom: 3px double #333;
            padding: 15px 0;
            margin: 20px 0;
            font-size: 20px;
            font-weight: bold;
            background: #f8f8f8;
        }
        .total-label {
            font-size: 14px;
            text-transform: uppercase;
        }
        .total-amount {
            font-size: 24px;
            color: #d32f2f;
        }
        .payment-slip-footer {
            margin-top: 20px;
            padding-top: 15px;
            text-align: center;
            font-size: 11px;
        }
        .thank-you {
            font-weight: bold;
            margin: 10px 0;
            font-size: 14px;
            color: #333;
        }
        .contact-info {
            margin: 5px 0;
            font-size: 11px;
            color: #666;
        }
        .web-info {
            margin: 8px 0;
            font-size: 11px;
            color: #666;
        }
        .generated {
            margin: 10px 0;
            font-size: 9px;
            color: #999;
        }
        .terms {
            margin: 5px 0;
            font-size: 9px;
            font-style: italic;
            color: #888;
        }
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .payment-method-card {
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }
        .payment-method-card:hover {
            border-color: #007bff;
            background: #f8f9fa;
        }
        .payment-method-card.selected {
            border-color: #007bff;
            background: #e7f3ff;
        }
        .payment-method-card i {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<script src="../assets/js/theme.js"></script>

<!-- Dashboard Container -->
<div class="dashboard-container">
    <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1><i class="fas fa-credit-card"></i> Payment</h1>
            <p>Complete your booking payment</p>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Payment processed successfully!
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Details</h3>
            </div>
            <div class="card-body">
                <div class="payment-slip-container">
                    <div class="payment-slip">
                        <div class="payment-slip-header">
                            <div class="logo-section">
                                <span class="receipt-icon">🚗</span>
                                <h2>RideRent Pro</h2>
                            </div>
                            <p class="tagline">Premium Vehicle Rental Service</p>
                            <div class="divider"></div>
                            <p class="receipt-title">PAYMENT RECEIPT</p>
                            <small class="booking-id">Receipt #: <?php echo str_pad($booking['booking_id'], 8, '0', STR_PAD_LEFT); ?></small>
                        </div>
                        
                        <div class="payment-slip-details">
                            <div class="section-title">CUSTOMER INFORMATION</div>
                            <div class="row">
                                <span>Name:</span>
                                <span><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                            </div>
                            <div class="row">
                                <span>Contact:</span>
                                <span><?php echo htmlspecialchars($booking['customer_phone']); ?></span>
                            </div>
                            <div class="row">
                                <span>Email:</span>
                                <span><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <div class="section-title">BOOKING DETAILS</div>
                            <div class="row">
                                <span>Vehicle:</span>
                                <span><?php echo htmlspecialchars($booking['vehicle_name']); ?></span>
                            </div>
                            <div class="row">
                                <span>Model:</span>
                                <span><?php echo htmlspecialchars($booking['brand']); ?> <?php echo htmlspecialchars($booking['model']); ?></span>
                            </div>
                            <div class="row">
                                <span>Pickup:</span>
                                <span><?php echo htmlspecialchars($booking['pickup_location']); ?></span>
                            </div>
                            <div class="row">
                                <span>Dropoff:</span>
                                <span><?php echo htmlspecialchars($booking['dropoff_location']); ?></span>
                            </div>
                            <div class="row">
                                <span>Period:</span>
                                <span><?php echo $booking['start_date']; ?> to <?php echo $booking['end_date']; ?></span>
                            </div>
                            <div class="row">
                                <span>Duration:</span>
                                <span><?php echo $booking['total_days']; ?> day(s)</span>
                            </div>
                            
                            <div class="divider"></div>
                            
                            <div class="section-title">PAYMENT BREAKDOWN</div>
                            <div class="row">
                                <span>Daily Rate:</span>
                                <span>৳<?php echo number_format($booking['price_per_day'], 2); ?> × <?php echo $booking['total_days']; ?></span>
                            </div>
                            <div class="row">
                                <span>Vehicle Cost:</span>
                                <span>৳<?php echo number_format($booking['price_per_day'] * $booking['total_days'], 2); ?></span>
                            </div>
                            <?php if($booking['driver_fee'] > 0): ?>
                            <div class="row">
                                <span>Driver Fee:</span>
                                <span>৳<?php echo number_format($booking['driver_fee'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="payment-slip-total">
                            <div class="row">
                                <span class="total-label">TOTAL AMOUNT</span>
                                <span class="total-amount">৳<?php echo number_format($booking['total_price'], 2); ?></span>
                            </div>
                        </div>
                        
                        <div class="payment-slip-details">
                            <div class="divider"></div>
                            <div class="section-title">PAYMENT STATUS</div>
                            <div class="row status-row">
                                <span>Status:</span>
                                <span class="status-badge <?php echo $booking['payment_status'] == 'Paid' ? 'status-paid' : 'status-pending'; ?>">
                                    <?php if($booking['payment_status'] == 'Paid'): ?>
                                        ✓ PAID IN FULL
                                    <?php else: ?>
                                        ⏳ PAYMENT PENDING
                                    <?php endif; ?>
                                </span>
                            </div>
                            <?php if($booking['payment_status'] == 'Paid'): ?>
                            <div class="row">
                                <span>Payment Method:</span>
                                <span><?php echo htmlspecialchars($booking['payment_method']); ?></span>
                            </div>
                            <div class="row">
                                <span>Transaction ID:</span>
                                <span><?php echo htmlspecialchars($booking['transaction_id']); ?></span>
                            </div>
                            <div class="row">
                                <span>Payment Date:</span>
                                <span><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="payment-slip-footer">
                            <div class="divider"></div>
                            <p class="thank-you">Thank you for choosing RideRent Pro!</p>
                            <p class="contact-info">📞 +880 1700-000000</p>
                            <p class="contact-info">✉️ support@riderentpro.com</p>
                            <p class="web-info">🌐 www.riderentpro.com</p>
                            <div class="divider"></div>
                            <p class="generated">Generated: <?php echo date('Y-m-d H:i:s'); ?></p>
                            <p class="terms">Terms & Conditions Apply</p>
                        </div>
                    </div>
                </div>

                <?php if($booking['payment_status'] != 'Paid'): ?>
                <div class="no-print">
                    <h4>Complete Payment</h4>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label class="form-label">Select Payment Method</label>
                            <div class="payment-methods">
                                <div class="payment-method-card" onclick="selectPaymentMethod(this, 'bKash')">
                                    <i class="fas fa-mobile-alt" style="color: #E2136E;"></i>
                                    <p><strong>bKash</strong></p>
                                </div>
                                <div class="payment-method-card" onclick="selectPaymentMethod(this, 'Nagad')">
                                    <i class="fas fa-mobile-alt" style="color: #F7931E;"></i>
                                    <p><strong>Nagad</strong></p>
                                </div>
                                <div class="payment-method-card" onclick="selectPaymentMethod(this, 'Card')">
                                    <i class="fas fa-credit-card" style="color: #1a1f71;"></i>
                                    <p><strong>Credit/Debit Card</strong></p>
                                </div>
                                <div class="payment-method-card" onclick="selectPaymentMethod(this, 'Cash')">
                                    <i class="fas fa-money-bill-wave" style="color: #28a745;"></i>
                                    <p><strong>Cash/Manual</strong></p>
                                </div>
                            </div>
                            <input type="hidden" name="payment_method" id="payment_method" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Transaction ID / Reference Number</label>
                            <input type="text" name="transaction_id" class="form-control" placeholder="Enter transaction ID" required>
                            <small class="text-muted">Enter the transaction ID from your payment confirmation</small>
                        </div>

                        <button type="submit" name="process_payment" class="btn btn-primary">
                            <i class="fas fa-check"></i> Process Payment
                        </button>
                    </form>
                </div>
                <?php endif; ?>

                <div class="no-print" style="margin-top: 20px;">
                    <?php if($booking['payment_status'] == 'Paid'): ?>
                        <button onclick="printReceipt()" class="btn btn-success">
                            <i class="fas fa-receipt"></i> Print Receipt
                        </button>
                        <a href="bookings.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    <?php else: ?>
                        <a href="bookings.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Bookings
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectPaymentMethod(element, method) {
    // Remove selected class from all cards
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selected class to clicked card
    element.classList.add('selected');
    
    // Set hidden input value
    document.getElementById('payment_method').value = method;
}

function printReceipt() {
    // Create a new window for printing
    const printWindow = window.open('', '_blank');
    const receiptContent = document.querySelector('.payment-slip-container').innerHTML;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt</title>
            <style>
                @page {
                    size: 80mm auto;
                    margin: 0;
                }
                body {
                    margin: 0;
                    padding: 5px;
                    font-family: 'Courier New', monospace;
                    font-size: 10px;
                    width: 80mm;
                }
                .payment-slip {
                    border: 2px solid #000;
                    padding: 8px;
                    background: #fff;
                }
                .logo-section {
                    text-align: center;
                    margin-bottom: 8px;
                }
                .receipt-icon {
                    font-size: 24px;
                }
                .payment-slip-header h2 {
                    font-size: 14px;
                    margin: 5px 0;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .tagline {
                    font-size: 8px;
                    margin: 2px 0;
                    font-style: italic;
                }
                .divider {
                    border-top: 1px dashed #000;
                    margin: 8px 0;
                }
                .receipt-title {
                    font-size: 10px;
                    font-weight: bold;
                    margin: 5px 0;
                    text-transform: uppercase;
                }
                .booking-id {
                    font-size: 8px;
                    font-weight: bold;
                }
                .section-title {
                    font-size: 9px;
                    font-weight: bold;
                    margin: 8px 0 5px 0;
                    text-transform: uppercase;
                    background: #f0f0f0;
                    padding: 2px 5px;
                }
                .payment-slip-details {
                    margin: 8px 0;
                }
                .payment-slip-details .row {
                    display: flex;
                    justify-content: space-between;
                    margin: 3px 0;
                    font-size: 9px;
                }
                .status-row {
                    background: #f9f9f9;
                    padding: 3px;
                }
                .status-badge {
                    font-weight: bold;
                    font-size: 8px;
                }
                .status-paid {
                    color: #000;
                    background: #e8f5e9;
                    padding: 2px 5px;
                }
                .status-pending {
                    color: #000;
                    background: #fff3e0;
                    padding: 2px 5px;
                }
                .payment-slip-total {
                    border-top: 2px solid #000;
                    border-bottom: 2px solid #000;
                    padding: 8px 0;
                    margin: 10px 0;
                    font-size: 12px;
                    font-weight: bold;
                }
                .total-label {
                    font-size: 10px;
                }
                .total-amount {
                    font-size: 14px;
                }
                .payment-slip-footer {
                    margin-top: 10px;
                    padding-top: 8px;
                    text-align: center;
                    font-size: 8px;
                }
                .thank-you {
                    font-weight: bold;
                    margin: 5px 0;
                    font-size: 9px;
                }
                .contact-info {
                    margin: 2px 0;
                    font-size: 8px;
                }
                .web-info {
                    margin: 3px 0;
                    font-size: 8px;
                }
                .generated {
                    margin: 5px 0;
                    font-size: 7px;
                    color: #666;
                }
                .terms {
                    margin: 3px 0;
                    font-size: 7px;
                    font-style: italic;
                }
            </style>
        </head>
        <body>
            ${receiptContent}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.focus();
    
    // Wait for the content to load before printing
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 250);
}
</script>

</body>
</html>