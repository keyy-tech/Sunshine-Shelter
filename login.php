<?php
session_start();

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'connections/db_connect.php'; // Ensure this path is correct

$message = ''; // Initialize message variable
$alert_class = ''; // Initialize alert class variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($db_connect) {
        // Prepare and execute query to fetch the user record
        $stmt = $db_connect->prepare("SELECT password FROM Staff WHERE username = ?");
        if (!$stmt) {
            die("Prepare failed: " . $db_connect->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        // Check if the user exists
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            // Verify the password
            if (password_verify($password, $hashed_password)) {
                $_SESSION['staff_logged_in'] = true; // Use staff_logged_in for consistency

                // Redirect to the dashboard
                header("Location: users/dashboard.php");
                exit();
            } else {
                $message = "Invalid password.";
                $alert_class = "alert-danger";
            }
        } else {
            $message = "Invalid username.";
            $alert_class = "alert-danger";
        }

        $stmt->close();
    } else {
        $message = "Database connection failed.";
        $alert_class = "alert-danger";
    }

    $db_connect->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <script src="js/bootstrap.bundle.min.js"></script>
    <title>Login</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h3 class="text-center">Login</h3>
        <p class="text-center pb-1 mt-0">Sunshine Haven Orphanage</p>
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo $alert_class; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="form-floating needs-validation" novalidate>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="floatingUsername" name="username" placeholder="Username" required>
                <label for="floatingUsername">Username</label>
                <div class="invalid-feedback">Please enter your username.</div>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password" required>
                <label for="floatingPassword">Password</label>
                <div class="invalid-feedback">Please enter your password.</div>
            </div>
            <button type="submit" class="btn btn-outline-primary mt-1 w-100">Login</button>
        </form>
    </div>
    <script>
        (function() {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
        })();
    </script>
</body>

</html>