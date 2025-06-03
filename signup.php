<?php
session_start();
include('db.php');

if (isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Email already registered.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user into database
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                header('Location: ./');
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Sign Up - Infive Print</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">
    <style>
        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 10px;
            margin-top: -20px;
            transform: translateY(-50%);
        }
        .form-group {
            position: relative;
        }
    </style>
</head>
<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand">
                            <img src="assets/img/infive_logo.png" alt="logo" width="100" class="shadow-light rounded-circle">
                        </div>
                        <div class="card card-primary">
                            <div class="card-header"><h4>Sign Up</h4></div>
                            <div class="card-body">
                                <?php if (isset($error)) { ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php } ?>
                                <form method="POST" action="" class="needs-validation" novalidate="">
                                    <div class="form-group">
                                        <label for="username">Username</label>
                                        <input id="username" type="text" class="form-control" name="username" required autofocus>
                                        <div class="invalid-feedback">Please fill in your username</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email" required>
                                        <div class="invalid-feedback">Please fill in a valid email</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input id="password" type="password" class="form-control" name="password" required>
                                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                                        <div class="invalid-feedback">Please fill in your password</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_password">Confirm Password</label>
                                        <input id="confirm_password" type="password" class="form-control" name="confirm_password" required>
                                        <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                                        <div class="invalid-feedback">Please confirm your password</div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" name="signup" class="btn btn-primary btn-lg btn-block">Sign Up</button>
                                    </div>
                                    <div class="text-center">
                                        Already have an account? <a href="./">Log in</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <script src="<?php echo BASE_URL; ?>/assets/modules/jquery.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/popper.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/stisla.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/scripts.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/custom.js"></script>
    <script>
        $(document).ready(function() {
            $('#togglePassword').click(function() {
                const passwordField = $('#password');
                const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
                passwordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });

            $('#toggleConfirmPassword').click(function() {
                const confirmPasswordField = $('#confirm_password');
                const type = confirmPasswordField.attr('type') === 'password' ? 'text' : 'password';
                confirmPasswordField.attr('type', type);
                $(this).toggleClass('fa-eye fa-eye-slash');
            });
        });
    </script>
</body>
</html>