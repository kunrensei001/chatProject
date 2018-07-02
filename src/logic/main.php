<?php
require_once 'utility.php';
if(checkLoginNG()){
    require('../login.php');
    exit();
}


?>

<?php
    require_once '../hidden/pathList.php';
    require_once '../hidden/DBaccess.php';

    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";
    $session_userName = $_SESSION['userName'];

    $imgPath = createImgPath((string)$_SESSION['user'], (int)$_POST['imgNo']);
    if(isSet($_POST["hatugen"])){
        if($_POST["hatugen"] != ""){

                $sql = "insert hatugen(timestamp, hatugenSya, hatugenNaiyou, imgPath, userColor) values
                    ('".date("Y/m/d H:i:s.u")."', '".$session_userName."', '".$_POST["hatugen"]."', '".$imgPath."', '".$_SESSION['userColor']."')";
                insert_update_deleteSQLexec($sql);
        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">

	<title>部屋</title>

	<style>
        .boxA:after	{content: "";
        	display: block;
        	clear: both}

        .chat	{float: left;
        	width: 70%}

        .task	{float: left;
        	width: 30%}
    </style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
</head>
<body>

	<div class="chat">
    	<form method="post" action="<?= $path_main ?>">

    	発言：<input type="text" name="hatugen">

    	<input type="submit" value="発言">
    		<button type="button" onclick="location.href='./main.php'">更新</button>
<br>
<div><button type="button" onclick="openStampList()">スタンプ選択(βver.) ラジオボタンをチェックして「発言」してね</button></div>
<div>
<table id ="stampList">
<tr>
<td><input name="hatugen" type="radio" value="okmacho.jpg"><img src="../stamp/okmacho.jpg" width="150" height="150"></td>
<td><input name="hatugen" type="radio" value="oosako.jpg"><img src="../stamp/oosako.jpg" width="150" height="150"></td>
<td><input name="hatugen" type="radio" value="gj.jpg"><img src="../stamp/gj.jpg" width="150" height="150"></td>
</tr>
<tr>
<td><input name="hatugen" type="radio" value="muri.jpg"><img src="../stamp/muri.jpg" width="150" height="150"></td>
<td><input name="hatugen" type="radio" value="thanksdg.jpg"><img src="../stamp/thanksdg.jpg" width="150" height="150"></td>
<td><input name="hatugen" type="radio" value="whymacho.jpg"><img src="../stamp/whymacho.jpg" width="150" height="150"></td>
</tr>
</table>
</div>
    	<br>
    	<p>
		<input type="radio" name="imgNo" value="1" checked="checked">その１
		<input type="radio" name="imgNo" value="2" >その２
		<input type="radio" name="imgNo" value="3" >その３
		</p>

    	</form>

    	<table border="1" style="table-layout:fixed;width:100%;">
        <colgroup>
            <col style="width:5%;">
            <col style="width:10%;">
            <col style="width:70%;">
            <col style="width:10%;">
        </colgroup>
    	<thead><tr>
        		<th>発言者</th>
        		<th>顔</th>
        		<th>発言</th>
                <th>時刻</th>
        		</tr>
    		<tbody>
    	<?php
            $sql_select = "select * from hatugen order by timestamp desc";
            $result = selectSQLexec($sql_select);
            foreach($result as $row){
         ?>

    				<tr>
					<td bgcolor="<?php echo $row['userColor'] ?>"
						style="text-align: center;"><?php echo $row['hatugenSya']?></td>
					<td bgcolor="<?php echo $row['userColor'] ?>"><img
						src="<?php echo $row['imgPath'] ?>" width="100%"></td>
					<td bgcolor="<?php echo $row['userColor'] ?>" style="word-wrap: break-word;">
					<?php
					if(strpos($row['hatugenNaiyou'],'.jpg') !== false){
					    ?>
					    <img src="../stamp/<?php echo $row['hatugenNaiyou']?>" width="150" height="150">
					    <?php
					} else{
					    echo $row['hatugenNaiyou'];
					}
					?>
						</td>
					<td bgcolor="<?php echo $row['userColor'] ?>"><?php echo $row['timestamp']?></td>
				</tr>

    	<?php
            }
    	?>
    		</tbody>
    	</table>

	</div>

	<div class="task">
		タスク用領域
		<div align="right">
		<table border="1"id="memberTable">
		<thead>
		<tr>
		<th>入室者</th>
		<th>最終ログイン</th>
		</tr>
		</thead>
		<tbody>
		<?php
        	$sql_select = "select * from LoginManagement order by LoginTime desc";
        	$memberResult = selectSQLexec($sql_select);
            foreach($memberResult as $mRow){
         ?>
    				<tr>
                        <td><?php echo $mRow['UserName']?></td>
                        <td><?php echo $mRow['LoginTime']?></td>
    				</tr>
    	<?php
            }
    	?>
		</tbody>
		</table>
		</div>
		<div>
<button type="button" onclick="showMember()">入室者一覧表示/非表示</button>
		</div>
		<br>
<div><button type="button" onclick="window.open('http://comcom0315.php.xdomain.jp/macho/index8.html', 'mywindow3', 'width=600, height=600, menubar=no, toolbar=no, scrollbars=yes')">公式ゲーム(βver.)を始める</button></div>
<br>
<div><button type="button" onclick="openUnityGame()">くそげー(βver.)を始める</button></div>
	</div>
<script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
<script type="text/javascript">
var timer;
var reloadTime = 1000 * 120; //2分毎に更新
var userName = <?php echo json_encode($session_userName); ?>;
var mainPage = "../../.."+ <?php echo json_encode($path_main); ?>;
var uPage = "../../.."+ <?php echo json_encode($path_ut); ?>;

function autoReload() {
    location.href = mainPage;
    return true;
}

function restartTimer() {
    clearInterval(timer);
    timer=setInterval('autoReload()', reloadTime);
    return true;
}

function load() {
    timer=setInterval('autoReload()', reloadTime);
    document.body.addEventListener("mousedown", restartTimer, false);
    document.body.addEventListener("mousemove", restartTimer, false);
    document.body.addEventListener("keydown", restartTimer, false);
    document.body.addEventListener("keypress", restartTimer, false);
    document.body.addEventListener("keyup", restartTimer, false);
}
document.addEventListener("DOMContentLoaded", load, false);


document.addEventListener("DOMContentLoaded", function(event) {
    console.log("DOM fully loaded and parsed");
    loginFn();
    logoutCheck();
    logUpdate();
  });

//ログインテスト OK
function loginFn() {
	    $.ajax({
	        url: uPage,
	        type: 'POST',
	        data: {login: userName},
	        success: function(res) {
	            console.log("ログイン "); //ajax successful
	        }
	    });
}

//ログアウトチェック
function logoutCheck() {
	jQuery.ajax({
		url: uPage,
		data: {action: "logout"},
        type: 'post',
        success: function(res) {
            console.log("ログアウト判定スタート "); // ajax successful
        }
    });
}

//ログイン情報update実行
function logUpdate() {
	jQuery.ajax({
		url: uPage,
		data: {update: userName},
        type: 'post',
        success: function(res) {
            console.log("更新テスト "); // ajax successful
        }
    });
}

//入室者一覧表示(block)/非表示(none)
document.getElementById("memberTable").style.display ="block";

function showMember(){
	var showOrHide = document.getElementById("memberTable");

	if(showOrHide.style.display=="block"){
		showOrHide.style.display ="none";
	}else{
		showOrHide.style.display ="block";
	}
}

//スタンプ表示/非表示
document.getElementById("stampList").style.display ="none";

function openStampList(){
	var showOrHide = document.getElementById("stampList");

	if(showOrHide.style.display=="block"){
		showOrHide.style.display ="none";
	}else{
		showOrHide.style.display ="block";
	}
}

function openUnityGame(){
	 var win = window.open('http://comcom0315.php.xdomain.jp/Game/index.html', '_blank');
}
</script>
</body>
</html>