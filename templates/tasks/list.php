<h2>Tasks</h2>

<a href="/tasks/create" class="btn">Create New Task</a>

<?php if (count($tasks) > 0): ?>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Status</th>
            <th>Created By</th>
            <th>Assigned To</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tasks as $task): ?>
        <tr>
            <td><?php echo $task['id']; ?></td>
            <td><?php echo htmlspecialchars($task['title']); ?></td>
            <td><?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?></td>
            <td><?php echo htmlspecialchars($task['creator_name']); ?></td>
            <td><?php echo $task['assignee_name'] ? htmlspecialchars($task['assignee_name']) : 'Unassigned'; ?></td>
            <td><?php echo $task['created_at']; ?></td>
            <td>
                <a href="/tasks/<?php echo $task['id']; ?>">View</a>
                <?php if ($permissions->canEditTask($task['id'])): ?>
                | <a href="/tasks/<?php echo $task['id']; ?>/edit">Edit</a>
                <?php endif; ?>
                <?php if ($permissions->canDeleteTask($task['id'])): ?>
                | <a href="/tasks/<?php echo $task['id']; ?>/delete" 
                     onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<p>No tasks found.</p>
<?php endif; ?> 