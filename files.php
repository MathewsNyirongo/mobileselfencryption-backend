<?php
    require_once('dbConnect.php');
    // require_once('dbConnect.php');
    // $db = new DbConnect();
    // $conn = $db->getDatabase();

    // if($_SERVER['REQUEST_METHOD']=='POST'){
    //     $ImageData = $_POST['image_data'];
    //     $ImageName = $_POST['image_tag'];
    //     $ImagePath = "upload/$ImageName.jpg";
    //     $ServerURL = "localhost:80/mobileselfencryption/$ImagePath";
    //     $userId = $_POST['user_id_fk'];
	// 	$sql = "INSERT INTO files (file_name, file_path, user_id_fk) VALUES ('$ImageName', '$ServerURL', '$userId')";
        
    //     if(mysqli_query($conn, $sql)){
 
    //         file_put_contents($ImagePath,base64_decode($ImageData));
    //         $json['success'] = 1;
    //         $json['message'] = "Image Uploaded Successfully";
	// 		echo json_encode($json);
    //     }else{
    //         $json['success'] = 0;
    //         $json['message'] = "Error Uploading Image";
	// 		echo json_encode($json);
	// 	}
	// 	mysqli_close($conn);
	// }else{
	// 	if($_SERVER['REQUEST_METHOD']=='GET'){
    //         $id = $_GET['id'];
    //         $sql = "select * from images where id = '$id'";
    //         $r = mysqli_query($con,$sql);
    //         $result = mysqli_fetch_array($r);
    //         header('content-type: image/jpeg');
    //         echo base64_decode($result['image']);
    //         mysqli_close($con);
            
    //     }else{
    //         echo "Error";
    //     }
    // }
    
    class Files{
        private $db;
        public function __construct(){
            $this->db = new DbConnect();
        }
        public function insert(){
            $imageName = $_FILES['image']['name'];

            $target = "images/".basename($imageName);
            $sql = "INSERT INTO files (file_name, file_path, user_id_fk) VALUES ('$imageName', '$target', '$userId')";
            try {
                $result = mysli_query($this->db->getDatabase(), $sql);
                if ($result == 1) {
                    move_uploaded_file($_FILES['image']['tmp_name'], $target);
                    $json['success'] = 1;
                    $json['message'] = "Successfully uploaded";
                } else {
                    $json['success'] = 0;
                    $json['message'] = "Upload failed";
                }
                mysqli_close($this->db->getDatabase());
            } catch (Exception $e) {
                $json['success'] = 0;
                $json['message'] = "Error: ".$e->getMessage();
                mysqli_close($this->db->getDatabase());
            }
        }

        public function select(){
            $sql = "SELECT * FROM files";
            $result = mysqli_query($this->db->getDatabase(), $sql);
            if ($result->num_rows>0) {
                $i = 0;
                $json = array();
                while($row = $result->fetch_assoc()){
                    $userFile = new stdClass;
                    $userFile->id = $row["id"];
                    $userFile->file_name = $row["file_name"];
                    $userFile->file_path = $row["file_path"];
                    $json["file".$i] = $userFile;
                    $i++;
                }
                $json["success"] = 1;
                $json["files"] = $i;
                echo json_encode($json);
            } else {
                $json['success'] = 0;
                $json['message'] = "Failed to retrieve your images";
                echo json_encode($json);
            }
        }

        public function handleRequest(){
            if (isset($_POST['file_name'])) {
                $this->insert();
            } else {
                $this->select();
            }
        }
    }

    $file = new Files();
    $file->handleRequest();
?>