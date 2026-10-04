<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

if (!isset($_GET["id"])) {
    die("Course ID is missing.");
}

$id = $_GET["id"];

$sql = "DELETE FROM courses WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: courses.php?deleted=1");
    exit();

} else {

    echo "Error deleting course: " . $stmt->error;

}

$stmt->close();

?>