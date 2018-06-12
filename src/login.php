<?php
    session_start();
    $token = md5(uniqid(rand(),true));
    $_SESSION['token'] = $token;

    $errMsg= '';
    if(isset($_SESSION['errMsg'])){
        $errMsg = $_SESSION['errMsg'];
        unset($_SESSION['errMsg']);
    }

    require_once './hidden/pathList.php';

?>




<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<title>ログイン</title>
</head>
<body>
	<h1>ログイン画面</h1>
	<form method="POST" action="<?= $path_login_judge ?>">

		<input type="hidden" name="token" value="<?=$token ?>">

    	ユーザＩＤ：<input type="text" name="id"><br>
    	パスワード：<input type="password" name="pass"><br>

    	<input type="submit" value="ログイン">

	</form>

	<h1 style="color:red"><?=$errMsg?></h1>

</body>
</html>