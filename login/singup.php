<?php
require_once('../koneksi.php'); // Sambungkan ke database

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Enkripsi password dengan Bcrypt

    // Periksa apakah email sudah digunakan
    $check_email_query = "SELECT * FROM users WHERE email = '$email'";
    $check_email_result = mysqli_query($con, $check_email_query);
    
    if (mysqli_num_rows($check_email_result) > 0) {
        // Email sudah digunakan
        echo "Email sudah digunakan!";
        return;
    }

    // Simpan data ke dalam database
    $register_query = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";
    
    if (mysqli_query($conn, $register_query)) {
        echo "Pendaftaran berhasil!";
    } else {
        echo "Error: " . $register_query . "<br>" . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>