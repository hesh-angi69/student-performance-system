<?php

include "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    if (empty($username) || empty($email) || empty($password)) {

        $message = "Please fill all required fields.";

    } else {

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, email, password, role)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $username,
            $email,
            $hashedPassword,
            $role
        );

        if ($stmt->execute()) {

            $message = "Registration successful! You can now login.";

        } else {

            if ($stmt->errno == 1062) {

                $message = "Username or email already exists.";

            } else {

                $message = "Registration failed.";

            }
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

    <title>Register - SPMS</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="auth-container">

    <div class="auth-box">

        <h1>Create Account</h1>

        <p>Student Performance Management System</p>

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


            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="Enter email"
                required
            >


            <label>Password</label>

            <input
                type="password"
                name="password"
                placeholder="Enter password"
                required
            >


            <label>Role</label>

            <select name="role">

                <option value="Lecturer">
                    Lecturer
                </option>

                <option value="Admin">
                    Admin
                </option>

            </select>


            <button type="submit">
                Register
            </button>

        </form>


        <p class="auth-link">

            Already have an account?

            <a href="login.php">
                Login
            </a>

        </p>

    </div>

</div>

</body>

</html>