<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$errorMessage = "";
$successMessage = "";

$conn = mysqli_connect('localhost', 'root', '', 'bank_db');
if (!$conn) {
    die('Connection Error: ' . mysqli_connect_error());
}

if (isset($_POST['submit'])) {
    $firstName  = trim(mysqli_real_escape_string($conn, $_POST['firstName']));
    $middleName = trim(mysqli_real_escape_string($conn, $_POST['middleName']));
    $lastName   = trim(mysqli_real_escape_string($conn, $_POST['lastName']));
    $email      = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $phone      = trim(mysqli_real_escape_string($conn, $_POST['phone']));
    $birthdate  = trim(mysqli_real_escape_string($conn, $_POST['birthdate']));
    $address    = trim(mysqli_real_escape_string($conn, $_POST['address']));
    $password   = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];
    if ($password !== $confirmPassword) {
        $errorMessage = "Passwords do not match.";
    }

    $destination = ""; 
    if (isset($_FILES['img']) && $_FILES['img']['error'] === 0) {
        $fileTmp  = $_FILES['img']['tmp_name'];
        $fileName = $_FILES['img']['name'];
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed  = ['jpg','jpeg','png','gif',];

        if (!in_array($fileExt, $allowed)) {
            $errorMessage = "Only JPG, JPEG, PNG, GIF files allowed.";
        } elseif ($_FILES['img']['size'] > 5 * 1024 * 1024) {
            $errorMessage = "Image size must be under 5MB.";
        } else {
            if (!is_dir('profile')) {
                mkdir('profile', 0777, true);
            }
            $newFileName = uniqid('img_') . "." . $fileExt;
            $destination = "profile/" . $newFileName;

            if (!move_uploaded_file($fileTmp, $destination)) {
                $errorMessage = "Failed to save uploaded image.";
            }
        }
    } else {
        $errorMessage = "Profile picture is required.";
    }

    $check = mysqli_query($conn, "SELECT * FROM user_accounts WHERE Email='$email' OR Phone='$phone'");
    if (mysqli_num_rows($check) > 0) {
        $row = mysqli_fetch_assoc($check);
        if ($row['Email'] === $email) {
         
            $errorMessage = "Email already exists.";
        } elseif ($row['Phone'] === $phone) {
            $errorMessage = "Phone number already exists.";
            
        }
    }
    if (empty($errorMessage)) {
        $sql = "INSERT INTO user_accounts 
            (FirstName, MiddleName, LastName, Email, Phone, Address, birthdate, Password, Img) 
            VALUES ('$firstName', '$middleName', '$lastName', '$email', '$phone', '$address', '$birthdate', '$password', '$destination')";
        if (mysqli_query($conn, $sql)) {
            $successMessage = "Registration successful! Welcome, $firstName $lastName.";
        } else {
            $errorMessage = "Error: " . mysqli_error($conn);
        }
    }
}
?>