<?php include('includes/header.php'); ?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-lg">
        <h2 class="text-center text-3xl font-extrabold text-gray-900">Create your account</h2>
        
        <form action="actions/register_action.php" method="POST" class="mt-8 space-y-6">
            <div class="grid grid-cols-2 gap-4">
                <input type="text" name="firstName" required placeholder="First Name" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <input type="text" name="lastName" required placeholder="Last Name" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <input type="email" name="email" required placeholder="Email address" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            <input type="password" name="password" required placeholder="Password" class="appearance-none rounded-lg relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            
            <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Register
            </button>
        </form>
    </div>
</div>

<?php include('includes/footer.php'); ?>