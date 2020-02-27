<?php
include_once './config/database.php';
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$secret_key = "JHJHBJHb&@#@dsdjkskjdh===";
$jwt = null;
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));


$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

$arr = explode(" ", $authHeader);


/*echo json_encode(array(
    "message" => "sd" .$arr[1]
));*/

$jwt = $arr[1];

if($jwt){

    try {

        $pid = "-1";
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 

        $user_id = $decoded->data->id;

        $table_name = 'Products';

        $product_name = $data->name;
        $product_category = $data->category;
        $product_reminder_interval = $data->reminder_interval;
        $product_expiry_date = $data->product_expiry_date;
        $product_created_date = date("d/m/yy");

        $query = "INSERT INTO " . $table_name . " (`id`, `user_id`, `name`, `category`, `reminder_interval`, `expiry_date`, `created_date`) VALUES (NULL, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare( $query );
        $stmt->bindParam(1, $user_id);
        $stmt->bindParam(2, $product_name);
        $stmt->bindParam(3, $product_category);
        $stmt->bindParam(4, $product_reminder_interval);
        $stmt->bindParam(5, $product_expiry_date);
        $stmt->bindParam(6, $product_created_date);

        $stmt->execute();
        $num = $stmt->rowCount();

        $rows = array();

        if($num == 1){
            http_response_code(200);
            echo json_encode(array(
                "message" => "Product added Succesfully",
                "status" => "Success"
            
            ));
        }else{
            http_response_code(403);
            echo json_encode(array(
                "message" => "Failed",
                "status" => "Error"
            ));
        }

        

    }catch (Exception $e){

    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
}

}else{
    http_response_code(401);
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => "error"
    ));
}
?>