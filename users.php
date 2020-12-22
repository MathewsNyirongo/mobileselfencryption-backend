<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, GET");
header("Access-Control-Allow-Headers: *");
include_once 'dbConnect.php';
error_reporting(E_ALL & ~E_NOTICE);

require_once('PHPMailer/PHPMailerAutoload.php');


$userId = "";
$email = "";
$isActive = "";
$newPassword = "";
$fullName = "";
$action = "";
$data = json_decode(file_get_contents('php://input'));
$userObject = new Users();
    
if (isset($data->isActive)) {
    $isActive = $data->isActive;
}

if (isset($data->userId)) {
    $userId = $data->userId;
}

if (isset($data->email)) {
    $email = $data->email;
}

if (isset($data->newPassword)) {
    $newPassword = $data->newPassword;
}

if (isset($data->fullName)) {
    $fullName = $data->fullName;
}

if (isset($data->action)) {
    $action = $data->action;
}

if (!empty($email) && !empty($action)) {
    if ($action == "isUserExist") {
        $json_array = $userObject->getUserByEmail($email);
    }
    if ($action == "sendMail") {
        $json_array = $userObject->sendMail($email, $fullName);
    }
    if ($action == "changePassword") {
        # code...
    }
    echo json_encode($json_array);
}

if (!empty($userId) && empty($isActive)) {
    $json_array = $userObject->getUserById($userId);
    echo json_encode($json_array);
}

if(!empty($isActive) && !empty($userId)){
    $json_array = $userObject->changeAccountStatus($isActive, $userId);
    echo json_encode($json_array);
}

class Users{    
    private $db;
    private $db_table = "users";
    private $messagesKey = "";
    private $contactsKey = "";
    private $notesKey = "";
    private $filesKey = "";
    
    public function __construct(){
        $this->db = new DbConnect();
    }
    
    public function sendMail($email, $fullName) {
        $code = rand(pow(10, 4), pow(10, 5)-1);
        $subJect = "Password reset verification code";
        $mail = new PHPMailer();
        try {
            //Server settings                     // Enable verbose debug output
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host = 'smtp.gmail.com';                    // Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   // Enable SMTP authentication
            $mail->Username = 'mathnyirongo@gmail.com';                     // SMTP username
            $mail->Password = 'airtellive';                               // SMTP password
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        
            //Recipients
            $mail->setFrom('no-reply@encryptionservices.com');
            $mail->addAddress($email, $fullName);     // Add a recipient
            //$mail->addAddress('ellen@example.com');               // Name is optional
        
            // Content
            $mail->isHTML(false);                                  // Set email format to HTML
            $mail->Subject = 'Password reset verification code';
           // $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
           // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
            $mail->Body = "Hi $fullName,\n\nYou have requested to reset your password. Use $code to verify. Ignore this email if you did not make this request.\n\n\nBest,\nThe Encryption Team.";
        
            $mail->send();
            $json["success"] = 1;
            $json["message"] = "Email sent successfully.";
            $json["code"] = $code;
            return $json;
        } catch (Exception $e) {
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            $json["success"] = 0;
            $json["message"] = "Failed to send email: {$mail->ErrorInfo}";
            return $json;
        }
    }
    public function changeAccountStatus($status, $userId){
        $sql = "UPDATE users SET isActive = '$status' WHERE id = '$userId'";
        if (mysqli_query($this->db->getDatabase(), $sql)) {
            $json["success"] = 1;
            $json["message"] = "Successfully updated record";
        }else {
            $json["success"] = 0;
            $json["message"] = "Failed to update record";
        }
        return $json;
    }

    public function getUserByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($this->db->getDatabase(), $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
                $json["success"] = 1;
                $json["message"] = "Successfully retrieved user";
                $json["data"]["fullName"] = $row["full_name"];
                $json["data"]["phoneNumber"] = $row["phone_number"];
                $json["data"]["email"] = $row["email"];
                $json["data"]["isActive"] = $row["isActive"];
            }
        }else {
            $json["success"] = 0;
            $json["message"] = "User not found";
        }
        return $json;
    }

    public function getUserById($userId) {
        $sql = "SELECT * FROM users WHERE id = '$userId'";
        $result = mysqli_query($this->db->getDatabase(), $sql);
        if (mysqli_num_rows($result) > 0) {
            while ($row = $result->fetch_assoc()) {
                $json["success"] = 1;
                $json["message"] = "Successfully retrieved user";
                $json["data"]["fullName"] = $row["full_name"];
                $json["data"]["phoneNumber"] = $row["phone_number"];
                $json["data"]["email"] = $row["email"];
                $json["data"]["isActive"] = $row["isActive"];
            }
        }else {
            $json["success"] = 0;
            $json["message"] = "User not found";
        }
        return $json;
    }
    public function getUser($email, $password){
        $query = "select * from users where email = '$email' AND password = '$password' Limit 1";
        $result = mysqli_query($this->db->getDatabase(), $query);
        if(mysqli_num_rows($result) > 0){
            while($row = $result->fetch_assoc()){
                $json["id"] = $row["id"];
                $json["isActive"] = $row["isActive"];
            }
            return $json;
        }
        return null;
    }
    
    public function isEmailExist($email){
        $query = "select * from users where email = '$email'";
        $result = mysqli_query($this->db->getDatabase(), $query);
        if(mysqli_num_rows($result) > 0){
            mysqli_close($this->db->getDatabase());
            return true;
        }
        return false;
    }
    
    public function isValidEmail($email){
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    public function createNewUser($fullName, $phoneNumber, $email, $password){
        $isValid = $this->isValidEmail($email);
        if(!$isValid){
            $json['success'] = 0;
            $json['message'] = "Invalid email address";
        }
        else{
            $isExisting = $this->isEmailExist($email);
            if(!$isExisting)
            {
                $query = "INSERT INTO users (full_name, phone_number, email, password) VALUES ('$fullName', '$phoneNumber', '$email', '$password')";
                $inserted = mysqli_query($this->db->getDatabase(), $query);
                if($inserted == 1){
                    $json['success'] = 1;
                    $json['message'] = "Successfully registered the user";  
                }else{
                    $json['success'] = 0;
                    $json['message'] = "Error in registering."; 
                }
                mysqli_close($this->db->getDatabase());
            }
            else{
                $json['success'] = 0;
                $json['message'] = "User with that email address already exists.";
            }
        }
        return $json;
    }
    
    public function getUserKeys($userId)
    {
        $user = array();
        $query = "SELECT * FROM encryption_keys WHERE user_id_fk='$userId' LIMIT 1";
        $result = mysqli_query($this->db->getDatabase(), $query);
        if(mysqli_num_rows($result) > 0){
            while($row = $result->fetch_assoc()){
                mysqli_close($this->db->getDatabase());
                $user["messages_key"] = $row["messages_key"];
                $user["contacts_key"] = $row["contacts_key"];
                $user["notes_key"] = $row["notes_key"];
                $user["files_key"] = $row["files_key"];
            }
        }
        return $user;
    }
    
    public function loginUser($email, $password){
        $json = array();
        $user = $this->getUser($email, $password);
        if($user!=0){
            $userKeys = $this->getUserKeys($user["id"]);
            $json['success'] = 1;
            $json['user_id'] = $user["id"];
            $json['message'] = "Login successfull";
            $json['messages_key'] = $userKeys["messages_key"];
            $json['contacts_key'] = $userKeys["contacts_key"];
            $json['notes_key'] = $userKeys["notes_key"];
            $json['files_key'] = $userKeys["files_key"];
            $json['isActive'] = $user["isActive"];
        }else{
            $json['success'] = 0;
            $json['message'] = "Incorrect credentials";
        }
        return $json;
    }
}
?>
