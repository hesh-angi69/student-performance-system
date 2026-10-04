<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";


/* Grade calculation function */

function calculateGrade($marks)
{
    if ($marks >= 85) {
        return "A+";
    } elseif ($marks >= 75) {
        return "A";
    } elseif ($marks >= 70) {
        return "A-";
    } elseif ($marks >= 65) {
        return "B+";
    } elseif ($marks >= 60) {
        return "B";
    } elseif ($marks >= 55) {
        return "B-";
    } elseif ($marks >= 50) {
        return "C+";
    } elseif ($marks >= 45) {
        return "C";
    } elseif ($marks >= 40) {
        return "C-";
    } elseif ($marks >= 35) {
        return "D";
    } else {
        return "F";
    }
}


/* Add marks */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST["student_id"];
    $course_id = $_POST["course_id"];
    $marks = $_POST["marks"];

    if ($marks < 0 || $marks > 100) {

        $message = "Marks must be between 0 and 100.";

    } else {

        $grade = calculateGrade($marks);

        $sql = "INSERT INTO marks
                (student_id, course_id, marks, grade)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iids",
            $student_id,
            $course_id,
            $marks,
            $grade
        );

        if ($stmt->execute()) {

            $message = "Marks added successfully!";

        } else {

            if ($stmt->errno == 1062) {

                $message = "Marks already exist for this student and course.";

            } else {

                $message = "Error: " . $stmt->error;

            }
        }

        $stmt->close();
    }
}


/* Get students */

$studentQuery = "
    SELECT id, student_id, full_name
    FROM students
    ORDER BY full_name
";

$studentResult = $conn->query($studentQuery);


/* Get courses */

$courseQuery = "
    SELECT id, course_code, course_name
    FROM courses
    ORDER BY course_code
";

$courseResult = $conn->query($courseQuery);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Marks - SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Add Student Marks</h1>

    <p>Enter marks for a student and course</p>


    <?php if (!empty($message)) { ?>

        <div class="message">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php } ?>


    <form method="POST">

        <!-- Student -->

        <label>Select Student</label>

        <select name="student_id" required>

            <option value="">
                Select Student
            </option>

            <?php

            while ($student = $studentResult->fetch_assoc()) {

            ?>

                <option value="<?php echo $student["id"]; ?>">

                    <?php
                    echo htmlspecialchars(
                        $student["student_id"]
                        . " - "
                        . $student["full_name"]
                    );
                    ?>

                </option>

            <?php

            }

            ?>

        </select>


        <!-- Course -->

        <label>Select Course</label>

        <select name="course_id" required>

            <option value="">
                Select Course
            </option>

            <?php

            while ($course = $courseResult->fetch_assoc()) {

            ?>

                <option value="<?php echo $course["id"]; ?>">

                    <?php
                    echo htmlspecialchars(
                        $course["course_code"]
                        . " - "
                        . $course["course_name"]
                    );
                    ?>

                </option>

            <?php

            }

            ?>

        </select>


        <!-- Marks -->

        <label>Marks</label>

        <input
            type="number"
            name="marks"
            min="0"
            max="100"
            step="0.01"
            placeholder="Enter marks"
            required
        >


        <button type="submit">
            Save Marks
        </button>

    </form>

    <br>

    <a href="marks.php">
        View All Marks
    </a>

</div>

</body>

</html>