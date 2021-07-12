<?php
$FirstName = '';
$LastName = ''; 
$PersonalNumber = '';
$Email = '';
$HashedPassword = '';
$StatusId = '';
$hasError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    (empty($_POST['FirstName'])) ? $FirstNameError = 'FirstName is required!':$FirstName = $_POST['FirstName'];

    (empty($_POST['LastName'])) ? $LastNameError = 'LastName is required!: $LastName = $_POST['LastName'];

    (empty($_POST['PersonalNumber'])) ? $PersonalNumberError = 'PersonalNumber is required!' : $PersonalNumber = $_POST['PersonalNumber'];

    (empty($_POST['Email'])) ? $EmailError = 'Email is required!' : $Email = $_POST['Email'];

    (empty($_POST['Password'])) ? $PasswordError = 'Password is required!' : $HashedPassword = password_hash($_POST['Password'], PASSWORD_DEFAULT);

    (empty($_POST['StatusId'])) ? $StatusIdError = 'Status is required!':$StatusId = $_POST['StatusId'];
}

$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'gtu';

$dsn = 'mysql:host=' . $host . ';dbname=' . $dbname;

try {
    $pdo = new PDO($dsn, $user, $password);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch (PDOException $e) {
    echo 'Error!: ' . $e->getMessage();
}


if (!(isset($FirstNameError, $LastNameError, $PersonalNumberError, $EmailError, $PasswordError, $StatusIdError))) {

    $sql = "SELECT * FROM users WHERE Email = :Email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['Email' => $Email]);
    $existingEmail = $stmt->fetch(PDO::FETCH_BOUND);

    if ($existingEmail) {
        $EmailError = 'This email address is already registered!';
        $hasError = true;
    }

    $sql = "SELECT * FROM users WHERE PersonalNumber = :PersonalNumber LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['PersonalNumber' => $PersonalNumber]);
    $existingPersonalNumber = $stmt->fetch(PDO::FETCH_BOUND);

    if ($existingPersonalNumber) {
        $PersonalNumberError = 'This Personal Number is already in use!';
        $hasError = true;
    }
}

if (!$hasError) {
    $sql = "INSERT INTO users (FirstName, LastName, PersonalNumber, Email, HashedPassword, StatusId) VALUES (:FirstName, :LastName, :PersonalNumber, :Email, :HashedPassword, :StatusId)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['FirstName' => $FirstName, 'LastName' => $LastName, 'PersonalNumber' => $PersonalNumber, 'Email' => $Email, 'HashedPassword' => $HashedPassword, 'StatusId' => $StatusId]);
    $submissionSuccess = true;  
}

include 'index.php';