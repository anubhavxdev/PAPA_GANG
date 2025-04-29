<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Define variables and initialize with empty values
$first_name = $last_name = $email = $location = $user_type = "";
$first_name_err = $last_name_err = $email_err = $location_err = $user_type_err = "";
$success_message = "";

// Get current user data
$user_id = $_SESSION["id"];
$sql = "SELECT * FROM users WHERE id = ?";

if($stmt = mysqli_prepare($conn, $sql)){
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    
    if(mysqli_stmt_execute($stmt)){
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_array($result, MYSQLI_ASSOC);
            $first_name = $user["first_name"];
            $last_name = $user["last_name"];
            $email = $user["email"];
            $location = $user["location"];
            $user_type = $user["user_type"];
        } else {
            // User not found
            session_destroy();
            header("location: login.php");
            exit();
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }
    
    mysqli_stmt_close($stmt);
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // Validate first name
    if(empty(trim($_POST["first-name"]))){
        $first_name_err = "Please enter your first name.";
    } else {
        $first_name = trim($_POST["first-name"]);
    }
    
    // Validate last name
    if(empty(trim($_POST["last-name"]))){
        $last_name_err = "Please enter your last name.";
    } else {
        $last_name = trim($_POST["last-name"]);
    }
    
    // Validate email
    if(empty(trim($_POST["email"]))){
        $email_err = "Please enter an email.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "si", $param_email, $user_id);
            
            // Set parameters
            $param_email = trim($_POST["email"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
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
    
    // Validate location
    if(empty(trim($_POST["location"]))){
        $location_err = "Please select your location.";
    } else {
        $location = trim($_POST["location"]);
    }
    
    // Validate user type
    if(empty(trim($_POST["user-type"]))){
        $user_type_err = "Please select user type.";
    } else {
        $user_type = trim($_POST["user-type"]);
    }
    
    // Check input errors before updating in database
    if(empty($first_name_err) && empty($last_name_err) && empty($email_err) && empty($location_err) && empty($user_type_err)){
        
        // Prepare an update statement
        $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, location = ?, user_type = ? WHERE id = ?";
         
        if($stmt = mysqli_prepare($conn, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssssi", $param_first_name, $param_last_name, $param_email, $param_location, $param_user_type, $param_id);
            
            // Set parameters
            $param_first_name = $first_name;
            $param_last_name = $last_name;
            $param_email = $email;
            $param_location = $location;
            $param_user_type = $user_type;
            $param_id = $user_id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Update session variables
                $_SESSION["first_name"] = $first_name;
                $_SESSION["last_name"] = $last_name;
                $_SESSION["email"] = $email;
                
                $success_message = "Profile updated successfully!";
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
    <title>Profile - AquaSave</title>
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
                <div class="flex items-center">
                    <div class="hidden md:block">
                        <div class="relative group">
                            <div class="flex items-center cursor-pointer">
                                <img src="https://images.unsplash.com/photo-1495774539583-885e02cca8c2" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                                <span class="ml-2 text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION["first_name"] . " " . $_SESSION["last_name"]); ?></span>
                                <i class="fas fa-chevron-down text-xs ml-1 text-gray-500"></i>
                            </div>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                                <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-user mr-2 text-blue-500"></i> Profile
                                </a>
                                <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-cog mr-2 text-blue-500"></i> Settings
                                </a>
                                <hr class="my-1">
                                <a href="logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50">
                                    <i class="fas fa-sign-out-alt mr-2 text-blue-500"></i> Logout
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="md:hidden">
                        <button id="menu-toggle" class="text-blue-800 focus:outline-none">
                            <i class="fas fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="../index.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Home</a>
                <a href="dashboard.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Dashboard</a>
                <a href="devices.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Devices</a>
                <a href="goals.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Goals</a>
                <a href="tips.html" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">Water Tips</a>
                <div class="mt-4 flex items-center">
                    <img src="https://images.unsplash.com/photo-1495774539583-885e02cca8c2" alt="Profile" class="h-8 w-8 rounded-full object-cover">
                    <span class="ml-2 text-gray-700 font-medium"><?php echo htmlspecialchars($_SESSION["first_name"] . " " . $_SESSION["last_name"]); ?></span>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="profile.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium border-l-4 border-blue-500 pl-2">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                    <a href="settings.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">
                        <i class="fas fa-cog mr-2"></i> Settings
                    </a>
                    <a href="logout.php" class="block py-2 text-blue-800 hover:text-blue-600 font-medium">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Profile Section -->
    <section class="pt-24 pb-12">
        <div class="container mx-auto px-4">
            <div class="max-w-3xl mx-auto">
                <h1 class="text-2xl md:text-3xl font-bold text-blue-800 mb-6">Your Profile</h1>
                
                <?php if(!empty($success_message)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $success_message; ?></span>
                    </div>
                <?php endif; ?>
                
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <div class="flex flex-col md:flex-row items-center mb-8">
                        <div class="mb-4 md:mb-0 md:mr-6">
                            <img src="https://images.unsplash.com/photo-1495774539583-885e02cca8c2" alt="Profile" class="h-24 w-24 rounded-full object-cover border-4 border-blue-100">
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-blue-800"><?php echo htmlspecialchars($first_name . " " . $last_name); ?></h2>
                            <p class="text-gray-600 mb-2"><?php echo htmlspecialchars($email); ?></p>
                            <div class="flex flex-wrap gap-2">
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-map-marker-alt mr-1"></i> <?php echo ucfirst(htmlspecialchars($location)); ?>
                                </span>
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-user mr-1"></i> <?php echo ucfirst(htmlspecialchars($user_type)); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="first-name" class="block text-gray-700 font-medium mb-2">First Name</label>
                                <input type="text" id="first-name" name="first-name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($first_name_err)) ? 'border-red-500' : ''; ?>"
                                       value="<?php echo $first_name; ?>">
                                <span class="text-red-500 text-xs"><?php echo $first_name_err; ?></span>
                            </div>
                            
                            <!-- Last Name -->
                            <div>
                                <label for="last-name" class="block text-gray-700 font-medium mb-2">Last Name</label>
                                <input type="text" id="last-name" name="last-name" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($last_name_err)) ? 'border-red-500' : ''; ?>"
                                       value="<?php echo $last_name; ?>">
                                <span class="text-red-500 text-xs"><?php echo $last_name_err; ?></span>
                            </div>
                        </div>
                        
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email Address</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($email_err)) ? 'border-red-500' : ''; ?>"
                                   value="<?php echo $email; ?>">
                            <span class="text-red-500 text-xs"><?php echo $email_err; ?></span>
                        </div>
                        
                        <!-- Location -->
                        <div>
                            <label for="location" class="block text-gray-700 font-medium mb-2">Location</label>
                            <select id="location" name="location" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent <?php echo (!empty($location_err)) ? 'border-red-500' : ''; ?>">
                                <option value="urban" <?php if ($location == "urban") echo "selected"; ?>>Urban</option>
                                <option value="suburban" <?php if ($location == "suburban") echo "selected"; ?>>Suburban</option>
                                <option value="rural" <?php if ($location == "rural") echo "selected"; ?>>Rural</option>
                                <option value="coastal" <?php if ($location == "coastal") echo "selected"; ?>>Coastal</option>
                                <option value="desert" <?php if ($location == "desert") echo "selected"; ?>>Desert</option>
                            </select>
                            <span class="text-red-500 text-xs"><?php echo $location_err; ?></span>
                        </div>
                        
                        <!-- User Type -->
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">User Type</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="bg-blue-50 hover:bg-blue-100 border-2 border-blue-100 hover:border-blue-500 rounded-lg p-4 cursor-pointer transition-all user-type-option">
                                    <input type="radio" id="user-type-homeowner" name="user-type" value="homeowner" class="hidden" <?php if ($user_type == "homeowner") echo "checked"; ?>>
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
                        
                        <div class="flex justify-between">
                            <a href="dashboard.php" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-md font-medium transition">
                                Back to Dashboard
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-4">Password Management</h2>
                    <p class="text-gray-600 mb-4">To change your password, use the button below to go to the password reset page.</p>
                    <a href="reset_password.php" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md font-medium transition inline-block">
                        Change Password
                    </a>
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

    <script>
        // Toggle mobile menu
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
        
        // Highlight selected user type
        document.querySelectorAll('.user-type-option').forEach(function(option) {
            const radio = option.querySelector('input[type="radio"]');
            
            if (radio.checked) {
                option.classList.add('border-blue-500', 'bg-blue-100');
            }
            
            option.addEventListener('click', function() {
                document.querySelectorAll('.user-type-option').forEach(function(opt) {
                    opt.classList.remove('border-blue-500', 'bg-blue-100');
                    opt.querySelector('input[type="radio"]').checked = false;
                });
                
                radio.checked = true;
                option.classList.add('border-blue-500', 'bg-blue-100');
            });
        });
    </script>
</body>
</html>
