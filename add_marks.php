<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST["student_id"];
    $course_id = $_POST["course_id"];
    $marks = $_POST["marks"];

    // Calculate Grade
    if ($marks >= 85) {
        $grade = "A+";
    } elseif ($marks >= 75) {
        $grade = "A";
    } elseif ($marks >= 70) {
        $grade = "A-";
    } elseif ($marks >= 65) {
        $grade = "B+";
    } elseif ($marks >= 60) {
        $grade = "B";
    } elseif ($marks >= 55) {
        $grade = "B-";
    } elseif ($marks >= 50) {
        $grade = "C+";
    } elseif ($marks >= 45) {
        $grade = "C";
    } elseif ($marks >= 40) {
        $grade = "C-";
    } elseif ($marks >= 35) {
        $grade = "D";
    } else {
        $grade = "F";
    }

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

        $message = "Marks added successfully.";
        $message_type = "success";

    } elseif ($stmt->errno == 1062) {

        $message = "Marks already exist for this student and course.";
        $message_type = "error";

    } else {

        $message = "Something went wrong. Please try again.";
        $message_type = "error";
    }

    $stmt->close();
}


// Get Students
$students_sql = "SELECT id, student_id, full_name
                 FROM students
                 ORDER BY student_id ASC";

$students_result = $conn->query($students_sql);


// Get Courses
$courses_sql = "SELECT id, course_code, course_name
                FROM courses
                ORDER BY course_code ASC";

$courses_result = $conn->query($courses_sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Marks | Student Performance Management System</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body>

<div class="dashboard-page">

    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

        <div class="sidebar-logo">

            <div class="sidebar-logo-icon">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>

            <div>
                <h2>SPMS</h2>
                <span>Performance System</span>
            </div>

        </div>


        <nav class="sidebar-menu">

            <a href="dashboard.php">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="students.php">
                <i class="fa-solid fa-users"></i>
                <span>Students</span>
            </a>

            <a href="courses.php">
                <i class="fa-solid fa-book"></i>
                <span>Courses</span>
            </a>

            <a href="marks.php" class="active">
                <i class="fa-solid fa-file-pen"></i>
                <span>Marks</span>
            </a>

            <a href="gpa.php">
                <i class="fa-solid fa-calculator"></i>
                <span>GPA Calculator</span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <a href="logout.php" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </div>

    </aside>


    <!-- ================= MAIN CONTENT ================= -->

    <main class="main-content">


        <!-- TOPBAR -->

        <header class="topbar">

            <div class="topbar-left">

                <span class="topbar-section">
                    Marks Management
                </span>

                <span class="topbar-separator">/</span>

                <span class="topbar-current">
                    Add Marks
                </span>

            </div>


            <div class="topbar-user">

                <div class="topbar-user-info">

                    <strong>
                        <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    </strong>

                    <span>
                        <?php echo htmlspecialchars($_SESSION["role"] ?? "User"); ?>
                    </span>

                </div>

                <div class="topbar-avatar">
                    <i class="fa-solid fa-user"></i>
                </div>

            </div>

        </header>


        <!-- PAGE CONTENT -->

        <section class="add-marks-container">


            <!-- PAGE HEADING -->

            <div class="add-marks-heading">

                <div>

                    <span class="page-eyebrow">
                        Academic Records
                    </span>

                    <h1>Add Student Marks</h1>

                    <p>
                        Record academic marks and automatically assign the
                        appropriate grade for each course.
                    </p>

                </div>


                <a href="marks.php" class="back-page-link">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back to Marks
                </a>

            </div>


            <!-- ALERT -->

            <?php if ($message != ""): ?>

                <div class="modern-alert <?php echo $message_type; ?>">

                    <?php if ($message_type == "success"): ?>

                        <i class="fa-solid fa-circle-check"></i>

                    <?php else: ?>

                        <i class="fa-solid fa-circle-exclamation"></i>

                    <?php endif; ?>

                    <span>
                        <?php echo htmlspecialchars($message); ?>
                    </span>

                </div>

            <?php endif; ?>


            <!-- CREATE MARKS LAYOUT -->

            <div class="marks-create-layout">


                <!-- ================= LEFT INFO PANEL ================= -->

                <div class="marks-info-panel">

                    <div class="marks-info-top">

                        <div class="large-marks-icon">

                            <i class="fa-solid fa-file-circle-plus"></i>

                        </div>

                        <span class="marks-info-label">
                            Marks Record
                        </span>

                        <h2>
                            Record a student's academic performance.
                        </h2>

                        <p>
                            Select a student, choose the relevant course,
                            and enter the marks obtained. The system will
                            automatically calculate the grade.
                        </p>

                    </div>


                    <div class="marks-info-features">

                        <div class="marks-info-feature">

                            <div class="marks-feature-check">
                                <i class="fa-solid fa-user-graduate"></i>
                            </div>

                            <div>
                                <strong>Student Selection</strong>
                                <span>
                                    Connect marks to the correct student.
                                </span>
                            </div>

                        </div>


                        <div class="marks-info-feature">

                            <div class="marks-feature-check">
                                <i class="fa-solid fa-book-open"></i>
                            </div>

                            <div>
                                <strong>Course Selection</strong>
                                <span>
                                    Assign marks to a registered course.
                                </span>
                            </div>

                        </div>


                        <div class="marks-info-feature">

                            <div class="marks-feature-check">
                                <i class="fa-solid fa-ranking-star"></i>
                            </div>

                            <div>
                                <strong>Automatic Grading</strong>
                                <span>
                                    Grade is calculated automatically.
                                </span>
                            </div>

                        </div>

                    </div>


                    <div class="marks-info-panel-footer">

                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Academic records are securely stored
                            in the system.
                        </span>

                    </div>

                </div>


                <!-- ================= RIGHT FORM ================= -->

                <div class="marks-form-panel">


                    <div class="marks-form-heading">

                        <div>

                            <span>
                                MARKS INFORMATION
                            </span>

                            <h2>
                                Enter Performance Details
                            </h2>

                        </div>

                        <div class="required-note">
                            * Required
                        </div>

                    </div>


                    <form method="POST"
                          action=""
                          class="modern-marks-form">


                        <!-- STUDENT -->

                        <div class="modern-marks-form-group">

                            <label>
                                Student
                                <span>*</span>
                            </label>

                            <div class="modern-marks-input-wrapper">

                                <div class="modern-marks-input-icon">
                                    <i class="fa-solid fa-user-graduate"></i>
                                </div>

                                <select
                                    name="student_id"
                                    class="modern-marks-input"
                                    required
                                >

                                    <option value="">
                                        Select Student
                                    </option>

                                    <?php while ($student = $students_result->fetch_assoc()): ?>

                                        <option value="<?php echo $student["id"]; ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $student["student_id"] .
                                                " - " .
                                                $student["full_name"]
                                            );
                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <small>
                                Choose the student who received these marks.
                            </small>

                        </div>


                        <!-- COURSE -->

                        <div class="modern-marks-form-group">

                            <label>
                                Course
                                <span>*</span>
                            </label>

                            <div class="modern-marks-input-wrapper">

                                <div class="modern-marks-input-icon">
                                    <i class="fa-solid fa-book-open"></i>
                                </div>

                                <select
                                    name="course_id"
                                    class="modern-marks-input"
                                    required
                                >

                                    <option value="">
                                        Select Course
                                    </option>

                                    <?php while ($course = $courses_result->fetch_assoc()): ?>

                                        <option value="<?php echo $course["id"]; ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $course["course_code"] .
                                                " - " .
                                                $course["course_name"]
                                            );
                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>

                            <small>
                                Select the course for this academic record.
                            </small>

                        </div>


                        <!-- MARKS -->

                        <div class="modern-marks-form-group">

                            <label>
                                Marks
                                <span>*</span>
                            </label>

                            <div class="modern-marks-input-wrapper">

                                <div class="modern-marks-input-icon">
                                    <i class="fa-solid fa-percent"></i>
                                </div>

                                <input
                                    type="number"
                                    name="marks"
                                    id="marksInput"
                                    class="modern-marks-input"
                                    placeholder="Enter marks (0 - 100)"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    required
                                >

                            </div>

                            <small>
                                Enter a value between 0 and 100.
                            </small>

                        </div>


                        <!-- GRADE PREVIEW -->

                        <div class="marks-grade-preview">

                            <div class="marks-grade-preview-icon">
                                <i class="fa-solid fa-award"></i>
                            </div>

                            <div>

                                <span>
                                    AUTOMATIC GRADE
                                </span>

                                <strong id="gradePreview">
                                    —
                                </strong>

                            </div>

                        </div>


                        <!-- ACTIONS -->

                        <div class="modern-marks-form-actions">

                            <a href="marks.php"
                               class="modern-marks-cancel-button">

                                <i class="fa-solid fa-xmark"></i>
                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="modern-marks-submit-button"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Add Marks

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </section>

    </main>

</div>


<!-- ================= GRADE PREVIEW SCRIPT ================= -->

<script>

const marksInput = document.getElementById("marksInput");
const gradePreview = document.getElementById("gradePreview");

marksInput.addEventListener("input", function () {

    const marks = parseFloat(this.value);

    if (isNaN(marks)) {
        gradePreview.textContent = "—";
        return;
    }

    let grade = "";

    if (marks >= 85) {
        grade = "A+";
    } else if (marks >= 75) {
        grade = "A";
    } else if (marks >= 70) {
        grade = "A-";
    } else if (marks >= 65) {
        grade = "B+";
    } else if (marks >= 60) {
        grade = "B";
    } else if (marks >= 55) {
        grade = "B-";
    } else if (marks >= 50) {
        grade = "C+";
    } else if (marks >= 45) {
        grade = "C";
    } else if (marks >= 40) {
        grade = "C-";
    } else if (marks >= 35) {
        grade = "D";
    } else {
        grade = "F";
    }

    gradePreview.textContent = grade;

});

</script>

</body>

</html>