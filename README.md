🧾 UPI QR-Based Payment Gateway with Paytm Transaction ID Verification (PHP + MySQL)

🎉 This UPI QR-based payment verification system is completely free to use – no hidden charges at all! 💸✅

✅ Overview
This is a custom UPI payment verification system built with PHP and MySQL that verifies a user's UPI payment using the Paytm Merchant Transaction Status API. The user scans a UPI QR to make the payment via any UPI app, and then enters the Transaction ID. The backend system then verifies this ID against Paytm's servers before confirming or rejecting the payment.

⚙️ System Workflow
1. User Input
The user fills out a form with the following details:

Name

Phone Number

Amount Paid

UPI Transaction ID (obtained from their UPI app after payment)

2. Transaction ID Duplication Check
The backend first checks if the submitted Transaction ID (txn_id) already exists in the database:

If yes: it rejects the payment to prevent fraud or reuse.

If no: it proceeds to verify the payment.

3. Verify with Paytm Transaction Status API
The script prepares a JSON payload containing:

MID (Merchant ID)

ORDERID (set to the Transaction ID entered by the user)

It sends this payload to the Paytm order/status API endpoint using a cURL request.

4. API Response Handling
The Paytm API responds with a JSON object containing payment status. The script checks:

STATUS == "TXN_SUCCESS"

TXNAMOUNT matches the amount submitted by the user

If both conditions are true:

The payment is marked as "Verified"

The details are stored in the payments table in MySQL

If either condition fails:

The payment is marked as "Failed"

The txn_id is stored with a "Failed-" prefix to differentiate it

5. User Notification
The user sees a message on screen:

✅ Green: Payment Verified Successfully

❌ Red: Payment Verification Failed / Transaction ID already used

🗃️ Database Table: payments
The table is assumed to have the following columns:

name (VARCHAR)

phone (VARCHAR)

amount (FLOAT/DECIMAL)

txn_id (VARCHAR, UNIQUE)

status (VARCHAR – "Verified" or "Failed")

🔒 Security Features
Prepared statements to prevent SQL injection

Duplicate transaction check to prevent reuse of the same ID

Server-side validation using Paytm's API for real-time status

🚀 Benefits
Lightweight and simple to integrate

Works with any UPI app (Google Pay, PhonePe, Paytm, etc.)

No need to manage UPI gateway sessions

Backend-controlled validation using official Paytm API

📌 Requirements
Paytm Merchant Account with access to API credentials

PHP 7.x or later with cURL enabled

MySQL Database (e.g., XAMPP, LAMP, etc.)
