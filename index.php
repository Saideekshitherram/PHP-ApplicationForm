<?php
include 'config.php'; // Include database connection

// Fetch students and their classes
$students_sql = "
    SELECT s.name, s.email, s.address, s.class_id, s.image, c.name AS class_name
    FROM student s
    LEFT JOIN classes c ON s.class_id = c.class_id
";
$students_result = $conn->query($students_sql);

include 'header.php'; // Include header
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <h1>Student List</h1>

    <?php if ($students_result->num_rows > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Class</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $students_result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['class_name']); // Display class name ?></td>
                        <td>
                            <?php if ($row['image']) { ?>
                                <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Student Image" width="100">
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No students found.</p>
    <?php } ?>

<?php include 'footer.php'; // Include footer ?>
</body>
</html>
