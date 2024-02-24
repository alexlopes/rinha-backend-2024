<?php
// Connect to database
$db = new mysqli('localhost', 'username', 'password', 'debits');

// Check for connection errors
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Handle incoming requests based on method and endpoint
if ($_SERVER['REQUEST_METHOD'] === 'PUT' && $_SERVER['REQUEST_URI'] === '/debit') {
    handleDebitRequest();
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $_SERVER['REQUEST_URI'] === '/statement') {
    handleStatementRequest();
} else {
    header('HTTP/1.1 404 Not Found');
    echo 'Endpoint not found';
}

function handleDebitRequest() {
    global $db;

    // Get JSON payload
    $json = file_get_contents('php://input');

    // Decode JSON
    $data = json_decode($json, true);

    // Validate and sanitize data
    // ... (add validation and sanitization logic here)

    // Prepare query
    $stmt = $db->prepare("INSERT INTO DEBITS (money) VALUES (?)");
    $stmt->bind_param("i", $data['money']);

    // Execute query
    if ($stmt->execute()) {
        header('HTTP/1.1 200 OK');
        echo 'Debit created successfully';
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Error creating debit';
    }

    $stmt->close();
}

function handleStatementRequest() {
    global $db;

    // Prepare query
    $stmt = $db->prepare("SELECT money FROM DEBITS");
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch data and create JSON response
    $debits = [];
    while ($row = $result->fetch_assoc()) {
        $debits[] = $row;
    }

    header('Content-Type: application/json');
    echo json_encode($debits);

    $stmt->close();
}

$db->close();
?>
