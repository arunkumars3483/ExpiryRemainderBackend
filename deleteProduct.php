<?php
include_once './config/database.php';
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: DELETE");
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

        $prod_id = "0";
        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 

        $user_id = $decoded->data->id;

        $table_name = 'Products';

        $prod_id = $data->id;
        
        $query = "DELETE FROM " . $table_name . " WHERE id = ? AND user_id = ?";

        $stmt = $conn->prepare( $query );
        $stmt->bindParam(1, $prod_id);
        $stmt->bindParam(2, $user_id);


        $stmt->execute();
        $num = $stmt->rowCount();

        $rows = array();

        if($num == 1){
            echo json_encode(array(
                "message" => "Product deleted Succesfully",
                "status" => "Success",
                "products" => $num
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
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => "error"
    ));
}
?>