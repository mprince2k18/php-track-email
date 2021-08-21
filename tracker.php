<?php
require ('config.php');
require ('phpmailer/PHPMailerAutoload.php');

$mailfrom = 'mprince2k16@gmail.com';
$pwd = "386816b0a6f927";


//sender's password
//-------------------------------------------------------SEND eMail----------------------------------------------------------------------
if (isset($_POST['mailto'])){
    try {
        $mail = new PHPMailer(true); //New instance,exceptions enabled with true
        $mailto = $_POST['mailto'];
        $subject = $_POST['subject'];
        $uniq_id = generateUnique();        
        $body = $_POST['message'];
        if(!$body){
            $body .= "This is the fixed message of test email to get notify when it is read.....";
        }
        $body .= "<img border='0' src='".PROJECT_URL."/trackonline.php?email=$mailto&uniq_id=$uniq_id&subject=$subject' width='1' height='1' alt='image for email'>";
        $mail->IsSMTP();                           // tell the class to use SMTP
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->Port = 2525;             // set the SMTP server port
        $mail->Host = "smtp.mailtrap.io"; // SMTP server
        $mail->Username = 'ed66f2434c0f89';     // SMTP server username
        $mail->Password = $pwd;            // SMTP server password
        $mail->From = 'mprince2k16@gmail.com';
        $mail->FromName = $mailfrom;
        $mail->AddAddress($mailto);
        $mail->Subject = $subject;
        $mail->AltBody = "Please return read receipt to me."; // optional, comment out and test
        $mail->WordWrap = 80; // set word wrap
        $mail->MsgHTML($body);
        $mail->IsHTML(true); // send as HTML
        $mail->Send();
        $date = date('Y-m-d H:i:s');        
        if(mysqli_query($con,"INSERT INTO `emails`(`uniq_id`,`mailfrom`, `mailto`, `subject`, `body`, `status`, `read_status`, `created_at`, `updated_at`) VALUES ('".$uniq_id."','".$mailfrom."','".$mailto."','".$subject."','".mysqli_real_escape_string($con,$body)."','1','0','".$date."','".$date."')")){
           // echo 'SUCCESS';
        }else{
           //  echo 'FAILED'.mysqli_error($con);
        }
        //return foll
        echo '<input id="id1" name="uniq_id" type="hidden" value="' . $uniq_id . '">'   
        . '<input id="email1" name="email" type="hidden" value="' . $mailto . '">'
        . '<label id="label1">Mail sent to <b>' . $mailto . '<b></label>';
    } catch (phpmailerException $e) {
        echo $e->errorMessage();
    }
}
////------------------------------------------READ email.txt-------------------------------------------------------
if (!empty($_POST['uniq_id'])) {
    $uniq_id = $_POST['uniq_id'];
    $to = $_POST['email'];
    $q = mysqli_query($con,"SELECT * FROM `emails` WHERE uniq_id = '".$uniq_id."'");
    $res = mysqli_fetch_assoc($q);
    if ($res['read_status'] == '1'){
        //$string = $email . " seen the mail on subject: '" . $sub . "' from ip: " . $ipAddress . " on " . $date . " and Id:" . $id . "\n";
        echo "<img id=\"closed-image\" src=\"".PROJECT_URL."/img/envelope-open.png\" alt=\"email not opened\"/><br><p id=\"closed-para\">"
        . "Mail sent from <b>" . $mailfrom . "</b><br> To <b>" . $to
        . "</b><br>has been<div id=\"color-read\"> opened on <b>".date('jS F Y, h:i:sa',strtotime($res['updated_at']))."</b></div></p>"
        . "<input id=\"id1\" name=\"id\" type=\"hidden\" value=\"" . $uniq_id . "\">";  //appended hidden input to keep previous data on the page.
    } else {
        echo "<img id=\"closed-image\" src=\"".PROJECT_URL."/img/envelope-closed.png\" alt=\"email not opened\"/><br><p id=\"closed-para\">"
        . "Mail sent from <b>" . $mailfrom . "</b><br> To <b>" . $to
        . "</b><br><div id=\"color-not-read\"> Not yet opened......</div></p>"
        . "<input id=\"id1\" name=\"id\" type=\"hidden\" value=\"" . $uniq_id . "\">";  //appended hidden input to keep previous data on the page.
    }
}










