<?php $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'victor.ross.04@gmail.com';
$password = 'Felicidad2018.';
$inbox = imap_open($hostname,$username,$password);
$emails = imap_search($inbox,'ALL');
foreach($emails as $e){
    $overview = imap_fetch_overview($inbox,$e,0);
    $message = imap_fetchbody($inbox,$e,2);
    // the body of the message is in $message
    $details = $overview[0];
	var_dump($details);
    // you can do a var_dump($details) to see which parts you need
    //then do whatever to insert them into your DB
}?>