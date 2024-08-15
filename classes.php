<?php
include 'config.php'; // Include database connection

// Handle adding a new class
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_class'])) {
    $class_name = trim($_POST['class_name']);

    if (!empty($class_name)) {
        $insert_sql = "INSERT INTO classes (name) VALUES (?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("s", $class_name);
        if ($stmt->execute()) {
            header("Location: classes.php"); // Redirect to avoid resubmission
            exit();
        } else {
            die("Failed to add class: " . $stmt->error);
        }
    } else {
        $error = "Class name is required.";
    }
}

// Handle editing a class
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $class_id = intval($_GET['edit']);
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_class'])) {
        $class_name = trim($_POST['class_name']);

        if (!empty($class_name)) {
            $update_sql = "UPDATE classes SET name = ? WHERE class_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $class_name, $class_id);
            if ($stmt->execute()) {
                header("Location: classes.php"); // Redirect to avoid resubmission
                exit();
            } else {
                die("Failed to update class: " . $stmt->error);
            }
        } else {
            $error = "Class name is required.";
        }
    } else {
        $class_query = "SELECT name FROM classes WHERE class_id = ?";
        $stmt = $conn->prepare($class_query);
        $stmt->bind_param("i", $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            die("Class not found.");
        }
        $class = $result->fetch_assoc();
    }
}

// Handle deleting a class
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $class_id = intval($_GET['delete']);
    $delete_sql = "DELETE FROM classes WHERE class_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $class_id);
    if ($stmt->execute()) {
        header("Location: classes.php"); // Redirect to avoid resubmission
        exit();
    } else {
        die("Failed to delete class: " . $stmt->error);
    }
}

// Fetch all classes
$classes_sql = "SELECT class_id, name FROM classes";
$classes_result = $conn->query($classes_sql);

include 'header.php'; // Include header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Manage Classes</h1>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <!-- Add New Class Form -->
    <h2>Add New Class</h2>
    <form action="classes.php" method="post">
        <div>
            <label for="class_name">Class Name:</label>
            <input type="text" id="class_name" name="class_name" required>
        </div>
        <div>
            <button type="submit" name="add_class">Add Class</button>
        </div>
    </form>

    <!-- Edit Class Form -->
    <?php if (isset($_GET['edit']) && is_numeric($_GET['edit'])) { ?>
        <h2>Edit Class</h2>
        <form action="classes.php?edit=<?php echo htmlspecialchars($class_id); ?>" method="post">
            <div>
                <label for="class_name">Class Name:</label>
                <input type="text" id="class_name" name="class_name" value="<?php echo htmlspecialchars($class['name']); ?>" required>
            </div>
            <div>
                <button type="submit" name="edit_class">Update Class</button>
            </div>
        </form>
    <?php } ?>

    <!-- List of Classes -->
    <h2>Class List</h2>
    <table>
        <thead>
            <tr>
                <th>Class ID</th>
                <th>Class Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($class = $classes_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($class['class_id']); ?></td>
                    <td><?php echo htmlspecialchars($class['name']); ?></td>
                    <td>
                        <a href="classes.php?edit=<?php echo htmlspecialchars($class['class_id']); ?>">Edit</a>
                        <a href="classes.php?delete=<?php echo htmlspecialchars($class['class_id']); ?>" onclick="return confirm('Are you sure you want to delete this class?');">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>
