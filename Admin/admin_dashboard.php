<?php

session_start();

require_once '../config/db.php';



if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {

    header("Location: ../login.php");

    exit();

}



// Fetch dashboard statistics

$totalUsers = $pdo->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];

$totalHR = $pdo->query("SELECT COUNT(*) as count FROM users WHERE userRole = 'HR_Staff'")->fetch()['count'];

$totalApplicants = $pdo->query("SELECT COUNT(*) as count FROM users WHERE userRole = 'Applicant'")->fetch()['count'];

$totalJobs = $pdo->query("SELECT COUNT(*) as count FROM jobs")->fetch()['count'];

$totalApplications = $pdo->query("SELECT COUNT(*) as count FROM applications")->fetch()['count'];



// Recent users

$recentUsers = $pdo->query("SELECT firstName, lastName, email, userRole, dateJoined FROM users ORDER BY dateJoined DESC LIMIT 5")->fetchAll();



// Application status breakdown

$appStats = $pdo->query("

    SELECT status, COUNT(*) as count 

    FROM applications 

    GROUP BY status

")->fetchAll();



// Active jobs breakdown

$jobStats = $pdo->query("

    SELECT jobStatus, COUNT(*) as count 

    FROM jobs 

    GROUP BY jobStatus

")->fetchAll();



include('../includes/header.php');

?>



<!-- Success/Error Messages -->

<?php if (isset($_SESSION['success'])): ?>

    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">

        <?= htmlspecialchars($_SESSION['success']) ?>

        <?php unset($_SESSION['success']); ?>

    </div>

<?php endif; ?>



<?php if (isset($_SESSION['error'])): ?>

    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">

        <?= htmlspecialchars($_SESSION['error']) ?>

        <?php unset($_SESSION['error']); ?>

    </div>

<?php endif; ?>



<!-- Welcome Section -->

<div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-12 mb-8">

    <div class="container mx-auto px-4">

        <div class="flex items-center justify-between">

            <div>

                <h1 class="text-3xl font-bold mb-2">Welcome back, <?= htmlspecialchars($_SESSION['firstName'] ?? 'Admin') ?>!</h1>

                <p class="text-blue-100">System administration and management dashboard</p>

            </div>

            <div class="hidden md:block">

                <i class="fas fa-shield-alt text-6xl text-blue-200"></i>

            </div>

        </div>

    </div>

</div>



<!-- Dashboard Statistics -->

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-600 mb-1">Total Users</p>

                <p class="text-2xl font-bold text-gray-800"><?= $totalUsers ?></p>

            </div>

            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">

                <i class="fas fa-users text-blue-600"></i>

            </div>

        </div>

    </div>

    

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-600 mb-1">HR Staff</p>

                <p class="text-2xl font-bold text-gray-800"><?= $totalHR ?></p>

            </div>

            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">

                <i class="fas fa-user-tie text-green-600"></i>

            </div>

        </div>

    </div>

    

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-600 mb-1">Applicants</p>

                <p class="text-2xl font-bold text-gray-800"><?= $totalApplicants ?></p>

            </div>

            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">

                <i class="fas fa-user-graduate text-purple-600"></i>

            </div>

        </div>

    </div>

    

    <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-orange-500">

        <div class="flex items-center justify-between">

            <div>

                <p class="text-sm text-gray-600 mb-1">Total Jobs</p>

                <p class="text-2xl font-bold text-gray-800"><?= $totalJobs ?></p>

            </div>

            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">

                <i class="fas fa-briefcase text-orange-600"></i>

            </div>

        </div>

    </div>

</div>



<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

    <!-- Application Status Overview -->

    <div class="bg-white rounded-xl shadow-sm p-6">

        <h2 class="text-xl font-bold text-gray-800 mb-4">

            <i class="fas fa-chart-pie mr-2"></i>Application Status Overview

        </h2>

        <div class="space-y-3">

            <?php foreach ($appStats as $stat): ?>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">

                    <div class="flex items-center">

                        <?php

                        $statusColors = [

                            'Pending' => 'bg-yellow-100 text-yellow-800',

                            'Shortlisted' => 'bg-green-100 text-green-800',

                            'Rejected' => 'bg-red-100 text-red-800',

                            'Hired' => 'bg-purple-100 text-purple-800'

                        ];

                        $statusClass = $statusColors[$stat['status']] ?? 'bg-gray-100 text-gray-800';

                        ?>

                        <span class="<?= $statusClass ?> text-xs px-2 py-1 rounded-full mr-3">

                            <?= htmlspecialchars($stat['status']) ?>

                        </span>

                        <span class="font-medium text-gray-700"><?= $stat['status'] ?></span>

                    </div>

                    <span class="text-2xl font-bold text-gray-800"><?= $stat['count'] ?></span>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="mt-4 pt-4 border-t">

            <div class="flex justify-between items-center">

                <span class="text-sm text-gray-600">Total Applications</span>

                <span class="text-xl font-bold text-blue-600"><?= $totalApplications ?></span>

            </div>

        </div>

    </div>



    <!-- Job Status Overview -->

    <div class="bg-white rounded-xl shadow-sm p-6">

        <h2 class="text-xl font-bold text-gray-800 mb-4">

            <i class="fas fa-chart-bar mr-2"></i>Job Status Overview

        </h2>

        <div class="space-y-3">

            <?php foreach ($jobStats as $stat): ?>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">

                    <div class="flex items-center">

                        <?php

                        $jobStatusColors = [

                            'Open' => 'bg-green-100 text-green-800',

                            'Closed' => 'bg-gray-100 text-gray-800'

                        ];

                        $jobStatusClass = $jobStatusColors[$stat['jobStatus']] ?? 'bg-gray-100 text-gray-800';

                        ?>

                        <span class="<?= $jobStatusClass ?> text-xs px-2 py-1 rounded-full mr-3">

                            <?= htmlspecialchars($stat['jobStatus']) ?>

                        </span>

                        <span class="font-medium text-gray-700"><?= $stat['jobStatus'] ?></span>

                    </div>

                    <span class="text-2xl font-bold text-gray-800"><?= $stat['count'] ?></span>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="mt-4 pt-4 border-t">

            <div class="flex justify-between items-center">

                <span class="text-sm text-gray-600">Total Job Postings</span>

                <span class="text-xl font-bold text-blue-600"><?= $totalJobs ?></span>

            </div>

        </div>

    </div>

</div>



<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <!-- Recent Users -->

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm p-6">

        <div class="flex justify-between items-center mb-4">

            <h2 class="text-xl font-bold text-gray-800">

                <i class="fas fa-user-clock mr-2"></i>Recent Users

            </h2>

            <a href="manage_users.php" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View All</a>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead>

                    <tr class="border-b border-gray-200">

                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Name</th>

                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Email</th>

                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Role</th>

                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Joined</th>

                    </tr>

                </thead>

                <tbody>

                    <?php foreach ($recentUsers as $user): ?>

                        <tr class="border-b border-gray-100 hover:bg-gray-50">

                            <td class="py-3 px-4">

                                <span class="font-medium text-gray-900">

                                    <?= htmlspecialchars($user['firstName'] . ' ' . $user['lastName']) ?>

                                </span>

                            </td>

                            <td class="py-3 px-4 text-sm text-gray-600"><?= htmlspecialchars($user['email']) ?></td>

                            <td class="py-3 px-4">

                                <span class="inline-block px-2 py-1 text-xs rounded-full 

                                    <?= $user['userRole'] === 'Admin' ? 'bg-purple-100 text-purple-800' : 

                                       ($user['userRole'] === 'HR_Staff' ? 'bg-green-100 text-green-800' : 

                                       'bg-blue-100 text-blue-800') ?>">

                                    <?= htmlspecialchars($user['userRole']) ?>

                                </span>

                            </td>

                            <td class="py-3 px-4 text-sm text-gray-600">

                                <?= date('M d, Y', strtotime($user['dateJoined'])) ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>





</div>

