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


/* =========================================
   SEARCH MARKS
========================================= */

if ($search != "") {

    $sql = "SELECT
                marks.id,
                students.student_id,
                students.full_name,
                courses.course_code,
                courses.course_name,
                courses.credits,
                marks.marks,
                marks.grade
            FROM marks

            INNER JOIN students
                ON marks.student_id = students.id

            INNER JOIN courses
                ON marks.course_id = courses.id

            WHERE students.student_id LIKE ?
               OR students.full_name LIKE ?
               OR courses.course_code LIKE ?
               OR courses.course_name LIKE ?

            ORDER BY marks.id DESC";

    $stmt = $conn->prepare($sql);

    $searchTerm = "%" . $search . "%";

    $stmt->bind_param(
        "ssss",
        $searchTerm,
        $searchTerm,
        $searchTerm,
        $searchTerm
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT
                marks.id,
                students.student_id,
                students.full_name,
                courses.course_code,
                courses.course_name,
                courses.credits,
                marks.marks,
                marks.grade
            FROM marks

            INNER JOIN students
                ON marks.student_id = students.id

            INNER JOIN courses
                ON marks.course_id = courses.id

            ORDER BY marks.id DESC";

    $result = $conn->query($sql);
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

    <title>Marks | SPMS</title>

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


        <a
            href="marks.php"
            class="active"
        >
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

    <main class="main-content marks-page">


        <!-- TOP BAR -->

        <div class="topbar">

            <div>

                <span class="breadcrumb">
                    Marks Management
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

        <div class="marks-header">


            <div class="marks-title">

                <h1>
                    Marks
                </h1>

                <p>
                    Manage student marks, grades and course performance.
                </p>

            </div>



            <a
                href="add_marks.php"
                class="add-marks-btn"
            >

                <span>＋</span>

                Add Marks

            </a>

        </div>



        <!-- SEARCH CARD -->

        <div class="marks-search-card">


            <form
                method="GET"
                class="marks-search-wrapper"
            >


                <div class="marks-search-input-wrapper">

                    <span class="marks-search-icon">
                        🔍
                    </span>


                    <input
                        type="text"
                        name="search"
                        class="marks-search-input"
                        placeholder="Search by student ID, name, course code or course name..."
                        value="<?php echo htmlspecialchars($search); ?>"
                    >

                </div>



                <button
                    type="submit"
                    class="marks-search-btn"
                >
                    Search
                </button>



                <?php if ($search != ""): ?>

                    <a
                        href="marks.php"
                        class="marks-clear-btn"
                    >
                        Clear
                    </a>

                <?php endif; ?>


            </form>

        </div>



        <!-- SUCCESS MESSAGES -->

        <?php if (isset($_GET["updated"])): ?>

            <div class="marks-alert marks-success">
                ✓ Marks updated successfully.
            </div>

        <?php endif; ?>


        <?php if (isset($_GET["deleted"])): ?>

            <div class="marks-alert marks-success">
                ✓ Marks deleted successfully.
            </div>

        <?php endif; ?>



        <!-- MARKS CARD -->

        <div class="marks-card">


            <!-- CARD HEADER -->

            <div class="marks-card-header">


                <div>

                    <h2>
                        📝 Marks Directory
                    </h2>

                    <p>
                        Student course performance records
                    </p>

                </div>


                <span class="marks-record-count">

                    <?php
                    echo $result->num_rows;
                    ?>

                    records

                </span>


            </div>



            <!-- TABLE -->

            <div class="marks-table-wrapper">


                <table class="marks-table">


                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Course</th>

                            <th>Credits</th>

                            <th>Marks</th>

                            <th>Grade</th>

                            <th>Actions</th>

                        </tr>

                    </thead>



                    <tbody>


                    <?php if ($result->num_rows > 0): ?>


                        <?php while ($row = $result->fetch_assoc()): ?>


                            <tr>


                                <!-- STUDENT -->

                                <td>

                                    <div class="marks-student-profile">


                                        <div class="marks-student-avatar">

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

                                            <div class="marks-student-name">

                                                <?php
                                                echo htmlspecialchars(
                                                    $row["full_name"]
                                                );
                                                ?>

                                            </div>


                                            <div class="marks-student-id">

                                                <?php
                                                echo htmlspecialchars(
                                                    $row["student_id"]
                                                );
                                                ?>

                                            </div>

                                        </div>


                                    </div>

                                </td>



                                <!-- COURSE -->

                                <td>

                                    <div class="marks-course-box">


                                        <div class="marks-course-code">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["course_code"]
                                            );
                                            ?>

                                        </div>


                                        <div class="marks-course-name">

                                            <?php
                                            echo htmlspecialchars(
                                                $row["course_name"]
                                            );
                                            ?>

                                        </div>


                                    </div>

                                </td>



                                <!-- CREDITS -->

                                <td>

                                    <span class="marks-credit-badge">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["credits"]
                                        );
                                        ?>

                                        Credits

                                    </span>

                                </td>



                                <!-- MARKS -->

                                <td>

                                    <div class="marks-score">

                                        <?php
                                        echo number_format(
                                            $row["marks"],
                                            2
                                        );
                                        ?>

                                    </div>


                                    <div class="marks-progress">

                                        <div
                                            class="marks-progress-bar"
                                            style="width: <?php echo min((float)$row["marks"], 100); ?>%;"
                                        ></div>

                                    </div>

                                </td>



                                <!-- GRADE -->

                                <td>

                                    <?php

                                    $gradeClass = "grade-f";

                                    if ($row["grade"] == "A+" ||
                                        $row["grade"] == "A" ||
                                        $row["grade"] == "A-") {

                                        $gradeClass = "grade-a";

                                    } elseif (
                                        $row["grade"] == "B+" ||
                                        $row["grade"] == "B" ||
                                        $row["grade"] == "B-"
                                    ) {

                                        $gradeClass = "grade-b";

                                    } elseif (
                                        $row["grade"] == "C+" ||
                                        $row["grade"] == "C" ||
                                        $row["grade"] == "C-"
                                    ) {

                                        $gradeClass = "grade-c";

                                    } elseif (
                                        $row["grade"] == "D"
                                    ) {

                                        $gradeClass = "grade-d";

                                    }

                                    ?>


                                    <span
                                        class="marks-grade <?php echo $gradeClass; ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars(
                                            $row["grade"]
                                        );
                                        ?>

                                    </span>

                                </td>



                                <!-- ACTIONS -->

                                <td>


                                    <div class="marks-actions">


                                        <a
                                            href="edit_marks.php?id=<?php echo $row["id"]; ?>"
                                            class="action-btn marks-edit-action"
                                            title="Edit Marks"
                                        >
                                            ✏️
                                        </a>



                                        <a
                                            href="delete_marks.php?id=<?php echo $row["id"]; ?>"
                                            class="action-btn marks-delete-action"
                                            title="Delete Marks"
                                            onclick="return confirm('Are you sure you want to delete this marks record?');"
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
                                colspan="6"
                                class="marks-empty-state"
                            >

                                <div class="marks-empty-icon">
                                    📝
                                </div>


                                <strong>
                                    No marks found
                                </strong>


                                <br>


                                <small>
                                    Try another search or add marks for a student.
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