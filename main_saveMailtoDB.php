<?php
require __DIR__ . '/vendor/autoload.php';

use Ddeboer\Imap\Server;

// $server = new Server('imap.gmail.com');
$server = new Server('export.imap.mail.yahoo.com');
include 'DBinfo.php';
// $connection is instance of \Ddeboer\Imap\Connection
// $connection = $server->authenticate('bibian.IMAP2020@gmail.com', 'bibian12345');
$connection = $server->authenticate('andy860826@yahoo.com.tw', 'rcfftxtbiwgjrjkf');

// $mailbox = $connection->getMailbox('INBOX');//只讀主收件匣，垃圾桶等等不管
$mailbox = $connection->getMailbox('Inbox');
$messages = $mailbox->getMessages();

$count_seen = 0;
// echo $connection->count();
// exit();

foreach ($messages as $message) {
    if($message->isSeen()){
        $count_seen += 1;
    } else{
        // $message->setFlag('\\Seen');

        $message_id = htmlspecialchars($message->getId());
        $from_who = htmlspecialchars($message->getFrom()->getName());
        $from_address = htmlspecialchars($message->getFrom()->getAddress());
        $title = htmlspecialchars ($message->getSubject());
        $time = date('Y-m-d H:i:s',$message->getDate()->getTimestamp());
        $text = htmlspecialchars ($message->getBodyHtml());
        

        if($message->getAttachments()!=array()){//存附件
            $file_path = array();
            $attachments = $message->getAttachments();
            foreach ($attachments as $attachment) {
                //如果要限制大小就用getSize來限制
                $save_path = '../pic/'.time()."_".$attachment->getFilename();
                file_put_contents(
                    $save_path,
                    $attachment->getDecodedContent()
                );
                $file_path[] = $save_path;
            }   
            $file_path=implode(",",$file_path);
        } else{
            $file_path = '';
        }
      

        $insertSQL_mail = "INSERT INTO mail(`message_ID`, `from_who`, `from_address`, `title`, `time`, `message_text`, `message_file_location`) 
        VALUES ('$message_id', '$from_who', '$from_address', '$title', '$time', '$text', '$file_path');";        
        
        $DB_Connect->query($insertSQL_mail);

        echo '<br>'."=============大分隔線-區分不同封信件=============".'<br>';
    }

}


echo "收件匣中有".$mailbox->count()."封信".'<br>';
echo "其中有".$count_seen."封信已經讀過，並未存到資料庫中。".'<br>';