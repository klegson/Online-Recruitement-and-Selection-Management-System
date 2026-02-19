<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DepEd Region V Online Recruitment Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .hero-bg {
            background: linear-gradient(135deg, #06508c 0%, #0a4d82 50%, #06508c 100%);
        }
        .primary-color { color: #06508c; }
        .primary-bg { background-color: #06508c; }
        .primary-hover:hover { background-color: #054073; }
        .light-blue { background-color: #e8f2ff; }
        .text-light-blue { color: #5a9fd4; }
        .border-primary { border-color: #06508c; }
        .deped-blue { color: #003366; }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #06508c;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }
        .nav-link:hover {
            color: #06508c;
            transform: translateY(-1px);
        }
        .nav-link.active {
            color: #06508c;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <img src="images/navlogo.jpg" alt="DepEd Logo" class="h-16 w-auto mr-3">
                    <div class="deped-blue">
                        <div class="text-xs font-bold leading-none">REPUBLIC OF THE PHILIPPINES</div>
                        <div class="text-lg font-black leading-none">DEPARTMENT OF EDUCATION</div>
                        <div class="text-sm font-bold leading-none">REGION V</div>
                        <div class="text-xs font-medium leading-none mt-1">DepEd Bicol CARES, SHARES and SERVES with a SMILE</div>
                    </div>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8 flex-1">
                    <div class="flex items-center space-x-8 ml-48">
                        <a href="index.php" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Home</a>
                        <a href="index.php#jobs" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Jobs</a>
                        <a href="index.php#how-to-apply" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">How To Apply</a>
                        <a href="index.php#faq" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">FAQ's</a>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="flex items-center space-x-3">
                    <a href="index.php" class="border border-primary primary-color px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        <i class="fas fa-arrow-left mr-2"></i> BACK
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden primary-color" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="index.php" class="block py-2 nav-link primary-color hover:text-light-blue">Home</a>
                <a href="index.php#jobs" class="block py-2 nav-link primary-color hover:text-light-blue">Jobs</a>
                <a href="index.php#how-to-apply" class="block py-2 nav-link primary-color hover:text-light-blue">How To Apply</a>
                <a href="index.php#faq" class="block py-2 nav-link primary-color hover:text-light-blue">FAQ's</a>
            </div>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="h-screen flex items-center justify-center">
        <div class="w-full max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-center min-h-screen">
                <!-- Left Side - Branding/Info -->
                <div class="hero-bg text-white p-12 flex flex-col justify-center items-center text-center hidden lg:block">
                    <div class="max-w-md">
                        <img src="images/navlogo.jpg" alt="DepEd Logo" class="h-24 w-auto mx-auto mb-8">
                        <h1 class="text-4xl font-black mb-4">Welcome Back</h1>
                        <h2 class="text-2xl font-bold mb-6">DepEd Region V</h2>
                        <p class="text-lg mb-12 leading-relaxed">
                            Empowering education through dedicated service and excellence in Region V.
                        </p>
                        <div class="space-y-6">
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                                <h3 class="font-semibold text-lg mb-2">Why Join Us?</h3>
                                <div class="space-y-3 text-left">
                                    <div class="flex items-center">
                                        <i class="fas fa-graduation-cap text-green-400 mr-3"></i>
                                        <span>Shape the future of education</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-green-400 mr-3"></i>
                                        <span>Join 50,000+ educators</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-chart-line text-green-400 mr-3"></i>
                                        <span>Career growth opportunities</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-white/80">
                                <p>Already have an account?</p>
                                <p class="font-semibold">Sign in to continue your journey</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Login Form -->
                <div class="bg-white p-8 lg:p-12 flex flex-col justify-center">
                    <div class="max-w-md mx-auto w-full">
                        <!-- Mobile Header -->
                        <div class="text-center mb-8 lg:hidden">
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-circle text-blue-700 text-3xl"></i>
                            </div>
                            <h1 class="text-3xl font-bold primary-color mb-2">Welcome Back</h1>
                            <p class="text-gray-600">Sign in to your DepEd recruitment account</p>
                        </div>

                        <!-- Desktop Header -->
                        <div class="hidden lg:block mb-8">
                            <h1 class="text-3xl font-bold primary-color mb-2">Sign In</h1>
                            <p class="text-gray-600">Access your DepEd recruitment account</p>
                        </div>

                        <?php if(isset($_GET['error'])): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Invalid email or password.
                            </div>
                        <?php endif; ?>

                        <!-- Login Form -->
                        <form action="actions/login_action.php" method="POST" class="space-y-6">
                            <!-- Email Field -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address
                                </label>
                                <div class="relative">
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           required
                                           class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                           placeholder="Enter your email">
                                    <i class="fas fa-envelope absolute left-4 top-4 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password
                                </label>
                                <div class="relative">
                                    <input type="password" 
                                           id="password" 
                                           name="password" 
                                           required
                                           class="w-full px-4 py-3 pl-12 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                           placeholder="Enter your password">
                                    <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                                    <button type="button" onclick="togglePassword()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="passwordToggle"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between">
                                <label class="flex items-center">
                                    <input type="checkbox" name="remember" class="w-4 h-4 text-primary-color border-gray-300 rounded focus:ring-primary-color">
                                    <span class="ml-2 text-sm text-gray-600">Remember me</span>
                                </label>
                                <a href="#" class="text-sm primary-color hover:text-light-blue transition-colors">
                                    Forgot password?
                                </a>
                            </div>

                            <!-- Login Button -->
                            <button type="submit" 
                                    class="w-full primary-bg text-white py-3 rounded-lg primary-hover transition-all font-semibold transform hover:scale-105 shadow-md">
                                SIGN IN
                            </button>

                            <!-- Social Login -->
                            <div class="grid grid-cols-2 gap-3">
                                <button type="button" 
                                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fab fa-google text-red-500 mr-2"></i>
                                    <span class="text-sm font-medium">Google</span>
                                </button>
                                <button type="button" 
                                        class="flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    <i class="fab fa-facebook text-blue-600 mr-2"></i>
                                    <span class="text-sm font-medium">Facebook</span>
                                </button>
                            </div>
                        </form>

                        <!-- Register Link -->
                        <div class="text-center mt-6">
                            <p class="text-gray-600">
                                Don't have an account? 
                                <a href="register.php" class="primary-color hover:text-light-blue font-semibold transition-colors">
                                    Register now
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>

