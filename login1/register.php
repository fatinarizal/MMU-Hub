<?php
session_start();
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="css/register.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
<div class="container">
    <div class="form-box box">
    <header>Register</header>
        <form method="POST" action="">
            <div class="form-box">
                <?php
                include "connection.php";
                if (isset($_POST["register"])) {
                    $username =  $_POST["username"];
                    $name = $_POST["name"];
                    $email =  $_POST["email"];
                    $phone = $_POST["phone"];
                    $password = $_POST["password"];
                    $role = $_POST["role"];
                    
                    // Check if username or email already exists
                    $checkQuery = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
                    $checkRes = mysqli_query($conn, $checkQuery);
                    
                    if (mysqli_num_rows($checkRes) > 0) {
                        // User already exists
                        echo "<div class='message'><p>Failed: Username or Email already exists.</p></div><br>";
                        echo "<a href='register.php'><button class='btn'>Go Back</button></a>";
                    } else {
                        // Insert user data into the database
                        $sql = "INSERT INTO users (username, name, email, phone, password, role) VALUES ('$username', '$name', '$email', '$phone','$password', '$role')";
                    
                        $res = mysqli_query($conn, $sql);
                    
                        if (!$res) {
                            echo "<div class='message'><p>Registration Failed: " . mysqli_error($conn) . "</p></div><br>";
                            echo "<a href='register.php'><button class='btn'>Go Back</button></a>";
                            
                        } else {
                            echo "<div class='message'><p>Registration Successful! Redirect to login page..</p></div><br>";
                            // echo "<a href='login.php'><button class='btn'>Go to Login</button></a>";
                            header("refresh:2; url=login.php");
                        }
                    }
                } else {

                ?>
                    <div class="userBox">
                        <label>Full Name</label>
                        <br>
                        <input type="text" name="name" required="">
                    </div>
                    <br>
                    <div class="userBox">
                        <label>Email</label>
                        <br>
                        <input type="email" name="email" required="">
                    </div>
                    <br>
                    <div class="userBox">
                        <label>Username</label>
                        <br>
                        <input type="text" name="username" required="">
                    </div>
                    <br>
                    <div class="userBox">
                        <label>Phone Number</label>
                        <br>
                        <input type="phone" name="phone" required="">
                    </div>
                    <br>
                    <div class="userBox">
                        <label>Password</label>
                        <br>
                        <input type="password" name="password" required="">
                    </div>
                    <br>
                    <div class="userBox">
                        <label>Role</label>
                        <br>
                        <select name="role" required="">
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <br>
                    <div class="center">
                        <button class="button buttonRegister" type="submit" name="register" style="font-size: 16px;">Register</button>
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
