<?php
/*
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
*/
require __DIR__ . '/vendor/autoload.php';
MercadoPago\SDK::setAccessToken('APP_USR-8709825494258279-092911-227a84b3ec8d8b30fff364888abeb67a-1160706432');
MercadoPago\SDK::setPublicKey('APP_USR-ff96fe80-6866-4888-847e-c69250754d38');  ///////////////
MercadoPago\SDK::setIntegratorId("dev_24c65fb163bf11ea96500242ac130004");

  switch($_POST["type"]) {
      case "payment":
          $payment = MercadoPago\Payment::find_by_id($_POST["data"]["id"]);
          break;
      case "plan":
          $plan = MercadoPago\Plan::find_by_id($_POST["data"]["id"]);
          break;
      case "subscription":
          $plan = MercadoPago\Subscription::find_by_id($_POST["data"]["id"]);
          break;
      case "invoice":
          $plan = MercadoPago\Invoice::find_by_id($_POST["data"]["id"]);
          break;
      case "point_integration_wh":
          // $_POST contiene la informaciòn relacionada a la notificaciòn.
          break;
  }
  

	//$json = file_get_contents("php://input");
    //file_put_contents("./webhook.txt", $json . PHP_EOL);
 
  //$wh = fopen( __DIR__ .'/webhook.txt', 'r+b');
  //////////$json = json_encode($json);
  //fwrite($wh, print_r($json, true));
  ///////////fwrite($wh, $json);
  //fclose($wh);
  


        $json = file_get_contents("php://input");
		header_remove();
        $code = '200 OK';
        http_response_code($code);
		header("Cache-Control: no-transform,public,max-age=300,s-maxage=900");
        header('Content-Type: application/json');
		header('Status: 200 OK');
        file_put_contents("./webhook.txt", $json);


?>