<?php
    require_once '../hidden/pathList.php';
    require_once '../logic/utility.php';
?>

<?php
function createStampTable():string{

    $stamps = getFileFromDirectory(STAMP_PATH.'/*');
    $count = 0;
    $result='';
    foreach($stamps as $stamp){

        if($count === 0){
            $result.='<tr>';
        }elseif($count === 3){
            $result.='</tr>';
            $count=0;
        }
        $result.='<td><input name="hatugen" type="radio" value="'.$stamp['fileName'].'"><img src="'.$stamp['filePath'].'" width="150" height="150"></td>';

        $count++;

    }

    return $result;

}

?>