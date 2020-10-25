<?php
include_once 'dbConnect.php';

$userId = "";
$id = "";
$title = "";
$text = "";
$dateTime = "";

if (isset($_POST['id'])) {
    $id = $_POST['id'];
}

if (isset($_POST['user_id_fk'])) {
    $userId = $_POST['user_id_fk'];
}

if (isset($_POST['note_title'])) {
    $title = $_POST['note_title'];
}

if (isset($_POST['note_text'])) {
    $text = $_POST['note_text'];
}

if (isset($_POST['date_time'])) {
    $dateTime = $_POST['date_time'];
}

$notes = new Notes();

if (!empty($title)) {
    $json_array = $notes->addNewNote($userId, $title, $text,$dateTime);
    echo json_encode($json_array);
}

if (empty($title) && !empty($userId)) {
    $json_array = $notes->getAllNotes($userId);
    echo json_encode($json_array);
}

class Notes{
    private $db;
    public function __construct(){
        $this->db = new DbConnect();
    }

    public function addNewNote($userId, $title, $text, $dateTime){
        $title = mysqli_real_escape_string($this->db->getDatabase(), $title);
        $text = mysqli_real_escape_string($this->db->getDatabase(), $text);
        $query = "INSERT INTO notes (note_title, note_text, date_time, user_id_fk) VALUES ('$title', '$text', '$dateTime', '$userId')";
        $inserted = mysqli_query($this->db->getDatabase(), $query);
        if($inserted == 1){
            $json['success'] = 1;
            $json['message'] = "Successfully encrypted note";  
        }else{
            // echo mysqli_error($this->db->getDatabase());
            $json['success'] = 0;
            $json['message'] = "Error in encrypting note."; 
        }
        mysqli_close($this->db->getDatabase());
        return $json;
    }

    public function getAllNotes($userId)
        {
            $json = array();
            $query = "SELECT * FROM notes WHERE user_id_fk='$userId'";
            $result = mysqli_query($this->db->getDatabase(), $query);
            if(mysqli_num_rows($result) > 0){
                $i = 0;
                $json["success"] = 1;
                while($row = $result->fetch_assoc()){
                    $userNote = new stdClass;
                    $userNote->id = $row["id"];
                    $userNote->note_title = $row["note_title"];
                    $userNote->date_time = $row["date_time"];
                    $userNote->note_text = $row["note_text"];
                    $json["note".$i] = $userNote;
                    $i++;
                }
                $json["notes"] = $i;
            }else{
                $json["success"] = 0;
                $json["message"] = "Failed to retrieve your notes.";
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }
}
?>