<?PHP
/**
 * @param class $message
 * @return Array
 * 抓取指定信件的Data，尚未跳脫
 */
function getDataFromMailbox($message)
{   
    $dataArray = array(
        'message_ID' => $message->getId(),
        'from_who' => $message->getFrom()->getName(),
        'from_address' => $message->getFrom()->getAddress(),
        'title' => $message->getSubject(),
        'time' => date('Y-m-d H:i:s',$message->getDate()->getTimestamp()),
        'message_text' => $message->getBodyHtml()
    );

    return $dataArray;
}

/**
 * @param Array $dataArray
 * @return Array
 * 將訊息做跳脫
 */
function handleSpecialChar($dataArray)
{
    foreach ($dataArray as $value) {
        $value = htmlspecialchars($value);
    }
    return $dataArray;
}

/**
 * @param class $message
 * @return String $name
 * 
 */
function saveAttachmentReturnPath($message)
{
    if($message->getAttachments()!=array()){//存附件
        $file_path = array();
        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment) {
            //如果要限制大小就用getSize來限制
            $save_path = 'pic/'.time()."_".$attachment->getFilename();
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
    return $file_path;
}





/**
 * @param Array $dataArray 
 * @param String $file_path
 * 傳入的Array格式應為：
 * `message_ID`, `from_who`, `from_address`, `title`, `time`, `message_text` 
 * 
 */
function insertDataToDB($dataArray, $file_path)
{
    include 'DBinfo.php';
    foreach ($dataArray as $value) {
        $value = $DB_Connect->real_escape_string($value);
    }
    
    $message_id = $DB_Connect->real_escape_string($dataArray['message_ID']);
    $from_who = $DB_Connect->real_escape_string($dataArray['from_who']);
    $from_address = $DB_Connect->real_escape_string($dataArray['from_address']);
    $title = $DB_Connect->real_escape_string($dataArray['title']);
    $time = $dataArray['time'];
    $text = $DB_Connect->real_escape_string($dataArray['message_text']);
    $file_path = $DB_Connect->real_escape_string($file_path);
    
    $insertSQL_mail = "INSERT INTO yahoo_huge(`message_ID`, `from_who`, `from_address`, `title`, `time`, `message_text`, `message_file_location`) 
        VALUES ('$message_id', '$from_who', '$from_address', '$title', '$time', '$text', '$file_path');";        
        
    $DB_Connect->query($insertSQL_mail);

}
