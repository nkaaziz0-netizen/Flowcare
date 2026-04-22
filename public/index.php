<?php
session_start();
include("../config/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user = $_POST['username'];
    $pass = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();

        if (password_verify($pass, $data['password'])) {

            $_SESSION['user_id'] = $data['id'];
            $_SESSION['role'] = $data['role'];

            if ($data['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: doctor/dashboard.php");
            }
            exit();
        }
    }

    echo "Invalid login!";
}
?>

<form method="POST">
    <input name="username" placeholder="Username" required><br>
    <input name="password" type="password" required><br>
    <button type="submit">Login</button>
</form>