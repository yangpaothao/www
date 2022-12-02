<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
//https://github.com/PHPMailer/PHPMailer


function sendmail($sendto, $replyto, $ccto, $bccto, $subject, $body, $attachment)
{
    //$attachmnt is the file location
    require ('PHPMailer/src/Exception.php');
    require ('PHPMailer/src/PHPMailer.php');
    require ('PHPMailer/src/SMTP.php');
    $mail = new PHPMailer(true);
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();    
        /*//Send using SMTP
        $mail->Host       = 'smtp.office365.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'contact@aviontracker.com';                     //SMTP username
        $mail->Password   = 'Can!BePharm0KOO';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->SMTPSecure = 'STARTTLS '; //secure transfer enabled
        $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
         */
        $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'yangpaothao@hotmail.com';                     //SMTP username
        $mail->Password   = 'm42YxLq18HFrAEG6';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->SMTPSecure = 'STARTTLS '; //secure transfer enabled
        $mail->Port       = 587; 
        //Recipients
        $mail->setFrom('contact@avion.com', 'Mailer');
        
        //We have to do the $mail-> for each individual in the send to list
        foreach($sendto as $key => $value)
        {
            foreach($value as $key2 => $value2)
            {
                //file_put_contents('./dodebug/debug.text', "in sendmail: ".$key2." => ".$value2, FILE_APPEND);
                $mail->addAddress($key2, $value2); //'email', 'firstname lastname', firstname and lastname is optional
            }
            //$mail->addAddress('yangpaothao@hotmail.com', 'Yang Thao'); 
        }
        if(count($replyto) > 0)
        {
            foreach($sendto as $key => $value)
            {
                foreach($value as $key2 => $value2)
                {
                    //file_put_contents('./dodebug/debug.text', "in sendmail: ".$key2." => ".$value2, FILE_APPEND);
                    $mail->addReplyTo($key2, $value2); //'email', 'firstname lastname', firstname and lastname is optional
                }
            }
        }
        if(count($ccto) > 0)
        {
            foreach($sendto as $key => $value)
            {
                foreach($value as $key2 => $value2)
                {
                    //file_put_contents('./dodebug/debug.text', "in sendmail: ".$key2." => ".$value2, FILE_APPEND);
                    $mail->addCC($key2, $value2); //'email', 'firstname lastname', firstname and lastname is optional
                }
            }
        }
        if(count($bccto) > 0)
        {
            foreach($sendto as $key => $value)
            {
                foreach($value as $key2 => $value2)
                {
                    //file_put_contents('./dodebug/debug.text', "in sendmail: ".$key2." => ".$value2, FILE_APPEND);
                    $mail->addBCC($key2, $value2); //'email', 'firstname lastname', firstname and lastname is optional
                }
            }
        }
        //Attachments, this foreach will handle multiple attachments.
        if(count($attachment) > 0)
        {
            foreach($attachment as $value)
            {
                $mail->addAttachment($value);         //Add attachments
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
            }
        }
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = "$subject";
        $mail->Body    = "$body"; //<br> to break line
        $mail->AltBody = "$body";
        $mail->send();
        //echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}