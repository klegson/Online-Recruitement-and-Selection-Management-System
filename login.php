<?php 
session_start();
?>
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
        body { font-family: 'Inter', sans-serif; }
        .glass-card { background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(8px); }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="grid grid-cols-1 lg:grid-cols-2 min-h-screen">
        <!-- Left Panel - Brand Side -->
        <div class="bg-[#003366] text-white hidden lg:flex flex-col justify-center items-center p-16">
            <div class="max-w-md text-center flex flex-col justify-center">
                <img src="images/navlogo.jpg" alt="DepEd Logo" class="h-24 w-auto mx-auto mb-8">
                <h1 class="text-3xl font-bold tracking-tight mb-4">Join the DepEd Bicol Team</h1>
                <p class="text-blue-200 mb-8">Empowering education through dedicated service and excellence in Region V.</p>
                
                <!-- Glassmorphism Card -->
                <div class="glass-card rounded-lg p-6 text-left">
                    <h3 class="font-semibold text-lg mb-4">Start Your Journey</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3 w-5"></i>
                            <span>Shape the future of education</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3 w-5"></i>
                            <span>Career growth opportunities</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3 w-5"></i>
                            <span>Competitive benefits package</span>
                        </li>
                    </ul>
                </div>
                
                <p class="text-sm text-blue-300 mt-8">Don't have an account? Register to get started.</p>
            </div>
        </div>

        <!-- Right Panel - Form Side -->
        <div class="bg-white flex flex-col justify-center items-center p-16 lg:p-20">
            <div class="w-full max-w-3xl">
                <!-- Back to Home -->
                <a href="index.php" class="inline-flex items-center text-slate-400 hover:text-slate-600 transition-colors mb-8">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span class="text-sm">Back to Home</span>
                </a>
                
                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-8">
                    <img src="images/navlogo.jpg" alt="DepEd Logo" class="h-16 w-auto mx-auto mb-4">
                    <h1 class="text-2xl font-bold text-[#003366] tracking-tight">Welcome Back</h1>
                    <p class="text-gray-600 text-sm mt-1">Sign in to your account</p>
                </div>
                
                <!-- Desktop Header -->
                <div class="hidden lg:block mb-8">
                    <h1 class="text-4xl font-bold text-[#003366] tracking-tight">Welcome Back</h1>
                    <p class="text-gray-600 text-lg mt-2">Sign in to your account</p>
                </div>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-5 py-4 rounded-lg mb-6">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <?php if(isset($_GET['success']) && $_GET['success'] == 'account_created'): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-5 py-4 rounded-lg mb-6">
                        <i class="fas fa-check-circle mr-2"></i>
                        Account created! Please login with your credentials.
                    </div>
                <?php endif; ?>

                <!-- Login Form -->
                <form action="actions/login_action.php" method="POST" class="space-y-6">
                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-base font-medium text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" required
                                class="w-full px-5 py-4 pl-12 border border-slate-300 rounded-lg text-base focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                placeholder="Enter your email">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-base font-medium text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-5 py-4 pl-12 pr-12 border border-slate-300 rounded-lg text-base focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                placeholder="Enter your password">
                            <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-slate-600">
                                <i class="fas fa-eye" id="passwordToggle"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="w-5 h-5 text-[#003366] border-slate-300 rounded focus:ring-[#003366]">
                            <span class="ml-2 text-base text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-base text-[#003366] hover:underline">Forgot password?</a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit" class="w-full bg-[#003366] text-white py-4 rounded-md hover:bg-[#002244] transition-colors font-bold text-lg">
                        SIGN IN
                    </button>
                </form>

                <!-- Register Link -->
                <div class="text-center mt-8">
                    <p class="text-gray-600 text-base">
                        Don't have an account? 
                        <a href="register.php" class="text-[#003366] font-semibold hover:underline">Register now</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
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