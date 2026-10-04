<?php

session_start();

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {

        $message = "Please enter username and password.";

    } else {

        $sql = "SELECT id, username, password, role
                FROM users
                WHERE username = ?";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param("s", $username);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows == 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {

                // Create session variables

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["username"];
                $_SESSION["role"] = $user["role"];

                // Redirect to dashboard

                header("Location: dashboard.php");
                exit();

            } else {

                $message = "Invalid username or password.";

            }

        } else {

            $message = "Invalid username or password.";

        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="auth-container">

    <div class="auth-box">

        <h1>Login</h1>

        <p>
            Student Performance Management System
        </p>

        <?php if (!empty($message)) { ?>

            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php } ?>


        <form method="POST">

            <label>Username</label>

            <input
                type="text"
                name="username"
                placeholder="Enter username"
                required
            >


            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >


            <button type="submit">
                Login
            </button>

        </form>


        <p class="auth-link">

            Don't have an account?

            <a href="register.php">
                Register
            </a>

        </p>

    </div>

</div>

</body>

</html>