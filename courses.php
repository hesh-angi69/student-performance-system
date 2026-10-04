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

$sql = "SELECT * FROM courses
        WHERE course_code LIKE ?
        OR course_name LIKE ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

$searchTerm = "%" . $search . "%";

$stmt->bind_param(
    "ss",
    $searchTerm,
    $searchTerm
);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Courses - SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Course Management</h1>

    <p>Manage all registered courses</p>


    <!-- Search -->

    <form method="GET" action="courses.php">

        <input
            type="text"
            name="search"
            placeholder="Search by course code or name"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            Search
        </button>

        <a href="courses.php">
            <button type="button">
                Clear
            </button>
        </a>

    </form>

    <br>


    <a href="add_course.php">

        <button>
            Add New Course
        </button>

    </a>

    <br><br>


    <!-- Course Table -->

    <table border="1" width="100%" cellpadding="10">

        <thead>

            <tr>

                <th>ID</th>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Credits</th>
                <th>Semester</th>
                <th>Action</th>

            </tr>

        </thead>

        <tbody>

        <?php

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

        ?>

            <tr>

                <td>
                    <?php echo $row["id"]; ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["course_code"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["course_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["credits"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["semester"]); ?>
                </td>

                <td>

                    <a href="edit_course.php?id=<?php echo $row["id"]; ?>">
                        Edit
                    </a>

                    |

                    <a
                        href="delete_course.php?id=<?php echo $row["id"]; ?>"
                        onclick="return confirm('Are you sure you want to delete this course?');"
                    >
                        Delete
                    </a>

                </td>

            </tr>

        <?php

            }

        } else {

        ?>

            <tr>

                <td colspan="6">
                    No courses found.
                </td>

            </tr>

        <?php

        }

        ?>

        </tbody>

    </table>

</div>

</body>

</html>

<?php

/*<a href="dashboard.php">
    ← Back to Dashboard
</a>*/

$stmt->close();

?>