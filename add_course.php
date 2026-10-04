<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_code = trim($_POST["course_code"]);
    $course_name = trim($_POST["course_name"]);
    $credits = $_POST["credits"];
    $semester = trim($_POST["semester"]);

    if (
        empty($course_code) ||
        empty($course_name) ||
        empty($credits) ||
        empty($semester)
    ) {

        $message = "Please fill all fields.";

    } else {

        $sql = "INSERT INTO courses
                (course_code, course_name, credits, semester)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssis",
            $course_code,
            $course_name,
            $credits,
            $semester
        );

        if ($stmt->execute()) {

            $message = "Course added successfully!";

        } else {

            if ($stmt->errno == 1062) {

                $message = "Course code already exists.";

            } else {

                $message = "Error: " . $stmt->error;

            }
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Course - SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Add New Course</h1>

    <p>Add a course to the system</p>

    <?php if (!empty($message)) { ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>


    <form method="POST">

        <label>Course Code</label>

        <input
            type="text"
            name="course_code"
            placeholder="Example: STAT 22651"
            required
        >


        <label>Course Name</label>

        <input
            type="text"
            name="course_name"
            placeholder="Enter course name"
            required
        >


        <label>Credits</label>

        <input
            type="number"
            name="credits"
            min="1"
            max="10"
            placeholder="Example: 3"
            required
        >


        <label>Semester</label>

        <select name="semester" required>

            <option value="">
                Select Semester
            </option>

            <option value="Semester I">
                Semester I
            </option>

            <option value="Semester II">
                Semester II
            </option>

        </select>


        <button type="submit">
            Add Course
        </button>

    </form>

    <br>

    <a href="courses.php">
        Back to Courses
    </a>

</div>

</body>

</html>