<?php
// Initialize the session
session_start();

// Check if the user is already logged in, if yes then redirect to appropriate page
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

// Define variables and initialize with empty values
$email = $password = "";
$email_err = $password_err = $login_err = "";

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
        if(isset($GLOBALS['db_connected']) && $GLOBALS['db_connected'] === true) {
            // Prepare a select statement
            $sql = "SELECT id, first_name, last_name, email, password, is_admin FROM users WHERE email = ?";
            
            if($stmt = $conn->prepare($sql)){
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("s", $param_email);
                
                // Set parameters
                $param_email = $email;
                
                // Attempt to execute the prepared statement
                if($stmt->execute()){
                    // Store result
                    $stmt->store_result();
                    
                    // Check if email exists, if yes then verify password
                    if($stmt->num_rows == 1){                    
                        // Bind result variables
                        $stmt->bind_result($id, $first_name, $last_name, $db_email, $hashed_password, $is_admin);
                        if($stmt->fetch()){
                            if(password_verify($password, $hashed_password)){
                                // Password is correct, so start a new session
                                session_start();
                                
                                // Store data in session variables
                                $_SESSION["loggedin"] = true;
                                $_SESSION["id"] = $id;
                                $_SESSION["email"] = $db_email;
                                $_SESSION["first_name"] = $first_name;
                                $_SESSION["last_name"] = $last_name;
                                $_SESSION["is_admin"] = $is_admin;
                                
                                // Redirect user to appropriate page
                                if($is_admin == 1){
                                    header("location: admin.php");
                                } else {
                                    header("location: dashboard.php");
                                }
                                exit;
                            } else{
                                // Password is not valid
                                $login_err = "Invalid email or password.";
                            }
                        }
                    } else{
                        // Email doesn't exist
                        $login_err = "Invalid email or password.";
                    }
                } else{
                    $login_err = "Oops! Something went wrong. Please try again later.";
                }
                
                // Close statement
                $stmt->close();
            }
        } else {
            // Database not connected, check for admin credentials directly
            if($email === "admin@aquasave.com" && $password === "admin123"){
                // Set admin session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["id"] = 1;
                $_SESSION["email"] = "admin@aquasave.com";
                $_SESSION["first_name"] = "Admin";
                $_SESSION["last_name"] = "User";
                $_SESSION["is_admin"] = 1;
                
                // Redirect to admin page
                header("location: admin.php");
                exit;
            } else {
                // Invalid credentials
                $login_err = "Invalid email or password.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                    <a href="../index.php" class="flex items-center">
                        <i class="fas fa-water text-blue-500 text-3xl mr-2"></i>
                        <span class="text-blue-800 font-bold text-xl">AquaSave</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.php" class="text-gray-600 hover:text-blue-500">Home</a>
                    <a href="tips.html" class="text-gray-600 hover:text-blue-500">Water Tips</a>
                    <a href="register.php" class="bg-blue-100 text-blue-600 hover:bg-blue-200 py-2 px-4 rounded-md transition">Sign Up</a>
                </div>
                <div class="md:hidden">
                    <button id="menu-toggle" class="text-gray-600 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="../index.php" class="block py-2 text-gray-600 hover:text-blue-500">Home</a>
                <a href="tips.html" class="block py-2 text-gray-600 hover:text-blue-500">Water Tips</a>
                <a href="register.php" class="block py-2 text-gray-600 hover:text-blue-500">Sign Up</a>
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
                
                <?php if(isset($GLOBALS['db_connected']) && $GLOBALS['db_connected'] === false): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">Database connection is not available. For demo purposes, you can use admin credentials below.</span>
                </div>
                <?php endif; ?>
                
                <?php 
                // Display login error if any
                if(!empty($login_err)){
                    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">' . $login_err . '</span>
                          </div>';
                }        
                ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
                
                <?php if(isset($GLOBALS['db_connected']) && $GLOBALS['db_connected'] === false): ?>
                <div class="mt-6 p-4 bg-gray-100 rounded-md">
                    <h3 class="font-medium text-gray-700 mb-2">Demo Credentials</h3>
                    <p class="text-sm text-gray-600">For demonstration purposes, use:</p>
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
                        <li><a href="../index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="dashboard.php" class="text-blue-200 hover:text-white">Dashboard</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-medium mb-4">Resources</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-blue-200 hover:text-white">FAQ</a></li>
                        <li><a href="#" class="text-blue-200 hover:text-white">Support</a></li>
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
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>
