<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if(isset($_POST['submit'])){

    $mail = new PHPMailer();

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'tengteng8132002@gmail.com';
    $mail->Password = 'zzvmemdazozxzadq';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('tengteng8132002@gmail.com');

    $mail->addAddress($_POST["email"]);

    $mail->isHTML(true);

    $mail->Subject = 'Message';
        $mail->Body = 'Dear, thank you for enquiry us!';

        if ($mail->send()){
            echo 'success';
        }
        
}else{
    echo 'not connected';
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
    <meta charset="UTF-8">
    <title>Send email</title>
    </head>

    <body>
        <form action="send.php" method="post">
            Email <input type="email" name="email" id="email" value=""><br/>
            Subject <input type="subject" name="subject" value=""><br/>
            Message <input type="message" name="message" value=""><br/>
            <button type="submit" name="submit" id="submit">Send</button>
        </form>
    </body>
</html>