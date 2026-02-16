<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Application Tracking System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .hero-bg {
            background-image: linear-gradient(rgba(0, 56, 168, 0.7), rgba(0, 56, 168, 0.7)), 
                              url('://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-white shadow-md">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-700 rounded-lg flex items-center justify-center">
                        <i class="fa-solid fa-people-roof text-white text-xl"></i>
                    </div>
                    <span class="ml-3 font-bold text-xl text-gray-800">Department of Education</span>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#home" class="text-gray-700 hover:text-blue-700 transition-colors">Home</a>
                    <a href="#jobs" class="text-gray-700 hover:text-blue-700 transition-colors">Jobs</a>
                    <a href="#how-to-apply" class="text-gray-700 hover:text-blue-700 transition-colors">How to Apply</a>
                    <a href="#faq" class="text-gray-700 hover:text-blue-700 transition-colors">FAQ's</a>
                </div>
                
                <!-- Login/Register Button -->
<div class="flex items-center space-x-3">
    <a href="login.php" class="border border-blue-700 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-50 transition-colors font-semibold">
        Login
    </a>
    <a href="register.php" class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
        Register
    </a>
</div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-700" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="#home" class="block py-2 text-gray-700 hover:text-blue-700">Home</a>
                <a href="#jobs" class="block py-2 text-gray-700 hover:text-blue-700">Jobs</a>
                <a href="#how-to-apply" class="block py-2 text-gray-700 hover:text-blue-700">How to Apply</a>
                <a href="#faq" class="block py-2 text-gray-700 hover:text-blue-700">FAQ's</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg text-white py-32">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Shape the Future of Philippine Education</h1>
            <p class="text-xl md:text-2xl mb-8 max-w-3xl mx-auto">
                Join our mission to empower minds and build a brighter tomorrow for the Filipino youth.
            </p>
            <button onclick="scrollToJobs()" class="bg-white text-blue-700 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                <i class="fas fa-search mr-2"></i>Explore Vacancies
            </button>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">Why Join DepEd</h2>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center group">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-hands-helping text-blue-700 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Impactful Public Service</h3>
                    <p class="text-gray-600">Make a meaningful difference in the lives of millions of Filipino students through quality education.</p>
                </div>
                
                <!-- Feature 2 -->
                <div class="text-center group">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-chart-line text-blue-700 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Continuous Career Growth</h3>
                    <p class="text-gray-600">Access professional development programs and clear career advancement pathways in public service.</p>
                </div>
                
                <!-- Feature 3 -->
                <div class="text-center group">
                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-shield-alt text-blue-700 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-3">Competitive Benefits & Stability</h3>
                    <p class="text-gray-600">Enjoy government-mandated benefits, job security, and competitive compensation packages.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Job Board Section -->
    <section id="jobs" class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">Current Vacancies</h2>
            
            <!-- Search and Filter -->
            <div class="max-w-4xl mx-auto mb-12">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text" 
                               placeholder="Search positions, divisions..." 
                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-700">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>
                    <button class="bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </div>
            
            <!-- Job Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Job 1 -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Administrative Officer I</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-700"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            SG-11
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-03-15</span>
                        </div>
                    </div>
                    <button class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 2 -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Administrative Officer II</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-700"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            SG-11
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-03-20</span>
                        </div>
                    </div>
                    <button class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 3 -->
                                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Administrative Officer III</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-700"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            SG-11
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-03-15</span>
                        </div>
                    </div>
                    <button class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 4 -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ICT Coordinator</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-700"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            SG-12
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-04-05</span>
                        </div>
                    </div>
                    <button class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 5 -->
            <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ICT Coordinator</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-700"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            SG-12
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-04-05</span>
                        </div>
                    </div>
                    <button class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 6 -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">ICT Coordinator</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 text-blue-700"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                            SG-12
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-04-05</span>
                        </div>
                    </div>
                    <button class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Apply Section -->
    <section id="how-to-apply" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">How to Apply</h2>
            
            <div class="max-w-4xl mx-auto">
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            1
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Create Account</h4>
                        <p class="text-gray-600 text-sm">Register with your email and complete your profile</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            2
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Browse Jobs</h4>
                        <p class="text-gray-600 text-sm">Search and filter positions that match your qualifications</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            3
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Submit Application</h4>
                        <p class="text-gray-600 text-sm">Upload required documents and submit your application</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-700 text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            4
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Track Status</h4>
                        <p class="text-gray-600 text-sm">Monitor your application status in real-time</p>
                    </div>
                </div>
            </div>
        </div>

            <!-- faq section -->
    <section id="faq" class="py-20 bg-white">
            <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-800 mb-12">Frequently asked questions</h2>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <div class="w-8 h-8 bg-blue-700 rounded-lg flex items-center justify-center mr-3">
                            <i class="fa-solid fa-people-roof text-white"></i>
                        </div>
                        <span class="font-bold text-lg">DepEd ATS</span>
                    </div>
                    <p class="text-gray-400 text-sm">Department of Education Application Tracking System</p>
                </div>
                
                <div class="flex flex-wrap justify-center md:justify-end space-x-6 text-sm">
                    <a href="#" class="hover:text-blue-400 transition-colors">Contact Us</a>
                    <a href="#" class="hover:text-blue-400 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-blue-400 transition-colors">Official DepEd Issuances</a>
                </div>
            </div>
            
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        // Smooth scroll to jobs section
        function scrollToJobs() {
            const jobsSection = document.getElementById('jobs');
            jobsSection.scrollIntoView({ behavior: 'smooth' });
        }

        // Smooth scroll for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>
</html>