<?php

namespace zk\tribunaldoc;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Config\Option;

class Doc {

    private static $db = "zk_tribunaldoc_count";
    private static $protocol = "http";

    public function getSADPage(){
        $module_id = self::getModuleId();
        return self::$protocol."://".Option::get($module_id, "sad_url");
    }
    private function getModuleId(){
        return pathinfo(dirname(__DIR__))["basename"];
    }
    public function getCount()
    {   
        $module_id = self::getModuleId();

        $authorization_string = Option::get($module_id, "sad_id").":".Option::get($module_id, "sad_password");
        $authorization = base64_encode($authorization_string);

        $url = self::getSADPage();

        $httpClient = new HttpClient(); 
        $httpClient->setHeader('Content-Type', 'application/xml');
        $httpClient->setHeader('Authorization', $authorization);
        $result = $httpClient->get($url);//Сменить на POST и добавить тело!
        $result = simplexml_load_string($result);
        $documents = $result->getDocumentsOnConsideration->response;
        $documentsCount = count($documents->document);

        return $documentsCount;
    }
    
    public function getNewCount(){
        global $USER, $DB;

        $newDocCount = 0;
        $docCount = self::getCount();

        $sqlCount = "SELECT doc_count FROM ".self::$db." WHERE user_id = ".$USER->GetID();
        $docCountResult = $DB->Query($sqlCount);
        $dbCount = $docCountResult->GetNext()["doc_count"];

        if($docCount > $dbCount){
            $newDocCount = $docCount - $dbCount;
        }

        return $newDocCount;
    }

    public function setCount(){
        $count = self::getCount();

        global $USER, $DB, $APPLICATION;

        $fields = array(
            "user_id" => $USER->GetID(),
            "doc_count" => $count
        );

        $sqlCheckRecord = "SELECT id FROM ".self::$db." WHERE user_id = ".$USER->GetID();
        $checkResult = $DB->Query($sqlCheckRecord);

        $idRecord = $checkResult->GetNext()["id"];

        if($idRecord){
            $DB->Update(
                self::$db,
                $fields,
                "WHERE id = ".$idRecord
            );
        }else{
            $DB->Insert(
                self::$db,
                $fields
            );
        }
        header("location:" . $APPLICATION->GetCurPage());
    }
}
?>