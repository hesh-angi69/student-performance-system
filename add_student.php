<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST["student_id"];
    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $course = $_POST["course"];
    $year = $_POST["year"];

    $sql = "INSERT INTO students 
            (student_id, full_name, email, phone, course, year)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "sssssi",
        $student_id,
        $full_name,
        $email,
        $phone,
        $course,
        $year
    );

    /*if ($stmt->execute()) {
        $message = "Student added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }*/
    
    if ($stmt->execute()) {

    $message = "Student added successfully!";

} else {

    if ($stmt->errno == 1062) {

        $message = "Student ID already exists. Please use a different Student ID.";

    } else {

        $message = "Error: " . $stmt->error;

    }

}    

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Student</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h1>Add Student</h1>

        <?php if ($message != ""): ?>

            <p>
                <?php echo $message; ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <input
                type="text"
                name="student_id"
                placeholder="Student ID"
                required
            >

            <br><br>

            <input
                type="text"
                name="full_name"
                placeholder="Full Name"
                required
            >

            <br><br>

            <input
                type="email"
                name="email"
                placeholder="Email"
                required
            >

            <br><br>

            <input
                type="text"
                name="phone"
                placeholder="Phone Number"
            >

            <br><br>

            <input
                type="text"
                name="course"
                placeholder="Course"
            >

            <br><br>

            <input
                type="number"
                name="year"
                placeholder="Year"
                min="1"
                max="4"
            >

            <br><br>

            <button type="submit">
                Add Student
            </button>

        </form>

    </div>

</body>

</html>