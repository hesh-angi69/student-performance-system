<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_code = trim($_POST["course_code"]);
    $course_name = trim($_POST["course_name"]);
    $credits = intval($_POST["credits"]);
    $semester = trim($_POST["semester"]);

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

        $message = "Course has been added successfully.";
        $message_type = "success";

    } else {

        if ($stmt->errno == 1062) {

            $message = "Course code already exists. Please use a different course code.";
            $message_type = "error";

        } else {

            $message = "Error: " . $stmt->error;
            $message_type = "error";
        }
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Course | SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="app-layout">

    <!-- ================= SIDEBAR ================= -->

    <aside class="sidebar">

        <div class="sidebar-brand">

            <div class="brand-logo">
                S
            </div>

            <div>
                <h2>SPMS</h2>
                <p>Student Performance</p>
            </div>

        </div>


        <nav class="sidebar-nav">

            <a href="dashboard.php">
                <span class="nav-icon">⌂</span>
                <span>Dashboard</span>
            </a>

            <a href="students.php">
                <span class="nav-icon">♙</span>
                <span>Students</span>
            </a>

            <a href="courses.php" class="active">
                <span class="nav-icon">▣</span>
                <span>Courses</span>
            </a>

            <a href="marks.php">
                <span class="nav-icon">▤</span>
                <span>Marks</span>
            </a>

            <a href="gpa.php">
                <span class="nav-icon">∑</span>
                <span>GPA Calculation</span>
            </a>

            <div class="nav-section-title">
                MANAGEMENT
            </div>

            <a href="#">
                <span class="nav-icon">◷</span>
                <span>Attendance</span>
            </a>

            <a href="#">
                <span class="nav-icon">▥</span>
                <span>Reports</span>
            </a>

            <a href="#">
                <span class="nav-icon">⚙</span>
                <span>Settings</span>
            </a>

        </nav>


        <div class="sidebar-bottom">

            <div class="sidebar-user">

                <div class="sidebar-user-avatar">
                    <?php
                    echo strtoupper(
                        substr($_SESSION["username"], 0, 1)
                    );
                    ?>
                </div>

                <div class="sidebar-user-info">

                    <strong>
                        <?php
                        echo htmlspecialchars($_SESSION["username"]);
                        ?>
                    </strong>

                    <span>
                        <?php
                        echo htmlspecialchars($_SESSION["role"]);
                        ?>
                    </span>

                </div>

            </div>


            <a href="logout.php" class="logout-link">
                <span>↪</span>
                Logout
            </a>

        </div>

    </aside>


    <!-- ================= MAIN CONTENT ================= -->

    <main class="main-content">


        <!-- TOP BAR -->

        <header class="topbar">

            <div class="topbar-left">

                <span class="topbar-section">
                    Course Management
                </span>

                <span class="topbar-separator">
                    /
                </span>

                <span class="topbar-current">
                    Add Course
                </span>

            </div>


            <div class="topbar-right">

                <div class="topbar-user">

                    <div class="topbar-avatar">

                        <?php
                        echo strtoupper(
                            substr($_SESSION["username"], 0, 1)
                        );
                        ?>

                    </div>

                    <div class="topbar-user-info">

                        <strong>
                            <?php
                            echo htmlspecialchars(
                                $_SESSION["username"]
                            );
                            ?>
                        </strong>

                        <span>
                            <?php
                            echo htmlspecialchars(
                                $_SESSION["role"]
                            );
                            ?>
                        </span>

                    </div>

                </div>

            </div>

        </header>


        <!-- ================= PAGE ================= -->

        <section class="add-course-container">


            <!-- PAGE HEADING -->

            <div class="add-course-heading">

                <div>

                    <a
                        href="courses.php"
                        class="modern-back-button"
                    >
                        <span>←</span>
                        Back to Courses
                    </a>

                    <h1>
                        Add New Course
                    </h1>

                    <p>
                        Create a new course in the academic course management system.
                    </p>

                </div>

            </div>


            <!-- ================= ALERT ================= -->

            <?php if ($message != ""): ?>

                <div
                    class="modern-alert
                    <?php echo $message_type; ?>"
                >

                    <div class="modern-alert-icon">

                        <?php
                        echo $message_type == "success"
                            ? "✓"
                            : "!";
                        ?>

                    </div>

                    <div class="modern-alert-content">

                        <strong>

                            <?php
                            echo $message_type == "success"
                                ? "Course Added"
                                : "Unable to Add Course";
                            ?>

                        </strong>

                        <span>
                            <?php
                            echo htmlspecialchars($message);
                            ?>
                        </span>

                    </div>

                </div>

            <?php endif; ?>


            <!-- ================= MAIN FORM LAYOUT ================= -->

            <div class="course-create-layout">


                <!-- LEFT INFORMATION PANEL -->

                <div class="course-info-panel">

                    <div class="course-info-top">

                        <div class="large-course-icon">
                            ▣
                        </div>

                        <div class="course-info-label">
                            COURSE RECORD
                        </div>

                        <h2>
                            Create a new
                            course profile
                        </h2>

                        <p>
                            Add accurate course information
                            to organize academic records and
                            student performance data.
                        </p>

                    </div>


                    <div class="course-info-features">


                        <div class="course-info-feature">

                            <div class="course-feature-check">
                                ✓
                            </div>

                            <div>

                                <strong>
                                    Course Identification
                                </strong>

                                <span>
                                    Code and course name
                                </span>

                            </div>

                        </div>


                        <div class="course-info-feature">

                            <div class="course-feature-check">
                                ✓
                            </div>

                            <div>

                                <strong>
                                    Credit Information
                                </strong>

                                <span>
                                    Define course credit value
                                </span>

                            </div>

                        </div>


                        <div class="course-info-feature">

                            <div class="course-feature-check">
                                ✓
                            </div>

                            <div>

                                <strong>
                                    Semester Organization
                                </strong>

                                <span>
                                    Assign the correct semester
                                </span>

                            </div>

                        </div>

                    </div>


                    <div class="course-info-panel-footer">

                        <span class="course-info-footer-icon">
                            i
                        </span>

                        <p>
                            Make sure the Course Code is unique
                            before submitting the form.
                        </p>

                    </div>

                </div>


                <!-- RIGHT FORM PANEL -->

                <div class="course-form-panel">


                    <div class="course-form-heading">

                        <div>

                            <span>
                                COURSE INFORMATION
                            </span>

                            <h2>
                                Course Details
                            </h2>

                        </div>

                        <div class="course-required-label">
                            * Required
                        </div>

                    </div>


                    <form
                        method="POST"
                        class="modern-course-form"
                    >


                        <!-- COURSE CODE -->

                        <div class="modern-course-form-group">

                            <label for="course_code">
                                Course Code
                                <span>*</span>
                            </label>

                            <div class="modern-course-input">

                                <div class="modern-course-input-icon">
                                    ID
                                </div>

                                <input
                                    type="text"
                                    id="course_code"
                                    name="course_code"
                                    placeholder="e.g. STAT 22651"
                                    value="<?php
                                    echo isset($_POST["course_code"])
                                        ? htmlspecialchars(
                                            $_POST["course_code"]
                                        )
                                        : "";
                                    ?>"
                                    required
                                >

                            </div>

                            <small>
                                Enter a unique course code.
                            </small>

                        </div>


                        <!-- COURSE NAME -->

                        <div class="modern-course-form-group">

                            <label for="course_name">
                                Course Name
                                <span>*</span>
                            </label>

                            <div class="modern-course-input">

                                <div class="modern-course-input-icon">
                                    C
                                </div>

                                <input
                                    type="text"
                                    id="course_name"
                                    name="course_name"
                                    placeholder="e.g. Statistical Programming"
                                    value="<?php
                                    echo isset($_POST["course_name"])
                                        ? htmlspecialchars(
                                            $_POST["course_name"]
                                        )
                                        : "";
                                    ?>"
                                    required
                                >

                            </div>

                            <small>
                                Enter the official course name.
                            </small>

                        </div>


                        <!-- CREDITS + SEMESTER -->

                        <div class="modern-course-form-row">


                            <!-- CREDITS -->

                            <div class="modern-course-form-group">

                                <label for="credits">
                                    Credits
                                    <span>*</span>
                                </label>

                                <div class="modern-course-input">

                                    <div class="modern-course-input-icon">
                                        Cr
                                    </div>

                                    <input
                                        type="number"
                                        id="credits"
                                        name="credits"
                                        placeholder="e.g. 3"
                                        min="1"
                                        max="10"
                                        value="<?php
                                        echo isset($_POST["credits"])
                                            ? htmlspecialchars(
                                                $_POST["credits"]
                                            )
                                            : "";
                                        ?>"
                                        required
                                    >

                                </div>

                                <small>
                                    Number of academic credits.
                                </small>

                            </div>


                            <!-- SEMESTER -->

                            <div class="modern-course-form-group">

                                <label for="semester">
                                    Semester
                                    <span>*</span>
                                </label>

                                <div class="modern-course-input">

                                    <div class="modern-course-input-icon">
                                        S
                                    </div>

                                    <select
                                        id="semester"
                                        name="semester"
                                        required
                                    >

                                        <option
                                            value=""
                                        >
                                            Select semester
                                        </option>

                                        <option
                                            value="Semester I"
                                            <?php
                                            if (
                                                isset($_POST["semester"]) &&
                                                $_POST["semester"] ==
                                                "Semester I"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Semester I
                                        </option>

                                        <option
                                            value="Semester II"
                                            <?php
                                            if (
                                                isset($_POST["semester"]) &&
                                                $_POST["semester"] ==
                                                "Semester II"
                                            ) {
                                                echo "selected";
                                            }
                                            ?>
                                        >
                                            Semester II
                                        </option>

                                    </select>

                                </div>

                                <small>
                                    Select the academic semester.
                                </small>

                            </div>

                        </div>


                        <!-- ACTIONS -->

                        <div class="modern-course-form-actions">

                            <a
                                href="courses.php"
                                class="modern-course-cancel-button"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="modern-course-submit-button"
                            >
                                <span>+</span>
                                Add Course
                            </button>

                        </div>


                    </form>

                </div>

            </div>

        </section>

    </main>

</div>

</body>

</html>