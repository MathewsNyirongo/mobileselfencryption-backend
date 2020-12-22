
<?php
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: POST, GET");
    header("Access-Control-Allow-Headers: *");
    require_once 'users.php';
    require_once 'contacts.php';
    $phoneNumber = "";
    $fullName = "";
    $password = "";
    $email = "";
    $userId = "";

    $data = json_decode(file_get_contents('php://input'));


    if (isset($data->email)) {
        $email = $data->email;
    }

    if(isset($data->password)){
        $password = $data->password;
    }

    if (isset($_POST['user_id_fk'])) {
        $userId = $_POST['user_id_fk'];
    }

    if(isset($_POST['full_name'])){
        $fullName = $_POST['full_name'];
    }

    if(isset($_POST['password'])){
        $password = $_POST['password'];
    }
    
    if(isset($_POST['email'])){
        $email = $_POST['email'];
    }
    
    if(isset($_POST['phone_number'])){
        $phoneNumber = $_POST['phone_number'];
    }

    $userObject = new Users();
    
    // Registration
    
    if(!empty($fullName) && !empty($password) && !empty($email) && !empty($phoneNumber)){
        $hashed_password = md5($password);
        $json_registration = $userObject->createNewUser($fullName, $phoneNumber, $email, $password);
        echo json_encode($json_registration);
    }
    
    // Login
    
    if(!empty($email) && !empty($password) && empty($fullName)){
        $hashed_password = md5($password);
        $json_array = $userObject->loginUser($email, $password);
        echo json_encode($json_array);
    }



    if(!empty($userId)) {
        $json_array = $keys->getUserKeys($userId);
        echo json_encode($json_array);
    }

    
?>
