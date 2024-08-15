<?php
include 'config.php'; // Include database connection

// Check if student ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid student ID.");
}

$student_id = intval($_GET['id']);

// Fetch student details
$sql = "
    SELECT s.id, s.name, s.email, s.address, s.image, s.class_id, c.name AS class_name
    FROM student s
    LEFT JOIN classes c ON s.class_id = c.class_id
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

// Fetch all classes for the dropdown
$classes = [];
$classes_sql = "SELECT class_id, name FROM classes";
$classes_result = $conn->query($classes_sql);

if ($classes_result !== false) {
    while ($row = $classes_result->fetch_assoc()) {
        $classes[] = $row;
    }
} else {
    die("Failed to fetch classes: " . $conn->error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $selected_class_id = isset($_POST['class_id']) ? trim($_POST['class_id']) : null;
    $image = $_FILES['image'];

    $errors = [];
    if (empty($name)) {
        $errors[] = "Name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    if ($image['error'] == UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png'];
        if (!in_array($image['type'], $allowed_types)) {
            $errors[] = "Invalid image format. Only JPG and PNG are allowed.";
        }
    }

    if (empty($errors)) {
        // Handle image upload
        $image_name = $student['image'];
        if ($image['error'] == UPLOAD_ERR_OK) {
            $image_name = uniqid() . '-' . basename($image['name']);
            $upload_dir = 'uploads/';
            if (!move_uploaded_file($image['tmp_name'], $upload_dir . $image_name)) {
                $errors[] = "Failed to upload image.";
            }
        }

        // Update student information
        if (empty($errors)) {
            $update_sql = "
                UPDATE student
                SET name = ?, email = ?, address = ?, class_id = ?, image = ?
                WHERE id = ?
            ";

            $stmt_update = $conn->prepare($update_sql);
            $stmt_update->bind_param("sssssi", $name, $email, $address, $selected_class_id, $image_name, $student_id);

            if ($stmt_update->execute()) {
                header("Location: index.php"); // Redirect to home page
                exit();
            } else {
                $errors[] = "Database error: " . $stmt_update->error;
            }
        }
    }
}

include 'header.php'; // Include header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Edit Student</h1>

    <?php
    if (!empty($errors)) {
        echo '<ul class="errors">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>';
    }
    ?>

    <form action="edit.php?id=<?php echo htmlspecialchars($student_id); ?>" method="post" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($student['name']); ?>">
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>">
        </div>
        <div>
            <label for="address">Address:</label>
            <textarea id="address" name="address"><?php echo htmlspecialchars($student['address']); ?></textarea>
        </div>
        <div>
            <label for="class_id">Class:</label>
            <textarea id="class_id" name="class_id"><?php echo htmlspecialchars($student['Class_id']); ?></textarea>
            
        </div>
        <div>
            <label for="image">Image (JPG, PNG):</label>
            <input type="file" id="image" name="image" accept="image/jpeg, image/png">
        </div>
        <div>
            <button type="submit">Update</button>
        </div>
    </form>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>
