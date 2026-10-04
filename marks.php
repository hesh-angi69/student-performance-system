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


/* Search Marks */

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

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Marks Management</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Marks Management</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["username"]); ?>
    </p>


    <!-- Search -->

    <form method="GET" class="search-form">

        <input
            type="text"
            name="search"
            placeholder="Search Student ID, Name, Course..."
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            🔍 Search
        </button>

        <a href="marks.php">
            Clear
        </a>

    </form>


    <br>


    <a href="add_marks.php">
        ➕ Add Marks
    </a>

    &nbsp;&nbsp;

    <a href="dashboard.php">
        🏠 Dashboard
    </a>


    <br><br>


    <!-- Success Messages -->

    <?php if (isset($_GET["updated"])): ?>

        <p class="message">
            Marks updated successfully!
        </p>

    <?php endif; ?>


    <?php if (isset($_GET["deleted"])): ?>

        <p class="message">
            Marks deleted successfully!
        </p>

    <?php endif; ?>


    <!-- Marks Table -->

    <table border="1" cellpadding="10" cellspacing="0">

        <tr>

            <th>ID</th>

            <th>Student ID</th>

            <th>Student Name</th>

            <th>Course Code</th>

            <th>Course Name</th>

            <th>Credits</th>

            <th>Marks</th>

            <th>Grade</th>

            <th>Action</th>

        </tr>


        <?php if ($result->num_rows > 0): ?>

            <?php while ($row = $result->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?php echo $row["id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["student_id"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["full_name"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["course_code"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["course_name"]); ?>
                    </td>

                    <td>
                        <?php echo $row["credits"]; ?>
                    </td>

                    <td>
                        <?php echo $row["marks"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["grade"]); ?>
                    </td>

                    <td>

                        <a href="edit_marks.php?id=<?php echo $row["id"]; ?>">
                            ✏️ Edit
                        </a>

                        |

                        <a
                            href="delete_marks.php?id=<?php echo $row["id"]; ?>"
                            onclick="return confirm('Are you sure you want to delete this marks record?');"
                        >
                            🗑️ Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>

                <td colspan="9">
                    No marks records found.
                </td>

            </tr>

        <?php endif; ?>

    </table>

</div>

</body>

</html>