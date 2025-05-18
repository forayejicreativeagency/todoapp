<?php
session_start();

// Redirect if no message or type is set
if (!isset($_SESSION['message'], $_SESSION['type'])) {
    header("Location: index.php");
    exit;
}

$message = $_SESSION['message'];
$type = $_SESSION['type']; // e.g., add, update, delete

// Determine dynamic title
switch ($type) {
    case 'add':
        $pageTitle = "Task Added";
        break;
    case 'update':
        $pageTitle = "Task Updated";
        break;
    case 'delete':
        $pageTitle = "Task Deleted";
        break;
    case 'error':
        $pageTitle = "Something Wrong";
        break;
    default:
        $pageTitle = "Success";
}

unset($_SESSION['message'], $_SESSION['type']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="css/main.min.css" />
</head>

<body>
    <div class="success-box <?php echo htmlspecialchars($type ?? 'default'); ?>">
        <p class="task-note">
            <strong>✔️ <?php echo htmlspecialchars($pageTitle); ?>:</strong> <?php echo htmlspecialchars($message); ?>
        </p>
        <p>Redirecting back to task list...</p>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "/";
        }, 1000);
    </script>
</body>

</html>