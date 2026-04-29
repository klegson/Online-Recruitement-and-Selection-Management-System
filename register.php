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
                <p class="text-blue-200 mb-8">Build a rewarding career shaping young minds and transforming communities through education.</p>
                
                <!-- Glassmorphism Card -->
                <div class="glass-card rounded-lg p-6 text-left">
                    <h3 class="font-semibold text-lg mb-4">Why Join Us</h3>
                    <ul class="space-y-3">
                        <li class="flex items-center">
                            <i class="fas fa-briefcase text-green-400 mr-3 w-5"></i>
                            <span>500+ career opportunities</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-map-marked-alt text-green-400 mr-3 w-5"></i>
                            <span>6 provinces in Region V</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-graduation-cap text-green-400 mr-3 w-5"></i>
                            <span>Professional development</span>
                        </li>
                    </ul>
                </div>
                
                <p class="text-sm text-blue-300 mt-8">Already have an account? Sign in to continue.</p>
            </div>
        </div>

        <!-- Right Panel - Form Side -->
        <div class="bg-white flex flex-col justify-center items-center p-6 lg:p-10">
            <div class="w-full max-w-3xl">
                <!-- Back to Home -->
                <a href="index.php" class="inline-flex items-center text-slate-400 hover:text-slate-600 transition-colors mb-4">
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span class="text-sm">Back to Home</span>
                </a>
                
                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-8">
                    <img src="images/navlogo.jpg" alt="DepEd Logo" class="h-12 w-auto mx-auto mb-3">
                     <h1 class="text-xl font-bold text-[#003366] tracking-tight">Create Account</h1>
                     <p class="text-gray-600 text-sm mt-1">Join DepEd Region V recruitment</p>
                </div>
                
                <!-- Desktop Header -->
                <div class="hidden lg:block mb-6">
                    <h1 class="text-3xl font-bold text-[#003366] tracking-tight">Sign Up</h1>
                    <p class="text-gray-600 text-base mt-1">Create your account</p>
                </div>

                 <?php if(isset($_GET['error'])): ?>
                     <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                         <i class="fas fa-exclamation-circle mr-2"></i>
                         Account creation failed. Please try again or use a different email.
                     </div>
                 <?php endif; ?>

                <!-- Register Form -->
                <form action="actions/register_action.php" method="POST" class="space-y-4">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                            <div class="relative">
                                <input type="text" id="firstName" name="firstName" required
                                    class="w-full px-4 py-3 pl-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                    placeholder="First name">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label for="middleName" class="block text-sm font-medium text-gray-700 mb-1">Middle Name</label>
                            <div class="relative">
                                <input type="text" id="middleName" name="middleName" required
                                    class="w-full px-4 py-3 pl-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                    placeholder="Middle name">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                            <div class="relative">
                                <input type="text" id="lastName" name="lastName" required
                                    class="w-full px-4 py-3 pl-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                    placeholder="Last name">
                                <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>
                        <div>
                            <label for="extension" class="block text-sm font-medium text-gray-700 mb-1">Extension</label>
                            <div class="relative">
                                <select id="extension" name="extension"
                                    class="w-full px-4 py-3 pl-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20 bg-white">
                                    <option value="">None</option>
                                    <option value="Jr.">Jr.</option>
                                    <option value="Sr.">Sr.</option>
                                    <option value="II">II</option>
                                    <option value="III">III</option>
                                    <option value="IV">IV</option>
                                    <option value="V">V</option>
                                </select>
                                <i class="fas fa-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="relative">
                            <input type="email" id="email" name="email" required
                                class="w-full px-4 py-3 pl-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                placeholder="Enter your email">
                            <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-3 pl-10 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                    placeholder="Create password">
                                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <button type="button" onclick="togglePassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-slate-600">
                                    <i class="fas fa-eye" id="passwordToggle"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <div class="relative">
                                <input type="password" id="confirmPassword" name="confirmPassword" required
                                    class="w-full px-4 py-3 pl-10 pr-10 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-[#003366] focus:ring-2 focus:ring-blue-600/20"
                                    placeholder="Confirm password">
                                <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <button type="button" onclick="toggleConfirmPassword()" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-slate-600">
                                    <i class="fas fa-eye" id="confirmPasswordToggle"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Register Button -->
                        <button type="submit" class="w-full bg-[#003366] text-white py-3 rounded-md hover:bg-[#002244] transition-colors font-bold text-base">
                         CREATE ACCOUNT
                     </button>
                </form>

                <!-- Login Link -->
                <div class="text-center mt-4">
                    <p class="text-gray-600 text-sm">
                         Already have an account? 
                         <a href="login.php" class="text-[#003366] font-semibold hover:underline">Login here</a>
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