<?php

include "db.php";

$message = "";

if (!isset($_GET["id"])) {
    die("Student ID is missing.");
}

$id = $_GET["id"];

/* Get existing student details */

$sql = "SELECT * FROM students WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();

$stmt->close();


/* Update student */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST["student_id"];
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $course = $_POST["course"];
    $year = $_POST["year"];

    $sql = "UPDATE students
            SET student_id = ?,
                full_name = ?,
                email = ?,
                phone = ?,
                course = ?,
                year = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssssssi",
        $student_id,
        $full_name,
        $email,
        $phone,
        $course,
        $year,
        $id
    );

    if ($stmt->execute()) {

        $message = "Student updated successfully!";

        /* Reload updated data */

        $sql = "SELECT * FROM students WHERE id = ?";

        $stmt2 = $conn->prepare($sql);
        $stmt2->bind_param("i", $id);
        $stmt2->execute();

        $result = $stmt2->get_result();

        $student = $result->fetch_assoc();

        $stmt2->close();

    } else {

        if ($stmt->errno == 1062) {

            $message = "Student ID already exists.";

        } else {

            $message = "Error: " . $stmt->error;

        }
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Student</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h1>Edit Student</h1>

        <?php if ($message != ""): ?>

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>


        <form method="POST">

            <input
                type="text"
                name="student_id"
                value="<?php echo htmlspecialchars($student["student_id"]); ?>"
                placeholder="Student ID"
                required
            >

            <br><br>


            <input
                type="text"
                name="full_name"
                value="<?php echo htmlspecialchars($student["full_name"]); ?>"
                placeholder="Full Name"
                required
            >

            <br><br>


            <input
                type="email"
                name="email"
                value="<?php echo htmlspecialchars($student["email"]); ?>"
                placeholder="Email"
                required
            >

            <br><br>


            <input
                type="text"
                name="phone"
                value="<?php echo htmlspecialchars($student["phone"]); ?>"
                placeholder="Phone Number"
            >

            <br><br>


            <input
                type="text"
                name="course"
                value="<?php echo htmlspecialchars($student["course"]); ?>"
                placeholder="Course"
            >

            <br><br>


            <input
                type="number"
                name="year"
                value="<?php echo htmlspecialchars($student["year"]); ?>"
                placeholder="Year"
                min="1"
                max="4"
            >

            <br><br>


            <button type="submit">
                Update Student
            </button>

        </form>

        <br>

        <a href="students.php">
            Back to Student List
        </a>

    </div>

</body>

</html>