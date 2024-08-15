<?php
include 'config.php'; // Include database connection

// Handle form submission for adding a new student
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_student'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $class_id = trim($_POST['class_id']); // Handle class_id as text
    
    // Check if the image was uploaded
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_file = $_FILES['image'];
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        $file_extension = pathinfo($image_file['name'], PATHINFO_EXTENSION);

        // Validate image format
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            // Generate a unique filename to avoid overwriting
            $unique_filename = uniqid() . '.' . $file_extension;
            $target_path = 'uploads/' . $unique_filename;

            // Move the uploaded file to the uploads directory
            if (move_uploaded_file($image_file['tmp_name'], $target_path)) {
                $image_path = $unique_filename;
            } else {
                die("Failed to move uploaded file.");
            }
        } else {
            die("Invalid image format. Only jpg and png files are allowed.");
        }
    }

    // Insert new student into the database
    if (!empty($name) && !empty($email) && !empty($class_id)) {
        $insert_sql = "INSERT INTO student (name, email, address, class_id, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sssss", $name, $email, $address, $class_id, $image_path);
        if ($stmt->execute()) {
            header("Location: index.php"); // Redirect to avoid resubmission
            exit();
        } else {
            die("Failed to add student: " . $stmt->error);
        }
    } else {
        $error = "Name, email, and class_id are required.";
    }
}

// Fetch classes for the dropdown
// Note: This is no longer needed if you're using a text field, so it has been removed.

include 'header.php'; // Include header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Add New Student</h1>

    <?php if (isset($error)) { ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php } ?>

    <form action="create.php" method="post" enctype="multipart/form-data">
        <div>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>
        <div>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div>
            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>
        </div>
        <div>
            <label for="class_id">Class ID:</label>
            <input type="text" id="class_id" name="class_id" required>
        </div>
        <div>
            <label for="image">Image (JPG, PNG):</label>
            <input type="file" id="image" name="image" accept="image/jpeg, image/png">
        </div>
        <div>
            <button type="submit" name="add_student">Add Student</button>
        </div>
    </form>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>
