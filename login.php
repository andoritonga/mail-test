<?php
$nama= $_POST['nama'];
$pesan= $_POST['pesan'];
include 'email.php';
include 'pengirim.php';

require 'PHPmailer/class.phpmailer.php';
$mail = new PHPMailer;

// Konfigurasi SMTP
$mail->isSMTP(true);
$mail->Host = 'webmail.banksampoerna.com';
$mail->SMTPAuth = true;
$mail->Username = $username;
$mail->Password = $password;
$mail->SMTPSecure = 'tls';
$mail->Port = 443;

$mail->setFrom($username, $alias);
$mail->addReplyTo($username, $alias);

// Menambahkan penerima
$mail->addAddress($to);

// Menambahkan beberapa penerima
//$mail->addAddress('penerima2@contoh.com');
//$mail->addAddress('penerima3@contoh.com');

// Menambahkan cc atau bcc 
//$mail->addCC('cc@contoh.com');
//$mail->addBCC('bcc@contoh.com');

// Subjek email
$mail->Subject = 'Kirim Email PHPMailer';

// Mengatur format email ke HTML
$mail->isHTML(true);

// Konten/isi email
$mailContent= "
Nama : $nama <br/>
Pesan: $pesan <br/><br/>
";
$mail->Body = $mailContent;

// Menambahakn lampiran
//$mail->addAttachment('lmp/file1.pdf');
//$mail->addAttachment('lmp/file2.png', 'nama-baru-file2.png'); //atur nama baru

// Kirim email
if(!$mail->send()){
	echo 'Mailer Error: ' . $mail->ErrorInfo;
}else{
	echo 'Pesan telah terkirim';
}
?>