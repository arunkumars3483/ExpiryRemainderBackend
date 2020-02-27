<?php
include_once './config/database.php';
require "./vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
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

        $decoded = JWT::decode($jwt, $secret_key, array('HS256'));

        // Access is granted. Add code of the operation here 

        $user_id = $decoded->data->id;

        $table_name = 'Products';

        $query = "SELECT * FROM " . $table_name . " WHERE user_id = ?";

        $stmt = $conn->prepare( $query );
        $stmt->bindParam(1, $user_id);
        $stmt->execute();
        $num = $stmt->rowCount();

        $rows = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            array_push($rows, $row);
          }
        http_response_code(200);
        echo json_encode(array(
            "message" => "Access granted:",
            "status" => "Success",
            "products" => $rows
        ));

    }catch (Exception $e){

    http_response_code(401);

    echo json_encode(array(
        "message" => "Access denied.",
        "status" => "Error",
        "error" => $e->getMessage()
    ));
}

}else{
    echo json_encode(array(
        "message" => "Access denied.",
        "status" => "Error",
        "error" => "error"
    ));
}
?>