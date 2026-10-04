<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$message_type = "";


// Get Course ID
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: courses.php");
    exit();
}

$id = intval($_GET["id"]);


// Fetch Course
$sql = "SELECT * FROM courses WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: courses.php");
    exit();
}

$course = $result->fetch_assoc();

$stmt->close();


// Update Course
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $course_code = trim($_POST["course_code"]);
    $course_name = trim($_POST["course_name"]);
    $credits = intval($_POST["credits"]);
    $semester = trim($_POST["semester"]);


    $update_sql = "UPDATE courses
                   SET course_code = ?,
                       course_name = ?,
                       credits = ?,
                       semester = ?
                   WHERE id = ?";

    $update_stmt = $conn->prepare($update_sql);

    $update_stmt->bind_param(
        "ssisi",
        $course_code,
        $course_name,
        $credits,
        $semester,
        $id
    );


    if ($update_stmt->execute()) {

        $message = "Course information updated successfully.";
        $message_type = "success";

        // Refresh displayed values
        $course["course_code"] = $course_code;
        $course["course_name"] = $course_name;
        $course["credits"] = $credits;
        $course["semester"] = $semester;

    } elseif ($update_stmt->errno == 1062) {

        $message = "This Course Code already exists.";
        $message_type = "error";

    } else {

        $message = "Unable to update course information.";
        $message_type = "error";
    }

    $update_stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Course | Student Performance Management System</title>

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

            <a href="courses.php" class="active">
                <i class="fa-solid fa-book"></i>
                <span>Courses</span>
            </a>

            <a href="marks.php">
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
                    Course Management
                </span>

                <span class="topbar-separator">/</span>

                <span class="topbar-current">
                    Edit Course
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



        <!-- ================= PAGE CONTENT ================= -->

        <section class="edit-course-container">


            <!-- PAGE HEADING -->

            <div class="edit-course-heading">

                <div>

                    <span class="page-eyebrow">
                        Course Management
                    </span>

                    <h1>Edit Course</h1>

                    <p>
                        Update course information and academic structure.
                    </p>

                </div>


                <a href="courses.php" class="back-page-link">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Courses

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



            <!-- ================= EDIT LAYOUT ================= -->

            <div class="course-edit-layout">


                <!-- LEFT INFO PANEL -->

                <div class="course-edit-info-panel">


                    <div class="course-edit-info-top">

                        <div class="large-course-edit-icon">

                            <i class="fa-solid fa-pen-to-square"></i>

                        </div>


                        <span class="course-edit-info-label">
                            Course Record
                        </span>


                        <h2>
                            Keep course information accurate and organized.
                        </h2>


                        <p>
                            Update course identification, credit information,
                            and semester details while keeping the academic
                            record consistent.
                        </p>

                    </div>



                    <!-- FEATURES -->

                    <div class="course-edit-info-features">


                        <div class="course-edit-info-feature">

                            <div class="course-edit-feature-check">

                                <i class="fa-solid fa-hashtag"></i>

                            </div>

                            <div>

                                <strong>Course Identification</strong>

                                <span>
                                    Maintain the correct course code and name.
                                </span>

                            </div>

                        </div>



                        <div class="course-edit-info-feature">

                            <div class="course-edit-feature-check">

                                <i class="fa-solid fa-coins"></i>

                            </div>

                            <div>

                                <strong>Credit Information</strong>

                                <span>
                                    Update the number of academic credits.
                                </span>

                            </div>

                        </div>



                        <div class="course-edit-info-feature">

                            <div class="course-edit-feature-check">

                                <i class="fa-solid fa-calendar-days"></i>

                            </div>

                            <div>

                                <strong>Semester Organization</strong>

                                <span>
                                    Keep semester allocation up to date.
                                </span>

                            </div>

                        </div>

                    </div>



                    <!-- FOOTER -->

                    <div class="course-edit-info-panel-footer">

                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Course records are securely maintained
                            in the system.
                        </span>

                    </div>

                </div>



                <!-- RIGHT FORM PANEL -->

                <div class="course-edit-form-panel">


                    <div class="course-edit-form-heading">

                        <div>

                            <span>
                                COURSE INFORMATION
                            </span>

                            <h2>
                                Update Course Details
                            </h2>

                        </div>


                        <div class="required-note">
                            * Required
                        </div>

                    </div>



                    <form method="POST"
                          action=""
                          class="modern-course-edit-form">


                        <!-- COURSE CODE -->

                        <div class="modern-course-edit-form-group">

                            <label>
                                Course Code
                                <span>*</span>
                            </label>


                            <div class="modern-course-edit-input-wrapper">

                                <div class="modern-course-edit-input-icon">

                                    <i class="fa-solid fa-hashtag"></i>

                                </div>


                                <input
                                    type="text"
                                    name="course_code"
                                    class="modern-course-edit-input"
                                    value="<?php echo htmlspecialchars($course["course_code"]); ?>"
                                    required
                                >

                            </div>

                            <small>
                                Example: STAT 22542
                            </small>

                        </div>



                        <!-- COURSE NAME -->

                        <div class="modern-course-edit-form-group">

                            <label>
                                Course Name
                                <span>*</span>
                            </label>


                            <div class="modern-course-edit-input-wrapper">

                                <div class="modern-course-edit-input-icon">

                                    <i class="fa-solid fa-book-open"></i>

                                </div>


                                <input
                                    type="text"
                                    name="course_name"
                                    class="modern-course-edit-input"
                                    value="<?php echo htmlspecialchars($course["course_name"]); ?>"
                                    required
                                >

                            </div>

                        </div>



                        <!-- CREDITS + SEMESTER -->

                        <div class="modern-course-edit-form-row">


                            <!-- CREDITS -->

                            <div class="modern-course-edit-form-group">

                                <label>
                                    Credits
                                    <span>*</span>
                                </label>


                                <div class="modern-course-edit-input-wrapper">

                                    <div class="modern-course-edit-input-icon">

                                        <i class="fa-solid fa-star"></i>

                                    </div>


                                    <input
                                        type="number"
                                        name="credits"
                                        class="modern-course-edit-input"
                                        value="<?php echo htmlspecialchars($course["credits"]); ?>"
                                        min="1"
                                        max="10"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- SEMESTER -->

                            <div class="modern-course-edit-form-group">

                                <label>
                                    Semester
                                    <span>*</span>
                                </label>


                                <div class="modern-course-edit-input-wrapper">

                                    <div class="modern-course-edit-input-icon">

                                        <i class="fa-solid fa-calendar"></i>

                                    </div>


                                    <select
                                        name="semester"
                                        class="modern-course-edit-input"
                                        required
                                    >

                                        <option value="Semester I"
                                            <?php
                                            if ($course["semester"] == "Semester I")
                                                echo "selected";
                                            ?>>
                                            Semester I
                                        </option>


                                        <option value="Semester II"
                                            <?php
                                            if ($course["semester"] == "Semester II")
                                                echo "selected";
                                            ?>>
                                            Semester II
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>



                        <!-- ACTIONS -->

                        <div class="modern-course-edit-form-actions">


                            <a href="courses.php"
                               class="modern-course-edit-cancel-button">

                                <i class="fa-solid fa-xmark"></i>

                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="modern-course-edit-submit-button"
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

</body>

</html>