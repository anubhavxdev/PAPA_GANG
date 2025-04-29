<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect to dashboard
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === 1) {
        header("location: admin.php");
    } else {
        header("location: dashboard.php");
    }
    exit;
}
 
// Include config file
require_once "config.php";
// Include database connection check utility
require_once "db_connection_check.php";
 
// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";

// Add a notification if database is not connected
$db_notification = "";
if (!$is_db_connected) {
    $db_notification = "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4' role='alert'>
                          <span class='block sm:inline'>Database connection is currently unavailable. You can still log in with admin credentials (admin@aquasave.com / admin123) for demonstration purposes.</span>
                        </div>";
}
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if email is empty
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter your email.";
    } else{
        $email = trim($_POST["email"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter your password.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($email_err) && empty($password_err)){
        // Check if database is connected
        if (!$is_db_connected) {
            // Database is not connected, check if admin credentials
            if (handle_admin_login_without_db($email, $password)) {
                // Admin login handled, redirect already done in the function
                exit;
            } else {
                // Not admin credentials or invalid
                $login_err = "Invalid email or password. Note: When database is unavailable, only admin login is supported.";
            }
        } else {
            // Database is connected, proceed with normal login
            // Prepare a select statement
            $sql = "SELECT id, first_name, last_name, email, password, is_admin FROM users WHERE email = ?";
            
            if($stmt = mysqli_prepare($conn, $sql)){
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "s", $param_email);
                
                // Set parameters
                $param_email = $email;
                
                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)){
                    // Store result
                    mysqli_stmt_store_result($stmt);
                    
                    // Check if email exists, if yes then verify password
                    if(mysqli_stmt_num_rows($stmt) == 1){                    
                        // Bind result variables
                        mysqli_stmt_bind_result($stmt, $id, $first_name, $last_name, $email, $hashed_password, $is_admin);
                        if(mysqli_stmt_fetch($stmt)){
                            if(password_verify($password, $hashed_password)){
                                // Password is correct, so start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["email"] = $email;
                                $_SESSION["first_name"] = $first_name;
                                $_SESSION["last_name"] = $last_name;
                                $_SESSION["is_admin"] = $is_admin;
                                $_SESSION["db_connected"] = true;
                                
                                // Redirect user to dashboard page
                                if($is_admin){
                                    header("location: admin.php");
                                } else {
                                    header("location: dashboard.php");
                                }
                                exit;
                            } else{
                                // Password is not valid, display a generic error message
                                $login_err = "Invalid email or password.";
                            }
                        }
                    } else{
                        // Email doesn't exist, display a generic error message
                        $login_err = "Invalid email or password.";
                    }
                } else{
                    $login_err = "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }
    
    // Close connection if it exists
    if (isset($conn) && $conn) {
        mysqli_close($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - AquaSave</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-blue-50 font-poppins">
    <!-- Navigation -->
    <nav class="bg-white shadow-md fixed w-full z-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="../index.html" class="flex items-center">
                        <i class="fas fa-water text-blue-500 text-3xl mr-2"></i>
                        <span class="text-blue-800 font-bold text-xl">AquaSave</span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="pt-24 pb-12 md:py-32">
        <div class="container mx-auto px-4">
            <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-blue-800 mb-2">Welcome Back</h1>
                    <p class="text-gray-600">Sign in to continue your AquaSave journey</p>
                </div>
                
                <?php 
                // Display database connection notification if needed
                if(!empty($db_notification)){
                    echo $db_notification;
                }
                
                // Display login error if any
                if(!empty($login_err)){
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">' . $login_err . '</span>
                          </div>';
                }        
                ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login-form" data-form-type="login">
                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                               placeholder="Enter your email address" value="<?php echo $email; ?>">
                        <span class="text-red-500 text-xs"><?php echo $email_err; ?></span>
                    </div>
                    
                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>"
                               placeholder="Enter your password">
                        <div class="text-right mt-1">
                            <a href="#" class="text-sm text-blue-600 hover:underline">Forgot Password?</a>
                        </div>
                        <span class="text-red-500 text-xs"><?php echo $password_err; ?></span>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium text-lg transition w-full text-center block">
                            Sign In
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Don't have an account? <a href="register.php" class="text-blue-600 hover:underline">Create one</a></p>
                </div>
                
                <?php if (!$is_db_connected): ?>
                <div class="mt-6 p-4 bg-gray-100 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-2">Demo Credentials</h3>
                    <p class="text-sm text-gray-600">For presentation purposes, you can use:</p>
                    <div class="mt-2 p-2 bg-white rounded border border-gray-200">
                        <p class="text-sm"><strong>Email:</strong> admin@aquasave.com</p>
                        <p class="text-sm"><strong>Password:</strong> admin123</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-blue-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-semibold mb-4">AquaSave</h3>
                    <p class="text-blue-200">Smart water conservation for a sustainable future.</p>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Quick Links</h4>
                    <ul class="space-y-2">
                        <li><a href="../index.html" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="dashboard.php" class="text-blue-200 hover:text-white">Dashboard</a></li>
                        <li><a href="devices.html" class="text-blue-200 hover:text-white">Devices</a></li>
                        <li><a href="goals.html" class="text-blue-200 hover:text-white">Goals</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="tips.html" class="text-blue-200 hover:text-white">Water Saving Tips</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Support</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Blog</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Contact</h4>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-envelope text-blue-300 mt-1 mr-2"></i>
                            <span class="text-blue-200">info@aquasave.com</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-phone text-blue-300 mt-1 mr-2"></i>
                            <span class="text-blue-200">+1 (555) 123-4567</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-blue-300 mt-1 mr-2"></i>
                            <span class="text-blue-200">123 Water St, Earth City, 12345</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-blue-700 mt-8 pt-6 text-center text-blue-300">
                <p>&copy; 2025 AquaSave. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.getElementById('menu-toggle')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
