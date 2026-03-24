<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - DepEd Region V Online Recruitment Portal</title>
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

    <!-- Register Section -->
    <section class="h-screen flex items-center justify-center">
        <div class="w-full max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-0 items-center min-h-screen">
                <!-- Left Side - Branding/Info -->
                <div class="hero-bg text-white p-12 flex flex-col justify-center items-center text-center hidden lg:block">
                    <div class="max-w-md">
                        <img src="images/navlogo.jpg" alt="DepEd Logo" class="h-24 w-auto mx-auto mb-8">
                        <h1 class="text-4xl font-black mb-4">Join Our Team</h1>
                        <h2 class="text-2xl font-bold mb-6">DepEd Region V</h2>
                        <p class="text-lg mb-12 leading-relaxed">
                            Build a rewarding career shaping young minds and transforming communities through education.
                        </p>
                        <div class="space-y-6">
                            <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                                <h3 class="font-semibold text-lg mb-2">Start Your Journey</h3>
                                <div class="space-y-3 text-left">
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase text-green-400 mr-3"></i>
                                        <span>500+ career opportunities</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marked-alt text-green-400 mr-3"></i>
                                        <span>6 provinces in Region V</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-award text-green-400 mr-3"></i>
                                        <span>Professional development</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-sm text-white/80">
                                <p>New to DepEd Region V?</p>
                                <p class="font-semibold">Create your account to get started</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Side - Register Form -->
                <div class="bg-white p-8 lg:p-12 flex flex-col justify-center">
                    <div class="max-w-md mx-auto w-full">
                        <!-- Mobile Header -->
                        <div class="text-center mb-8 lg:hidden">
                            <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-plus text-blue-700 text-3xl"></i>
                            </div>
                            <h1 class="text-3xl font-bold primary-color mb-2">Create Account</h1>
                            <p class="text-gray-600">Join DepEd Region V recruitment portal</p>
                        </div>

                        <!-- Desktop Header -->
                        <div class="hidden lg:block mb-8">
                            <h1 class="text-3xl font-bold primary-color mb-2">Sign Up</h1>
                            <p class="text-gray-600">Create your DepEd recruitment account</p>
                        </div>

                        <?php if(isset($_GET['error'])): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                Account creation failed. Please try again or use a different email.
                            </div>
                        <?php endif; ?>

                        <!-- Register Form -->
                        <form action="actions/register_action.php" method="POST" class="space-y-4">
                            <!-- Name Fields -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                                        First Name
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="firstName" 
                                               name="firstName" 
                                               required
                                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                               placeholder="First name">
                                        <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                                    </div>
                                </div>
                                <div>
                                    <label for="middleName" class="block text-sm font-medium text-gray-700 mb-2">
                                        Middle Name
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="middleName" 
                                               name="middleName" 
                                               required
                                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                               placeholder="Middle name">
                                        <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Name
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="lastName" 
                                               name="lastName" 
                                               required
                                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                               placeholder="Last name">
                                        <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                                    </div>
                                </div>
                                <div>
                                    <label for="extension" class="block text-sm font-medium text-gray-700 mb-2">
                                        Extension (Jr., Sr., III, etc.)
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               id="extension" 
                                               name="extension" 
                                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                               placeholder="Extension (optional)">
                                        <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                                    </div>
                                </div>
                            </div>

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

                            <!-- Password Fields -->
                            <div class="grid grid-cols-2 gap-4">
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
                                               placeholder="Create password">
                                        <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                                        <button type="button" onclick="togglePassword()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="passwordToggle"></i>
                                        </button>
                                    </div>
                                </div>
                                <div>
                                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                                        Confirm
                                    </label>
                                    <div class="relative">
                                        <input type="password" 
                                               id="confirmPassword" 
                                               name="confirmPassword" 
                                               required
                                               class="w-full px-4 py-3 pl-12 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c focus:border-transparent"
                                               placeholder="Confirm password">
                                        <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                                        <button type="button" onclick="toggleConfirmPassword()" class="absolute right-4 top-4 text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-eye" id="confirmPasswordToggle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>


                            <!-- Register Button -->
                            <button type="submit" 
                                    class="w-full primary-bg text-white py-3 rounded-lg primary-hover transition-all font-semibold transform hover:scale-105 shadow-md">
                                CREATE ACCOUNT
                            </button>

                        </form>

                        <!-- Login Link -->
                        <div class="text-center mt-6">
                            <p class="text-gray-600">
                                Already have an account? 
                                <a href="login.php" class="primary-color hover:text-light-blue font-semibold transition-colors">
                                    Login here
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

        // Confirm password visibility toggle
        function toggleConfirmPassword() {
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const confirmPasswordToggle = document.getElementById('confirmPasswordToggle');
            
            if (confirmPasswordInput.type === 'password') {
                confirmPasswordInput.type = 'text';
                confirmPasswordToggle.classList.remove('fa-eye');
                confirmPasswordToggle.classList.add('fa-eye-slash');
            } else {
                confirmPasswordInput.type = 'password';
                confirmPasswordToggle.classList.remove('fa-eye-slash');
                confirmPasswordToggle.classList.add('fa-eye');
            }
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>