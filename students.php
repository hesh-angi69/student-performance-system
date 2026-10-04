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

/* Search Students */

if ($search != "") {

    $sql = "SELECT *
            FROM students
            WHERE student_id LIKE ?
               OR full_name LIKE ?
               OR email LIKE ?
               OR course LIKE ?
            ORDER BY id DESC";

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

    $sql = "SELECT *
            FROM students
            ORDER BY id DESC";

    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Students | SPMS</title>

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

        <a href="students.php" class="active">
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

        <a href="logout.php" class="logout-link">
            🚪 Logout
        </a>

    </aside>


    <!-- MAIN CONTENT -->

    <main class="main-content students-page">

        <!-- TOP BAR -->

        <div class="topbar">

            <div>

                <span class="breadcrumb">
                    Student Management
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

        <div class="students-header">

            <div class="students-title">

                <h1>
                    Students
                </h1>

                <p>
                    Manage student profiles,
                    academic information and records.
                </p>

            </div>

            <a
                href="add_student.php"
                class="add-student-btn"
            >
                <span>＋</span>
                Add Student
            </a>

        </div>


        <!-- SEARCH -->

        <div class="search-card">

            <form
                method="GET"
                class="search-wrapper"
            >

                <div class="search-input-wrapper">

                    <span class="search-icon">
                        🔍
                    </span>

                    <input
                        type="text"
                        name="search"
                        class="search-input"
                        placeholder="Search by ID, name, email or course..."
                        value="<?php
                            echo htmlspecialchars($search);
                        ?>"
                    >

                </div>

                <button
                    type="submit"
                    class="search-btn"
                >
                    Search
                </button>

                <?php if ($search != ""): ?>

                    <a
                        href="students.php"
                        class="clear-btn"
                    >
                        Clear
                    </a>

                <?php endif; ?>

            </form>

        </div>


        <!-- STUDENT TABLE -->

        <div class="students-card">

            <div class="students-card-header">

                <div>

                    <h2>
                        👨‍🎓 Student Directory
                    </h2>

                    <p>
                        All registered students
                    </p>

                </div>

                <span class="record-count">

                    <?php
                    echo $result->num_rows;
                    ?>

                    records

                </span>

            </div>


            <div class="students-table-wrapper">

                <table class="students-table">

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Email</th>

                            <th>Phone</th>

                            <th>Course</th>

                            <th>Year</th>

                            <th>Actions</th>

                        </tr>

                    </thead>


                    <tbody>

                    <?php if ($result->num_rows > 0): ?>

                        <?php
                        while (
                            $row =
                            $result->fetch_assoc()
                        ):
                        ?>

                            <tr>

                                <!-- STUDENT -->

                                <td>

                                    <div class="student-profile">

                                        <div class="student-avatar">

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

                                            <div class="student-name">

                                                <?php
                                                echo htmlspecialchars(
                                                    $row["full_name"]
                                                );
                                                ?>

                                            </div>

                                            <div class="student-id">

                                                <?php
                                                echo htmlspecialchars(
                                                    $row["student_id"]
                                                );
                                                ?>

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                <!-- EMAIL -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row["email"]
                                    );
                                    ?>

                                </td>


                                <!-- PHONE -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row["phone"]
                                    );
                                    ?>

                                </td>


                                <!-- COURSE -->

                                <td>

                                    <span class="course-badge">

                                        <?php
                                        echo htmlspecialchars(
                                            $row["course"]
                                        );
                                        ?>

                                    </span>

                                </td>


                                <!-- YEAR -->

                                <td>

                                    <span class="year-badge">

                                        Year
                                        <?php
                                        echo $row["year"];
                                        ?>

                                    </span>

                                </td>


                                <!-- ACTIONS -->

                                <td>

                                    <div class="student-actions">

                                        <a
                                            href="edit_student.php?id=<?php
                                                echo $row["id"];
                                            ?>"
                                            class="action-btn edit-action"
                                            title="Edit Student"
                                        >
                                            ✏️
                                        </a>

                                        <a
                                            href="delete_student.php?id=<?php
                                                echo $row["id"];
                                            ?>"
                                            class="action-btn delete-action"
                                            title="Delete Student"
                                            onclick="return confirm('Are you sure you want to delete this student?');"
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
                                class="empty-state"
                            >

                                <div class="empty-icon">
                                    👨‍🎓
                                </div>

                                <strong>
                                    No students found
                                </strong>

                                <br>

                                <small>
                                    Try another search
                                    or add a new student.
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