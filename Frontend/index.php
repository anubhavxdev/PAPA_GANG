<?php
// Initialize the session
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$is_admin = isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === 1;
$user_name = $logged_in ? $_SESSION["first_name"] . " " . $_SESSION["last_name"] : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaSave - Smart Water Conservation</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body class="bg-gray-light font-poppins">
    <!-- Navigation -->
    <nav class="modern-navbar fixed w-full z-10">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <i class="fas fa-water text-primary text-3xl mr-2"></i>
                    <span class="text-dark font-bold text-xl">AquaSave</span>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="index.php" class="nav-link active">Home</a>
                    <?php if ($logged_in): ?>
                        <a href="pages/dashboard.php" class="nav-link">Dashboard</a>
                        <a href="pages/devices.html" class="nav-link">Devices</a>
                        <a href="pages/goals.html" class="nav-link">Goals</a>
                    <?php endif; ?>
                    <a href="pages/tips.html" class="nav-link">Water Tips</a>
                    
                    <?php if ($logged_in): ?>
                        <div class="relative group ml-4">
                            <button class="flex items-center space-x-1 text-dark hover:text-primary">
                                <span><?php echo htmlspecialchars($user_name); ?></span>
                                <i class="fas fa-chevron-down text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20 hidden group-hover:block">
                                <a href="pages/profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i> Profile
                                </a>
                                <?php if ($is_admin): ?>
                                <a href="pages/admin.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i> Admin Panel
                                </a>
                                <?php endif; ?>
                                <a href="pages/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="pages/login.php" class="btn-primary">Login</a>
                        <a href="pages/register.php" class="btn-outline">Sign Up</a>
                    <?php endif; ?>
                </div>
                <div class="md:hidden">
                    <button id="menu-toggle" class="text-dark focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block py-2 text-dark font-medium border-l-4 border-primary pl-2">Home</a>
                <?php if ($logged_in): ?>
                    <a href="pages/dashboard.php" class="block py-2 text-dark hover:text-primary font-medium">Dashboard</a>
                    <a href="pages/devices.html" class="block py-2 text-dark hover:text-primary font-medium">Devices</a>
                    <a href="pages/goals.html" class="block py-2 text-dark hover:text-primary font-medium">Goals</a>
                <?php endif; ?>
                <a href="pages/tips.html" class="block py-2 text-dark hover:text-primary font-medium">Water Tips</a>
                
                <?php if ($logged_in): ?>
                    <div class="mt-2 border-t border-gray-200 pt-2">
                        <span class="block py-2 text-dark font-medium"><?php echo htmlspecialchars($user_name); ?></span>
                        <a href="pages/profile.php" class="block py-2 text-dark hover:text-primary font-medium pl-4">
                            <i class="fas fa-user mr-2"></i> Profile
                        </a>
                        <?php if ($is_admin): ?>
                        <a href="pages/admin.php" class="block py-2 text-dark hover:text-primary font-medium pl-4">
                            <i class="fas fa-cog mr-2"></i> Admin Panel
                        </a>
                        <?php endif; ?>
                        <a href="pages/logout.php" class="block py-2 text-dark hover:text-primary font-medium pl-4">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                <?php else: ?>
                    <div class="flex mt-2 space-x-2">
                        <a href="pages/login.php" class="block py-2 px-4 bg-primary text-white rounded-md text-center w-1/2">Login</a>
                        <a href="pages/register.php" class="block py-2 px-4 border border-primary text-primary rounded-md text-center w-1/2">Sign Up</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section pt-24 pb-12 md:pt-32 md:pb-20">
        <div class="container mx-auto px-4">
            <div class="hero-content flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 mb-8 md:mb-0">
                    <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-full mb-4 font-medium text-sm">Intelligent Water Management</span>
                    <h1 class="text-3xl md:text-5xl font-bold text-dark mb-4">Smart Water <span class="text-primary">Conservation</span> Dashboard</h1>
                    <p class="text-lg text-gray-600 mb-8">Track, analyze, and optimize your water usage with our intelligent platform. Save water, save money, and help protect our planet's most precious resource.</p>
                    <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                        <?php if (!$logged_in): ?>
                            <a href="pages/register.php" class="btn-primary text-center">Get Started</a>
                        <?php else: ?>
                            <a href="pages/dashboard.php" class="btn-primary text-center">Go to Dashboard</a>
                        <?php endif; ?>
                        <a href="#features" class="btn-outline text-center">Learn More</a>
                    </div>
                </div>
                <div class="md:w-1/2">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1464925257126-6450e871c667" alt="Water Conservation" class="rounded-2xl shadow-xl relative z-10">
                        <div class="absolute -bottom-4 -right-4 w-24 h-24 bg-secondary rounded-lg z-0 opacity-50"></div>
                        <div class="absolute -top-4 -left-4 w-32 h-32 bg-primary rounded-lg z-0 opacity-20"></div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-full mb-3 font-medium text-sm">Features</span>
                <h2 class="text-2xl md:text-3xl font-bold text-dark mb-4">Why Choose AquaSave?</h2>
                <p class="text-gray-600">Our platform provides everything you need to monitor and conserve water efficiently</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="modern-card p-6 hover:border-primary hover:border border-transparent border">
                    <div class="bg-primary bg-opacity-10 w-16 h-16 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-tachometer-alt text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark text-center mb-3">Real-time Monitoring</h3>
                    <p class="text-gray-600 text-center">Track your water usage in real-time with intuitive dashboards and visualizations.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="modern-card p-6 hover:border-primary hover:border border-transparent border">
                    <div class="bg-primary bg-opacity-10 w-16 h-16 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-lightbulb text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark text-center mb-3">Smart Recommendations</h3>
                    <p class="text-gray-600 text-center">Receive personalized water-saving tips based on your usage patterns and location.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="modern-card p-6 hover:border-primary hover:border border-transparent border">
                    <div class="bg-primary bg-opacity-10 w-16 h-16 rounded-xl flex items-center justify-center mb-6 mx-auto">
                        <i class="fas fa-bullseye text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-dark text-center mb-3">Conservation Goals</h3>
                    <p class="text-gray-600 text-center">Set and track water conservation goals with progress metrics and achievements.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-gray-light">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 bg-primary bg-opacity-10 text-primary rounded-full mb-3 font-medium text-sm">Process</span>
                <h2 class="text-2xl md:text-3xl font-bold text-dark mb-4">How It Works</h2>
                <p class="text-gray-600">Get started with our easy-to-use water conservation platform in four simple steps</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="modern-card text-center px-4 py-6 relative">
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold z-10">1</div>
                    <div class="bg-white w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mx-auto shadow-md border border-gray-100">
                        <i class="fas fa-user-plus text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-3">Register</h3>
                    <p class="text-gray-600">Create your free account to get started with water monitoring.</p>
                </div>
                
                <!-- Step 2 -->
                <div class="modern-card text-center px-4 py-6 relative">
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold z-10">2</div>
                    <div class="bg-white w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mx-auto shadow-md border border-gray-100">
                        <i class="fas fa-plug text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-3">Connect Devices</h3>
                    <p class="text-gray-600">Link your water-consuming devices to the platform.</p>
                </div>
                
                <!-- Step 3 -->
                <div class="modern-card text-center px-4 py-6 relative">
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold z-10">3</div>
                    <div class="bg-white w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mx-auto shadow-md border border-gray-100">
                        <i class="fas fa-chart-line text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-3">Monitor Usage</h3>
                    <p class="text-gray-600">Track your water consumption with detailed analytics.</p>
                </div>
                
                <!-- Step 4 -->
                <div class="modern-card text-center px-4 py-6 relative">
                    <div class="absolute -top-4 -right-4 w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold z-10">4</div>
                    <div class="bg-white w-20 h-20 rounded-2xl flex items-center justify-center mb-6 mx-auto shadow-md border border-gray-100">
                        <i class="fas fa-tint-slash text-primary text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-dark mb-3">Save Water</h3>
                    <p class="text-gray-600">Implement recommendations to reduce your water footprint.</p>
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
                        <li><a href="index.php" class="text-blue-200 hover:text-white">Home</a></li>
                        <li><a href="pages/dashboard.php" class="text-blue-200 hover:text-white">Dashboard</a></li>
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
