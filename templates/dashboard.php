<h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h2>

<div class="grid md:grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold text-gray-700 mb-4">Recent Tasks</h3>
        
        <?php if (empty($recentTasks)): ?>
            <p class="text-gray-500">You don't have any tasks yet.</p>
            <div class="mt-4">
                <a href="/tasks/create" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded transition-colors">Create Your First Task</a>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($recentTasks as $task): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= htmlspecialchars($task['title']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-<?= $task['status'] ?>">
                                        <?= ucfirst(str_replace('_', ' ', $task['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= htmlspecialchars($task['created_at']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="/tasks/<?= $task['id'] ?>/edit" class="text-primary hover:text-primary-dark">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                <a href="/tasks" class="inline-block bg-primary hover:bg-primary-dark text-white font-medium py-2 px-4 rounded transition-colors">View All Tasks</a>
            </div>
        <?php endif; ?>
    </div>
</div> 