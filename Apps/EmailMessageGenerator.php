<?php
namespace Apps;

class EmailMessageGenerator
{
  static function createEmail($billObject){
    $emailParams = parse_ini_file("config/config.ini");

    $app = new TemplateView();
    $msg = $app->generateView($billObject);

     // Create the Transport
    $transport = (new \Swift_SmtpTransport('outgoing.ccny.cuny.edu', 587, 'tls'))
      -> setUsername($emailParams['userName'])
      -> setPassword($emailParams['userPassword']);
    $mailer = new \Swift_Mailer($transport);

        // create and register logger
    $sentEmaillogger = new \Swift_Plugins_Loggers_ArrayLogger();
    $mailer->registerPlugin(new \Swift_Plugins_LoggerPlugin($sentEmaillogger));

        // Create a message
    $message = (new \Swift_Message('Core Facilities Equipment Billings'))
      -> setFrom($emailParams['fromName'])
      -> setTo($emailParams['sentTo']) //($msgDataObject->userEmailAddress);
      -> setContentType("text/html")
      -> setBody($msg);

    if(!empty($billObject->attachmentArray)){
      foreach($billObject->attachmentArray as $document){
          $attachment = \Swift_Attachment::fromPath('data/' . $document);
          $message -> attach($attachment);
      }
    }

    //echo $message->toString();

        // Send the message
    $mailer->send($message, $failures);

        // output log
    file_put_contents('data/sentEmails.log', $sentEmaillogger->dump());
  }
}
