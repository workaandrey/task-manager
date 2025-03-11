<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold text-gray-800">Create New Task</h2>
    <a href="/tasks" class="text-primary hover:text-primary-dark font-medium">Back to Tasks</a>
</div>

<div class="bg-white p-6 rounded-lg shadow">
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/tasks/create">
        <div class="mb-4">
            <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Title</label>
            <input type="text" name="title" id="title" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary" 
                   required>
        </div>
        
        <div class="mb-4">
            <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary"></textarea>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select name="status" id="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="pending">Pending</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </div>
            
            <div>
                <label for="assigned_to" class="block text-gray-700 text-sm font-bold mb-2">Assign To</label>
                <select name="assigned_to" id="assigned_to" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-primary focus:border-primary">
                    <option value="">Unassigned</option>
                    <!-- User list would be pulled from database in a real app -->
                </select>
            </div>
        </div>
        
        <div class="flex justify-end space-x-2">
            <a href="/tasks" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:ring transition-colors">
                Cancel
            </a>
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring transition-colors">
                Create Task
            </button>
        </div>
    </form>
</div> 