<?php
    date_default_timezone_set('Asia/Tokyo'); //東京時間にする
    $y = date('Y');
    $m = date('m')+0;
    $week = ['日','月','火','水','木','金','土'];
    $lastday = date('t', mktime(0, 0, 0, $m, 1, $y));
    
    //json取得
    $file = file_get_contents('./json/days'.$m.'.json');
    $data = mb_convert_encoding($file, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $array = json_decode($data,true);
    
    //デコード
    function decode_html($word){
        return html_entity_decode($word);
    }

?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>スケジュールカレンダー | 当月</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<link rel="stylesheet" href="common/reset.css">
	<link rel="stylesheet" href="common/style.css">
	<!--ファビコン32x32-->
	<link rel="shortcut icon" href="favicon.ico" type="image/vnd.microsoft.icon">
</head>
<body>
<div id="wrapper">
<header id="frontHeader">
    <h1 id="title">Myカレンダー予定表</h1>
    <p>管理画面から書き出されたものが表示されています。</p>
</header>
<main id="scheduleMain">
    <h2 id="ym"><?php echo $y; ?>年<span><?php echo $m; ?></span>月</h2>
    <p id="next"><a href="schedule2.php">翌月<span class="material-icons md-24">keyboard_arrow_right</span></a></p>
    <table id="cale">
        <tr>
            <?php
                foreach($week as $weeks){
                    echo '<td>'.$weeks.'</td>';
                }       
            ?>
        </tr>
        <tr>
        <?php
            // 1日の曜日を取得
            $wd1 = date('w', mktime(0, 0, 0, $m, 1, $y));
            // その数だけ空白を表示
            for ($i = 1; $i <= $wd1; $i++) {
                echo "<td> </td>";
            }
            // 1日から月末日までの表示
            $d = 1;
            $n = 0;
            //当月用休日設定
            require_once(dirname(__FILE__).'/holiday.php');
            //holiday.phpで祝日$holiday設定
            for($d = 1; $d <= $lastday; $d++){
                echo '<td class="'.$holiday[$d].'"><span class="days">'.$d.'</span><div class="memoUp">'.decode_html($array[$n]).'</div></td>';
                if (date("w", mktime(0, 0, 0, $m, $d, $y)) == 6) {
                    // 週を終了
                    echo "</tr>";
                    // 次の週がある場合は新たな行を準備
                    if (checkdate($m, $d + 1, $y)) {
                        echo "<tr>";
                    }
                }
                $n++;
            }
            // 最後の週の土曜日まで空欄を作る
            $wdx = date("w", mktime(0, 0, 0, $m + 1, 0, $y));
            for ($i = 1; $i < 7 - $wdx; $i++) {
            echo "<td> </td>";
            }
        ?>
        </tr>
    </table>
</main>
<footer>
       <small>Copyright</small>
   </footer>
</div>
</body>
</html>