<?php
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$dbname = "payment_system";

// Connect to MySQL
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $amount = $_POST['amount'];
    $txnId = $_POST['txn_id'];
    
    // Check if transaction ID already exists
    $checkQuery = "SELECT * FROM payments WHERE txn_id = ?"; 
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("s", $txnId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<h2 style='color: red;'>This Transaction ID is already used! Payment Failed.</h2>";
    } else {
        // Paytm Merchant ID
        $merchantId = "ZKSOCp56674546598917";

        // Paytm API Endpoint
        $url = "https://securegw.paytm.in/order/status";
        $payload = [
            "MID" => $merchantId,
            "ORDERID" => $txnId
        ];

        // Convert payload to JSON
        $jsonPayload = json_encode($payload);

        // cURL Request to Paytm API
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Content-Length: " . strlen($jsonPayload)
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        // Decode Response
        $result = json_decode($response, true);

        if ($result["STATUS"] == "TXN_SUCCESS" && $result["TXNAMOUNT"] == $amount) {
            // Insert into database for successful payment
            $insertQuery = "INSERT INTO payments (name, phone, amount, txn_id, status) VALUES (?, ?, ?, ?, 'Verified')";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssds", $name, $phone, $amount, $txnId);
            $stmt->execute();

            echo "<h2 style='color: green;'>Payment Verified Successfully!</h2>";
        } else {
            // Modify transaction ID to include 'Failed-' suffix
            $failedTxnId = "Failed-" . $txnId;

            // Insert failed transaction
            $insertQuery = "INSERT INTO payments (name, phone, amount, txn_id, status) VALUES (?, ?, ?, ?, 'Failed')";
            $stmt = $conn->prepare($insertQuery);
            $stmt->bind_param("ssds", $name, $phone, $amount, $failedTxnId);
            $stmt->execute();

            echo "<h2 style='color: red;'>Payment Verification Failed!</h2>";
        }
    }
} else {
    echo "Invalid Request!";
}

$conn->close();
?>
