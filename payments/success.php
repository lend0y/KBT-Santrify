<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once 'payment-functions.php';

// Get order details from URL parameters
$orderNumber = isset($_GET['order']) ? sanitize($_GET['order']) : '';
$paymentMethod = isset($_GET['method']) ? sanitize($_GET['method']) : '';

// Redirect if no order number
if (empty($orderNumber)) {
    redirect('../services.php');
}

$pageTitle = 'Pembayaran Berhasil';
require_once '../includes/header.php';
?>

<section class="payment-success-section">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Pembayaran Berhasil!</h1>
            <p>Terima kasih atas pendaftaran Anda di program Santrify.</p>
            
            <div class="order-details">
                <div class="detail-row">
                    <span class="label">Nomor Pesanan:</span>
                    <span class="value"><?php echo $orderNumber; ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Metode Pembayaran:</span>
                    <span class="value"><?php echo $paymentMethod; ?></span>
                </div>
            </div>
            
            <p class="next-steps">Tim kami akan segera menghubungi Anda untuk langkah selanjutnya. Silahkan cek WhatsApp Anda secara berkala.</p>
            
            <div class="action-buttons">
                <a href="../index.php" class="btn btn-primary">Kembali ke Beranda</a>
                <a href="#" class="btn btn-outline whatsapp-btn" onclick="window.open('<?php echo generateWhatsAppLink($orderNumber, 'Program Santrify', $paymentMethod); ?>', '_blank');">
                    <i class="fab fa-whatsapp"></i> Hubungi Kami via WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>