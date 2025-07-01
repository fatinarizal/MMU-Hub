<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/login.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<div class="container">
    <div class="form-box box">
    <header>Login</header>
        <form method="POST" action="">
            <div class="form-box">
                <?php
                include "connection.php";
                
                if (isset($_POST["login"])) {
                    $username = $_POST["username"];
                    $password = $_POST["password"];
                    
                    // Query to check if the user exists and verify credentials
                    $sqluser = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
                    $resuser = mysqli_query($conn, $sqluser);

                    if (mysqli_num_rows($resuser) == 1) {
                        // User exists, retrieve user info
                        $user = mysqli_fetch_assoc($resuser);
                        
                        // Store user details in session
                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["username"] = $user["username"];
                        $_SESSION["role"] = $user["role"];

                        // Redirect based on user role
                        if ($_SESSION["role"] == 'student') {
                            header('Location: user/userdashboard.php');
                            exit();
                        } elseif ($_SESSION["role"] == 'staff') {
                            header('Location: user/userdashboard.php');
                            exit();
                        }
                    } 

                    // Prepare query to check if the user is an admin
                    $sqladmin = "SELECT * FROM admins WHERE username = '$username' AND password = '$password' AND role = 'admin'";
                    $resadmin = mysqli_query($conn, $sqladmin);
                    
                    if(mysqli_num_rows($resadmin) == 1){
                        // User exists, retrieve user info
                        $user = mysqli_fetch_assoc($resadmin);

                        // Store user details in session
                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["username"] = $user["username"];
                        $_SESSION["role"] = $user["role"];

                        header('Location: admin/admindashboard.php');
                        exit();
                    }
                
                     // Invalid credentials
                    echo "<div class='message'><p>Failed: Invalid username or password.</p></div><br>";
                    echo "<a href='login.php'><button class='btn'>Go Back</button></a>";
                } else {
                ?>
                    <div class="userBox">
                        <label>Username</label>
                        <input type="text" name="username" required="">
                    </div>
                    <div class="userBox">
                        <label>Password</label>
                        <input type="password" name="password" required="">
                    </div>
                    <br>
                    <div class="center">
                        <button class="button buttonLogin" type="submit" name="login" style="font-size: 16px;">Login</button>
                    </div>
                    <br>
                    <div class="links">
                        If you have not registered yet, click <a href="register.php">Register</a>
                    </div>
            </div>
        </form>
    </div>
    <?php
        }
    ?>
</div>
</body>

</html>
