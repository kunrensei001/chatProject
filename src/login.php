<?php
    session_start();
    $token = md5(uniqid(rand(),true));
    $_SESSION['token'] = $token;

    $errMsg= '';
    if(isset($_SESSION['errMsg'])){
        $errMsg = $_SESSION['errMsg'];
        unset($_SESSION['errMsg']);
    }

?>


<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>ログイン</title>
</head>
<body>
	<h1>ログイン画面</h1>
	<form method="POST" action="/chat_v01/logic/login_judge.php">

		<input type="hidden" name="token" value="<?=$token ?>">

    	ユーザＩＤ：<input type="text" name="id"><br>
    	パスワード：<input type="password" name="pass"><br>

    	<input type="submit" value="ログイン">

	</form>

	<h1 style="color:red"><?=$errMsg?></h1>

</body>
</html>