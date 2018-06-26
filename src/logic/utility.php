<?php
function checkLoginNG(){

    session_start();

    $token='';
    if(isset($_SESSION['token'])){
        $token = $_SESSION['token'];
    }
    $post_token='';
    if(isset($_SESSION['post_token'])){
        $post_token=$_SESSION['post_token'];
    }

    $result = false;

    if($token === '' or $post_token === ''){
        $result = true;
    }
    if($token !== $post_token){
        $result = true;

    }else{
        $result = false;
    }

    $userName="";
    if(isset($_SESSION['userName'])){
        $userName = $_SESSION['userName'];
    }

    if($userName === ''){
        $result = true;
    }

    if($result){
        $_SESSION['errMsg'] = "不正な処理です。トップからログインしてください。";
        return $result;
    }else{
        return $result;
    }
}


function getPDO(){
    require_once '../hidden/DBaccess.php';
    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
    $pdo = new PDO($dsn,$user,$password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}

function deleteLogoutUser(){
    /*
     * 1.loginManageテーブルの全レコードを取得する？
     *   それとも、SQL分で30分経過しているレコードを取得する？
     *   時間の計算ってできるのかな？
     *   できるけど、SQLで実行するのはちょっと違うかな。
     *   そのまま取得する。
     *
     * 2.全レコードをDateTime::diffで比較して３０分経過しているレコードの
     *   primarykeyを配列に格納する。
     *
     * 3.格納している配列をすべて削除する。
     *
     *
     *
     *
     */

    $pdo = getPDO();
    $sql = 'select * from LoginManagement';
    $stm = $pdo->prepare($sql);
    $stm->execute();

    $selectResult = $stm->fetchAll(PDO::FETCH_ASSOC);
    $tableTimestamp = null;
    $now = new DateTime();
    $deleteTargetRecords =[];

    foreach($result as $row){
        $diff = $now->diff(new DateTime($row['LoginTime']))->format('%i');
        if($diff > 30){
            $deleteTargetRecords[] = $row['UserName'];
        }
    }

    //削除用のロジックをつくるところから始める

    $pdo = null;



}
function insertInLoginMngTable($userName){

    $pdo = getPDO();

    $date = new DateTime();

    $sql = "insert LoginManagement(UserName, LoginTime) values ('".$userName."', '".date_format($date, 'Y-m-d H:i:s.u')."')";

    $stm = $pdo->prepare($sql);
    $stm->execute();

    $pdo = null;

}
function existUserInLoginMngTable($userName){
    $resut = false;

    $pdo = getPDO();

    $sql = "select * from LoginManagement where UserName = '".$userName."'";

    $stm = $pdo->prepare($sql);
    $stm->execute();

    $pdo = null;

    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    if(count($result)==1){
        $result = true;
    }

    return $result;
}

?>
