<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>問與答 列表</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
  <div class="m-2">
    <table class="table table-bordered">
      <thead>
        <tr>
          <td>#</td>
          <td>信件標題</td>
          <td>寄件人</td>
          <td>寄件人地址</td>
          <td>寄件時間</td>
          <td>信件內文</td>
        </tr>
      </thead>
      <tbody>
        <?php
        
        include 'DBinfo.php';
        
        $query = "SELECT * FROM `mail` ORDER BY `time`DESC";
        $result = $DB_Connect->query($query);
        if (!$result) die("Fatal Error");
        
        $rows = $result->num_rows;//符合qurey查找資格的行數有幾行，等等for迴圈要用
        
        for ($j = 0 ; $j < $rows ; ++$j)
        {
        $array = $result->fetch_array(MYSQLI_ASSOC);
       
        $html  = '';
        $html .= '<tr>';
        
        $html .= '<td>';
        $html .= $j+1;
        $html .= '</td>';

        $html .= '<td>';//信件標題↓
        $html .= $array['title'];
        $html .= '</td>';

        $html .= '<td>';//寄件人
        $html .= $array['from_who'];
        $html .= '</td>';

        $html .= '<td>';//寄件人地址
        $html .= $array['from_address'];
        $html .= '</td>';

        $html .= '<td>';//寄件時間
        $html .= $array['time'];
        $html .= '</td>';

        $html .= '<td>';//寄件內文
        $html .= $array['message_text'];
        $html .= '</td>';

        $html .= '</tr>';
        echo $html;
        }

        ?>

        

      </tbody>
    </table>
  </div>

</body>
</html>