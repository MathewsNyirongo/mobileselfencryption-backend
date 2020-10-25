<?php
include_once 'dbConnect.php';
$messageSender = "";
$dateTime = "";
$messageText = "";
$userId = "";
$id = "";

if (isset($_POST['id'])) {
    $id = $_POST['id'];
}

if (isset($_POST['user_id_fk'])) {
    $userId = $_POST['user_id_fk'];
}

if (isset($_POST['message_sender'])) {
    $messageSender = $_POST['message_sender'];
}

if (isset($_POST['date_time'])) {
    $dateTime = $_POST['date_time'];
}

if (isset($_POST['message_text'])) {
    $messageText = $_POST['message_text'];
}

$messages = new Messages();

if (!empty($messageText)) {
    $json_array = $messages->addNewMessage($userId, $dateTime, $messageSender, $messageText);
    echo json_encode($json_array);
};

if(!empty($userId) && empty($messageText)) {
    $json_array = $messages->getAllMessages($userId);
    echo json_encode($json_array);
}


class Messages{
    private $db;
    public function __construct(){
        $this->db = new DbConnect();
    }

    public function addNewMessage($userId, $dateTime, $messageSender, $messageText)
    {
        $messageText = mysqli_real_escape_string($this->db->getDatabase(), $messageText);
        $messageSender = mysqli_real_escape_string($this->db->getDatabase(), $messageSender);
        $query = "INSERT INTO messages (message_sender, date_time, message_text, user_id_fk) VALUES ('$messageSender', '$dateTime', '$messageText', '$userId')";
        $inserted = mysqli_query($this->db->getDatabase(), $query);
            if($inserted == 1){
                $json['success'] = 1;
                $json['message'] = "Successfully encrypted message";  
            }else{
                // echo mysqli_error($this->db->getDatabase());
                $json['success'] = 0;
                $json['message'] = "Error in encrypting message."; 
            }
            mysqli_close($this->db->getDatabase());
            return $json;
    }

    public function getAllMessages($userId)
        {
            $json = array();
            $query = "SELECT * FROM messages WHERE user_id_fk='$userId'";
            $result = mysqli_query($this->db->getDatabase(), $query);
            if(mysqli_num_rows($result) > 0){
                $i = 0;
                $json["success"] = 1;
                while($row = $result->fetch_assoc()){
                    $userMessage = new stdClass;
                    $userMessage->id = $row["id"];
                    $userMessage->message_sender = $row["message_sender"];
                    $userMessage->date_time = $row["date_time"];
                    $userMessage->message_text = $row["message_text"];
                    $json["message".$i] = $userMessage;
                    $i++;
                }
                $json["messages"] = $i;
            }else{
                $json["success"] = 0;
                $json["message"] = "Failed to retrieve your messages.";
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }
}
?>