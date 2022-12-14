<?php
$nama= $_POST['nama'];
$pesan= $_POST['pesan'];
$to= $_POST['to'];

// //Import PHPMailer classes into the global namespace
// //These must be at the top of your script, not inside a function
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

// //Create an instance; passing `true` enables exceptions
// $mail = new PHPMailer(true);

// try {
//     //Server settings
//                 //Enable verbose debug output
//     $mail->isSMTP();                                            
//     $mail->Host       = 'smtp.banksampoerna.com';                    
//     $mail->SMTPAuth   = true;                                 
//     $mail->Username   = 'yohannes.ritonga@banksammpoerna.com';                    
//     $mail->Password   = 'November11';                             
//     $mail->Port       = 443;                                   

//     //Recipients
//     $mail->setFrom('yohannes.ritonga@banksampoerna.com', $nama);
//     $mail->addAddress($to, 'Joe User');     //Add a recipient
//     $mail->addReplyTo('yohannes.ritonga@banskampoerna.com', 'No-Reply');

//     //Content
//     $mail->isHTML(true);                                  //Set email format to HTML
//     $mail->Subject = 'Here is the subject';
//     $mail->Body    = $pesan;
//     $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

//     $mail->send();
//     echo 'Message has been sent';
// } catch (Exception $e) {
//     echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
// }
require_once 'php-ews/vendor/autoload.php';

use \jamesiarmes\PhpEws\Client;
use \jamesiarmes\PhpEws\Request\CreateItemType;
use \jamesiarmes\PhpEws\Request\SendItemType;

use \jamesiarmes\PhpEws\ArrayType\ArrayOfRecipientsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfAllItemsType;
use \jamesiarmes\PhpEws\ArrayType\NonEmptyArrayOfBaseItemIdsType;

use \jamesiarmes\PhpEws\Enumeration\BodyTypeType;
use \jamesiarmes\PhpEws\Enumeration\MessageDispositionType;
use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\ItemIdType;
use \jamesiarmes\PhpEws\Type\TargetFolderIdType;

use \jamesiarmes\PhpEws\Type\BodyType;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\MessageType;
use \jamesiarmes\PhpEws\Type\SingleRecipientType;

$message_id = 'AAMkADk0N2E4OTQxLWRlOTYtNGUxZC05NzE1LTU4ZmI5NGVkZTZmYQBGAAAAAADeofKHfJ96S5ndHNLg9VfeBwAr1MfeoTJdQ7jgaw/bSgljAAAAAAEPAAAr1MfeoTJdQ7jgaw/bSgljAABueQnrAAA=';
$change_key = 'CQAAABYAAAAr1MfeoTJdQ7jgaw/bSgljAABugzYP';

// Set connection information.
$host = 'webmail.banksampoerna.com';
$username = 'yohannes.ritonga@banksampoerna.com'; // Masukkan alamat email anda
$password = 'December12'; // Masukkan akun email anda Password anda
$version = Client::VERSION_2010;

$client = new Client($host, $username, $password, $version);

// Build the request,
$request = new CreateItemType();
$request->Items = new NonEmptyArrayOfAllItemsType();

// Save the message, but do not send it.
$request->MessageDisposition = MessageDispositionType::SEND_AND_SAVE_COPY;

// Create the message.
$message = new MessageType();
$message->Subject = 'EWS Test Message';
$message->ToRecipients = new ArrayOfRecipientsType();

// Set the sender.
$message->From = new SingleRecipientType();
$message->From->Mailbox = new EmailAddressType();
$message->From->Mailbox->EmailAddress = $username;

// Set the recipient.
$recipient = new EmailAddressType();
$recipient->Name = $nama;
$recipient->EmailAddress = $to;
$message->ToRecipients->Mailbox[] = $recipient;

// Set the message body.
$message->Body = new BodyType();
$message->Body->BodyType = BodyTypeType::TEXT;
$message->Body->_ = $pesan;

// Add the message to the request.
$request->Items->Message[] = $message;

$response = $client->CreateItem($request);

// Build the request.
$request = new SendItemType();
$request->SaveItemToFolder = true;
$request->ItemIds = new NonEmptyArrayOfBaseItemIdsType();

// Add the message to the request.
$item = new ItemIdType();
$item->Id = $message_id;
$item->ChangeKey = $change_key;
$request->ItemIds->ItemId[] = $item;

// Configure the folder to save the sent message to.
$send_folder = new TargetFolderIdType();
$send_folder->DistinguishedFolderId = new DistinguishedFolderIdType();
$send_folder->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::SENT;
$request->SavedItemFolderId = $send_folder;

$response = $client->SendItem($request);
if(!defined('STDIN'))  define('STDIN',  fopen('php://stdin',  'rb'));
if(!defined('STDOUT')) define('STDOUT', fopen('php://stdout', 'wb'));
if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));
$response_messages = $response->ResponseMessages->SendItemResponseMessage;
foreach ($response_messages as $response_message) {
    // Make sure the request succeeded.
    if ($response_message->ResponseClass != ResponseClassType::SUCCESS) {
        $code = $response_message->ResponseCode;
        $message = $response_message->MessageText;
        fwrite(STDERR, "Message failed to send with \"$code: $message\"\n");
        continue;
    }

    fwrite(STDOUT, "Message sent successfully.\n");
    echo '<script language="javascript">';
    echo 'alert("message successfully sent")';
    echo '</script>';
}

?>
