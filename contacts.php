<?php    
    include_once 'dbConnect.php';

    $contactName = "";
    $number1 = "";
    $number2 = "";
    $number3 = "";
    $number4 = "";
    $number5 = "";
    $userId = "";
    $id = "";

    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }

    if (isset($_POST['user_id_fk'])) {
        $userId = $_POST['user_id_fk'];
    }

    if (isset($_POST['contact_name'])) {
        $contactName = $_POST['contact_name'];
    }

    if (isset($_POST['contact_number1'])) {
        $number1 = $_POST['contact_number1'];
    }

    if (isset($_POST['contact_number2'])) {
        $number2 = $_POST['contact_number1'];
    }

    if (isset($_POST['contact_number2'])) {
        $number2 = $_POST['contact_number2'];
    }

    if (isset($_POST['contact_number3'])) {
        $number3 = $_POST['contact_number3'];
    }

    if (isset($_POST['contact_number4'])) {
        $number4 = $_POST['contact_number4'];
    }

    if (isset($_POST['contact_number5'])) {
        $number5 = $_POST['contact_number5'];
    }

    $contacts = new Contacts();
    // Insert contacts
    if (!empty($contactName) && !empty($number1)) {
        $json_array = $contacts->addNewContact($contactName, $number1, $number2, $number3, $number4, $number5, $userId);
        echo json_encode($json_array);
    }

    if(!empty($userId)){
        $json_array = $contacts->getAllContacts($userId);
        echo json_encode($json_array);
    }

    if(!empty($id)){
        $json_array = $contacts->deleteContact($id);
        echo json_encode($json_array);
    }
    
    class Contacts{
        private $db;
        public function __construct(){
            $this->db = new DbConnect();
        }

        public function addNewContact($name, $number1, $number2, $number3, $number4, $number5, $userId){
            $query = "INSERT INTO contacts (contact_name, contact_number1, contact_number2, contact_number3, contact_number4, contact_number5,user_id_fk) VALUES ('$name', '$number1', '$number2', '$number3', '$number4', '$number5', '$userId')";
            $inserted = mysqli_query($this->db->getDatabase(), $query);
            if($inserted == 1){
                $json['success'] = 1;
                $json['message'] = "Successfully encrypted contact";  
            }else{
                //echo mysqli_error($this->db->getDatabase());
                $json['success'] = 0;
                $json['message'] = "Error in encrypting contact."; 
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }

        public function deleteContact($id){
            $json = array();
            $query = "DELETE FROM contacts WHERE id='$id'";
            $result = mysqli_query($this->db->getDatabase(), $query);
            if ($result === TRUE) {
                $json["success"] = 1;
                $json["message"] = "Successfully decrypted contact";
            }else {
                $json["success"] = 0;
                $json["message"] = "Failed to decrypted contact";
            }
            return $json;
        }

        public function getAllContacts($userId)
        {
            $json = array();
            $query = "SELECT * FROM contacts WHERE user_id_fk='$userId'";
            $result = mysqli_query($this->db->getDatabase(), $query);
            if(mysqli_num_rows($result) > 0){
                $i = 0;
                $json["success"] = 1;
                while($row = $result->fetch_assoc()){
                    $userContact = new stdClass;
                    $userContact->id = $row["id"];
                    $userContact->contact_name = $row["contact_name"];
                    $userContact->contact_number1 = $row["contact_number1"];
                    $userContact->contact_number2 = $row["contact_number2"];
                    $userContact->contact_number3 = $row["contact_number3"];
                    $userContact->contact_number4 = $row["contact_number4"];
                    $userContact->contact_number5 = $row["contact_number5"];
                    $json["user".$i] = $userContact;
                    $i++;
                }
                $json["contacts"] = $i;
            }else{
                $json["success"] = 0;
                $json["message"] = "Failed to retrieve your contacts.";
            }
            mysqli_close($this->db->getDatabase());
            return $json;
        }
    }
    
?>