<div class="bg-white rounded-lg shadow-md p-6 max-w-2xl mx-auto mt-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Task</h2>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error']) ?></span>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/tasks/<?= $task['id'] ?>/edit" class="space-y-4">
        <input type="hidden" name="id" value="<?= $task['id'] ?>">
        <div class="space-y-2">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" name="title" id="title" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                value="<?= htmlspecialchars($task['title']) ?>" required>
        </div>
        
        <div class="space-y-2">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" id="description" rows="4" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            ><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
        </div>
        
        <div class="space-y-2">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select name="status" id="status" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="pending" <?= $task['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="in_progress" <?= $task['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                <option value="completed" <?= $task['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
        </div>
        
        <div class="space-y-2">
            <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assign To</label>
            <select name="assigned_to" id="assigned_to" 
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Unassigned</option>
                <!-- User list would be pulled from database in a real app -->
                <?php if(isset($task['assigned_to']) && !empty($task['assigned_to'])): ?>
                    <option value="<?= $task['assigned_to'] ?>" selected>
                        <?= htmlspecialchars($task['assignee_name'] ?? 'User ID: ' . $task['assigned_to']) ?>
                    </option>
                <?php endif; ?>
            </select>
        </div>
        
        <div class="flex space-x-4 pt-4">
            <button type="submit" 
                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Update Task
            </button>
            <a href="/tasks" 
                class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                Cancel
            </a>
        </div>
    </form>
</div> 