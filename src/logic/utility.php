<?php
//ログ情報更新コントローラー
if (isset($_POST['update'])) {
    updateLoginMngTable($_POST['update']);
}
//ログインコントローラー
if (isset($_POST['login'])) {
    insertInLoginMngTable($_POST['login']);
}

//自動ログアウトコントローラ―
if (isset($_POST['action']) && ! empty($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'logout':
            deleteLogoutUser();
            break;
    }
}

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

/**
 *  注意：このメソッドでPDOを取得するけど、
 *  呼び出し元で破棄しないといけない。
 *  呼び出し元に任せてしまうが・・・いいのだろうか？ダメな気がする。
 *
 * @return PDO
 */
function getPDO(){
    require '../hidden/DBaccess.php';
    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
    $pdo = new PDO($dsn,$user,$password);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    return $pdo;
}

/**
 *  概要：ログイン管理テーブルメンテナンス処理。
 *  　　　実行時とLoginTimeを比較し、３０分経過しているレコードを削除する。
 */
function deleteLogoutUser(){

    $pdo = getPDO();
    $sql_select = 'select * from LoginManagement';
    $stm = $pdo->prepare($sql_select);
    $stm->execute();

    $selectResult = $stm->fetchAll(PDO::FETCH_ASSOC);
    $tableTimestamp = null;
    $now = new DateTime();

    $deleteTargetRecords =[];

    foreach($selectResult as $row){
        $diff = $now->diff(new DateTime($row['LoginTime']))->format('%i');

        if($diff > 30){
            $deleteTargetRecords[] = $row['UserName'];
        }
    }

    $sql_delete='';
    foreach($deleteTargetRecords as $deleteTarget){

        $sql_delete = "Delete from LoginManagement where UserName = '".$deleteTarget."'";

        $stm = $pdo->prepare($sql_delete);
        $stm->execute();


    }

    $pdo = null;

}

/**
 *  概要：LoginManagementテーブルにレコードを追加する。
 *
 *
 * @param String $userName
 */
function insertInLoginMngTable($userName){

    $pdo = getPDO();

    $date = new DateTime();

    $sql = "insert LoginManagement(UserName, LoginTime) values ('".$userName."', '".date_format($date, 'Y-m-d H:i:s.u')."')";

    $stm = $pdo->prepare($sql);
    $stm->execute();

    $pdo = null;

}
/**
 *  概要：LoginManagementテーブルに存在するレコードを更新する。
 * @param String $userName
 */
function updateLoginMngTable($userName){
    $pdo = getPDO();

    $date = new DateTime();

    $sql = "update LoginManagement set LoginTime = '".date_format($date, 'Y-m-d H:i:s.u')."' where UserName = '".$userName."'";

    $stm = $pdo->prepare($sql);
    $stm->execute();

    $pdo = null;
}

/**
 *  概要：LoginManagementテーブルに調査対象のユーザー情報が存在するか返す。
 *  結果：存在する場合=true、存在しない場合=false
 *
 * @param String $userName
 * @return boolean|array
 */
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

/**
 *
 */
function createImgPath(string $user, int $imgNo):string{

    define('IMGPATH_PRE','http://comcom0315.php.xdomain.jp/chat/img/');

    $pdo = getPDO();
    $sql = "select * from userImg where user = '".$user."' and imgNo =".$imgNo.";";
    $stm = $pdo->prepare($sql);
    $stm->execute();

    $imgPath_suffix='';
    $result = $stm->fetchAll(PDO::FETCH_ASSOC);
    if(count($result) == 0){
        $defaultSQL = "select * from userImg where user = '".$user."' and imgNo = 1;";
        $stm = $pdo->prepare($defaultSQL);
        $stm->execute();
        $result_second = $stm->fetchAll(PDO::FETCH_ASSOC);

        $imgPath_suffix = $result_second[0]['imgPath'];
    }else{
        foreach($result as $row){
            $imgPath_suffix = $row['imgPath'];
        }
    }

    $pdo = null;
    return IMGPATH_PRE.$imgPath_suffix;
}
?>
