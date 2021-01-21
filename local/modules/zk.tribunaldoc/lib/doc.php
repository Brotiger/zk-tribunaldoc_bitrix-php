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
    public function getInfo()
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
                    <a:dateTo>'. date("Y-m-d\TG:i:s") .'</a:dateTo>
                </request>
            </getDocumentsOnConsideration>
        </message>';

        $module_id = self::getModuleId();

        $authorization_string = Option::get($module_id, "sad_id").":".Option::get($module_id, "sad_password");
        $authorization = base64_encode($authorization_string);

        #$url = self::getSADPage("http").self::$postfix;
        $url = self::getSADPage("http");//local

        $httpClient = new HttpClient(); 
        $httpClient->setHeader('Content-Type', 'application/xml');
        $httpClient->setHeader('Host', $module_id);
        $httpClient->setHeader('Content-Length', mb_strlen($body, '8bit'));
        $httpClient->setHeader('Authorization', "Basic ".$authorization);
        #$result = $httpClient->post($url, $body);
        $result = $httpClient->get($url);//local
        if($result){
            $result = simplexml_load_string($result);
            if($result->getDocumentsOnConsideration){
                $documents = $result->getDocumentsOnConsideration->response;
                $documentsCount = count($documents->document);

                return $documentsCount;
            }
            return false;
        }else{
            return false;
        }
    }

    public function getCount(){
        $info = self::getInfo();
        if($info){
            self::setCount($info);
            return $info;
        }else{
            $countInDB = self::getCountInDB();

            if(!$countInDB){
                return 0;
            }
            return $countInDB;
        }
    }

    public function getCountInDB(){
        global $USER, $DB;
        $sqlCount = "SELECT doc_count FROM ".self::$db." WHERE user_id = ".$USER->GetID();
        $docCountResult = $DB->Query($sqlCount);
        $dbCount = $docCountResult->GetNext()["doc_count"];
        return $dbCount;
    }

    public function getViewCountInDB(){
        global $USER, $DB;
        $sqlCount = "SELECT new_doc_count FROM ".self::$db." WHERE user_id = ".$USER->GetID();
        $docCountResult = $DB->Query($sqlCount);
        $dbCount = $docCountResult->GetNext()["new_doc_count"];
        return $dbCount;
    }
    
    public function setViewCount($count = false){
        global $USER, $DB, $APPLICATION;
        if($count === false){
            $count = self::getCountInDB();
        }
        $fields = array(
            "user_id" => $USER->GetID(),
            "new_doc_count" => $count
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
    public function getNewCount($push = true){
        global $USER;
        $newDocCount = 0;

        $docCount = self::getCountInDB();
        $dbCount = self::getViewCountInDB();
        $module_id = self::getModuleId();

        if($docCount != $dbCount){
            $newDocCount = $docCount - $dbCount;
            if($newDocCount > 0){
                if($push){
                    if (\Bitrix\Main\Loader::includeModule('im'))
                    {
                    \CIMNotify::Add([
                            "TO_USER_ID" => $USER->GetId(),
                            "NOTIFY_TYPE" => IM_NOTIFY_SYSTEM, 
                            "NOTIFY_MODULE" => $module_id,
                            "NOTIFY_MESSAGE" => "У вас есь непросмотренные документы.[br] Количество: ".$newDocCount,
                            "PUSH_MESSAGE" => "У вас есь непросмотренные документы. Количество: ".$newDocCount,
                            "PUSH_IMPORTANT" => "N",
                        ]);
                    }
                }
            }else{
                self::setViewCount($docCount);
                $newDocCount = 0;
            }
        }

        return $newDocCount;
    }

    public function setCount($count){
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