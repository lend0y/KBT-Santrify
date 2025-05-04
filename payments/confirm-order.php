<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once 'payment-functions.php';

// Get package information
$packageId = isset($_GET['package']) ? sanitize($_GET['package']) : '';
$package = getPackageDetails($packageId);

// Check if package exists
if (!$package) {
    redirect('../pages/services.php');
}

$orderNumber = generateOrderNumber();

// Process payment submission
$payment_success = false;
$paymentMethod = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $paymentMethod = isset($_POST['payment_method']) ? sanitize($_POST['payment_method']) : '';
    
    // In a real system, you'd process the payment here
    // For this example, we'll just simulate a successful payment
    $payment_success = true;
    
    // Save order to database
    if (isLoggedIn()) {
        saveOrder($orderNumber, $packageId, $_SESSION['user_id'], $paymentMethod, $package['price']);
    }
    
    // If payment successful, redirect to WhatsApp
    if ($payment_success) {
        $whatsappLink = generateWhatsAppLink($orderNumber, $package['name'], $paymentMethod);
        
        // Redirect to WhatsApp
        header("Location: $whatsappLink");
        exit;
    }
}

$pageTitle = 'Konfirmasi Pesanan';
require_once '../includes/header.php';
?>

<section class="confirm-order-section">
    <div class="container">
        <h1 class="text-center"><i class="fas fa-check-circle"></i> Konfirmasi Pesanan</h1>
        
        <div class="order-summary">
            <h2>Ringkasan Pesanan</h2>
            <div class="order-detail">
                <div class="detail-row">
                    <span class="label">Nomor Pesanan:</span>
                    <span class="value"><?php echo $orderNumber; ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Program:</span>
                    <span class="value"><?php echo $package['name']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Deskripsi:</span>
                    <span class="value"><?php echo $package['description']; ?></span>
                </div>
                <div class="detail-row total">
                    <span class="label">Total Pembayaran:</span>
                    <span class="value">Rp <?php echo number_format($package['price'], 0, ',', '.'); ?></span>
                </div>
            </div>
        </div>
        
        <div class="payment-section">
            <h2>Pilih Metode Pembayaran</h2>
            
            <form action="" method="POST">
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" id="bank_transfer" name="payment_method" value="Bank Transfer" checked>
                        <label for="bank_transfer">
                            <i class="fas fa-university"></i>
                            <span>Transfer Bank</span>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="gopay" name="payment_method" value="GoPay">
                        <label for="gopay">
                            <i class="fas fa-wallet"></i>
                            <span>GoPay</span>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="ovo" name="payment_method" value="OVO">
                        <label for="ovo">
                            <i class="fas fa-wallet"></i>
                            <span>OVO</span>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="dana" name="payment_method" value="DANA">
                        <label for="dana">
                            <i class="fas fa-wallet"></i>
                            <span>DANA</span>
                        </label>
                    </div>
                    
                    <div class="payment-option">
                        <input type="radio" id="cash" name="payment_method" value="Tunai">
                        <label for="cash">
                            <i class="fas fa-money-bill-wave"></i>
                            <span>Tunai</span>
                        </label>
                    </div>
                </div>
                
                <div class="bank-details" id="bank_details">
                    <h3>Informasi Rekening</h3>
                    <div class="bank-info">
                        <p><strong>Bank BCA</strong></p>
                        <p>No. Rekening: 3310723699</p>
                        <p>Atas Nama: Ahmad Qayyim</p>
                    </div>
                    <div class="bank-info">
                        <p><strong>Bank BCA</strong></p>
                        <p>No. Rekening: 6225004012</p>
                        <p>Atas Nama: Tengku Rabbani</p>
                    </div>
                </div>
                
                <div class="e-wallet-details" id="ewallet_details" style="display: none;">
                    <h3>Informasi E-Wallet</h3>
                    <div class="ewallet-info">
                        <p>Nomor GoPay/OVO/DANA: +62 822-1414-5814</p>
                        <p>Atas Nama: Santrify</p>
                    </div>
                </div>
                
                <div class="cash-details" id="cash_details" style="display: none;">
                    <h3>Informasi Pembayaran Tunai</h3>
                    <p>Silahkan datang ke kantor Santrify di:</p>
                    <p>Jl. Tegalgondo, Kabupaten Malang</p>
                    <p>Jam operasional: Senin-Jumat (08.00-16.00)</p>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
                    <a href="../pages/services.php" class="btn btn-outline">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</section>

<style>
/* Inline CSS untuk memastikan tampilan benar */
.confirm-order-section {
    padding: 60px 0;
    background-color: #f5f5f5;
}

.confirm-order-section h1 {
    margin-bottom: 40px;
    color: #004445;
    font-size: 1.8rem;
    font-weight: 700;
}

.confirm-order-section h1 i {
    margin-right: 10px;
    color: #28a745;
}

.order-summary {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    padding: 30px;
    margin-bottom: 30px;
}

.order-summary h2 {
    color: #004445;
    margin-bottom: 20px;
    font-size: 1.3rem;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.order-detail {
    padding: 0 10px;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px dashed #eee;
}

.detail-row .label {
    font-weight: 600;
    color: #333;
}

.detail-row.total {
    margin-top: 10px;
    padding: 15px 0;
    border-top: 2px solid #eee;
    border-bottom: none;
    font-weight: 700;
    font-size: 1.1rem;
    color: #004445;
}

.payment-section {
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    padding: 30px;
}

.payment-section h2 {
    color: #004445;
    margin-bottom: 20px;
    font-size: 1.3rem;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.payment-option {
    position: relative;
}

.payment-option input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.payment-option label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
    background-color: #f9f9f9;
    border: 2px solid #eee;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option label i {
    font-size: 2rem;
    margin-bottom: 10px;
    color: #2c786c;
}

.payment-option input[type="radio"]:checked + label {
    border-color: #2c786c;
    background-color: rgba(44, 120, 108, 0.05);
}

.bank-details, .e-wallet-details, .cash-details {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
}

.bank-details h3, .e-wallet-details h3, .cash-details h3 {
    color: #004445;
    margin-bottom: 15px;
    font-size: 1.1rem;
}

.bank-info {
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.bank-info:last-child {
    margin-bottom: 0;
}

.bank-info p {
    margin-bottom: 5px;
}

.bank-info p:last-child {
    margin-bottom: 0;
}

.ewallet-info, .cash-details p {
    padding: 15px;
    background-color: white;
    border-radius: 8px;
    margin-bottom: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.form-actions .btn {
    flex: 1;
}

.btn-primary {
    background-color: #2c786c;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 5px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #004445;
}

.btn-outline {
    background-color: transparent;
    color: #2c786c;
    border: 2px solid #2c786c;
    padding: 12px 20px;
    border-radius: 5px;
    font-weight: 600;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-outline:hover {
    background-color: #2c786c;
    color: white;
}

.text-center {
    text-align: center;
}

@media (max-width: 768px) {
    .detail-row {
        flex-direction: column;
    }
    
    .detail-row .label {
        margin-bottom: 5px;
    }
    
    .payment-options {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('input[name="payment_method"]');
    const bankDetails = document.getElementById('bank_details');
    const ewalletDetails = document.getElementById('ewallet_details');
    const cashDetails = document.getElementById('cash_details');
    
    function updatePaymentDetails() {
        const selectedPayment = document.querySelector('input[name="payment_method"]:checked').value;
        
        // Hide all details first
        bankDetails.style.display = 'none';
        ewalletDetails.style.display = 'none';
        cashDetails.style.display = 'none';
        
        // Show relevant details
        if (selectedPayment === 'Bank Transfer') {
            bankDetails.style.display = 'block';
        } else if (selectedPayment === 'GoPay' || selectedPayment === 'OVO' || selectedPayment === 'DANA') {
            ewalletDetails.style.display = 'block';
        } else if (selectedPayment === 'Tunai') {
            cashDetails.style.display = 'block';
        }
    }
    
    // Add event listeners to payment options
    paymentOptions.forEach(option => {
        option.addEventListener('change', updatePaymentDetails);
    });
    
    // Initialize display
    updatePaymentDetails();
});
</script>

<?php require_once '../includes/footer.php'; ?>