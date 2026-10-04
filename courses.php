<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$search = "";

if (isset($_GET["search"])) {
    $search = trim($_GET["search"]);
}

if ($search != "") {

    $sql = "SELECT *
            FROM courses
            WHERE course_code LIKE ?
               OR course_name LIKE ?
               OR semester LIKE ?
            ORDER BY id DESC";

    $stmt = $conn->prepare($sql);

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "sss",
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT *
            FROM courses
            ORDER BY id DESC";

    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Courses | SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="dashboard">

    <!-- SIDEBAR -->

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

        <a href="courses.php" class="active">
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

        <a href="logout.php" class="logout-link">
            🚪 Logout
        </a>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content courses-page">

        <!-- TOP BAR -->

        <div class="topbar">

            <div>

                <span class="breadcrumb">
                    Course Management
                </span>

            </div>


            <div class="user-info">

                👤
                <?php echo htmlspecialchars($_SESSION["username"]); ?>

                <br>

                <small>
                    <?php echo htmlspecialchars($_SESSION["role"]); ?>
                </small>

            </div>

        </div>


        <!-- PAGE HEADER -->

        <div class="courses-header">

            <div class="courses-title">

                <h1>Courses</h1>

                <p>
                    Manage course information, credits and semesters.
                </p>

            </div>


            <a href="add_course.php" class="add-course-btn">

                <span>＋</span>

                Add Course

            </a>

        </div>


        <!-- SEARCH -->

        <div class="course-search-card">

            <form method="GET" class="course-search-wrapper">

                <div class="course-search-input-wrapper">

                    <span class="course-search-icon">
                        🔍
                    </span>

                    <input
                        type="text"
                        name="search"
                        class="course-search-input"
                        placeholder="Search by course code, name or semester..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                </div>


                <button
                    type="submit"
                    class="course-search-btn"
                >
                    Search
                </button>


                <?php if ($search != ""): ?>

                    <a
                        href="courses.php"
                        class="course-clear-btn"
                    >
                        Clear
                    </a>

                <?php endif; ?>

            </form>

        </div>


        <!-- COURSE TABLE -->

        <div class="courses-card">

            <div class="courses-card-header">

                <div>

                    <h2>
                        📚 Course Directory
                    </h2>

                    <p>
                        All registered courses
                    </p>

                </div>


                <span class="course-record-count">

                    <?php echo $result->num_rows; ?>

                    records

                </span>

            </div>


            <div class="courses-table-wrapper">

                <table class="courses-table">

                    <thead>

                        <tr>

                            <th>Course Code</th>

                            <th>Course Name</th>

                            <th>Credits</th>

                            <th>Semester</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php while ($row = $result->fetch_assoc()): ?>

                            <tr>

                                <!-- COURSE CODE -->

                                <td>

                                    <div class="course-code-box">

                                        <div class="course-code-icon">
                                            📘
                                        </div>

                                        <div>

                                            <div class="course-code">
                                                <?php
                                                echo htmlspecialchars(
                                                    $row["course_code"]
                                                );
                                                ?>
                                            </div>

                                            <div class="course-label">
                                                Course
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- COURSE NAME -->

                                <td>

                                    <div class="course-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["course_name"]
                                        );
                                        ?>

                                    </div>

                                </td>


                                <!-- CREDITS -->

                                <td>

                                    <span class="credits-badge">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["credits"]
                                        );
                                        ?>

                                        Credits

                                    </span>

                                </td>


                                <!-- SEMESTER -->

                                <td>

                                    <?php if ($row["semester"] == "Semester I"): ?>

                                        <span class="semester-badge semester-one">
                                            Semester I
                                        </span>

                                    <?php else: ?>

                                        <span class="semester-badge semester-two">
                                            Semester II
                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- ACTIONS -->

                                <td>

                                    <div class="course-actions">

                                        <a
                                            href="edit_course.php?id=<?php echo $row["id"]; ?>"
                                            class="action-btn course-edit-action"
                                            title="Edit Course"
                                        >
                                            ✏️
                                        </a>


                                        <a
                                            href="delete_course.php?id=<?php echo $row["id"]; ?>"
                                            class="action-btn course-delete-action"
                                            title="Delete Course"
                                            onclick="return confirm('Are you sure you want to delete this course?');"
                                        >
                                            🗑️
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>


                    <?php else: ?>

                        <tr>

                            <td
                                colspan="5"
                                class="course-empty-state"
                            >

                                <div class="course-empty-icon">
                                    📚
                                </div>

                                <strong>
                                    No courses found
                                </strong>

                                <br>

                                <small>
                                    Try another search or add a new course.
                                </small>

                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>

</div>

</body>

</html>