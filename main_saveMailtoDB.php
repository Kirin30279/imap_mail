<?php
require __DIR__ . '/vendor/autoload.php';
include 'src/saveFunction.php';
include 'LoginInfo.php';
use Ddeboer\Imap\Server;
echo time();//測試用，正式可刪

$server = new Server('pop.mail.yahoo.com');
$connection = $server->authenticate($username, $password);

$mailbox = $connection->getMailbox('INBOX');//只讀主收件匣
$messages = $mailbox->getMessages();

$count_seen = 0;//計算本次的信，正式可刪

foreach ($messages as $message) {
    if($message->isSeen()){
        $count_seen += 1;
    } else{
        // $message->setFlag('\\Seen');
        $dataArray = getDataFromMailbox($message);
        $dataArray = handleSpecialChar($dataArray);
        
        /*
        * saveAttachmentReturnPath是存附件，存完回傳路徑(type:String)
        * 可再想想如何拆，一個函數做一件事情就好
        */
        $file_path = saveAttachmentReturnPath($message);

        insertDataToDB($dataArray, $file_path);
        echo '<br>'."=============大分隔線-區分不同封信件=============".'<br>';//正式要刪
    }

}

echo "收件匣中有".$mailbox->count()."封信".'<br>';
echo "其中有".$count_seen."封信已經讀過，並未存到資料庫中。".'<br>';

echo time();//測試用，正式可刪
