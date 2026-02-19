<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DepEd Region V Online Recruitment Portal</title>
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
<body class="bg-gray-50">
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
                        <a href="#home" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Home</a>
                        <a href="#jobs" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">Jobs</a>
                        <a href="#how-to-apply" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">How To Apply</a>
                        <a href="#faq" class="nav-link primary-color hover:text-light-blue transition-colors font-medium">FAQ's</a>
                    </div>
                </div>
                
                <!-- Login/Register Button -->
                <div class="flex items-center space-x-3">
                    <a href="login.php" class="border border-primary primary-color px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors font-semibold">
                        LOGIN
                    </a>
                    <a href="register.php" class="primary-bg text-white px-4 py-2 rounded-lg primary-hover transition-colors font-semibold">
                        REGISTER
                    </a>
                </div>
                
                <!-- Mobile Menu Button -->
                <button class="md:hidden primary-color" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden pb-4">
                <a href="#home" class="block py-2 nav-link primary-color hover:text-light-blue">Home</a>
                <a href="#jobs" class="block py-2 nav-link primary-color hover:text-light-blue">Jobs</a>
                <a href="#how-to-apply" class="block py-2 nav-link primary-color hover:text-light-blue">How To Apply</a>
                <a href="#faq" class="block py-2 nav-link primary-color hover:text-light-blue">FAQ's</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-bg text-white py-32">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-7xl font-black mb-4 tracking-tight">DEPED REGION V</h1>
            <h2 class="text-4xl md:text-6xl font-black mb-8 tracking-tight">ONLINE RECRUITMENT PORTAL</h2>
            <p class="text-xl md:text-2xl mb-12 max-w-4xl mx-auto font-light leading-relaxed">
                Join our mission to empower minds and build a brighter tomorrow for the Filipino youth.
            </p>
            <button onclick="scrollToJobs()" class="bg-white text-gray-800 px-10 py-4 rounded-full text-lg font-bold hover:bg-gray-100 transition-all transform hover:scale-105 shadow-lg">
                Explore Vacancies
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
                               class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-06508c">
                        <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                    </div>
                    <button class="primary-bg text-white px-6 py-3 rounded-lg primary-hover transition-colors font-semibold">
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
                        <i class="fas fa-map-marker-alt mr-2 primary-color"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="light-blue primary-color px-3 py-1 rounded-full text-sm font-semibold">
                            SG-11
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-03-15</span>
                        </div>
                    </div>
                    <button class="w-full primary-bg text-white py-2 rounded-lg primary-hover transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 2 -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Administrative Officer II</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 primary-color"></i>
                        <span>Division of Albay</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="light-blue primary-color px-3 py-1 rounded-full text-sm font-semibold">
                            SG-11
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-03-20</span>
                        </div>
                    </div>
                    <button class="w-full primary-bg text-white py-2 rounded-lg primary-hover transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>

                <!-- Job 3 -->
                <div class="bg-white rounded-2xl shadow-md hover:shadow-lg transition-shadow p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Administrative Officer III</h3>
                    <div class="flex items-center text-gray-600 mb-3">
                        <i class="fas fa-map-marker-alt mr-2 primary-color"></i>
                        <span>Division of Camarines Sur</span>
                    </div>
                    <div class="flex items-center justify-between mb-4">
                        <span class="light-blue primary-color px-3 py-1 rounded-full text-sm font-semibold">
                            SG-12
                        </span>
                        <div class="flex items-center text-gray-500 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            <span>Deadline: 2025-03-25</span>
                        </div>
                    </div>
                    <button class="w-full primary-bg text-white py-2 rounded-lg primary-hover transition-colors font-semibold">
                        View & Apply
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Apply Section -->
    <section id="how-to-apply" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center primary-color mb-12">How to Apply</h2>
            
            <div class="max-w-4xl mx-auto">
                <div class="grid md:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 primary-bg text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            1
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Create Account</h4>
                        <p class="text-gray-600 text-sm">Register with your email and complete your profile</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 primary-bg text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            2
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Browse Jobs</h4>
                        <p class="text-gray-600 text-sm">Search and filter positions that match your qualifications</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 primary-bg text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            3
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Submit Application</h4>
                        <p class="text-gray-600 text-sm">Upload required documents and submit your application</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 primary-bg text-white rounded-full flex items-center justify-center mx-auto mb-4 font-bold">
                            4
                        </div>
                        <h4 class="font-semibold text-gray-800 mb-2">Track Status</h4>
                        <p class="text-gray-600 text-sm">Monitor your application status in real-time</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-20 light-blue">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl md:text-4xl font-bold text-center primary-color mb-12">Frequently Asked Questions</h2>
            
            <div class="max-w-4xl mx-auto">
                <div class="space-y-6">
                    <!-- FAQ 1 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">What are the basic requirements to apply for DepEd positions?</h3>
                            <i class="fas fa-chevron-down text-primary-color mt-1 transition-transform duration-300"></i>
                        </div>
                        <p class="text-gray-600 mt-3 hidden">Basic requirements include: Filipino citizenship, at least 18 years old, good moral character, and no pending administrative case. Specific positions may require additional qualifications such as educational attainment, civil service eligibility, and relevant work experience.</p>
                    </div>
                    
                    <!-- FAQ 2 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">How do I submit my application through the online portal?</h3>
                            <i class="fas fa-chevron-down text-primary-color mt-1 transition-transform duration-300"></i>
                        </div>
                        <p class="text-gray-600 mt-3 hidden">To submit your application: 1) Create an account on our portal, 2) Complete your profile with accurate information, 3) Browse available positions, 4) Click "Apply Now" on your desired position, 5) Upload required documents (resume, transcript, certificates), 6) Review and submit your application.</p>
                    </div>
                    
                    <!-- FAQ 3 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">What is the typical recruitment process timeline?</h3>
                            <i class="fas fa-chevron-down text-primary-color mt-1 transition-transform duration-300"></i>
                        </div>
                        <p class="text-gray-600 mt-3 hidden">The recruitment process typically takes 4-6 weeks: 1) Application screening (1 week), 2) Written examination (if applicable, 1 week), 3) Initial interview (1 week), 4) Final interview (1 week), 5) Background check and reference verification (1 week), 6) Job offer and onboarding (1 week).</p>
                    </div>
                    
                    <!-- FAQ 4 -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex justify-between items-start cursor-pointer" onclick="toggleFAQ(this)">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Can I apply for multiple positions simultaneously?</h3>
                            <i class="fas fa-chevron-down text-primary-color mt-1 transition-transform duration-300"></i>
                        </div>
                        <p class="text-gray-600 mt-3 hidden">Yes, you can apply for multiple positions as long as you meet the qualifications for each role. However, we recommend focusing on positions that best match your skills and career goals. Each application will be evaluated independently based on your qualifications and the specific requirements of each position.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="primary-bg text-white py-12">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-2">
                        <img src="images/logo.png" alt="DepEd Logo" class="h-8 w-auto mr-3">
                        <span class="font-bold text-lg">DepEd Region V</span>
                    </div>
                    <p class="text-gray-300 text-sm">Department of Education Application Tracking System</p>
                </div>
                
                <div class="flex flex-wrap justify-center md:justify-end space-x-6 text-sm">
                    <a href="#" class="hover:text-gray-300 transition-colors">Contact Us</a>
                    <a href="#" class="hover:text-gray-300 transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-gray-300 transition-colors">Official DepEd Issuances</a>
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

        // Toggle FAQ answers
        function toggleFAQ(element) {
            const answer = element.nextElementSibling;
            const chevron = element.querySelector('i');
            
            answer.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180');
        }
    </script>
</body>
</html>