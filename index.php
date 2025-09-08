<?php 

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: *");

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require "vendor/autoload.php";
use libams\oApp;
$app = new oApp('SGM','10.10.77.106','sgp','pa55word','dbsgm','3306');


$mth = $_SERVER['REQUEST_METHOD'];
 


switch ($mth)
{
    case 'GET':
        $oData = $_GET;
    break;

    case 'POST':
    if (strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false) {
        $input = file_get_contents("php://input");
        $oData = json_decode($input, true);
    } else 
    {
        // Form biasa
        $oData = $_POST;
    }
    break;
   

    case 'PUT':
    $oData = json_decode(file_get_contents("php://input"), true);
    break;

    default:
    // Kode 405 kalau tidak ditangani
    http_response_code(405);
    $oData = array("status"=>0,"method"=>$mth);


}

$token = $oData["q"];

switch ($token)
{
    case 'coa':
        require "../server/accounting/acc.coa.php";
        break ;
    case 'mastercabang':
        require "../server/global.mastercabang.php";
        break ;

    default:
        $rsp = array("status"=>0,"msg"=>"Running, token undefines ".$token);
        echo json_encode($rsp);
        break;
}