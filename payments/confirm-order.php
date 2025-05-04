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
        <h1><i class="fas fa-check-circle"></i> Konfirmasi Pesanan</h1>
        
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
                        <p>No. Rekening: 1234567890</p>
                        <p>Atas Nama: Yayasan Santrify</p>
                    </div>
                    <div class="bank-info">
                        <p><strong>Bank Mandiri</strong></p>
                        <p>No. Rekening: 0987654321</p>
                        <p>Atas Nama: Yayasan Santrify</p>
                    </div>
                </div>
                
                <div class="e-wallet-details" id="ewallet_details" style="display: none;">
                    <h3>Informasi E-Wallet</h3>
                    <div class="ewallet-info">
                        <p>Nomor GoPay/OVO/DANA: 081234567890</p>
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