<?php
//ログ情報更新コントローラー
if (isset($_POST['update'])) {
    updateLoginMngTable($_POST['update']);
}
//ログインコントローラー
//test
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

    $sql_select = 'select * from LoginManagement';
    $selectResult = selectSQLexec($sql_select);

    $tableTimestamp = null;
    $now = new DateTime();

    $deleteTargetRecords =[];

    foreach($selectResult as $row){

        $diff = ($now->getTimestamp() - (new DateTime($row['LoginTime']))->getTimestamp())/60;
        if($diff > 30){
            $deleteTargetRecords[] = $row['UserName'];
        }
    }

    $sql_delete='';
    foreach($deleteTargetRecords as $deleteTarget){

        $sql_delete = "Delete from LoginManagement where UserName = '".$deleteTarget."'";

        insert_update_deleteSQLexec($sql_delete);

    }

}

/**
 *  概要：LoginManagementテーブルにレコードを追加する。
 *
 *
 * @param String $userName
 */
function insertInLoginMngTable($userName){

    $date = new DateTime();
    $sql = "insert LoginManagement(UserName, LoginTime) values ('".$userName."', '".date_format($date, 'Y-m-d H:i:s.u')."')";

    insert_update_deleteSQLexec($sql);

}
/**
 *  概要：LoginManagementテーブルに存在するレコードを更新する。
 * @param String $userName
 */
function updateLoginMngTable($userName){

    $date = new DateTime();

    $sql = "update LoginManagement set LoginTime = '".date_format($date, 'Y-m-d H:i:s.u')."' where UserName = '".$userName."'";

    insert_update_deleteSQLexec($sql);
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

    $sql = "select * from LoginManagement where UserName = '".$userName."'";

    $result = selectSQLexec($sql);

    if(count($result)==1){
        $result = true;
    }

    return $result;
}

/**
 *
 */
function createImgPath(string $user, int $imgNo):string{

    $sql = "select * from userImg where user = '".$user."' and imgNo =".$imgNo.";";
    $result = selectSQLexec($sql);

    $imgPath_suffix='';
    if(count($result) == 0){

        $defaultSQL = "select * from userImg where user = '".$user."' and imgNo = 1;";
        $result_second = selectSQLexec($defaultSQL);

        $imgPath_suffix = $result_second[0]['imgPath'];

    }else{
        foreach($result as $row){
            $imgPath_suffix = $row['imgPath'];
        }
    }

    require '../hidden/pathList.php';

    return IMGPATH_PRE.$imgPath_suffix;
}

function insert_update_deleteSQLexec(string $sql){

    try{
        $pdo =getPDO();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare($sql);
        $stm->execute();

        $pdo = null;
    }catch(Exception $e){
        echo '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
        exit();
    }
    $pdo = null;
}

function selectSQLexec(string $sql):array{
    try{
        $pdo =getPDO();
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stm = $pdo->prepare($sql);
        $stm->execute();

        $pdo = null;
        $result = $stm->fetchAll(PDO::FETCH_ASSOC);

        return $result;

    }catch(Exception $e){
        echo '<span class="error">エラーがありました。</span><br>';
        echo $e->getMessage();
        exit();
    }

}
/**
 *  指定したパス配下にあるファイルすべて取得する。
 *
 *  パスの指定方法は
 *      絶対パス
 *          レンタルサーバーなので「ドキュメントルート」を指定する必要がある。
 *      相対パス☆おすすめ
 *          呼び出すスクリプトの場所によって変わるので注意。
 *
 *  相対パスを指定しましょう。
 *  絶対パス指定だとサーバーの構成丸見えになってしまうため。
 *
 *  最後は「/*」で終わること。
 *
 *
 * @param string $path ファイルを取得したい
 * @return array ファイル名とファイルパスがセットになった連想配列
 */
function getFileFromDirectory(string $path):array{

    $results = [];

    foreach(glob($path) as $file){
        if(is_file($file)){

            $result=[
                'fileName'=>pathinfo($file, PATHINFO_BASENAME),
                'filePath'=>$file
            ];

            $results[] = $result;

        }
    }

    return $results;

}
?>
