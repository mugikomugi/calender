<?php
    //セッション破棄
    session_start();
    $_SESSION = array();
    if(isset($_COOKIE[session_name()]) === true){
        setcookie(session_name(),'',time()-4200,'/');
    }
    session_destroy();
?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>スケジュールカレンダー | ログアウト</title>
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
    <h1 id="title">Myカレンダーログアウト</h1>
</header>
<main id="loginPage">
    <p class="guidance">ログアウトしました。</p>
    <p id="update"><a href="index.php">ログインページへ</a></p>
</main>
<footer>
   <small>Copyright</small>
</footer>
</div>
</body>
</html>