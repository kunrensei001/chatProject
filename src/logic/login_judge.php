<?php

session_start();

$session_token = '';
if(isset($_SESSION['token'])){
    $session_token = $_SESSION['token'];
}

$token = '';
if(isset($_POST['token'])){
    $token = $_POST['token'];
}

$errMsg='';
if($token === ''){
    $errMsg='不正な処理です。トップからログインしてください。';
}

if($token !== $session_token){
    $errMsg='不正な処理です。トップからログインしてください。';
}

if($errMsg !== ''){
    $_SESSION['errMsg'] = $errMsg;
    require('../login.php');
    exit();
}

$_SESSION['post_token']= $token;

?>


<?php

    require_once '../hidden/DBaccess.php';
    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
    $loginFLG = false;
    $userName = "";
    $imgPath = "";

    if(isSet($_POST['id']) && isSet($_POST['pass'])){

        try{
            $pdo = new PDO($dsn,$user,$password);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "select * from user where user = '".$_POST["id"]."'";
            $stm = $pdo->prepare($sql);
            $stm->execute();

            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
            foreach ($result as $row){
                if($row['pass'] == $_POST['pass']){
                    $loginFLG = true;
                    $userName = $row['userName'];
                    $imgPath = $row['imgPath'];
                }
            }

            $pdo = null;
        }catch(Exception $e){
            echo '<span class="error">エラーがありました。</span><br>';
            echo $e->getMessage();
            exit();
        }
    }

    session_start();
    $_SESSION['userName'] = $userName;
    $_SESSION['imgPath'] = $imgPath;

    if($loginFLG){

        require('./main.php');

    }else{
        $_SESSION['errMsg'] = 'ログインに失敗しました。再度実行してください。';
        require('../login.php');
    }

?>