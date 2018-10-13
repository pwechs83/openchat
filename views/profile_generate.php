<?php

require_once (dirname(__DIR__).'/vendor/autoload.php');
use ChatApp\Session;
use ChatApp\Profile;
use Dotenv\Dotenv;
$dotenv = new Dotenv(dirname(__DIR__));
$dotenv->load();

$connect = mysqli_connect(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASSWORD'),
    getenv('DB_NAME')
);

$userId = Session::get('start');
$data = '';
if (isset($_POST['submit'])) {
    $data = Profile::getProfile($userId);
    if ($data != NULL):
        $status = get($_POST['status'], $data['status']);
        $edu = get($_POST['education'], $data['education']);
        $gender = get($_POST['gender'], $data['gender']);
        
        if(!empty($_FILES["userpic"]["tmp_name"])){
            
            $path = $_FILES["userpic"]["tmp_name"];
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64pic = 'data:image/' . $type . ';base64,' . base64_encode($data);
          
            $pic = get($base64pic, $data['pic']);
        }
        $query = "UPDATE profile set status = '$status', education = '$edu', gender = '$gender', pic = '$pic' where login_id = '$userId'";
        if ($result = $connect->query($query)) {
            header('Location:'.getenv('APP_URL').'/views/account.php');
        } else {
            header("Location:".getenv('APP_URL')."/views/error.php");
        }
    endif;
} else {
    header("Location:".getenv('APP_URL')."/views/error.php");
}

function get($value, $default)
{
    $value = trim($value);
    return (isset($value) ? $value : $default);
}
