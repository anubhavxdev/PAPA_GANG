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
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ?";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    $email_err = "This email is already taken.";
                } else {
                    $email = trim($_POST["email"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
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
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (first_name, last_name, email, password, location, user_type) VALUES (?, ?, ?, ?, ?, ?)";
         
        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $param_first_name, $param_last_name, $param_email, $param_password, $param_location, $param_user_type);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            $param_location = $location;
            $param_user_type = $user_type;
            
            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to login page
                header("location: login.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    
    // Close connection
    mysqli_close($conn);
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
                    <a href="../index.html" class="flex items-center">
                        <i class="fas fa-water text-blue-500 text-3xl mr-2"></i>
                        <span class="text-blue-800 font-bold text-xl">AquaSave</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="../index.html" class="text-blue-800 hover:text-blue-600 font-medium">Home</a>
                    <a href="dashboard.php" class="text-blue-800 hover:text-blue-600 font-medium">Dashboard</a>
                    <a href="devices.html" class="text-blue-800 hover:text-blue-600 font-medium">Devices</a>
                    <a href="goals.html" class="text-blue-800 hover:text-blue-600 font-medium">Goals</a>
                    <a href="tips.html" class="text-blue-800 hover:text-blue-600 font-medium">Water Tips</a>
                </div>
                <div class="md:hidden">
                    <button id="menu-toggle" class="text-blue-800 focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="../index.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Home</a>
                <a href="dashboard.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Dashboard</a>
                <a href="devices.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Devices</a>
                <a href="goals.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Goals</a>
                <a href="tips.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Water Tips</a>
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
                    
                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($password_err)) ? 'border-red-500' : ''; ?>"
                               placeholder="Create a strong password">
                        <p class="text-xs text-gray-500 mt-1">Must be at least 8 characters with numbers, lowercase and uppercase letters</p>
                        <span class="text-red-500 text-xs"><?php echo $password_err; ?></span>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label for="confirm-password" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($confirm_password_err)) ? 'border-red-500' : ''; ?>"
                               placeholder="Confirm your password">
                        <span class="text-red-500 text-xs"><?php echo $confirm_password_err; ?></span>
                    </div>
                    
                    <!-- Location -->
                    <div class="mb-6">
                        <label for="location" class="block text-gray-700 font-medium mb-2">Location</label>
                        <select id="location" name="location" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent water-input <?php echo (!empty($location_err)) ? 'border-red-500' : ''; ?>">
                            <option value="">Select your location type</option>
                            <option value="urban" <?php if ($location == "urban") echo "selected"; ?>>Urban</option>
                            <option value="suburban" <?php if ($location == "suburban") echo "selected"; ?>>Suburban</option>
                            <option value="rural" <?php if ($location == "rural") echo "selected"; ?>>Rural</option>
                            <option value="coastal" <?php if ($location == "coastal") echo "selected"; ?>>Coastal</option>
                            <option value="desert" <?php if ($location == "desert") echo "selected"; ?>>Desert</option>
                        </select>
                        <span class="text-red-500 text-xs"><?php echo $location_err; ?></span>
                    </div>
                    
                    <!-- User Type -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">User Type</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 hover:bg-blue-100 border-2 border-blue-100 hover:border-blue-500 rounded-lg p-4 cursor-pointer transition-all user-type-option">
                                <input type="radio" id="user-type-homeowner" name="user-type" value="homeowner" class="hidden" <?php if ($user_type == "homeowner") echo "checked"; ?> checked>
                                <label for="user-type-homeowner" class="flex flex-col items-center cursor-pointer">
                                    <i class="fas fa-home text-blue-500 text-2xl mb-2"></i>
                                    <span class="font-medium">Homeowner</span>
                                </label>
                            </div>
                            <div class="bg-blue-50 hover:bg-blue-100 border-2 border-blue-100 hover:border-blue-500 rounded-lg p-4 cursor-pointer transition-all user-type-option">
                                <input type="radio" id="user-type-business" name="user-type" value="business" class="hidden" <?php if ($user_type == "business") echo "checked"; ?>>
                                <label for="user-type-business" class="flex flex-col items-center cursor-pointer">
                                    <i class="fas fa-building text-blue-500 text-2xl mb-2"></i>
                                    <span class="font-medium">Business</span>
                                </label>
                            </div>
                            <div class="bg-blue-50 hover:bg-blue-100 border-2 border-blue-100 hover:border-blue-500 rounded-lg p-4 cursor-pointer transition-all user-type-option">
                                <input type="radio" id="user-type-expert" name="user-type" value="expert" class="hidden" <?php if ($user_type == "expert") echo "checked"; ?>>
                                <label for="user-type-expert" class="flex flex-col items-center cursor-pointer">
                                    <i class="fas fa-user-graduate text-blue-500 text-2xl mb-2"></i>
                                    <span class="font-medium">Water Expert</span>
                                </label>
                            </div>
                        </div>
                        <span class="text-red-500 text-xs"><?php echo $user_type_err; ?></span>
                    </div>
                    
                    <!-- Terms and Conditions -->
                    <div class="mb-6">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="terms" name="terms" type="checkbox" required
                                       class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500">
                            </div>
                            <label for="terms" class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-md font-medium text-lg transition w-full md:w-auto">
                            Create Account
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Already have an account? <a href="login.php" class="text-blue-600 hover:underline">Login In</a></p>
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
                    <h4 class="font-medium mb-4">Connect With Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-blue-200 hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="border-t border-blue-700 mt-8 pt-6 text-center">
                <p class="text-blue-200">&copy; 2023 AquaSave. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../Frontend/js/main.js"></script>
</body>
</html>
