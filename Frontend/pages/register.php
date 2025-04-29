<?php
// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$first_name = $last_name = $email = $password = $confirm_password = $location = $user_type = "";
$first_name_err = $last_name_err = $email_err = $password_err = $confirm_password_err = $location_err = $user_type_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validate first name
    if (empty(trim($_POST["first-name"]))) {
        $first_name_err = "Please enter your first name.";
    } else {
        $first_name = trim($_POST["first-name"]);
    }
    
    // Validate last name
    if (empty(trim($_POST["last-name"]))) {
        $last_name_err = "Please enter your last name.";
    } else {
        $last_name = trim($_POST["last-name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter an email.";
    } else {
        // Check if database is connected
        if(isset($GLOBALS['db_connected']) && $GLOBALS['db_connected'] === true) {
            // Prepare a select statement
            $sql = "SELECT id FROM users WHERE email = ?";
            
            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("s", $param_email);
                
                // Set parameters
                $param_email = trim($_POST["email"]);
                
                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Store result
                    $stmt->store_result();
                    
                    if ($stmt->num_rows == 1) {
                        $email_err = "This email is already taken.";
                    } else {
                        $email = trim($_POST["email"]);
                    }
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                $stmt->close();
            }
        } else {
            // Database not connected, just validate email format
            if (filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
                $email = trim($_POST["email"]);
            } else {
                $email_err = "Please enter a valid email address.";
            }
        }
    }
    
    // Validate password
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter a password.";     
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $password_err = "Password must have at least 8 characters.";
    } else {
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if (empty(trim($_POST["confirm-password"]))) {
        $confirm_password_err = "Please confirm password.";     
    } else {
        $confirm_password = trim($_POST["confirm-password"]);
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
    
    // Validate location
    if (empty(trim($_POST["location"]))) {
        $location_err = "Please select your location.";
    } else {
        $location = trim($_POST["location"]);
    }
    
    // Validate user type
    if (empty(trim($_POST["user-type"]))) {
        $user_type_err = "Please select user type.";
    } else {
        $user_type = trim($_POST["user-type"]);
    }
    
    // Check input errors before inserting in database
    if (empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err) && empty($location_err) && empty($user_type_err)) {
        
        // Check if database is connected
        if(isset($GLOBALS['db_connected']) && $GLOBALS['db_connected'] === true) {
            // Prepare an insert statement
            $sql = "INSERT INTO users (first_name, last_name, email, password, location, user_type) VALUES (?, ?, ?, ?, ?, ?)";
             
            if ($stmt = $conn->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bind_param("ssssss", $param_first_name, $param_last_name, $param_email, $param_password, $param_location, $param_user_type);
                
                // Set parameters
                $param_first_name = $first_name;
                $param_last_name = $last_name;
                $param_email = $email;
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                $param_location = $location;
                $param_user_type = $user_type;
                
                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    // Redirect to login page
                    header("location: login.php");
                    exit();
                } else {
                    echo "Oops! Something went wrong. Please try again later.";
                }

                // Close statement
                $stmt->close();
            }
        } else {
            // Database not connected, just redirect to login page with a message
            $_SESSION['registration_success'] = true;
            $_SESSION['registration_message'] = "Registration successful! Please login with your credentials.";
            header("location: login.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AquaSave</title>
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
                    <a href="login.php" class="bg-blue-600 text-white hover:bg-blue-700 py-2 px-4 rounded-md transition">Login</a>
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
                <a href="login.php" class="block py-2 text-gray-600 hover:text-blue-500">Login</a>
            </div>
        </div>
    </nav>

    <!-- Registration Section -->
    <section class="pt-24 pb-12 md:py-32">
        <div class="container mx-auto px-4">
            <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-blue-800 mb-2">Create Your Account</h1>
                    <p class="text-gray-600">Join the AquaSave community and start your water conservation journey</p>
                </div>
                
                <?php if(isset($GLOBALS['db_connected']) && $GLOBALS['db_connected'] === false): ?>
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">Database connection is not available. Registration will be simulated for demo purposes.</span>
                </div>
                <?php endif; ?>
                
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registration-form" data-validate="true" data-form-type="registration">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- First Name -->
                        <div>
                            <label for="first-name" class="block text-gray-700 font-medium mb-2">First Name</label>
                            <input type="text" id="first-name" name="first-name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($first_name_err)) ? 'border-red-500' : ''; ?>"
                                   placeholder="Enter your first name" value="<?php echo $first_name; ?>">
                            <span class="text-red-500 text-xs"><?php echo $first_name_err; ?></span>
                        </div>
                        
                        <!-- Last Name -->
                        <div>
                            <label for="last-name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                            <input type="text" id="last-name" name="last-name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($last_name_err)) ? 'border-red-500' : ''; ?>"
                                   placeholder="Enter your last name" value="<?php echo $last_name; ?>">
                            <span class="text-red-500 text-xs"><?php echo $last_name_err; ?></span>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="mb-6">
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                               placeholder="Enter your email address" value="<?php echo $email; ?>">
                        <span class="text-red-500 text-xs"><?php echo $email_err; ?></span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                            <input type="password" id="password" name="password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>"
                                   placeholder="Create a password">
                            <span class="text-red-500 text-xs"><?php echo $password_err; ?></span>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div>
                            <label for="confirm-password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                            <input type="password" id="confirm-password" name="confirm-password" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>"
                                   placeholder="Confirm your password">
                            <span class="text-red-500 text-xs"><?php echo $confirm_password_err; ?></span>
                        </div>
                    </div>
                    
                    <div class="mb-6">
                        <label for="location" class="block text-gray-700 font-medium mb-2">Location</label>
                        <select id="location" name="location" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($location_err)) ? 'border-red-500' : ''; ?>">
                            <option value="">Select your location type</option>
                            <option value="urban" <?php if($location == "urban") echo "selected"; ?>>Urban</option>
                            <option value="suburban" <?php if($location == "suburban") echo "selected"; ?>>Suburban</option>
                            <option value="rural" <?php if($location == "rural") echo "selected"; ?>>Rural</option>
                        </select>
                        <span class="text-red-500 text-xs"><?php echo $location_err; ?></span>
                    </div>
                    
                    <div class="mb-8">
                        <label for="user-type" class="block text-gray-700 font-medium mb-2">User Type</label>
                        <select id="user-type" name="user-type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($user_type_err)) ? 'border-red-500' : ''; ?>">
                            <option value="">Select your user type</option>
                            <option value="residential" <?php if($user_type == "residential") echo "selected"; ?>>Residential</option>
                            <option value="commercial" <?php if($user_type == "commercial") echo "selected"; ?>>Commercial</option>
                            <option value="industrial" <?php if($user_type == "industrial") echo "selected"; ?>>Industrial</option>
                        </select>
                        <span class="text-red-500 text-xs"><?php echo $user_type_err; ?></span>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium text-lg transition w-full md:w-auto">
                            Create Account
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login</a></p>
                </div>
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
                        <li><a href="login.php" class="text-blue-200 hover:text-white">Login</a></li>
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
