<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$message_type = "";


// Get Mark ID
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: marks.php");
    exit();
}

$id = intval($_GET["id"]);


// Fetch Existing Mark
$sql = "SELECT * FROM marks WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: marks.php");
    exit();
}

$mark = $result->fetch_assoc();

$stmt->close();


// Update Mark
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = intval($_POST["student_id"]);
    $course_id = intval($_POST["course_id"]);
    $marks = floatval($_POST["marks"]);


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


    $update_sql = "UPDATE marks
                   SET student_id = ?,
                       course_id = ?,
                       marks = ?,
                       grade = ?
                   WHERE id = ?";

    $update_stmt = $conn->prepare($update_sql);

    $update_stmt->bind_param(
        "iidsi",
        $student_id,
        $course_id,
        $marks,
        $grade,
        $id
    );


    if ($update_stmt->execute()) {

        $message = "Marks information updated successfully.";
        $message_type = "success";

        // Refresh displayed values
        $mark["student_id"] = $student_id;
        $mark["course_id"] = $course_id;
        $mark["marks"] = $marks;
        $mark["grade"] = $grade;

    } elseif ($update_stmt->errno == 1062) {

        $message = "Marks already exist for this student and course.";
        $message_type = "error";

    } else {

        $message = "Unable to update marks information.";
        $message_type = "error";
    }

    $update_stmt->close();
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

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        Edit Marks | Student Performance Management System
    </title>

    <link rel="stylesheet"
          href="style.css">

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

                <span class="topbar-separator">
                    /
                </span>

                <span class="topbar-current">
                    Edit Marks
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



        <!-- ================= PAGE ================= -->

        <section class="edit-marks-container">


            <!-- PAGE HEADING -->

            <div class="edit-marks-heading">

                <div>

                    <span class="page-eyebrow">
                        Academic Records
                    </span>

                    <h1>
                        Edit Marks
                    </h1>

                    <p>
                        Update academic performance records and
                        automatically recalculate the grade.
                    </p>

                </div>


                <a href="marks.php"
                   class="back-page-link">

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

                        <?php
                        echo htmlspecialchars($message);
                        ?>

                    </span>

                </div>

            <?php endif; ?>



            <!-- ================= MAIN LAYOUT ================= -->

            <div class="marks-edit-layout">


                <!-- ================= LEFT PANEL ================= -->

                <div class="marks-edit-info-panel">


                    <div class="marks-edit-info-top">


                        <div class="large-marks-edit-icon">

                            <i class="fa-solid fa-pen-to-square"></i>

                        </div>


                        <span class="marks-edit-info-label">
                            Marks Record
                        </span>


                        <h2>
                            Update academic performance accurately.
                        </h2>


                        <p>
                            Modify the student, course or marks record.
                            The system automatically updates the grade
                            based on the new marks.
                        </p>

                    </div>



                    <!-- FEATURES -->

                    <div class="marks-edit-info-features">


                        <div class="marks-edit-info-feature">

                            <div class="marks-edit-feature-check">

                                <i class="fa-solid fa-user-graduate"></i>

                            </div>


                            <div>

                                <strong>
                                    Student Record
                                </strong>

                                <span>
                                    Select the correct student.
                                </span>

                            </div>

                        </div>



                        <div class="marks-edit-info-feature">

                            <div class="marks-edit-feature-check">

                                <i class="fa-solid fa-book-open"></i>

                            </div>


                            <div>

                                <strong>
                                    Course Record
                                </strong>

                                <span>
                                    Update the relevant course.
                                </span>

                            </div>

                        </div>



                        <div class="marks-edit-info-feature">

                            <div class="marks-edit-feature-check">

                                <i class="fa-solid fa-ranking-star"></i>

                            </div>


                            <div>

                                <strong>
                                    Automatic Grading
                                </strong>

                                <span>
                                    Grade changes automatically with marks.
                                </span>

                            </div>

                        </div>

                    </div>



                    <!-- FOOTER -->

                    <div class="marks-edit-info-panel-footer">

                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Academic records are securely maintained
                            in the system.
                        </span>

                    </div>

                </div>



                <!-- ================= FORM PANEL ================= -->

                <div class="marks-edit-form-panel">


                    <div class="marks-edit-form-heading">

                        <div>

                            <span>
                                MARKS INFORMATION
                            </span>

                            <h2>
                                Update Performance Details
                            </h2>

                        </div>


                        <div class="required-note">
                            * Required
                        </div>

                    </div>



                    <form method="POST"
                          action=""
                          class="modern-marks-edit-form">


                        <!-- STUDENT -->

                        <div class="modern-marks-edit-form-group">

                            <label>
                                Student
                                <span>*</span>
                            </label>


                            <div class="modern-marks-edit-input-wrapper">

                                <div class="modern-marks-edit-input-icon">

                                    <i class="fa-solid fa-user-graduate"></i>

                                </div>


                                <select
                                    name="student_id"
                                    class="modern-marks-edit-input"
                                    required
                                >

                                    <?php

                                    while (
                                        $student =
                                        $students_result->fetch_assoc()
                                    ):

                                    ?>

                                        <option
                                            value="<?php echo $student["id"]; ?>"

                                            <?php
                                            if (
                                                $student["id"]
                                                ==
                                                $mark["student_id"]
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $student["student_id"]
                                                . " - "
                                                . $student["full_name"]
                                            );

                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <small>
                                Select the student for this academic record.
                            </small>

                        </div>



                        <!-- COURSE -->

                        <div class="modern-marks-edit-form-group">

                            <label>
                                Course
                                <span>*</span>
                            </label>


                            <div class="modern-marks-edit-input-wrapper">

                                <div class="modern-marks-edit-input-icon">

                                    <i class="fa-solid fa-book-open"></i>

                                </div>


                                <select
                                    name="course_id"
                                    class="modern-marks-edit-input"
                                    required
                                >

                                    <?php

                                    while (
                                        $course =
                                        $courses_result->fetch_assoc()
                                    ):

                                    ?>

                                        <option
                                            value="<?php echo $course["id"]; ?>"

                                            <?php
                                            if (
                                                $course["id"]
                                                ==
                                                $mark["course_id"]
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >

                                            <?php

                                            echo htmlspecialchars(
                                                $course["course_code"]
                                                . " - "
                                                . $course["course_name"]
                                            );

                                            ?>

                                        </option>

                                    <?php endwhile; ?>

                                </select>

                            </div>


                            <small>
                                Select the course associated with this record.
                            </small>

                        </div>



                        <!-- MARKS -->

                        <div class="modern-marks-edit-form-group">

                            <label>
                                Marks
                                <span>*</span>
                            </label>


                            <div class="modern-marks-edit-input-wrapper">

                                <div class="modern-marks-edit-input-icon">

                                    <i class="fa-solid fa-percent"></i>

                                </div>


                                <input
                                    type="number"
                                    name="marks"
                                    id="editMarksInput"
                                    class="modern-marks-edit-input"
                                    value="<?php echo htmlspecialchars($mark["marks"]); ?>"
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

                        <div class="marks-edit-grade-preview">


                            <div class="marks-edit-grade-preview-icon">

                                <i class="fa-solid fa-award"></i>

                            </div>


                            <div>

                                <span>
                                    AUTOMATIC GRADE
                                </span>


                                <strong id="editGradePreview">
                                    <?php echo htmlspecialchars($mark["grade"]); ?>
                                </strong>

                            </div>

                        </div>



                        <!-- ACTIONS -->

                        <div class="modern-marks-edit-form-actions">


                            <a href="marks.php"
                               class="modern-marks-edit-cancel-button">

                                <i class="fa-solid fa-xmark"></i>

                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="modern-marks-edit-submit-button"
                            >

                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Changes

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </section>

    </main>

</div>



<!-- ================= GRADE PREVIEW ================= -->

<script>

const editMarksInput =
    document.getElementById("editMarksInput");

const editGradePreview =
    document.getElementById("editGradePreview");


function calculateGrade(marks) {

    if (marks >= 85) {
        return "A+";
    }

    if (marks >= 75) {
        return "A";
    }

    if (marks >= 70) {
        return "A-";
    }

    if (marks >= 65) {
        return "B+";
    }

    if (marks >= 60) {
        return "B";
    }

    if (marks >= 55) {
        return "B-";
    }

    if (marks >= 50) {
        return "C+";
    }

    if (marks >= 45) {
        return "C";
    }

    if (marks >= 40) {
        return "C-";
    }

    if (marks >= 35) {
        return "D";
    }

    return "F";
}


editMarksInput.addEventListener("input", function () {

    const marks = parseFloat(this.value);

    if (isNaN(marks)) {

        editGradePreview.textContent = "—";

        return;
    }

    editGradePreview.textContent =
        calculateGrade(marks);

});

</script>

</body>

</html>