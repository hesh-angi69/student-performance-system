<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";


/* =========================================
   TOTAL STUDENTS
========================================= */

$studentQuery = $conn->query(
    "SELECT COUNT(*) AS total
     FROM students"
);

$studentData = $studentQuery->fetch_assoc();

$totalStudents = $studentData["total"];


/* =========================================
   TOTAL COURSES
========================================= */

$courseQuery = $conn->query(
    "SELECT COUNT(*) AS total
     FROM courses"
);

$courseData = $courseQuery->fetch_assoc();

$totalCourses = $courseData["total"];


/* =========================================
   CALCULATE STUDENT GPAs
========================================= */

$gradePoints = [

    "A+" => 4.0,
    "A"  => 4.0,
    "A-" => 3.7,

    "B+" => 3.5,
    "B"  => 3.0,
    "B-" => 2.7,

    "C+" => 2.5,
    "C"  => 2.0,
    "C-" => 1.7,

    "D" => 1.0,
    "F" => 0.0

];


$gpaQuery = $conn->query(

    "SELECT
        students.id AS student_id,
        students.student_id AS student_code,
        students.full_name,
        courses.credits,
        marks.grade

     FROM marks

     INNER JOIN students
        ON marks.student_id = students.id

     INNER JOIN courses
        ON marks.course_id = courses.id"

);


$studentGPAs = [];


while ($row = $gpaQuery->fetch_assoc()) {

    $studentId = $row["student_id"];

    $credits = (float)$row["credits"];

    $grade = $row["grade"];


    if (isset($gradePoints[$grade])) {

        $point = $gradePoints[$grade];

    } else {

        $point = 0;

    }


    if (!isset($studentGPAs[$studentId])) {

        $studentGPAs[$studentId] = [

            "student_code" => $row["student_code"],

            "full_name" => $row["full_name"],

            "total_credits" => 0,

            "total_points" => 0

        ];

    }


    $studentGPAs[$studentId]["total_credits"]
        += $credits;


    $studentGPAs[$studentId]["total_points"]
        += ($point * $credits);

}


/* =========================================
   CALCULATE GPA VALUES
========================================= */

$totalGPA = 0;

$studentsWithGPA = 0;

$atRiskStudents = 0;


foreach ($studentGPAs as $id => &$student) {

    if ($student["total_credits"] > 0) {

        $student["gpa"] =
            $student["total_points"]
            /
            $student["total_credits"];

        $totalGPA += $student["gpa"];

        $studentsWithGPA++;


        /* AT-RISK CONDITION */

        if ($student["gpa"] < 2.00) {

            $atRiskStudents++;

        }

    } else {

        $student["gpa"] = 0;

    }

}

unset($student);


/* =========================================
   AVERAGE GPA
========================================= */

if ($studentsWithGPA > 0) {

    $averageGPA =
        $totalGPA
        /
        $studentsWithGPA;

} else {

    $averageGPA = 0;

}


/* =========================================
   TOP STUDENTS
========================================= */

$topStudents = $studentGPAs;

usort(
    $topStudents,
    function ($a, $b) {

        return $b["gpa"] <=> $a["gpa"];

    }
);

$topStudents =
    array_slice(
        $topStudents,
        0,
        5
    );


/* =========================================
   RECENT MARKS
========================================= */

$recentMarksSql = "

    SELECT

        students.student_id,
        students.full_name,

        courses.course_code,

        marks.marks,
        marks.grade

    FROM marks

    INNER JOIN students
        ON marks.student_id = students.id

    INNER JOIN courses
        ON marks.course_id = courses.id

    ORDER BY marks.id DESC

    LIMIT 6

";

$recentMarks =
    $conn->query($recentMarksSql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Dashboard | SPMS
    </title>


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


        <h2>
            SPMS
        </h2>


        <p class="sidebar-title">
            Student Performance
        </p>


        <a
            href="dashboard.php"
            class="active"
        >
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


        <a href="gpa.php">
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

    <main class="main-content dashboard-page">


        <!-- TOPBAR -->

        <div class="topbar">


            <div>

                <span class="breadcrumb">
                    Overview
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

        <div class="dashboard-welcome">


            <div>

                <h1>
                    Welcome back,
                    <?php
                    echo htmlspecialchars(
                        $_SESSION["username"]
                    );
                    ?> 👋
                </h1>


                <p>
                    Here's an overview of your student performance system.
                </p>

            </div>


            <div class="dashboard-date">

                📊

                Academic Overview

            </div>


        </div>



        <!-- =====================================
             STAT CARDS
        ====================================== -->

        <div class="dashboard-stat-grid">


            <!-- STUDENTS -->

            <div class="dashboard-stat-card">


                <div class="dashboard-stat-icon students-stat">
                    👨‍🎓
                </div>


                <div>

                    <span>
                        Total Students
                    </span>


                    <strong>

                        <?php
                        echo $totalStudents;
                        ?>

                    </strong>


                    <small>
                        Registered students
                    </small>

                </div>


            </div>



            <!-- COURSES -->

            <div class="dashboard-stat-card">


                <div class="dashboard-stat-icon courses-stat">
                    📚
                </div>


                <div>

                    <span>
                        Total Courses
                    </span>


                    <strong>

                        <?php
                        echo $totalCourses;
                        ?>

                    </strong>


                    <small>
                        Available courses
                    </small>

                </div>


            </div>



            <!-- AVERAGE GPA -->

            <div class="dashboard-stat-card">


                <div class="dashboard-stat-icon gpa-stat">
                    🧮
                </div>


                <div>

                    <span>
                        Average GPA
                    </span>


                    <strong>

                        <?php
                        echo number_format(
                            $averageGPA,
                            2
                        );
                        ?>

                    </strong>


                    <small>
                        Out of 4.00
                    </small>

                </div>


            </div>



            <!-- AT RISK -->

            <div class="dashboard-stat-card risk-card">


                <div class="dashboard-stat-icon risk-stat">
                    ⚠️
                </div>


                <div>

                    <span>
                        At-Risk Students
                    </span>


                    <strong>

                        <?php
                        echo $atRiskStudents;
                        ?>

                    </strong>


                    <small>
                        GPA below 2.00
                    </small>

                </div>


            </div>


        </div>



        <!-- =====================================
             DASHBOARD GRID
        ====================================== -->

        <div class="dashboard-content-grid">


            <!-- =================================
                 RECENT MARKS
            ================================== -->

            <div class="dashboard-panel">


                <div class="dashboard-panel-header">


                    <div>

                        <h2>
                            📝 Recent Marks
                        </h2>

                        <p>
                            Latest student performance records
                        </p>

                    </div>


                    <a href="marks.php">
                        View All
                    </a>


                </div>



                <div class="dashboard-table-wrapper">


                    <table class="dashboard-table">


                        <thead>

                            <tr>

                                <th>
                                    Student
                                </th>

                                <th>
                                    Course
                                </th>

                                <th>
                                    Marks
                                </th>

                                <th>
                                    Grade
                                </th>

                            </tr>

                        </thead>


                        <tbody>


                        <?php if (
                            $recentMarks &&
                            $recentMarks->num_rows > 0
                        ): ?>


                            <?php while (
                                $row =
                                $recentMarks->fetch_assoc()
                            ): ?>


                                <tr>


                                    <td>

                                        <div class="dashboard-student">

                                            <div class="dashboard-avatar">

                                                <?php
                                                echo strtoupper(
                                                    substr(
                                                        $row["full_name"],
                                                        0,
                                                        1
                                                    )
                                                );
                                                ?>

                                            </div>


                                            <div>

                                                <strong>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row["full_name"]
                                                    );
                                                    ?>

                                                </strong>

                                                <small>

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $row["student_id"]
                                                    );
                                                    ?>

                                                </small>

                                            </div>

                                        </div>

                                    </td>


                                    <td>

                                        <span class="dashboard-course">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["course_code"]
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <td>

                                        <strong>

                                            <?php
                                            echo number_format(
                                                $row["marks"],
                                                2
                                            );
                                            ?>

                                        </strong>

                                    </td>


                                    <td>

                                        <span class="dashboard-grade">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["grade"]
                                            );
                                            ?>

                                        </span>

                                    </td>


                                </tr>


                            <?php endwhile; ?>


                        <?php else: ?>


                            <tr>

                                <td
                                    colspan="4"
                                    class="dashboard-empty"
                                >

                                    No marks records available.

                                </td>

                            </tr>


                        <?php endif; ?>


                        </tbody>


                    </table>


                </div>


            </div>



            <!-- =================================
                 TOP STUDENTS
            ================================== -->

            <div class="dashboard-panel">


                <div class="dashboard-panel-header">


                    <div>

                        <h2>
                            🏆 Top Students
                        </h2>

                        <p>
                            Highest current GPA
                        </p>

                    </div>


                    <a href="gpa.php">
                        GPA
                    </a>


                </div>



                <div class="top-students-list">


                <?php if (count($topStudents) > 0): ?>


                    <?php
                    $rank = 1;
                    ?>


                    <?php foreach (
                        $topStudents
                        as $student
                    ): ?>


                        <div class="top-student-item">


                            <div class="rank-number">

                                <?php
                                echo $rank;
                                ?>

                            </div>


                            <div class="top-student-avatar">

                                <?php
                                echo strtoupper(
                                    substr(
                                        $student["full_name"],
                                        0,
                                        1
                                    )
                                );
                                ?>

                            </div>


                            <div class="top-student-info">


                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $student["full_name"]
                                    );
                                    ?>

                                </strong>


                                <small>

                                    <?php
                                    echo htmlspecialchars(
                                        $student["student_code"]
                                    );
                                    ?>

                                </small>


                            </div>


                            <div class="top-student-gpa">

                                <?php
                                echo number_format(
                                    $student["gpa"],
                                    2
                                );
                                ?>

                            </div>


                        </div>


                        <?php
                        $rank++;
                        ?>


                    <?php endforeach; ?>


                <?php else: ?>


                    <div class="dashboard-empty">

                        No GPA data available.

                    </div>


                <?php endif; ?>


                </div>


            </div>


        </div>



        <!-- =====================================
             QUICK ACTIONS
        ====================================== -->

        <div class="dashboard-panel quick-actions-panel">

    <div class="dashboard-panel-header">
        <div>
            <h3>⚡ Quick Actions</h3>
            <p>Quickly manage your student performance system</p>
        </div>
    </div>

    <div class="quick-actions">

        <a href="add_student.php" class="quick-action student-action">
            <div class="quick-action-icon">👨‍🎓</div>

            <div class="quick-action-content">
                <h4>Add Student</h4>
                <p>Register a new student</p>
            </div>

            <span class="quick-action-arrow">→</span>
        </a>


        <a href="add_course.php" class="quick-action course-action">
            <div class="quick-action-icon">📚</div>

            <div class="quick-action-content">
                <h4>Add Course</h4>
                <p>Create a new course</p>
            </div>

            <span class="quick-action-arrow">→</span>
        </a>


        <a href="add_marks.php" class="quick-action marks-action">
            <div class="quick-action-icon">📝</div>

            <div class="quick-action-content">
                <h4>Add Marks</h4>
                <p>Enter student marks</p>
            </div>

            <span class="quick-action-arrow">→</span>
        </a>


        <a href="gpa.php" class="quick-action gpa-action">
            <div class="quick-action-icon">🧮</div>

            <div class="quick-action-content">
                <h4>Calculate GPA</h4>
                <p>View student GPA</p>
            </div>

            <span class="quick-action-arrow">→</span>
        </a>

    </div>

</div>


    </main>

</div>


</body>

</html>