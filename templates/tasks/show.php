<h2>Task Details</h2>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="task-detail">
    <h3><?= htmlspecialchars($task['title']) ?></h3>
    
    <div class="task-meta">
        <p>
            <strong>Status:</strong>
            <span class="status-badge status-<?= htmlspecialchars($task['status']) ?>">
                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $task['status']))) ?>
            </span>
        </p>
        <p><strong>Created By:</strong> <?= htmlspecialchars($task['creator_name'] ?? 'Unknown') ?></p>
        <p><strong>Assigned To:</strong> <?= htmlspecialchars($task['assignee_name'] ?? 'Unassigned') ?></p>
        <p><strong>Created:</strong> <?= htmlspecialchars($task['created_at']) ?></p>
        <?php if(isset($task['updated_at'])): ?>
            <p><strong>Last Updated:</strong> <?= htmlspecialchars($task['updated_at']) ?></p>
        <?php endif; ?>
    </div>
    
    <div class="task-description">
        <h4>Description</h4>
        <p><?= htmlspecialchars($task['description'] ?? 'No description provided.') ?></p>
    </div>
    
    <div class="task-actions">
        <a href="/tasks" class="btn">Back to Tasks</a>
        
        <?php if($permissions->canEditTask($task['id'])): ?>
            <a href="/tasks/<?= $task['id'] ?>/edit" class="btn">Edit Task</a>
        <?php endif; ?>
        
        <?php if($permissions->canDeleteTask($task['id'])): ?>
            <a href="/tasks/<?= $task['id'] ?>/delete" class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to delete this task?')">Delete Task</a>
        <?php endif; ?>
    </div>
</div> 