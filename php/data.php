<?php
    while ($row = mysqli_fetch_assoc($query)) {
        $stmt = $conn->prepare("SELECT * FROM messages 
                                WHERE (incoming_msg_id = ? OR outgoing_msg_id = ?) 
                                AND (outgoing_msg_id = ? OR incoming_msg_id = ?) 
                                ORDER BY msg_id DESC LIMIT 1");
        $stmt->bind_param("iiii", $row['unique_id'], $row['unique_id'], $outgoing_id, $outgoing_id);
        $stmt->execute();
        $query2 = $stmt->get_result();

        $row2 = mysqli_fetch_assoc($query2);
        $result = (mysqli_num_rows($query2) > 0) ? $row2['msg'] : "No message available";
        
        $msg = (strlen($result) > 28) ? substr($result, 0, 28) . '...' : $result;
        
        $you = (isset($row2['outgoing_msg_id']) && $outgoing_id == $row2['outgoing_msg_id']) ? "You: " : "";
        
        $offline = ($row['status'] == "Offline now") ? "offline" : "";
        
        $hid_me = ($outgoing_id == $row['unique_id']) ? "hide" : "";

        $output .= '<a href="chat.php?user_id='. $row['unique_id'] .'">
                        <div class="content">
                            <img src="php/images/'. $row['img'] .'" alt="">
                            <div class="details">
                                <span>'. $row['fname'] . " " . $row['lname'] .'</span>
                                <p>'. $you . $msg .'</p>
                            </div>
                        </div>
                        <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                    </a>';
    }
?>
