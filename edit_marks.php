<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";

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

if (!isset($_GET["id"])) {
    header("Location: marks.php");
    exit();
}

$id = intval($_GET["id"]);

/* Get existing mark */

$sql = "SELECT
            marks.id,
            marks.student_id,
            marks.course_id,
            marks.marks,
            marks.grade,
            students.student_id AS student_code,
            students.full_name,
            courses.course_code,
            courses.course_name
        FROM marks
        INNER JOIN students
            ON marks.student_id = students.id
        INNER JOIN courses
            ON marks.course_id = courses.id
        WHERE marks.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    header("Location: marks.php");
    exit();
}

$mark = $result->fetch_assoc();

$stmt->close();


/* Update mark */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $marks = floatval($_POST["marks"]);

    if ($marks < 0 || $marks > 100) {

        $message = "Marks must be between 0 and 100.";

    } else {

        $grade = calculateGrade($marks);

        $sql = "UPDATE marks
                SET marks = ?, grade = ?
                WHERE id = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "dsi",
            $marks,
            $grade,
            $id
        );

        if ($stmt->execute()) {

            header("Location: marks.php?updated=1");
            exit();

        } else {

            $message = "Error updating marks.";
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Edit Marks</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Edit Marks</h1>

    <a href="marks.php">← Back to Marks</a>

    <?php if (!empty($message)): ?>

        <p class="message">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php endif; ?>


    <form method="POST">

        <label>Student ID</label>

        <input
            type="text"
            value="<?php echo htmlspecialchars($mark["student_code"]); ?>"
            disabled
        >


        <label>Student Name</label>

        <input
            type="text"
            value="<?php echo htmlspecialchars($mark["full_name"]); ?>"
            disabled
        >


        <label>Course</label>

        <input
            type="text"
            value="<?php
                echo htmlspecialchars(
                    $mark["course_code"] .
                    " - " .
                    $mark["course_name"]
                );
            ?>"
            disabled
        >


        <label>Marks</label>

        <input
            type="number"
            name="marks"
            min="0"
            max="100"
            step="0.01"
            value="<?php echo htmlspecialchars($mark["marks"]); ?>"
            required
        >


        <button type="submit">
            Update Marks
        </button>

    </form>

</div>

</body>

</html>