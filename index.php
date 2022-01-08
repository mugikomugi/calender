<?php
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


$err = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = $_POST['id'];
    $pass = $_POST['pass'];
    $id = html_esc($id);
    $pass = html_esc($pass);
    
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
    
    //IDとパスワード設定
    if($id === 'sample' && $pass === 'sample'){
        session_start();
        $_SESSION['login'] = 1;
        //control.phpへ飛ばす
        header('Location: control.php');
        exit();       
    } elseif($id === '' || $pass === '') {
        $err = 'IDとパスワードを入力してください。';
    } else {
        $err = 'IDかパスワード、もしくは両方違います。';
    }
}

?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>スケジュールカレンダー | ログイン</title>
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
    <h1 id="title">Myカレンダーログイン</h1>
</header>
<main id="loginPage">
    <form method="post" action="">
      <!-- トークンの値をvalueに -->
      <input type="hidden" name="csrf_token" value="<?php echo $token ?>">
      <div>
           <label>ID</label>
           <input type="text" name="id">
      </div>
      <div>
           <label>PASSWORD</label>
           <input type="password" name="pass">
       </div>
       <p class="center red"><?php echo $err; ?></p>
       <p id="update"><input type="submit" value="ログイン"></p>
    </form>
</main>
<footer>
   <small>Copyright</small>
</footer>
</div>
</body>
</html>