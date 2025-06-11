<?php
// Set content type for JSON response
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required_fields = ['name', 'email', 'subject', 'message'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        $errors[] = ucfirst($field) . ' is required';
    }
}

// Validate email format
if (!empty($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

// If there are validation errors, return them
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Sanitize input data
$name = htmlspecialchars(trim($input['name']));
$email = filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL);
$phone = isset($input['phone']) ? htmlspecialchars(trim($input['phone'])) : '';
$subject = htmlspecialchars(trim($input['subject']));
$message = htmlspecialchars(trim($input['message']));

// Create email content
$to = 'mmpublicschool@gmail.com';
$email_subject = 'Contact Form: ' . $subject;
$email_body = "
New contact form submission from Mary Matha Public School website

Name: $name
Email: $email
Phone: $phone
Subject: $subject

Message:
$message

---
Sent from: {$_SERVER['HTTP_HOST']}
IP Address: {$_SERVER['REMOTE_ADDR']}
Date: " . date('Y-m-d H:i:s') . "
";

$headers = "From: $email\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Try to send email
$mail_sent = mail($to, $email_subject, $email_body, $headers);

// Log the submission to a file (optional)
$log_entry = date('Y-m-d H:i:s') . " - Name: $name, Email: $email, Subject: $subject\n";
file_put_contents('contact_submissions.log', $log_entry, FILE_APPEND | LOCK_EX);

// Send response
if ($mail_sent) {
    echo json_encode([
        'success' => true,
        'message' => 'Thank you for your message! We will get back to you soon.'
    ]);
} else {
    // Email failed, but we still logged it
    echo json_encode([
        'success' => true,
        'message' => 'Your message has been received and logged. We will get back to you soon.'
    ]);
}
?>
