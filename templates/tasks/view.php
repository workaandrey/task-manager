<h2>Task Details</h2>

<div>
    <h3><?php echo htmlspecialchars($task_data['title']); ?></h3>
    
    <p><strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $task_data['status'])); ?></p>
    
    <p><strong>Description:</strong><br>
    <?php echo nl2br(htmlspecialchars($task_data['description'])); ?></p>
    
    <p><strong>Created By:</strong> <?php echo htmlspecialchars($task_data['creator_name']); ?></p>
    <p><strong>Assigned To:</strong> <?php echo $task_data['assignee_name'] ? htmlspecialchars($task_data['assignee_name']) : 'Unassigned'; ?></p>
    <p><strong>Created At:</strong> <?php echo $task_data['created_at']; ?></p>
    <p><strong>Updated At:</strong> <?php echo $task_data['updated_at']; ?></p>
    
    <div>
        <a href="/tasks" class="btn">Back to Tasks</a>
        <?php if ($permissions->canEditTask($task_data['id'])): ?>
        <a href="/tasks/<?php echo $task_data['id']; ?>/edit" class="btn">Edit</a>
        <?php endif; ?>
        <?php if ($permissions->canDeleteTask($task_data['id'])): ?>
        <a href="/tasks/<?php echo $task_data['id']; ?>/delete" class="btn" 
           onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
        <?php endif; ?>
    </div>
</div> 