<?php 
session_start();
include_once "config.php";

if (!isset($_SESSION['unique_id'])) {
    header("Location: login.php");
    exit();
}

$error_message = ''; // Variable to store error messages

if (isset($_POST['submit'])) {
    $fname = mysqli_real_escape_string($conn, $_POST['fname']);
    $lname = mysqli_real_escape_string($conn, $_POST['lname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    
    $hashedPassword = md5($password);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if (isset($_FILES['image'])) {
            $img_name = $_FILES['image']['name'];
            $img_type = $_FILES['image']['type'];
            $tmp_name = $_FILES['image']['tmp_name'];
            
            $img_explode = explode('.', $img_name);
            $img_ext = end($img_explode);
            $extensions = ["jpeg", "png", "jpg"];
    
            if (in_array($img_ext, $extensions)) {
                $types = ["image/jpeg", "image/jpg", "image/png"];
                if (in_array($img_type, $types)) {
                    $time = time();
                    $new_img_name = $time . $img_name;
    
                    if (move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
                        $unique_id = $_SESSION['unique_id'];
    
                        $updateQuery = "UPDATE users SET 
                            fname = '$fname', 
                            lname = '$lname', 
                            email = '$email', 
                            password = '$hashedPassword', 
                            img = '$new_img_name' 
                            WHERE unique_id = $unique_id";
                        
                        if (mysqli_query($conn, $updateQuery)) {
                            header("Location: ../users.php");
                            exit();
                        } else {
                            $error_message = "Error updating record: " . mysqli_error($conn);
                        }
                    } else {
                        $error_message = "Failed to upload image!";
                    }
                } else {
                    $error_message = "Please upload a valid image file - jpeg, png, jpg";
                }
            } else {
                $error_message = "Please upload a valid image file - jpeg, png, jpg";
            }
        }
    } else {
        $error_message = "$email is not a valid email!";
    }
}

$sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
if(mysqli_num_rows($sql) > 0){
    $row = mysqli_fetch_assoc($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User Data</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="wrapper">
    <section class="form signup">
      <header>
      <a href="../" class="back-link" >
        <i class="fas fa-arrow-left" style="color: #000000; margin-right: 5px;"></i> 
      </a>
      Chat App
      </header>
      <div style="color: #721c24;
            padding: 8px 10px;
            text-align: center;
            border-radius: 5px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            margin-bottom: 10px;
            <?php echo empty($error_message) ? 'display: none;' : 'display: block;'; ?>"> 
            <?php echo $error_message; ?>
      </div>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="name-details">
          <div class="field input">
            <label>First Name</label>
            <input type="text" name="fname" placeholder="<?php echo $row["fname"]?>" required>
          </div>
          <div class="field input">
            <label>Last Name</label>
            <input type="text" name="lname" placeholder="<?php echo $row["lname"]?>" >
          </div>
        </div>
        <div class="field input">
          <label>Email Address</label>
          <input type="text" name="email" placeholder="<?php echo $row["email"]?>" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter new password" required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="field image">
          <label>Select Image</label>
          <input type="file" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Updated">
        </div>
      </form>
    </section>
  </div>

</body>
</html>
