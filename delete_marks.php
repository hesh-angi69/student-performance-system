<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

if (!isset($_GET["id"])) {
    header("Location: marks.php");
    exit();
}

$id = intval($_GET["id"]);

/* Delete mark */

$sql = "DELETE FROM marks WHERE id = ?";

$stmt = $conn->prepare($sql);

$stmt->bind_param("i", $id);

if ($stmt->execute()) {

    header("Location: marks.php?deleted=1");
    exit();

} else {

    echo "Error deleting marks.";
}

$stmt->close();

?>