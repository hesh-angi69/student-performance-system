<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

if (!isset($_GET["id"])) {
    die("Course ID is missing.");
}

$id = $_GET["id"];

$message = "";


/* Get existing course */

$sql = "SELECT * FROM courses WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows != 1) {
    die("Course not found.");
}

$course = $result->fetch_assoc();

$stmt->close();


/* Update course */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_code = trim($_POST["course_code"]);
    $course_name = trim($_POST["course_name"]);
    $credits = $_POST["credits"];
    $semester = trim($_POST["semester"]);

    $sql = "UPDATE courses
            SET course_code = ?,
                course_name = ?,
                credits = ?,
                semester = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "ssisi",
        $course_code,
        $course_name,
        $credits,
        $semester,
        $id
    );

    if ($stmt->execute()) {

        $message = "Course updated successfully!";

        $course["course_code"] = $course_code;
        $course["course_name"] = $course_name;
        $course["credits"] = $credits;
        $course["semester"] = $semester;

    } else {

        if ($stmt->errno == 1062) {

            $message = "Course code already exists.";

        } else {

            $message = "Error updating course.";

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

    <title>Edit Course - SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Edit Course</h1>

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
            value="<?php echo htmlspecialchars($course["course_code"]); ?>"
            required
        >


        <label>Course Name</label>

        <input
            type="text"
            name="course_name"
            value="<?php echo htmlspecialchars($course["course_name"]); ?>"
            required
        >


        <label>Credits</label>

        <input
            type="number"
            name="credits"
            value="<?php echo htmlspecialchars($course["credits"]); ?>"
            min="1"
            max="10"
            required
        >


        <label>Semester</label>

        <select name="semester" required>

            <option
                value="Semester I"
                <?php if ($course["semester"] == "Semester I") echo "selected"; ?>
            >
                Semester I
            </option>

            <option
                value="Semester II"
                <?php if ($course["semester"] == "Semester II") echo "selected"; ?>
            >
                Semester II
            </option>

        </select>


        <button type="submit">
            Update Course
        </button>

    </form>

    <br>

    <a href="courses.php">
        Back to Courses
    </a>

</div>

</body>

</html>