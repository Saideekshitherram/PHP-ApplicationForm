<?php
include 'config.php'; // Include database connection

// Check if student ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid student ID.");
}

$student_id = intval($_GET['id']);

// Fetch student details along with class names
$sql = "
    SELECT s.name, s.email, s.address, s.image, s.created_at, c.name AS class_name
    FROM student s
    LEFT JOIN student_classes sc ON s.id = sc.student_id
    LEFT JOIN classes c ON sc.class_id = c.class_id
    WHERE s.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if student exists
if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

// Fetch associated classes for the student
$classes_sql = "
    SELECT c.name
    FROM student_classes sc
    JOIN classes c ON sc.class_id = c.class_id
    WHERE sc.student_id = ?
";

$classes_stmt = $conn->prepare($classes_sql);
$classes_stmt->bind_param("i", $student_id);
$classes_stmt->execute();
$classes_result = $classes_stmt->get_result();

$classes = [];
while ($row = $classes_result->fetch_assoc()) {
    $classes[] = $row['name'];
}

include 'header.php'; // Include header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Student Details</h1>

    <div>
        <h2><?php echo htmlspecialchars($student['name']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($student['email']); ?></p>
        <p><strong>Address:</strong> <?php echo htmlspecialchars($student['address']); ?></p>
        <p><strong>Creation Date:</strong> <?php echo htmlspecialchars($student['created_at']); ?></p>

        <?php if (!empty($classes)) { ?>
            <p><strong>Classes:</strong> <?php echo implode(', ', $classes); ?></p>
        <?php } else { ?>
            <p>No classes assigned.</p>
        <?php } ?>

        <?php if (!empty($student['image'])) { ?>
            <p><strong>Image:</strong><br>
            <img src="uploads/<?php echo htmlspecialchars($student['image']); ?>" alt="Student Image" style="max-width: 200px;"></p>
        <?php } else { ?>
            <p>No image available.</p>
        <?php } ?>
    </div>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>
