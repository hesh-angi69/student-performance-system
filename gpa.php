<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$selectedStudent = "";
$studentInfo = null;
$gpa = null;
$totalCredits = 0;
$totalGradePoints = 0;
$marksResult = null;


/* =========================================
   GET ALL STUDENTS
========================================= */

$studentsSql = "SELECT id, student_id, full_name
                FROM students
                ORDER BY full_name";

$studentsResult = $conn->query($studentsSql);


/* =========================================
   SELECTED STUDENT
========================================= */

if (isset($_GET["student_id"])) {

    $selectedStudent = intval($_GET["student_id"]);

    /* GET STUDENT DETAILS */

    $studentSql = "SELECT id, student_id, full_name, email, course, year
                   FROM students
                   WHERE id = ?";

    $studentStmt = $conn->prepare($studentSql);

    $studentStmt->bind_param(
        "i",
        $selectedStudent
    );

    $studentStmt->execute();

    $studentResult = $studentStmt->get_result();

    if ($studentResult->num_rows == 1) {

        $studentInfo = $studentResult->fetch_assoc();

    }

    $studentStmt->close();


    /* =========================================
       GET MARKS + COURSES
    ========================================= */

    if ($studentInfo) {

        $marksSql = "SELECT
                        courses.course_code,
                        courses.course_name,
                        courses.credits,
                        marks.marks,
                        marks.grade
                     FROM marks

                     INNER JOIN courses
                         ON marks.course_id = courses.id

                     WHERE marks.student_id = ?

                     ORDER BY courses.course_code";

        $marksStmt = $conn->prepare($marksSql);

        $marksStmt->bind_param(
            "i",
            $selectedStudent
        );

        $marksStmt->execute();

        $marksResult = $marksStmt->get_result();


        /* =========================================
           GPA CALCULATION
        ========================================= */

        while ($row = $marksResult->fetch_assoc()) {

            $grade = $row["grade"];
            $credits = (float)$row["credits"];

            $gradePoint = 0;


            switch ($grade) {

                case "A+":
                    $gradePoint = 4.0;
                    break;

                case "A":
                    $gradePoint = 4.0;
                    break;

                case "A-":
                    $gradePoint = 3.7;
                    break;

                case "B+":
                    $gradePoint = 3.5;
                    break;

                case "B":
                    $gradePoint = 3.0;
                    break;

                case "B-":
                    $gradePoint = 2.7;
                    break;

                case "C+":
                    $gradePoint = 2.5;
                    break;

                case "C":
                    $gradePoint = 2.0;
                    break;

                case "C-":
                    $gradePoint = 1.7;
                    break;

                case "D":
                    $gradePoint = 1.0;
                    break;

                case "F":
                    $gradePoint = 0.0;
                    break;
            }


            $totalCredits += $credits;

            $totalGradePoints +=
                $gradePoint * $credits;
        }


        if ($totalCredits > 0) {

            $gpa =
                $totalGradePoints /
                $totalCredits;

        }


        /*
         * Execute the query again so that
         * the result can be displayed in table.
         */

        $marksStmt->close();

        $marksStmt = $conn->prepare($marksSql);

        $marksStmt->bind_param(
            "i",
            $selectedStudent
        );

        $marksStmt->execute();

        $marksResult = $marksStmt->get_result();

    }

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

    <title>GPA Calculation | SPMS</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>

<div class="dashboard">


    <!-- =====================================
         SIDEBAR
    ====================================== -->

    <aside class="sidebar">

        <h2>SPMS</h2>

        <p class="sidebar-title">
            Student Performance
        </p>


        <a href="dashboard.php">
            🏠 Dashboard
        </a>


        <a href="students.php">
            👨‍🎓 Students
        </a>


        <a href="courses.php">
            📚 Courses
        </a>


        <a href="marks.php">
            📝 Marks
        </a>


        <a
            href="gpa.php"
            class="active"
        >
            🧮 GPA Calculation
        </a>


        <a href="#">
            📅 Attendance
        </a>


        <a href="#">
            📊 Reports
        </a>


        <a href="#">
            ⚙ Settings
        </a>


        <a
            href="logout.php"
            class="logout-link"
        >
            🚪 Logout
        </a>

    </aside>



    <!-- =====================================
         MAIN CONTENT
    ====================================== -->

    <main class="main-content gpa-page">


        <!-- TOP BAR -->

        <div class="topbar">

            <div>

                <span class="breadcrumb">
                    Academic Performance
                </span>

            </div>


            <div class="user-info">

                👤

                <?php
                echo htmlspecialchars(
                    $_SESSION["username"]
                );
                ?>

                <br>

                <small>

                    <?php
                    echo htmlspecialchars(
                        $_SESSION["role"]
                    );
                    ?>

                </small>

            </div>

        </div>



        <!-- PAGE HEADER -->

        <div class="gpa-header">


            <div class="gpa-title">

                <h1>
                    GPA Calculation
                </h1>

                <p>
                    Select a student to automatically calculate their GPA.
                </p>

            </div>

        </div>



        <!-- STUDENT SELECT CARD -->

        <div class="gpa-select-card">


            <div class="gpa-select-content">


                <div class="gpa-select-icon">
                    🎓
                </div>


                <div class="gpa-select-info">

                    <h3>
                        Select Student
                    </h3>

                    <p>
                        Choose a student to view their academic performance.
                    </p>

                </div>


            </div>



            <form
                method="GET"
                class="gpa-form"
            >


                <select
                    name="student_id"
                    class="gpa-student-select"
                    onchange="this.form.submit()"
                >

                    <option value="">
                        -- Select a Student --
                    </option>


                    <?php while ($student = $studentsResult->fetch_assoc()): ?>

                        <option
                            value="<?php echo $student["id"]; ?>"
                            <?php
                            if (
                                $selectedStudent ==
                                $student["id"]
                            ) {
                                echo "selected";
                            }
                            ?>
                        >

                            <?php
                            echo htmlspecialchars(
                                $student["student_id"]
                            );
                            ?>

                            -

                            <?php
                            echo htmlspecialchars(
                                $student["full_name"]
                            );
                            ?>

                        </option>

                    <?php endwhile; ?>

                </select>


                <noscript>

                    <button
                        type="submit"
                        class="gpa-calculate-btn"
                    >
                        Calculate GPA
                    </button>

                </noscript>


            </form>

        </div>



        <?php if ($studentInfo): ?>


            <!-- =====================================
                 STUDENT PROFILE
            ====================================== -->

            <div class="gpa-student-card">


                <div class="gpa-student-profile">


                    <div class="gpa-large-avatar">

                        <?php
                        echo strtoupper(
                            substr(
                                $studentInfo["full_name"],
                                0,
                                1
                            )
                        );
                        ?>

                    </div>


                    <div>

                        <h2>

                            <?php
                            echo htmlspecialchars(
                                $studentInfo["full_name"]
                            );
                            ?>

                        </h2>


                        <p>

                            Student ID:

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $studentInfo["student_id"]
                                );
                                ?>

                            </strong>

                        </p>

                    </div>

                </div>



                <div class="gpa-student-details">


                    <div>

                        <span>
                            Course
                        </span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $studentInfo["course"]
                            );
                            ?>

                        </strong>

                    </div>


                    <div>

                        <span>
                            Year
                        </span>

                        <strong>

                            Year
                            <?php
                            echo htmlspecialchars(
                                $studentInfo["year"]
                            );
                            ?>

                        </strong>

                    </div>


                    <div>

                        <span>
                            Email
                        </span>

                        <strong>

                            <?php
                            echo htmlspecialchars(
                                $studentInfo["email"]
                            );
                            ?>

                        </strong>

                    </div>

                </div>

            </div>



            <!-- =====================================
                 GPA SUMMARY CARDS
            ====================================== -->

            <div class="gpa-summary-grid">


                <!-- GPA -->

                <div class="gpa-summary-card gpa-main-card">


                    <div class="gpa-card-icon">
                        🧮
                    </div>


                    <div>

                        <span>
                            Current GPA
                        </span>


                        <strong>

                            <?php

                            if ($gpa !== null) {

                                echo number_format(
                                    $gpa,
                                    2
                                );

                            } else {

                                echo "0.00";

                            }

                            ?>

                        </strong>


                        <small>
                            Out of 4.00
                        </small>

                    </div>

                </div>



                <!-- CREDITS -->

                <div class="gpa-summary-card">


                    <div class="gpa-card-icon credits-icon">
                        📚
                    </div>


                    <div>

                        <span>
                            Total Credits
                        </span>


                        <strong>

                            <?php
                            echo number_format(
                                $totalCredits,
                                0
                            );
                            ?>

                        </strong>


                        <small>
                            Completed credits
                        </small>

                    </div>

                </div>



                <!-- GRADE POINTS -->

                <div class="gpa-summary-card">


                    <div class="gpa-card-icon points-icon">
                        ⭐
                    </div>


                    <div>

                        <span>
                            Grade Points
                        </span>


                        <strong>

                            <?php
                            echo number_format(
                                $totalGradePoints,
                                2
                            );
                            ?>

                        </strong>


                        <small>
                            Weighted points
                        </small>

                    </div>

                </div>

            </div>



            <!-- =====================================
                 COURSE PERFORMANCE
            ====================================== -->

            <div class="gpa-courses-card">


                <div class="gpa-courses-header">


                    <div>

                        <h2>
                            📊 Course Performance
                        </h2>

                        <p>
                            Marks and grades used for GPA calculation.
                        </p>

                    </div>


                    <span class="gpa-course-count">

                        <?php
                        echo $marksResult
                            ? $marksResult->num_rows
                            : 0;
                        ?>

                        Courses

                    </span>

                </div>



                <div class="gpa-table-wrapper">


                    <table class="gpa-table">


                        <thead>

                            <tr>

                                <th>
                                    Course
                                </th>

                                <th>
                                    Credits
                                </th>

                                <th>
                                    Marks
                                </th>

                                <th>
                                    Grade
                                </th>

                                <th>
                                    Grade Point
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php if (
                            $marksResult &&
                            $marksResult->num_rows > 0
                        ): ?>


                            <?php while (
                                $row =
                                $marksResult->fetch_assoc()
                            ): ?>


                                <?php

                                switch ($row["grade"]) {

                                    case "A+":
                                    case "A":
                                        $displayPoint = 4.0;
                                        break;

                                    case "A-":
                                        $displayPoint = 3.7;
                                        break;

                                    case "B+":
                                        $displayPoint = 3.5;
                                        break;

                                    case "B":
                                        $displayPoint = 3.0;
                                        break;

                                    case "B-":
                                        $displayPoint = 2.7;
                                        break;

                                    case "C+":
                                        $displayPoint = 2.5;
                                        break;

                                    case "C":
                                        $displayPoint = 2.0;
                                        break;

                                    case "C-":
                                        $displayPoint = 1.7;
                                        break;

                                    case "D":
                                        $displayPoint = 1.0;
                                        break;

                                    default:
                                        $displayPoint = 0.0;
                                }

                                ?>


                                <tr>


                                    <td>

                                        <div class="gpa-course-info">

                                            <strong>

                                                <?php
                                                echo htmlspecialchars(
                                                    $row["course_code"]
                                                );
                                                ?>

                                            </strong>

                                            <span>

                                                <?php
                                                echo htmlspecialchars(
                                                    $row["course_name"]
                                                );
                                                ?>

                                            </span>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="gpa-credit-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["credits"]
                                            );
                                            ?>

                                            Credits

                                        </span>

                                    </td>


                                    <td>

                                        <strong class="gpa-marks">

                                            <?php
                                            echo number_format(
                                                $row["marks"],
                                                2
                                            );
                                            ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <span class="gpa-grade">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["grade"]
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <strong class="gpa-point">

                                            <?php
                                            echo number_format(
                                                $displayPoint,
                                                1
                                            );
                                            ?>

                                        </strong>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="5"
                                    class="gpa-empty-state"
                                >

                                    <div>
                                        📚
                                    </div>

                                    <strong>
                                        No marks available
                                    </strong>

                                    <br>

                                    <small>
                                        Add marks for this student to calculate GPA.
                                    </small>

                                </td>

                            </tr>


                        <?php endif; ?>


                        </tbody>


                    </table>

                </div>

            </div>



        <?php else: ?>


            <!-- =====================================
                 EMPTY STATE
            ====================================== -->

            <div class="gpa-welcome-card">


                <div class="gpa-welcome-icon">
                    🎓
                </div>


                <h2>
                    Select a Student
                </h2>


                <p>
                    Choose a student from the dropdown above
                    to view their GPA and course performance.
                </p>

            </div>


        <?php endif; ?>


    </main>

</div>


</body>

</html>