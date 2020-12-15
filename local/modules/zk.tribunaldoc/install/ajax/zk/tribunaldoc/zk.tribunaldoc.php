<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/bitrix/modules/main/include/prolog_before.php");

if(CModule::includeModule("zk.tribunaldoc")){
    if(!isset($_POST["viewsad"])){
        zk\tribunaldoc\Doc::setCount();
        $count = zk\tribunaldoc\Doc::getCount();
        $newCount = zk\tribunaldoc\Doc::getNewCount();
        $newInfo = array(
            "count" => $count,
            "new_count" => $newCount
        );
        $newInfo = json_encode($newInfo);
        echo $newInfo;
    }
}
?>