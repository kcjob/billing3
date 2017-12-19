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
    $getQuery = "SELECT CII.invoice, CII.details, CII.service_id, CII.total, CI.filename, ci.payer, cu.email, concat(cu.firstname, ' ', cu.lastname) AS name FROM core_invoice_item AS CII, core_invoice AS CI, core_users AS CU WHERE CII.invoice = CI.number and cu.id = ci.payer";

    $getResults = $dbConnection->query($getQuery);
    //print($getResults->num_rows);
    $obj = new ServiceDetails();

    //Create an array of each user as objects
		while($row = $getResults -> fetch_assoc()){

      //check whether data is already in object
      if(!$obj->userName and $row['name'])
      {
        $obj->userName = $row['name'];
        $obj->email = $row['email'];

        array_push($obj->serviceInfoArray, $obj->details = $row['details']);
        if(!in_array($row['filename'], $obj->attachmentArray))
        {
          array_push($obj->attachmentArray, $obj->fileName = $row['filename']);
        }
        $obj->invoiceNumber = $row['invoice'];
        //$obj->details = $row['details'];
        $obj->service_id = $row['service_id'];
        $obj->total = $row['total'];
        //array_push($allServices, $obj);
      }elseif($obj->userName == $row['name']) {

        array_push($obj->serviceInfoArray, $obj->details = $row['details']);
        if(!in_array($row['filename'], $obj->attachmentArray))
        {
          array_push($obj->attachmentArray, $obj->fileName = $row['filename']);
        }
      } elseif ($obj->userName != $row['name']){

        var_dump($obj);

        echo '***************************' . "\r\n";
        echo '*CONFIGURE AND SEND EMAIL *' . "\r\n";
        echo 'ß**************************' . "\r\n";

        $obj = new ServiceDetails();

        $obj->userName = $row['name'];
        $obj->email = $row['email'];

        array_push($obj->serviceInfoArray, $obj->details = $row['details']);
        if(!in_array($row['filename'], $obj->attachmentArray))
        {
          array_push($obj->attachmentArray, $obj->fileName = $row['filename']);

          $obj->invoiceNumber = $row['invoice'];
          $obj->service_id = $row['service_id'];
          $obj->total = $row['total'];
        }
      }
		} //end while

    var_dump($obj);

    echo '***************************' . "\r\n";
    echo '*CONFIGURE AND SEND EMAIL *' . "\r\n";
    echo 'ß**************************' . "\r\n";

		//return $allServices;
  }

}
