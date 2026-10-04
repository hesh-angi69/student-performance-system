<?php

include "db.php";

if (!isset($_GET["id"])) {
    die("Student ID is missing.");
}

$id = $_GET["id"];

$sql = "DELETE FROM students WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: students.php?deleted=1");
    exit();

} else {

    echo "Error deleting student: " . $stmt->error;

}

$stmt->close();

?>