<?php
require_once 'utility.php';
if(checkLoginNG()){
    require('../login.php');
    exit();
}


?>

<?php

    require_once '../hidden/DBaccess.php';

    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8";

    $session_userName = $_SESSION['userName'];
    $session_imgPath = $_SESSION['imgPath'];
    if(isSet($_POST["hatugen"])){
        if($_POST["hatugen"] != ""){

            try{
                $pdo = new PDO($dsn,$user,$password);
                $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $sql = "insert hatugen(timestamp, hatugenSya, hatugenNaiyou, imgPath) values
                    ('".date("Y/m/d H:i:s.u")."', '".$session_userName."', '".$_POST["hatugen"]."', '".$session_imgPath."')";
                $stm = $pdo->prepare($sql);
                $stm->execute();

                $pdo = null;
            }catch(Exception $e){
                echo '<span class="error">エラーがありました。</span><br>';
                echo $e->getMessage();
                exit();
            }

        }
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">

	<title>部屋</title>
</head>
<body>

	<form method="post" action="/chat/logic/main.php">

	発言：<input type="text" name="hatugen">

	<input type="submit" value="発言">
		<button type="button" onclick="location.href='/chat/logic/main.php'">更新</button>


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
    	try{
    	    $pdo = new PDO($dsn,$user,$password);
    	    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
    	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql_select = "select * from hatugen order by timestamp desc";
            $stm = $pdo->prepare($sql_select);
            $stm->execute();
            $result = $stm->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row){
     ?>

				<tr>
					<td style="text-align: center;"><?php echo $row['hatugenSya']?></td>
					<td><img src="<?php echo $row['imgPath'] ?>" width="100%"></td>
					<td style="word-wrap:break-word;"><?php echo $row['hatugenNaiyou']?></td>
                    <td><?php echo $row['timestamp']?></td>
				</tr>

	<?php
            }
    	    $pdo = null;
    	}catch(Exception $e){
    	    echo '<span class="error">エラーがありました。</span><br>';
    	    echo $e->getMessage();
    	    exit();
    	}

	?>
		</tbody>
	</table>


</body>
</html>