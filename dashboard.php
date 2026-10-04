<?php



session_start();

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}



include "db.php";

/* Total Students */
$studentQuery = "SELECT COUNT(*) AS total_students FROM students";
$studentResult = $conn->query($studentQuery);
$studentData = $studentResult->fetch_assoc();

$totalStudents = $studentData["total_students"];

/* Total Courses */

$courseQuery = "SELECT COUNT(*) AS total_courses FROM courses";

$courseResult = $conn->query($courseQuery);

$courseData = $courseResult->fetch_assoc();

$totalCourses = $courseData["total_courses"];



?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard - Student Performance Management System</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="dashboard">

    <!-- Sidebar -->

    <aside class="sidebar">

        <h2>SPMS</h2>

        <p class="system-name">
            Student Performance
        </p>

        <nav>

            <a href="dashboard.php" class="active">
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

            <a href="#">
                📅 Attendance
            </a>

            <a href="#">
                📊 Reports
            </a>

            <a href="#">
                ⚙ Settings
            </a>

        </nav>

    </aside>


    <!-- Main Content -->

    <main class="main-content">

        <!-- Top Bar -->

        <header class="topbar">

            <div>

                <h1>Dashboard</h1>

                <p>
                    Welcome to Student Performance Management System
                </p>

            </div>

            <div class="user-info">

    👤
    <?php echo htmlspecialchars($_SESSION["username"]); ?>

    <br>

    <small>
        <?php echo htmlspecialchars($_SESSION["role"]); ?>
    </small>

    |

    <a href="logout.php">
        Logout
    </a>

</div>

        </header>


        <!-- Dashboard Cards -->

        <section class="cards">

            <div class="card">

                <div class="card-icon">
                    👨‍🎓
                </div>

                <div>

                    <h3>
                        <?php echo $totalStudents; ?>
                    </h3>

                    <p>Total Students</p>

                </div>

            </div>


            <div class="card">

                <div class="card-icon">
                    📚
                </div>

                <div>

                    <h3>
                         <?php echo $totalCourses; ?>
                    </h3>

                         <p>Total Courses</p>

                </div>

            </div>


            <div class="card">

                <div class="card-icon">
                    🧮
                </div>

                <div>

                    <h3>0.00</h3>

                    <p>Average GPA</p>

                </div>

            </div>


            <div class="card">

                <div class="card-icon">
                    ⚠️
                </div>

                <div>

                    <h3>0</h3>

                    <p>At-Risk Students</p>

                </div>

            </div>

        </section>


        <!-- Dashboard Sections -->

        <section class="dashboard-grid">

            <div class="dashboard-box">

                <h2>Recent Students</h2>

                <p>
                    Recently registered students will appear here.
                </p>

            </div>


            <div class="dashboard-box">

                <h2>Performance Overview</h2>

                <p>
                    Student performance statistics will appear here.
                </p>

            </div>

        </section>

    </main>

</div>

</body>

</html>