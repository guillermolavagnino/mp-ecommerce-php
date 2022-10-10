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
  
  //if (isset($payment) && !empty($payment)) {
	//$json = file_get_contents($payment);
	$json = file_get_contents("php://input");
    //file_put_contents("https://glavagnino-mp-ecommerce-php.herokuapp.com/webhook.txt", $json . PHP_EOL);
  //}
 
  $wh = fopen( __DIR__ .'/webhook.txt', 'r+b');
  //fwrite($wh, "WEBHOOK");
  $json = json_decode($json, true);
  $json = json_encode($json . PHP_EOL, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  fwrite($wh, $json);
  fclose($wh);


 
   $merchant_order = NULL;
 
   switch($_GET["topic"]) {
       case "payment":
           $payment_ipn = MercadoPago\Payment::find_by_id($_GET["id"]);
           // Get the payment and the corresponding merchant_order reported by the IPN.
           $merchant_order = MercadoPago\MerchantOrder::find_by_id($payment_ipn->order->id);
           break;
       case "merchant_order":
           $merchant_order = MercadoPago\MerchantOrder::find_by_id($_GET["id"]);
           break;
   }
 
   $paid_amount = 0;
   foreach ($merchant_order->payments as $payment_ipn) {  
       if ($payment_ipn['status'] == 'approved'){
           $paid_amount += $payment_ipn['transaction_amount'];
       }
   }
 
   // If the payment's transaction amount is equal (or bigger) than the merchant_order's amount you can release your items
   if($paid_amount >= $merchant_order->total_amount){
       if (count($merchant_order->shipments)>0) { // The merchant_order has shipments
           if($merchant_order->shipments[0]->status == "ready_to_ship") {
               $msj = "Totalmente pagado. Imprime la etiqueta y libera tu artículo.";
			   file_put_contents("./ipn.txt", $msj);
           }
       } else { // The merchant_order don't has any shipments
           $msj = "Totalmente pagado. Libera tu artículo.";
		   file_put_contents("./ipn.txt", $msj);
       }
   } else {
       $msj = "Aún no pagado. No liberes tu artículo.";
	   file_put_contents("./ipn.txt", $msj);
   }

header("HTTP/1.1 200 OK");

?>