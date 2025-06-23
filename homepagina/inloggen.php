<?php
session_start();

// Database verbinding
$host = 'localhost';
$dbname = 'project4';
$username = 'root'; // Pas aan naar jouw database settings
$password = '';     // Pas aan naar jouw database settings

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database verbinding mislukt: " . $e->getMessage());
}

// JSON response functie
function sendResponse($success, $message, $redirect = null, $role = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect,
        'role' => $role
    ]);
    exit;
}

// Controleer of het een POST request is
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse(false, 'Alleen POST requests toegestaan');
}

// Controleer of action parameter bestaat
if (!isset($_POST['action'])) {
    sendResponse(false, 'Geen actie opgegeven');
}

if ($_POST['action'] == 'login') {
    $team_name = trim($_POST['team_name']);
    $password = $_POST['password'] ?? '';
    
    if (empty($team_name)) {
        sendResponse(false, 'Voer gebruikersnaam in!');
    }
    
    if (empty($password)) {
        sendResponse(false, 'Voer wachtwoord in!');
    }
    
    // Zoek gebruiker
    $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
    $stmt->execute([$team_name]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        // Login succesvol
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        
        if ($user['role'] == 'admin') {
            sendResponse(true, 'Admin login succesvol!', 'admin/dashboard.php', 'admin');
        } else {
            sendResponse(true, 'Login succesvol!', '../kamer_1/room1.html', 'speler');
        }
    } else {
        sendResponse(false, 'Verkeerde gebruikersnaam of wachtwoord!');
    }
}

if ($_POST['action'] == 'register') {
    $team_name = trim($_POST['team_name']);
    $password = 'default123'; // Standaard wachtwoord voor registratie
    
    if (empty($team_name)) {
        sendResponse(false, 'Voer team naam in!');
    }
    
    // Check of gebruiker al bestaat
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$team_name]);
    
    if ($stmt->fetch()) {
        sendResponse(false, 'Team naam bestaat al!');
    }
    
    // Maak nieuwe gebruiker
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'speler')");
    
    if ($stmt->execute([$team_name, $hashed_password])) {
        sendResponse(true, 'Team geregistreerd! Gebruik wachtwoord: default123');
    } else {
        sendResponse(false, 'Registratie mislukt!');
    }
}

// Als geen geldige actie
sendResponse(false, 'Ongeldige actie');
?>