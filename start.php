<?php
require_once(__DIR__ .'/vendor/autoload.php');

use \Apps\DBConnect;
use \Apps\ServicesDAO;
use \Apps\ServiceDetails;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('bills');
$dbStream = new StreamHandler('data/billing.log', Logger::ERROR);
$log->pushHandler($dbStream);

try {
    $connection = DBConnect::getConnection();
} catch (Exception $e) {
    $log->error($e->getMessage());
    echo "Problem retriving data from file\r\n";
    die();
}

try {
    $servicesObject = ServicesDAO::getServices($connection);
} catch (Exception $e) {
    $log->error($e->getMessage());
    echo "Problem retriving data from file\r\n";
    die();
}
print_r($servicesObject);
