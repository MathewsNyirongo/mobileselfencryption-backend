<?php    
    include_once 'dbConnect.php';

    $messageKey = "";
    $contactsKey = "";
    $notesKey = "";
    $filesKey = "";
    $userId = "";

    if (isset($_POST['user_id_fk'])) {
        $userId = $_POST['user_id_fk'];
    }

    if(isset($_POST['messages_key'])){
        $messageKey = $_POST['messages_key'];
    }

    if(isset($_POST['contacts_key'])){
        $contactsKey = $_POST['contacts_key'];
    }

    if(isset($_POST['notes_key'])){
        $notesKey = $_POST['notes_key'];
    }

    if(isset($_POST['files_key'])){
        $filesKey = $_POST['files_key'];
    }

    $keys = new Keys();
    // Set message key
    if(!empty($messageKey)){
        if (isset($_POST['user_id_fk'])) {
            $userId = $_POST['user_id_fk'];
            $json_array = $keys->setNewMessageKey($messageKey, $userId);
            echo json_encode($json_array);
        }else {
            echo json_encode("Pass in user id");
        }
    }

    // Set contact key
    if(!empty($contactsKey)){
        if (isset($_POST['user_id_fk'])) {
            $userId = $_POST['user_id_fk'];
            $json_array = $keys->setNewContactKey($contactsKey, $userId);
            echo json_encode($json_array);
        }else {
            echo json_encode("Pass in user id");
        }
    }

    // Set notes key
    if(!empty($notesKey)){
        if (isset($_POST['user_id_fk'])) {
            $userId = $_POST['user_id_fk'];
            $json_array = $keys->setNewNotesKey($notesKey, $userId);
            echo json_encode($json_array);
        }else {
            echo json_encode("Pass in user id");
        }
    }

    // Set files key
    if(!empty($filesKey)){
        if (!empty($userId)) {
            $json_array = $keys->setNewFilesKey($filesKey, $userId);
            echo json_encode($json_array);
        }
    }

    class Keys{
        private $db;
        public function __construct(){
            $this->db = new DbConnect();
        }

        public function isNewEncryptionKeys($userId){
            $query = "SELECT * FROM encryption_keys WHERE user_id_fk='$userId' LIMIT 1";
            $result = mysqli_query($this->db->getDatabase(), $query);
            if(mysqli_num_rows($result) > 0){
                return FALSE;
            }
            return TRUE;
        }

        public function setNewMessageKey($newMessageKey, $userId){
            $flag = $this->isNewEncryptionKeys($userId);
            if ($flag) {
                $query = "INSERT INTO encryption_keys (messages_key, contacts_key, notes_key, files_key, user_id_fk) VALUES ('$newMessageKey', '', '', '', '$userId')";
            } else {
                $query = "UPDATE encryption_keys SET messages_key = '$newMessageKey' WHERE user_id_fk='$userId'";
            }
            $inserted = mysqli_query($this->db->getDatabase(), $query);
            if($inserted == 1){
                $json['success'] = 1;
                $json['message'] = "Successfully set new message key";  
            }else{
                $json['success'] = 0;
                $json['message'] = "Error in setting new message key."; 
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }

        public function setNewContactKey($newContactKey, $userId){
            $flag = $this->isNewEncryptionKeys($userId);
            if ($flag) {
                $query = "INSERT INTO encryption_keys (messages_key, contacts_key, notes_key, files_key, user_id_fk) VALUES ('', '$newContactKey', '', '', '$userId')";
            } else {
                $query = "UPDATE encryption_keys SET contacts_key = '$newContactKey' WHERE user_id_fk='$userId'";
            }
            $inserted = mysqli_query($this->db->getDatabase(), $query);
            if($inserted == 1){
                $json['success'] = 1;
                $json['message'] = "Successfully set new contact key";  
            }else{
                $json['success'] = 0;
                $json['message'] = "Error in setting new contact key."; 
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }

        public function setNewNotesKey($newNotesKey, $userId){
            $flag = $this->isNewEncryptionKeys($userId);
            if ($flag) {
                $query = "INSERT INTO encryption_keys (messages_key, contacts_key, notes_key, files_key, user_id_fk) VALUES ('', '', '$newNotesKey', '', '$userId')";
            } else {
                $query = "UPDATE encryption_keys SET notes_key = '$newNotesKey' WHERE user_id_fk='$userId'";
            }
            $inserted = mysqli_query($this->db->getDatabase(), $query);
            if($inserted == 1){
                $json['success'] = 1;
                $json['message'] = "Successfully set new notes key";  
            }else{
                $json['success'] = 0;
                $json['message'] = "Error in setting new notes key."; 
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }

        public function setNewFilesKey($newFilesKey, $userId){
            $flag = $this->isNewEncryptionKeys($userId);
            if ($flag) {
                $query = "INSERT INTO encryption_keys (messages_key, contacts_key, notes_key, files_key, user_id_fk) VALUES ('', '', '', '$newFilesKey', '$userId')";
            } else {
                $query = "UPDATE encryption_keys SET files_key = '$newFilesKey' WHERE user_id_fk='$userId'";
            }
            $inserted = mysqli_query($this->db->getDatabase(), $query);
            if($inserted == 1){
                $json['success'] = 1;
                $json['message'] = "Successfully set new files key";  
            }else{
                $json['success'] = 0;
                $json['message'] = "Error in setting new files key."; 
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }
    }
?>