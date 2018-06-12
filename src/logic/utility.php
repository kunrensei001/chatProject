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

    if($result){
        $_SESSION['errMsg'] = "不正な処理です。トップからログインしてください。";
        return $result;
    }else{
        return $result;
    }
}
?>
