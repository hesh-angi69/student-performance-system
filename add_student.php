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

    $student_id = trim($_POST["student_id"]);
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $course = trim($_POST["course"]);
    $year = intval($_POST["year"]);

    $sql = "INSERT INTO students
            (student_id, full_name, email, phone, course, year)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssi",
        $student_id,
        $full_name,
        $email,
        $phone,
        $course,
        $year
    );

    if ($stmt->execute()) {

        $message = "Student has been added successfully.";
        $message_type = "success";

    } else {

        if ($stmt->errno == 1062) {

            $message = "Student ID already exists. Please use a different Student ID.";
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

    <title>Add Student | SPMS</title>

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

            <a href="students.php" class="active">
                <span class="nav-icon">♙</span>
                <span>Students</span>
            </a>

            <a href="courses.php">
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


    <!-- ================= MAIN ================= -->

    <main class="main-content">


        <!-- TOP BAR -->

        <header class="topbar">

            <div class="topbar-left">

                <span class="topbar-section">
                    Student Management
                </span>

                <span class="topbar-separator">
                    /
                </span>

                <span class="topbar-current">
                    Add Student
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

        <section class="add-student-container">


            <!-- PAGE HEADING -->

            <div class="add-student-heading">

                <div>

                    <a
                        href="students.php"
                        class="modern-back-button"
                    >
                        <span>←</span>
                        Back to Students
                    </a>

                    <h1>
                        Add New Student
                    </h1>

                    <p>
                        Add a new student to the performance management system.
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
                                ? "Student Added"
                                : "Unable to Add Student";
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

            <div class="student-create-layout">


                <!-- LEFT INFORMATION PANEL -->

                <div class="student-info-panel">

                    <div class="student-info-top">

                        <div class="large-student-icon">
                            ♙
                        </div>

                        <div class="info-label">
                            STUDENT RECORD
                        </div>

                        <h2>
                            Create a new
                            student profile
                        </h2>

                        <p>
                            Enter accurate student information
                            to maintain a complete academic record.
                        </p>

                    </div>


                    <div class="info-features">

                        <div class="info-feature">

                            <div class="feature-check">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Personal Information
                                </strong>

                                <span>
                                    Name and contact details
                                </span>
                            </div>

                        </div>


                        <div class="info-feature">

                            <div class="feature-check">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Academic Information
                                </strong>

                                <span>
                                    Course and academic year
                                </span>
                            </div>

                        </div>


                        <div class="info-feature">

                            <div class="feature-check">
                                ✓
                            </div>

                            <div>
                                <strong>
                                    Secure Record
                                </strong>

                                <span>
                                    Stored in the student database
                                </span>
                            </div>

                        </div>

                    </div>


                    <div class="info-panel-footer">

                        <span class="info-footer-icon">
                            i
                        </span>

                        <p>
                            Make sure the Student ID is unique
                            before submitting the form.
                        </p>

                    </div>

                </div>


                <!-- RIGHT FORM PANEL -->

                <div class="student-form-panel">

                    <div class="student-form-heading">

                        <div>

                            <span>
                                STUDENT INFORMATION
                            </span>

                            <h2>
                                Student Details
                            </h2>

                        </div>

                        <div class="required-label">
                            * Required
                        </div>

                    </div>


                    <form
                        method="POST"
                        class="modern-student-form"
                    >


                        <!-- Student ID -->

                        <div class="modern-form-group">

                            <label for="student_id">
                                Student ID
                                <span>*</span>
                            </label>

                            <div class="modern-input">

                                <div class="modern-input-icon">
                                    ID
                                </div>

                                <input
                                    type="text"
                                    id="student_id"
                                    name="student_id"
                                    placeholder="PS/2022/001"
                                    value="<?php
                                    echo isset($_POST["student_id"])
                                        ? htmlspecialchars(
                                            $_POST["student_id"]
                                        )
                                        : "";
                                    ?>"
                                    required
                                >

                            </div>

                            <small>
                                Unique university identification number.
                            </small>

                        </div>


                        <!-- Full Name -->

                        <div class="modern-form-group">

                            <label for="full_name">
                                Full Name
                                <span>*</span>
                            </label>

                            <div class="modern-input">

                                <div class="modern-input-icon">
                                    Aa
                                </div>

                                <input
                                    type="text"
                                    id="full_name"
                                    name="full_name"
                                    placeholder="Enter full name"
                                    value="<?php
                                    echo isset($_POST["full_name"])
                                        ? htmlspecialchars(
                                            $_POST["full_name"]
                                        )
                                        : "";
                                    ?>"
                                    required
                                >

                            </div>

                            <small>
                                Enter the student's full name.
                            </small>

                        </div>


                        <!-- Email + Phone -->

                        <div class="modern-form-row">


                            <div class="modern-form-group">

                                <label for="email">
                                    Email Address
                                    <span>*</span>
                                </label>

                                <div class="modern-input">

                                    <div class="modern-input-icon">
                                        @
                                    </div>

                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        placeholder="student@example.com"
                                        value="<?php
                                        echo isset($_POST["email"])
                                            ? htmlspecialchars(
                                                $_POST["email"]
                                            )
                                            : "";
                                        ?>"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="modern-form-group">

                                <label for="phone">
                                    Phone Number
                                </label>

                                <div class="modern-input">

                                    <div class="modern-input-icon">
                                        #
                                    </div>

                                    <input
                                        type="text"
                                        id="phone"
                                        name="phone"
                                        placeholder="07X XXXXXXX"
                                        value="<?php
                                        echo isset($_POST["phone"])
                                            ? htmlspecialchars(
                                                $_POST["phone"]
                                            )
                                            : "";
                                        ?>"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- Course + Year -->

                        <div class="modern-form-row">


                            <div class="modern-form-group">

                                <label for="course">
                                    Course
                                </label>

                                <div class="modern-input">

                                    <div class="modern-input-icon">
                                        C
                                    </div>

                                    <input
                                        type="text"
                                        id="course"
                                        name="course"
                                        placeholder="BSc Hons Statistics"
                                        value="<?php
                                        echo isset($_POST["course"])
                                            ? htmlspecialchars(
                                                $_POST["course"]
                                            )
                                            : "";
                                        ?>"
                                    >

                                </div>

                            </div>


                            <div class="modern-form-group">

                                <label for="year">
                                    Academic Year
                                </label>

                                <div class="modern-input">

                                    <div class="modern-input-icon">
                                        Y
                                    </div>

                                    <input
                                        type="number"
                                        id="year"
                                        name="year"
                                        placeholder="1 - 4"
                                        min="1"
                                        max="4"
                                        value="<?php
                                        echo isset($_POST["year"])
                                            ? htmlspecialchars(
                                                $_POST["year"]
                                            )
                                            : "";
                                        ?>"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- ACTIONS -->

                        <div class="modern-form-actions">

                            <a
                                href="students.php"
                                class="modern-cancel-button"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="modern-submit-button"
                            >
                                <span>+</span>
                                Add Student
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