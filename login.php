<?php
session_start();
include('db.php');
$_SESSION['logged_id'] = 0;
$_SESSION['user'] = null;

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate inputs
    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['logged_id'] = $user['id'];
            $_SESSION['user'] = [
                'username' => $user['username'],
                'email' => $user['email']
            ];
            header('Location: ./dashboard');
            exit;
        } else {
            $_SESSION['logged_id'] = 0;
            $error = "Invalid email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login - Infive Print</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/modules/fontawesome/css/all.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/components.css">
    <style>
        .form-group {
            position: relative;
        }
        .form-control {
            padding-right: 40px; /* Space for the icon */
        }
        .toggle-password {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px; /* Adjust icon size */
            color: #6c757d; /* Bootstrap secondary color for consistency */
            line-height: 1; /* Ensure vertical alignment */
            margin-top: 15px;
        }
        .toggle-password:hover {
            color: #1BA664; /* Match project's primary color */
        }
        /* Responsive adjustments */
        @media (max-width: 767px) {
            .form-control {
                padding-right: 35px;
            }
            .toggle-password {
                right: 8px;
                font-size: 14px;
                margin-top: 15px;
            }
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
                            <div class="card-header"><h4>Login</h4></div>
                            <div class="card-body">
                                <?php if (isset($error)) { ?>
                                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                                <?php } ?>
                                <form method="POST" action="" class="needs-validation" novalidate="">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" class="form-control" name="email" tabindex="1" required autofocus>
                                        <div class="invalid-feedback">Please fill in your email</div>
                                    </div>
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                                        <div class="invalid-feedback">Please fill in your password</div>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">Login</button>
                                    </div>
                                    <div class="text-center">
                                        Don't have an account? <a href="#" onclick="promptForPassword()">Sign up</a>
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
        });

        function promptForPassword() {
            let password = prompt("Please enter the signup password:");
            if (password === null) {
                // User canceled, do nothing
                return;
            }
            // Check if password matches "Infive@2025"
            if (password !== "Infive@6969") {
                alert("Incorrect password. Please enter the correct password.");
                promptForPassword(); // Retry
                return;
            }
            // Store password in session via AJAX
            $.ajax({
                url: 'store_signup_password.php',
                type: 'POST',
                data: { signup_password: password },
                success: function(response) {
                    // Redirect to signup page
                    window.location.href = '<?php echo BASE_URL; ?>/signup';
                },
                error: function() {
                    alert("Error storing password. Please try again.");
                }
            });
        }
    </script>
</body>
</html>