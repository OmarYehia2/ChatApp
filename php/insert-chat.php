<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    include_once "config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $fileName = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $fileName = $_FILES['file']['name'];
        $fileTmpName = $_FILES['file']['tmp_name'];
        $fileSize = $_FILES['file']['size'];
        $fileError = $_FILES['file']['error'];
        $fileType = $_FILES['file']['type'];

        $fileExt = strtolower(end(explode('.', $fileName)));
        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if ($fileSize < 5000000) {
                    $fileNewName = uniqid('', true) . "." . $fileExt;
                    $fileDestination = 'uploads/' . $fileNewName;

                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    } else {
                        echo "Error uploading your file.";
                        exit();
                    }
                } else {
                    echo "File is too large!";
                    exit();
                }
            } else {
                echo "There was an error uploading the file.";
                exit();
            }
        } else {
            echo "Invalid file type!";
            exit();
        }
    }

    if (!empty($message) || !empty($fileName)) {
        $sql = mysqli_query($conn, "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg, file)
                                    VALUES ('{$incoming_id}', '{$outgoing_id}', '{$message}', '{$fileName}')") or die();
    }
} else {
    header("location: ../login.php");
}
?>
