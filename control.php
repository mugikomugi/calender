<?php
    session_start();
    session_regenerate_id(true);
    if(isset($_SESSION['login']) === false){
        echo 'ログインしていません。';
        exit();
    }

    //XSS
    function html_esc($word){
        return htmlspecialchars($word,ENT_QUOTES,'UTF-8');
    }
    
    //トークン生成
    function getCSRFToken()
    {
        $nonce = base64_encode(openssl_random_pseudo_bytes(48));
        setcookie('XSRF-TOKEN', $nonce);
        return $nonce;
    }
    $token = getCSRFToken();
    $token = html_esc($token);
    

    date_default_timezone_set('Asia/Tokyo'); //東京時間にする
    $y = date('Y');
    $m = date('m')+0;//+intをつけないと祝日設定できない
    $week = ['日','月','火','水','木','金','土'];
    //月末の日を取得
    $lastday = date("t", mktime(0, 0, 0, $m , 1 , $y));

    //ファイル名は月で変動
    $file = file_get_contents('./json/days'.$m.'.json');
    $data = mb_convert_encoding($file, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
    $array = json_decode($data,true);  


    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        //postトークン追加
        function validateCSRFToken ($post_token)
        {
            return isset($_COOKIE['XSRF-TOKEN']) && $_COOKIE['XSRF-TOKEN'] === $post_token;
        }
        if(isset($_POST['csrf_token']) && validateCSRFToken($_POST['csrf_token'])){
            //OKだったら空文字でスルー
            echo '';
        } else {
            echo 'トークンが不正です。';
            exit();
        }    
        //パスをサーバーと合わせておく
        header('Access-Control-Allow-Origin: your_url');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('X-Frame-Options: SAMEORIGIN');
        //postトークンここまで
        
        //name値をfor文で書き出し
        for($i = 0; $i < $lastday; $i++){
            $name[$i] = html_esc($_POST['name'.$i]);
        }
        
        //JSON形式に変換 
        $json = json_encode($name);
        //JSON書き出し、パーミッション変更忘れずに
        //タイマー設定で前月にもデータを入れておかないと消えてしまう
        file_put_contents('./json/days'.($m-1).'.json',$json);
        file_put_contents('./json/days'.$m.'.json',$json);
        //jsonファイルを取得、valueに再代入
        $file = file_get_contents('./json/days'.$m.'.json');
        //文字コードをUTF-8に変換する
        $data = mb_convert_encoding($file, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $array = json_decode($data,true);

    }

?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>スケジュールカレンダー管理画面 | 当月</title>
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
   <header id="logout" class="clearfix"><a href="logout.php">ログアウト</a></header>
   <main>
     <h1 id="title">Myカレンダー管理画面</h1>
     <div id="infoLeft">
        <div>
            <p id="ym"><?php echo $y; ?>年<span><?php echo $m; ?></span>月</p>
            <p id="next"><a href="control2.php">翌月<span class="material-icons md-24">keyboard_arrow_right</span></a></p>
            <p id="view"><a target="_blank" rel="noopener" href="schedule.php">プレビュー</a></p>
        </div>
        <p id="info">HTMLも入力できます。<br>リンクや画像も貼ってカスタマイズができます。入力後更新ボタンを押すと表示用のページに自動入力されます。変更の場合は上書きしてください。</p>
     </div>
     <form action="" method="post">
     <!-- トークンの値をvalueに -->
     <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
     <table id="cale">
        <tr><?php
                foreach($week as $weeks){
                    echo '<td>'.$weeks.'</td>';
                }            
            ?>
        </tr>
        <?php
      // 1日の曜日を取得
            $wd1 = date("w", mktime(0, 0, 0, $m, 1, $y));
            // その数だけ空白を表示
            for ($i = 1; $i <= $wd1; $i++) {
            echo "<td> </td>";
            }
 
            // 1日から月末日までの表示
            $d = 1;
            $n = 0;

            //休日を共通にしたかったので別ファイルに
            require_once(dirname(__FILE__).'/holiday.php');
            //holiday.phpで祝日設定
            for($d = 1; $d <= $lastday; $d++) {
                echo '<td class="'.$holiday[$d].'"><span class="days">'.$d.'</span><textarea class="memo" name="name'.$n.'" value="'.$array[$n].'">'.$array[$n].'</textarea></td>';
                // 今日が土曜日の場合は…
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

            // 最後の週の土曜日まで移動
            $wdx = date("w", mktime(0, 0, 0, $m + 1, 0, $y));
            for ($i = 1; $i < 7 - $wdx; $i++) {
            echo "<td> </td>";
            }
        ?>
     </table>
     <p id="update"><input type="submit" value="更新する"></p>
     </form>
   </main>
   <footer>
       <small>Copyright</small>
   </footer>
</div>	
</body>
</html>