<?php

namespace zk\tribunaldoc;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Config\Option;

class Doc {

    private static $db = "zk_tribunaldoc_count";
    private static $postfix = "/uapi/?url=uapi/sendRequest";

    public function getSADPage($protocol){
        $module_id = self::getModuleId();
        return $protocol."://".Option::get($module_id, "sad_url");
    }
    private function getModuleId(){
        return pathinfo(dirname(__DIR__))["basename"];
    }
    public function getCount()
    {   
        global $USER;
        $userId = $USER->GetID();

        $rsUser = $USER->GetByID($userId);
        $arUser = $rsUser->Fetch();

        $userSADid = $arUser["UF_PRAKTIKA"];

        $body = '<?xml version="1.0" encoding="UTF-8"?>
        <message xmlns:a="urn:sd-praktika:api" xmlns="urn:sd-praktika:api">
            <getDocumentsOnConsideration>
                <request a:userId="'.$userSADid.'">
                    <a:dateTo>'. date("Y-m-d\TG:i:s") .'/a:dateTo>
                </request>
            </getDocumentsOnConsideration>
        </message>';

        $module_id = self::getModuleId();

        $authorization_string = Option::get($module_id, "sad_id").":".Option::get($module_id, "sad_password");
        $authorization = base64_encode($authorization_string);

        #$url = self::getSADPage().self::$postfix;
        $url = self::getSADPage("http");//local

        $httpClient = new HttpClient(); 
        $httpClient->setHeader('Content-Type', 'application/xml');
        $httpClient->setHeader('Host', $module_id);
        $httpClient->setHeader('Content-Length', mb_strlen($body, '8bit'));
        $httpClient->setHeader('Authorization', "Basic ".$authorization);
        #$result = $httpClient->post($url, $body);
        $result = $httpClient->get($url);//local
        $result = simplexml_load_string($result);
        $documents = $result->getDocumentsOnConsideration->response;
        $documentsCount = count($documents->document);

        return $documentsCount;
    }
    
    public function getNewCount($push = true){
        global $USER, $DB;

        $newDocCount = 0;
        $docCount = self::getCount();

        $sqlCount = "SELECT doc_count FROM ".self::$db." WHERE user_id = ".$USER->GetID();
        $docCountResult = $DB->Query($sqlCount);
        $dbCount = $docCountResult->GetNext()["doc_count"];

        if($docCount > $dbCount){
            $newDocCount = $docCount - $dbCount;
            if($push){
                if (\Bitrix\Main\Loader::includeModule('im'))
                {
                \CIMNotify::Add([
                        "TO_USER_ID" => $USER->GetId(),
                        "NOTIFY_TYPE" => IM_NOTIFY_SYSTEM, 
                        "NOTIFY_MODULE" => "intranet", 
                        "NOTIFY_EVENT" => "security_otp",
                        "NOTIFY_MESSAGE" => "У вас есь непросмотренные документы.[br] Количество: ".$newDocCount,
                        "PUSH_MESSAGE" => "У вас есь непросмотренные документы. Количество: ".$newDocCount,
                        "PUSH_IMPORTANT" => "N",
                    ]);
                }
            }
        }else if($docCount < $dbCount){//Если количество документов в СЭД уменьшилось
            self::setCount();
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
    }
}
?>