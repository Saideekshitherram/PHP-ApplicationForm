<?php
include 'config.php'; // Include database connection

// Check if student ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid student ID.");
}

$student_id = intval($_GET['id']);

// Fetch student details to get image path
$sql = "SELECT image FROM student WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();
$image_path = 'uploads/' . $student['image'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Delete the student record
    $delete_student_sql = "DELETE FROM student WHERE id = ?";
    $stmt_delete = $conn->prepare($delete_student_sql);
    $stmt_delete->bind_param("i", $student_id);

    if ($stmt_delete->execute()) {
        // Delete associated classes
        $delete_classes_sql = "DELETE FROM student_classes WHERE student_id = ?";
        $stmt_delete_classes = $conn->prepare($delete_classes_sql);
        $stmt_delete_classes->bind_param("i", $student_id);
        $stmt_delete_classes->execute();

        // Delete the image file
        if (!empty($student['image']) && file_exists($image_path)) {
            unlink($image_path);
        }

        header("Location: index.php"); // Redirect to home page
        exit();
    } else {
        die("Failed to delete student: " . $stmt_delete->error);
    }
}

include 'header.php'; // Include header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Deletion</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Confirm Deletion</h1>
    <p>Are you sure you want to delete the student with ID <?php echo htmlspecialchars($student_id); ?>?</p>

    <form action="delete.php?id=<?php echo htmlspecialchars($student_id); ?>" method="post">
        <button type="submit" name="confirm" value="yes">Delete</button>
        <a href="index.php">Cancel</a>
    </form>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>
