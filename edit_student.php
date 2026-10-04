<?php

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$message = "";
$message_type = "";


// Get Student ID
if (!isset($_GET["id"]) || empty($_GET["id"])) {
    header("Location: students.php");
    exit();
}

$id = intval($_GET["id"]);


// Fetch Student
$sql = "SELECT * FROM students WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: students.php");
    exit();
}

$student = $result->fetch_assoc();

$stmt->close();


// Update Student
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = trim($_POST["student_id"]);
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $course = trim($_POST["course"]);
    $year = intval($_POST["year"]);


    $update_sql = "UPDATE students
                   SET student_id = ?,
                       full_name = ?,
                       email = ?,
                       phone = ?,
                       course = ?,
                       year = ?
                   WHERE id = ?";

    $update_stmt = $conn->prepare($update_sql);

    $update_stmt->bind_param(
        "sssssii",
        $student_id,
        $full_name,
        $email,
        $phone,
        $course,
        $year,
        $id
    );


    if ($update_stmt->execute()) {

        $message = "Student information updated successfully.";
        $message_type = "success";

        // Refresh displayed values
        $student["student_id"] = $student_id;
        $student["full_name"] = $full_name;
        $student["email"] = $email;
        $student["phone"] = $phone;
        $student["course"] = $course;
        $student["year"] = $year;

    } elseif ($update_stmt->errno == 1062) {

        $message = "This Student ID already exists.";
        $message_type = "error";

    } else {

        $message = "Unable to update student information.";
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

    <title>Edit Student | Student Performance Management System</title>

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

            <a href="students.php" class="active">
                <i class="fa-solid fa-users"></i>
                <span>Students</span>
            </a>

            <a href="courses.php">
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
                    Student Management
                </span>

                <span class="topbar-separator">/</span>

                <span class="topbar-current">
                    Edit Student
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

        <section class="edit-student-container">


            <!-- PAGE HEADING -->

            <div class="edit-student-heading">

                <div>

                    <span class="page-eyebrow">
                        Student Management
                    </span>

                    <h1>Edit Student</h1>

                    <p>
                        Update the student's personal and academic information.
                    </p>

                </div>


                <a href="students.php" class="back-page-link">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Students

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

            <div class="student-edit-layout">


                <!-- LEFT INFORMATION PANEL -->

                <div class="student-edit-info-panel">


                    <div class="student-edit-info-top">

                        <div class="large-student-edit-icon">

                            <i class="fa-solid fa-user-pen"></i>

                        </div>


                        <span class="student-edit-info-label">
                            Student Record
                        </span>


                        <h2>
                            Update student information with confidence.
                        </h2>


                        <p>
                            Keep student records accurate and up to date.
                            Changes made here will be reflected throughout
                            the performance management system.
                        </p>

                    </div>



                    <!-- FEATURES -->

                    <div class="student-edit-info-features">


                        <div class="student-edit-info-feature">

                            <div class="student-edit-feature-check">

                                <i class="fa-solid fa-id-card"></i>

                            </div>

                            <div>

                                <strong>Student Identity</strong>

                                <span>
                                    Update the student's identification details.
                                </span>

                            </div>

                        </div>



                        <div class="student-edit-info-feature">

                            <div class="student-edit-feature-check">

                                <i class="fa-solid fa-address-book"></i>

                            </div>

                            <div>

                                <strong>Contact Information</strong>

                                <span>
                                    Maintain accurate email and phone details.
                                </span>

                            </div>

                        </div>



                        <div class="student-edit-info-feature">

                            <div class="student-edit-feature-check">

                                <i class="fa-solid fa-graduation-cap"></i>

                            </div>

                            <div>

                                <strong>Academic Details</strong>

                                <span>
                                    Keep course and academic year information updated.
                                </span>

                            </div>

                        </div>

                    </div>



                    <!-- FOOTER -->

                    <div class="student-edit-info-panel-footer">

                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Student information is securely maintained
                            in the system.
                        </span>

                    </div>

                </div>



                <!-- RIGHT FORM PANEL -->

                <div class="student-edit-form-panel">


                    <div class="student-edit-form-heading">

                        <div>

                            <span>
                                STUDENT INFORMATION
                            </span>

                            <h2>
                                Update Student Details
                            </h2>

                        </div>


                        <div class="required-note">
                            * Required
                        </div>

                    </div>



                    <form method="POST"
                          action=""
                          class="modern-student-edit-form">


                        <!-- STUDENT ID -->

                        <div class="modern-student-edit-form-group">

                            <label>
                                Student ID
                                <span>*</span>
                            </label>


                            <div class="modern-student-edit-input-wrapper">

                                <div class="modern-student-edit-input-icon">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>


                                <input
                                    type="text"
                                    name="student_id"
                                    class="modern-student-edit-input"
                                    value="<?php echo htmlspecialchars($student["student_id"]); ?>"
                                    required
                                >

                            </div>

                        </div>



                        <!-- FULL NAME -->

                        <div class="modern-student-edit-form-group">

                            <label>
                                Full Name
                                <span>*</span>
                            </label>


                            <div class="modern-student-edit-input-wrapper">

                                <div class="modern-student-edit-input-icon">
                                    <i class="fa-solid fa-user"></i>
                                </div>


                                <input
                                    type="text"
                                    name="full_name"
                                    class="modern-student-edit-input"
                                    value="<?php echo htmlspecialchars($student["full_name"]); ?>"
                                    required
                                >

                            </div>

                        </div>



                        <!-- EMAIL + PHONE -->

                        <div class="modern-student-edit-form-row">


                            <!-- EMAIL -->

                            <div class="modern-student-edit-form-group">

                                <label>
                                    Email
                                    <span>*</span>
                                </label>


                                <div class="modern-student-edit-input-wrapper">

                                    <div class="modern-student-edit-input-icon">

                                        <i class="fa-solid fa-envelope"></i>

                                    </div>


                                    <input
                                        type="email"
                                        name="email"
                                        class="modern-student-edit-input"
                                        value="<?php echo htmlspecialchars($student["email"]); ?>"
                                        required
                                    >

                                </div>

                            </div>



                            <!-- PHONE -->

                            <div class="modern-student-edit-form-group">

                                <label>
                                    Phone
                                </label>


                                <div class="modern-student-edit-input-wrapper">

                                    <div class="modern-student-edit-input-icon">

                                        <i class="fa-solid fa-phone"></i>

                                    </div>


                                    <input
                                        type="text"
                                        name="phone"
                                        class="modern-student-edit-input"
                                        value="<?php echo htmlspecialchars($student["phone"]); ?>"
                                    >

                                </div>

                            </div>

                        </div>



                        <!-- COURSE + YEAR -->

                        <div class="modern-student-edit-form-row">


                            <!-- COURSE -->

                            <div class="modern-student-edit-form-group">

                                <label>
                                    Course
                                </label>


                                <div class="modern-student-edit-input-wrapper">

                                    <div class="modern-student-edit-input-icon">

                                        <i class="fa-solid fa-book"></i>

                                    </div>


                                    <input
                                        type="text"
                                        name="course"
                                        class="modern-student-edit-input"
                                        value="<?php echo htmlspecialchars($student["course"]); ?>"
                                    >

                                </div>

                            </div>



                            <!-- YEAR -->

                            <div class="modern-student-edit-form-group">

                                <label>
                                    Academic Year
                                </label>


                                <div class="modern-student-edit-input-wrapper">

                                    <div class="modern-student-edit-input-icon">

                                        <i class="fa-solid fa-calendar"></i>

                                    </div>


                                    <select
                                        name="year"
                                        class="modern-student-edit-input"
                                    >

                                        <option value="1"
                                            <?php if ($student["year"] == 1) echo "selected"; ?>>
                                            Year 1
                                        </option>

                                        <option value="2"
                                            <?php if ($student["year"] == 2) echo "selected"; ?>>
                                            Year 2
                                        </option>

                                        <option value="3"
                                            <?php if ($student["year"] == 3) echo "selected"; ?>>
                                            Year 3
                                        </option>

                                        <option value="4"
                                            <?php if ($student["year"] == 4) echo "selected"; ?>>
                                            Year 4
                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>



                        <!-- ACTIONS -->

                        <div class="modern-student-edit-form-actions">


                            <a href="students.php"
                               class="modern-student-edit-cancel-button">

                                <i class="fa-solid fa-xmark"></i>

                                Cancel

                            </a>


                            <button
                                type="submit"
                                class="modern-student-edit-submit-button"
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