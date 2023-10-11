<?php
$servername = "localhost";
$username = "imad8736_jarap";
$password = "691322js";
$db = "imad8736_webpas";

// Create connection
$con = mysqli_connect($servername, $username, $password, $db);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signup'])) {
        $username = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
        $email = htmlspecialchars($_POST['email'], ENT_QUOTES, 'UTF-8');
        $password = $_POST['password'];
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
            $stmt_check_email = mysqli_prepare($con, "SELECT * FROM user_id WHERE email = ?");
            mysqli_stmt_bind_param($stmt_check_email, "s", $email);
            mysqli_stmt_execute($stmt_check_email);
            $result_check_email = mysqli_stmt_get_result($stmt_check_email);

            if (mysqli_num_rows($result_check_email) > 0) {
                $errors[] = "Email is already registered.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $stmt_insert = mysqli_prepare($con, "INSERT INTO user_id (username, email, password) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_insert, "sss", $username, $email, $hashed_password);

                if (mysqli_stmt_execute($stmt_insert)) {
                    $_SESSION['registration_success'] = true;
                    header("Location: ../index.html");
                    exit();
                } else {
                    echo "Error: " . mysqli_stmt_error($stmt_insert);
                }

                mysqli_stmt_close($stmt_insert);
            }

            mysqli_stmt_close($stmt_check_email);
        } else {
            foreach ($errors as $error) {
                echo $error . "<br>";
            }
        }
    }

    if (isset($_POST['login'])) {
        $login_email = htmlspecialchars($_POST['login_email'], ENT_QUOTES, 'UTF-8');
        $login_password = $_POST['login_password'];
        $login_errors = array();

        if (empty($login_email)) {
            $login_errors[] = "Email is required.";
        } elseif (!filter_var($login_email, FILTER_VALIDATE_EMAIL)) {
            $login_errors[] = "Invalid email format.";
        }

        if (empty($login_password)) {
            $login_errors[] = "Password is required.";
        }

        if (empty($login_errors)) {
            $stmt_check_login = mysqli_prepare($con, "SELECT * FROM user_id WHERE email = ?");
            mysqli_stmt_bind_param($stmt_check_login, "s", $login_email);
            mysqli_stmt_execute($stmt_check_login);
            $result_check_login = mysqli_stmt_get_result($stmt_check_login);

            if (mysqli_num_rows($result_check_login) == 1) {
                $row = mysqli_fetch_assoc($result_check_login);
                if (password_verify($login_password, $row['password'])) {
                    $_SESSION['logged_in'] = true;
                    header("Location: https://webpas.my.id/"); // Alamat URL tujuan setelah login
                    exit();
                } else {
                    $login_errors[] = "Login failed. Please check your email and password.";
                }
            } else {
                $login_errors[] = "Login failed. Please check your email and password.";
            }

            mysqli_stmt_close($stmt_check_login);
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
</head>

<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form method="POST" action="">
                <h1>Create Account</h1>
                <div class="social-icons">
                    <!-- <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class "icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
                </div>
                <span>or use your email for registration</span>
                <input type="text" name="username" placeholder="Username">
                <input type="email" name="email" placeholder="Email">
                <input type="password" name="password" placeholder="Password">
                <button type="submit" name="signup">Sign Up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form method="POST" action="">
                <h1>Sign In</h1>
                <div class="social-icons">
                    <!-- <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-github"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-linkedin-in"></i></a> -->
                </div>
                <span>or use your email password</span>
                <input type="email" name="login_email" placeholder="Email">
                <input type="password" name="login_password" placeholder="Password">
                <a href="#">Forget Your Password?</a>
                <button type="submit" name="login">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal details to use all site features</p>
                    <button class="hidden" id="login">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hello, Friend!</h1>
                    <p>Register with your personal details to use all site features</p>
                    <button class="hidden" id="register">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>