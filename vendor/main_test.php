<?php
date_default_timezone_set("Asia/Taipei");
require __DIR__ . '/autoload.php';

use Ddeboer\Imap\Server;

$server = new Server('imap.gmail.com');

// $connection is instance of \Ddeboer\Imap\Connection
$connection = $server->authenticate('bibian.IMAP2020@gmail.com', 'bibian12345');


$mailboxes = $connection->getMailboxes();

foreach ($mailboxes as $mailbox) {
    // Skip container-only mailboxes
    // @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
    if ($mailbox->getAttributes() & \LATT_NOSELECT) {
        continue;
    }

    // $mailbox is instance of \Ddeboer\Imap\Mailbox
    printf('Mailbox "%s" has %s messages', $mailbox->getName(), $mailbox->count());
}

$mailbox = $connection->getMailbox('INBOX');

$messages = $mailbox->getMessages();

foreach ($messages as $message) {
    if($message->isSeen())  
    echo '<br>'."=============大分隔線-區分不同封信件=============".'<br>';
   
    echo '<br>'.'<----------------每封信的時間差----------------->'.'<br>';
    echo time();
    echo '<br>'.'<----------------每封信的時間差----------------->'.'<br>';
    if($message->isSeen()){
        echo"這封讀過了讀過了讀過了讀過了讀過了讀過了讀過了讀過了".'<br>';
        echo"※※※※※※※※讀信專用符號※※※※※※※※※※";
    } else{
        echo "＃＃＃＃＃＃＃＃還沒看過還沒看過＃＃＃＃＃＃＃＃＃".'<br>';
    }
    $message->setFlag('\\Seen');

    echo '以下是$message->getId()'.'<br>';
    var_dump(htmlspecialchars($message->getId())); 
    /**
     * 這個ID是unique的，存在DB要用這個當Primary Key
     * <q5XcvA8MCnRAUsaw-GOJdw.0@notifications.google.com>
     * 網頁上無法直接顯示，因為這個外面包了一個<>
    */
    echo '<br>'.'<--------------------------------->'.'<br>';


    // echo '以下是$message->getFrom()'.'<br>';
    // var_dump($message->getFrom()); 
    // /**
    //  * ->name:
    //  * ->address:
    //  */
    // echo '<br>'.'<--------------------------------->'.'<br>';


    echo '以下是$message->getFrom()->getName()'.'<br>';
    var_dump($message->getFrom()->getName()); //寄件人姓名
    echo '<br>'.'<--------------------------------->'.'<br>';

    echo '以下是$message->getFrom()->getAddress()'.'<br>';
    var_dump($message->getFrom()->getAddress());  //寄件人信箱
    echo '<br>'.'<--------------------------------->'.'<br>';

    echo '以下是$message->getSubject()'.'<br>';
    var_dump($message->getSubject()); //信件標題
    echo '<br>'.'<--------------------------------->'.'<br>';

    echo '以下是$message->getTo()'.'<br>';
    var_dump($message->getTo()); 
    echo '<br>'.'<--------------------------------->'.'<br>';

    echo '以下是$message->getDate()'.'<br>';
    var_dump(date('Y-m-d H:i:s',$message->getDate()->getTimestamp())); 
    /**'Y-m-d H:i:s'
     * 這個是信件的時間，格式如下：
     * object(DateTimeImmutable)#38 (3) { ["date"]=> string(26) "2020-10-05 07:54:19.000000" ["timezone_type"]=> int(2) ["timezone"]=> string(3) "GMT" }
     */
    echo '<br>'.'<--------------------------------->'.'<br>';

    // echo '以下是$message->getBodyHtml()'.'<br>';
    // var_dump($message->getBodyHtml()); 
    // echo '<br>'.'<--------------------------------->'.'<br>';

    echo '以下是$message->getBodyText()'.'<br>';
    var_dump($message->getBodyText()); 
    /**
     * 這個就是文字內容了
     */
    echo '<br>'.'<--------------------------------->'.'<br>';

    echo '以下是$message->getAttachments()'.'<br>';
    var_dump($message->getAttachments()); 
    /**
     * 附件
     */
    if($message->getAttachments()!=array()){
        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment) {
            /*
            *如果要限制大小就用getSize來限制
            */
            file_put_contents(
                '../pic/'. time() . $attachment->getFilename(),
                $attachment->getDecodedContent()
            );
        }   
    }
     
    echo '<br>'.'<--------------------------------->'.'<br>';
    

    echo '<br>'."=============大分隔線-區分不同封信件=============".'<br>';
}



?>