<h2 class="text-2xl font-bold text-gray-800 mb-6">Login</h2>

<div class="max-w-md mx-auto">
    <div class="bg-white p-8 rounded-lg shadow-md">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username or Email</label>
                <input type="text" name="username" id="username" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" 
                       required>
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" 
                       required>
            </div>
            
            <div class="flex items-center justify-between mb-6">
                <button type="submit" 
                        class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring transition-colors">
                    Login
                </button>
            </div>
        </form>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="/register" class="font-medium text-primary hover:text-primary-dark">Register here</a>
            </p>
        </div>
    </div>
</div> 