<?php

namespace Apps;

class ServicesDAO
{
  /**
  * Data Access Object
  * Because static methods are callable without an instance of the object created,
  * the pseudo-variable $this is not available inside the method declared as static.
  */
  private $option_name;
  private $user_id;

  function __construc()
  {

  }

  static function getServices($dbConnection)
  {
    $allServices = [];
    $getQuery = "SELECT CII.invoice, CII.details, CII.service_id, CII.total, CII.total, CI.filename, ci.payer, cu.email, concat(cu.firstname, ' ', cu.lastname) AS name FROM core_invoice_item AS CII, core_invoice AS CI, core_users AS CU WHERE CII.invoice = CI.number and cu.id = ci.payer";

    $getResults = $dbConnection->query($getQuery);
    //print($getResults->num_rows);
    $billObject = new ServiceDetails();

    //Create an array of each user as objects
		while($row = $getResults -> fetch_assoc()){

      //check whether data is already in object
      if(!$billObject->userName and $row['name'])
      {
        $billObject->userName = $row['name'];
        $billObject->email = $row['email'];

        array_push($billObject->serviceInfoArray, $billObject->details = $row['details']); #, $row['total']);
        if(!in_array($row['filename'], $billObject->attachmentArray))
        {
          array_push($billObject->attachmentArray, $billObject->fileName = $row['filename']);
        }
        $billObject->invoiceNumber = $row['invoice'];
        //$billObject->details = $row['details'];
        $billObject->service_id = $row['service_id'];
        $billObject->total = $row['total'];
        //array_push($allServices, $billObject);
      }elseif($billObject->userName == $row['name']) {

        array_push($billObject->serviceInfoArray, $billObject->details = $row['details']);
        if(!in_array($row['filename'], $billObject->attachmentArray))
        {
          array_push($billObject->attachmentArray, $billObject->fileName = $row['filename']);
        }
      } elseif ($billObject->userName != $row['name']){

        //echo '***************************' . "\r\n";
        //echo '*CONFIGURE AND SEND EMAIL *' . "\r\n";
        //echo 'ß**************************' . "\r\n";
try {
        EmailMessageGenerator::createEmail($billObject);
} catch(Exception $e){
        $log->error($e->getMessage());
        echo "Could not generate email message\r\n";
        die();
      }

        $billObject = new ServiceDetails();

        $billObject->userName = $row['name'];
        $billObject->email = $row['email'];

        array_push($billObject->serviceInfoArray, $billObject->details = $row['details']); //, $row['total']);
        if(!in_array($row['filename'], $billObject->attachmentArray))
        {
          array_push($billObject->attachmentArray, $billObject->fileName = $row['filename']);

          $billObject->invoiceNumber = $row['invoice'];
          $billObject->service_id = $row['service_id'];
          $billObject->total = $row['total'];
        }
      }
		} //end while

    //echo '***************************' . "\r\n";
    //echo '*CONFIGURE AND SEND EMAIL *' . "\r\n";
    //echo 'ß**************************' . "\r\n";

    try {
            EmailMessageGenerator::createEmail($billObject);
    } catch(Exception $e){
            $log->error($e->getMessage());
            echo "Could not generate email message\r\n";
            die();
          }


		//return $allServices;
  }

}
