<?php

include "db.php";

$sql = "SELECT * FROM students ORDER BY id DESC";

$result = $conn->query($sql);

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

        <p>All registered students</p>

        <a href="add_student.php">
            <button>Add New Student</button>
        </a>

        <br><br>

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
                                <?php echo $row["student_id"]; ?>
                            </td>

                            <td>
                                <?php echo $row["full_name"]; ?>
                            </td>

                            <td>
                                <?php echo $row["email"]; ?>
                            </td>

                            <td>
                                <?php echo $row["phone"]; ?>
                            </td>

                            <td>
                                <?php echo $row["course"]; ?>
                            </td>

                            <td>
                                <?php echo $row["year"]; ?>
                            </td>

                            <td>

                                <a href="edit_student.php?id=<?php echo $row["id"]; ?>">
                                   Edit
                                </a>

                        </td>

                        </tr>

                <?php

                    }

                } else {

                ?>

                    <tr>

                        <td colspan="7">
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