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
    foreach ($dataArray as &$value) {
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


function saveMailtoPDF($dataArray, $file_path){

    $title = $dataArray['title'];
    // ob_start();
    // echo "<html>";
    // echo "寄件人: ".$dataArray['from_who']."<br>";
    // echo "寄件人地址: ".$dataArray['from_address']."<br>";
    // echo "信件標題: ".$title."<br>";
    // echo "來信時間: ".$dataArray['time']."<br>";
    // echo "附件: ".$file_path."<br>";//之後加if判斷輸出
    // echo "<h1>信件內容請見下頁:</h1> "."<br>";
    // echo $dataArray['message_text'];
    // echo "</html>";
    // $content = ob_get_clean();
    $mail_info = '';
    $mail_info .= "<h1>本頁為寄件資訊:</h1>  "."<br>";
    $mail_info .= "寄件人: ".$dataArray['from_who']."<br>";
    $mail_info .= "寄件人地址: ".$dataArray['from_address']."<br>";
    $mail_info .= "信件標題: ".$title."<br>";
    $mail_info .= "來信時間: ".$dataArray['time']."<br>";
    $mail_info .= "附件: ".$file_path."<br>";//之後加if判斷輸出
    $mail_info .= "<h1>信件內容請見下頁:</h1>  "."<br>";
    $mail_info .= "<h1>=========================</h1>  "."<br>";
    $titleForFilename = str_replace(array('\\','/',':','*','?','"','<','>','|'),' ',$title);
    
    $config = [
        'mode' => '-aCJK', 
        "autoScriptToLang" => true,
        "autoLangToFont" => true,
    ];

    $mpdf =new \Mpdf\Mpdf($config);
    $mpdf->WriteHTML($mail_info);
    $mpdf->AddPage();
    $mpdf->WriteHTML(($dataArray['message_text']));
    $mpdf->Output("$titleForFilename".".pdf");


}