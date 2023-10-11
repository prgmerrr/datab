<?php
require_once('../koneksi.php'); // Sambungkan ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

    // Periksa apakah email ada di database
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $check_email_result = mysqli_query($conn, $check_email_query);

    if (mysqli_num_rows($check_email_result) == 1) {
        // Email ditemukan, periksa password
        $row = mysqli_fetch_assoc($check_email_result);
        $hashed_password = $row['password'];

        if (password_verify($password, $hashed_password)) {
            // Password cocok, buat sesi login
            session_start();
            $_SESSION['user_id'] = $row['id'];
            echo "Login berhasil!";
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Email tidak ditemukan!";
    }
}

mysqli_close($conn);
?>