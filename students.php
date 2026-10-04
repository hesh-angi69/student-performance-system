<?php

include "db.php";

$search = "";

if (isset($_GET["search"])) {
    $search = $_GET["search"];
}

$sql = "SELECT * FROM students
        WHERE student_id LIKE ?
        OR full_name LIKE ?
        ORDER BY id DESC";

$stmt = $conn->prepare($sql);

$searchTerm = "%" . $search . "%";

$stmt->bind_param("ss", $searchTerm, $searchTerm);

$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student List</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="container">

    <h1>Student List</h1>

    <p>Search and manage registered students</p>

    <!-- Search Form -->

    <form method="GET" action="students.php">

        <input
            type="text"
            name="search"
            placeholder="Search by Student ID or Name"
            value="<?php echo htmlspecialchars($search); ?>"
        >

        <button type="submit">
            Search
        </button>

        <a href="students.php">
            <button type="button">
                Clear
            </button>
        </a>

    </form>

    <br>

    <a href="add_student.php">
        <button>Add New Student</button>
    </a>

    <br><br>

    <!-- Student Table -->

    <table border="1" width="100%" cellpadding="10">

        <thead>

            <tr>

                <th>ID</th>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Course</th>
                <th>Year</th>
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
                    <?php echo htmlspecialchars($row["student_id"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["full_name"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["email"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["phone"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["course"]); ?>
                </td>

                <td>
                    <?php echo htmlspecialchars($row["year"]); ?>
                </td>

                <td>

                    <a href="edit_student.php?id=<?php echo $row["id"]; ?>">
                        Edit
                    </a>

                    |

                    <a
                        href="delete_student.php?id=<?php echo $row["id"]; ?>"
                        onclick="return confirm('Are you sure you want to delete this student?');"
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

                <td colspan="8">
                    No students found.
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

$stmt->close();

?>