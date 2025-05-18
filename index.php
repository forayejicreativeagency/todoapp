<?php
session_start(); // Start session at the very top

define("TASK_PATH", "server/data.json");

// Ensure directory exists
$dir = dirname(TASK_PATH);
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

// Handle add task
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["task"]) && !empty(trim($_POST["task"]))) {
    try {
        $task = [
            "taskID" => random_int(100000, 999999), // 6-character unique ID
            "task" => htmlspecialchars_decode(trim($_POST["task"])),
            "done" => false,
        ];

        $tasks = [];
        if (file_exists(TASK_PATH)) {
            $tasks = json_decode(file_get_contents(TASK_PATH), true) ?? [];
        }

        $tasks[] = $task;

        if (file_put_contents(TASK_PATH, json_encode($tasks, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("Failed to write to task file.");
        }

        $_SESSION['message'] = "Task added successfully!";
        $_SESSION['type'] = "add";

        header("Location: success.php");
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = "Error: " . $e->getMessage();
        $_SESSION['type'] = "error";

        header("Location: success.php");
        exit;
    }
}


// Handle delete or toggle status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"], $_POST["taskID"])) {
    $tasks = json_decode(file_get_contents(TASK_PATH), true) ?? [];
    $taskID = $_POST["taskID"];
    $message = '';
    $type = '';

    foreach ($tasks as $index => $task) {
        if ($task["taskID"] == $taskID) {
            if ($_POST["action"] === "delete") {
                unset($tasks[$index]);
                $message = "Task deleted successfully!";
                $type = "delete";
            } elseif ($_POST["action"] === "toggle") {
                $tasks[$index]["done"] = !$task["done"];
                $message = "Task status updated!";
                $type = "update";
            }
            break;
        }
    }

    file_put_contents(TASK_PATH, json_encode(array_values($tasks), JSON_PRETTY_PRINT));

    $_SESSION['message'] = $message;
    $_SESSION['type'] = $type;

    header("Location: success.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple To-Do App</title>
    <link rel="stylesheet" href="css/main.min.css" />
</head>


<body>
    <section class="ct-section">
        <div class="ct-section-inner ct-flex-column">
            <div class="ct-width-full bg-white radius-10">
                <h1 class="ct-heading ct-heading-1 p-20" style="text-align: center;">Simple To-Do App</h1>
            </div>
            <div class="ct-flex-row" style="flex: auto; height: 100%; overflow: hidden;">
                <!-- Add Task -->
                <div class="bg-white ct-flex-column ct-wrapper p-20 radius-10" style="max-width: 350px;">
                    <h2 class="ct-heading ct-heading-2">Add task</h2>
                    <form id="todo-form" method="post" class="ct-flex-row" style="gap: 0;">
                        <input type="text" name="task" id="todo-input" class="p-12 ct-border-none bg-cyan-gray text-black" placeholder="Task Name" required>
                        <button type="submit" class="p-12 pl-25 pr-25 bg-blue text-white ct-border-none">Add</button>
                    </form>
                    <!-- Optional Work Project Requirements -->
                    <div>
                        <h2 class="ct-heading ct-heading-2">Project Requirements</h2>
                        <p class="mt-10"><strong>Project Requirements for Simple To-Do App</strong></p>

                        <p>[For doing this task, must watch the project's Live class.]</p>

                        <p class="mt-10"><strong>Add Task</strong></p>
                        <p>Allow users to add a new task by typing it in an input box and clicking a button.</p>
                        <p>Tasks should not be empty or contain only spaces.</p>

                        <p class="mt-10"><strong>Mark Task as Done/Undone</strong></p>
                        <p>Clicking on a task should mark it as "done" (with a line-through style) or undo it.</p>

                        <p class="mt-10"><strong>Delete Task</strong></p>
                        <p>Add a "Delete" button next to each task to remove it.</p>

                        <p class="mt-10"><strong>Save Tasks</strong></p>
                        <p>All tasks should be saved in a file (tasks.json) so they remain even after reloading the page.</p>

                        <p class="mt-10"><strong>Redirect After Action</strong></p>
                        <p>After adding, marking, or deleting a task, the page should reload automatically.</p>

                        <p class="mt-10"><strong>UI Design (If you don't know HTML CSS please follow project's live class)</strong></p>
                        <p>Create a simple and clean layout for the app.</p>
                        <p>Use basic CSS or a library like Milligram for styling.</p>

                        <p class="mt-10"><strong>File Handling in PHP</strong></p>
                        <p>Use PHP to handle adding, deleting, and marking tasks.</p>
                        <p>Save and load tasks from the tasks.json file.</p>

                        <p class="mt-10"><strong>Security</strong></p>
                        <p>Use htmlspecialchars to avoid security issues like XSS.</p>
                    </div>
                </div>

                <div class="bg-white ct-flex-column ct-wrapper radius-10 p-20" style="flex: auto;">
                    <h2 class="ct-heading ct-heading-2">Task List</h2>
                    <div class="ct-width-full ct-flex-row ct-heading-2 bg-blue p-10" style="margin-bottom: -20px;">
                        <p class="task-id task-table-header text-white">Task ID</p>
                        <p class="task-name task-table-header text-white">Task Name</p>
                        <p class="task-status task-table-header text-white">Task Status</p>
                        <p class="task-update task-table-header text-white">Status Update</p>
                        <p class="task-delete task-table-header text-white">Delete</p>
                    </div>
                    <div class="task-container">
                        <?php
                       $data = json_decode(file_get_contents(TASK_PATH), true);

                       $tasks = array_reverse($data);
                
                        if (empty($tasks)) { ?>
                            <p class="ct-width-full p-20" style="text-align: center; font-weight: 600; font-size: 20px;">Taskbox is empty. Please add a task through the task form first.</p>
                        <?php  } else { ?>
                            <?php
                            foreach ($tasks as $task) { ?>
                                <div class="task-item ct-width-full ct-flex-row ct-task-table-repeat <?php echo $task['done'] ? 'task-done' : ''; ?>">
                                    <div class="task-id task-table-header"><?php echo htmlspecialchars($task["taskID"]); ?></div>
                                    <div class="task-name"><?php echo htmlspecialchars($task["task"]); ?></div>
                                    <div class="task-status">
                                        <?php if ($task["done"]) : ?>
                                            <span class="text-green">Completed</span>
                                        <?php else : ?>
                                            <span class="text-red">Pending</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="task-update">
                                        <form action="" method="post" class="ct-width-full">
                                            <input type="hidden" name="action" value="toggle">
                                            <input type="hidden" name="taskID" value="<?php echo $task['taskID']; ?>">
                                            <button type="submit" class="ct-action-button ct-width-full p-12 radius-5 <?php echo $task['done'] ? 'ct-action-button-danger' : ''; ?>">
                                                <?php echo $task["done"] ? "Mark Pending" : "Mark Completed"; ?>
                                            </button>
                                        </form>

                                    </div>
                                    <div class="task-delete">
                                        <form action="" method="post" class="ct-width-full">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="taskID" value="<?php echo $task['taskID']; ?>">
                                            <button class="ct-action-button ct-width-full p-12 bg-red border-none text-white radius-5" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>