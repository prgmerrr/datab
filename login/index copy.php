<?php
session_start(); // Mulai sesi (harus dipanggil di awal file)

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    // Ambil data dari formulir pendaftaran
    $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
    $password = $_POST['password'];

    // Validasi data (tambahkan validasi sesuai kebutuhan Anda)
    $errors = array();
    
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6 || strlen($password) > 20) {
        $errors[] = "Password must be between 6 and 20 characters.";
    }

    if (empty($errors)) {
        // Periksa apakah email sudah ada di database sebelum menyimpan
        $servername = "localhost";
        $db_username = "root";
        $db_password = "";
        $db_name = "km_online";

        $conn = new mysqli($servername, $db_username, $db_password, $db_name);

        // Periksa koneksi
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Gunakan prepared statement untuk mencegah SQL Injection
        $stmt_check_email = $conn->prepare("SELECT * FROM user_id WHERE email = ?");
        $stmt_check_email->bind_param("s", $email);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            // Email sudah digunakan
            $errors[] = "Email is already registered.";
        } else {
            // Hash kata sandi sebelum menyimpannya ke dalam database
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Simpan data ke dalam database (gunakan prepared statement)
            $stmt_insert = $conn->prepare("INSERT INTO user_id (username, email, password) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt_insert->execute()) {
                // Pendaftaran berhasil
                $_SESSION['registration_success'] = true;
                header("Location: ../index.html"); // Ganti dengan halaman sukses pendaftaran Anda
                exit();
            } else {
                echo "Error: " . $stmt_insert->error;
            }

            $stmt_insert->close();
        }

        $stmt_check_email->close();
        $conn->close();
    } else {
        foreach ($errors as $error) {
            echo $error . "<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Login</title>
    <style>
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 10px;
        background-color: #4CAF50;
        /* Hijau untuk notifikasi sukses */
        color: #fff;
        border-radius: 5px;
        display: none;
    }

    .notification.error {
        background-color: #f44336;
        /* Merah untuk notifikasi error */
    }
    </style>
</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form>
                <h1>Create Account</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email for registeration</span>
                <input type="text" placeholder="Name">
                <input type="email" placeholder="Email">
                <input type="password" placeholder="Password">
                <button>Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form>
                <h1>Sign In</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a>
                </div>
                <span>or use your email password</span>
                <input type="email" placeholder="Email">
                <input type="password" placeholder="Password">
                <a href="#">Forget Your Password?</a>
                <button>Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all of site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all of site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>

</html>