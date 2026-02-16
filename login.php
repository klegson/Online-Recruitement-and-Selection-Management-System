<?php include('includes/header.php'); ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-lg">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Login</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                Invalid email or password.
            </div>
        <?php endif; ?>

        <form action="actions/login_action.php" method="POST" class="mt-8 space-y-6">
            <input type="email" name="email" required placeholder="Email address" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <input type="password" name="password" required placeholder="Password" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            
            <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-700 hover:bg-blue-800 transition-colors">
                Login
            </button>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>

