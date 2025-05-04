<?php
/**
 * Payment Functions for Santrify
 * Contains functions for handling payments and order processing
 */

// Function to generate a unique order number
function generateOrderNumber() {
    return 'SNT-' . date('Ymd') . '-' . rand(1000, 9999);
}

// Get package details
function getPackageDetails($packageId) {
    $packages = [
        'tahsin' => [
            'name' => 'Tahsin Qur\'an',
            'price' => 200000,
            'description' => 'Program belajar membaca Al-Qur\'an dengan koreksi tajwid oleh guru bersertifikat'
        ],
        'tahfidz' => [
            'name' => 'Tahfidz Intensive',
            'price' => 350000,
            'description' => 'Program hafalan Al-Qur\'an dengan setoran harian dan bimbingan musyrif'
        ]
    ];
    
    return isset($packages[$packageId]) ? $packages[$packageId] : null;
}

// Generate WhatsApp link with pre-populated message
function generateWhatsAppLink($orderNumber, $packageName, $paymentMethod) {
    $whatsappNumber = '+6281231651832'; // Replace with your actual number
    $message = "Assalamu'alaikum, saya telah mendaftar program *{$packageName}* dengan nomor order: *{$orderNumber}*. Metode pembayaran: *{$paymentMethod}*. Mohon informasi untuk langkah selanjutnya.";
    
    return "https://wa.me/{$whatsappNumber}?text=" . urlencode($message);
}

// Function to save order to database (to be implemented)
function saveOrder($orderNumber, $packageId, $userId, $paymentMethod, $amount) {
    global $conn;
    
    // In a real implementation, this would save the order to a database
    // For example:
    /*
    try {
        $stmt = $conn->prepare("INSERT INTO orders (order_number, package_id, user_id, payment_method, amount, status, created_at) 
                               VALUES (:order_number, :package_id, :user_id, :payment_method, :amount, 'pending', NOW())");
        $stmt->bindParam(':order_number', $orderNumber);
        $stmt->bindParam(':package_id', $packageId);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->bindParam(':amount', $amount);
        return $stmt->execute();
    } catch(PDOException $e) {
        return false;
    }
    */
    
    // For now, just return true
    return true;
}
?>
