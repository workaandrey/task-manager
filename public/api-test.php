<?php
declare(strict_types=1);

// This is a simple test file to demonstrate API usage
// You can call this in a browser to view sample API calls

// Load settings
require_once __DIR__ . '/../vendor/autoload.php';

$baseUrl = 'http://' . $_SERVER['HTTP_HOST'];

// Sample tasks endpoint
$tasksUrl = $baseUrl . '/api/tasks';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
        h1, h2 { color: #333; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 5px; overflow: auto; }
        .endpoint { background: #e9f7ef; padding: 10px; border-radius: 5px; margin-bottom: 10px; }
        .method { font-weight: bold; display: inline-block; width: 80px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; 
                border-radius: 4px; cursor: pointer; margin: 5px 0; }
        button:hover { background: #45a049; }
        #response { min-height: 100px; }
    </style>
</head>
<body>
    <h1>API Testing</h1>
    
    <p>Test the Task Manager API. You need to be logged in to use these endpoints.</p>
    
    <div>
        <h2>Task Endpoints</h2>
        
        <div class="endpoint">
            <span class="method">GET</span> <?= $tasksUrl ?>
            <button onclick="fetchTasks()">Test</button>
            <p>Get all tasks (filtered by your permissions)</p>
        </div>
        
        <div class="endpoint">
            <span class="method">GET</span> <?= $tasksUrl ?>/1
            <button onclick="fetchTask(1)">Test</button>
            <p>Get a specific task by ID</p>
        </div>
        
        <div class="endpoint">
            <span class="method">POST</span> <?= $tasksUrl ?>
            <button onclick="createTask()">Test</button>
            <p>Create a new task</p>
        </div>
        
        <div class="endpoint">
            <span class="method">POST</span> <?= $tasksUrl ?>/1
            <button onclick="updateTask(1)">Test</button>
            <p>Update an existing task</p>
        </div>
        
        <div class="endpoint">
            <span class="method">DELETE</span> <?= $tasksUrl ?>/1
            <button onclick="deleteTask(1)">Test</button>
            <p>Delete a task</p>
        </div>
    </div>
    
    <h2>Response:</h2>
    <pre id="response">Click a Test button above to see the API response...</pre>
    
    <script>
        // Display API response
        function showResponse(data, error = null) {
            const responseElement = document.getElementById('response');
            
            if (error) {
                responseElement.textContent = 'Error: ' + error;
                return;
            }
            
            try {
                if (typeof data === 'string') {
                    // Try to parse if it's JSON string
                    const parsed = JSON.parse(data);
                    responseElement.textContent = JSON.stringify(parsed, null, 2);
                } else {
                    responseElement.textContent = JSON.stringify(data, null, 2);
                }
            } catch (e) {
                // If not JSON, show as is
                responseElement.textContent = data;
            }
        }
        
        // Fetch all tasks
        function fetchTasks() {
            fetch('<?= $tasksUrl ?>', {
                method: 'GET',
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => showResponse(data))
            .catch(error => showResponse(null, error));
        }
        
        // Fetch a specific task
        function fetchTask(id) {
            fetch(`<?= $tasksUrl ?>/${id}`, {
                method: 'GET',
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => showResponse(data))
            .catch(error => showResponse(null, error));
        }
        
        // Create a new task
        function createTask() {
            const newTask = {
                title: 'API Created Task',
                description: 'This task was created through the API',
                status: 'pending'
            };
            
            fetch('<?= $tasksUrl ?>', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(newTask)
            })
            .then(response => response.json())
            .then(data => showResponse(data))
            .catch(error => showResponse(null, error));
        }
        
        // Update a task
        function updateTask(id) {
            const updatedTask = {
                title: 'Updated via API',
                status: 'in_progress'
            };
            
            fetch(`<?= $tasksUrl ?>/${id}`, {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(updatedTask)
            })
            .then(response => response.json())
            .then(data => showResponse(data))
            .catch(error => showResponse(null, error));
        }
        
        // Delete a task
        function deleteTask(id) {
            fetch(`<?= $tasksUrl ?>/${id}`, {
                method: 'DELETE',
                credentials: 'include'
            })
            .then(response => response.json())
            .then(data => showResponse(data))
            .catch(error => showResponse(null, error));
        }
    </script>
</body>
</html> 