<?php

namespace zk\tribunaldoc;

use Bitrix\Main\Web\HttpClient;
use Bitrix\Main\Config\Option;

class Main {
    public function getDocuments()
    {   
        $module_id = pathinfo(dirname(__DIR__))["basename"];

        $authorization_string = Option::get($module_id, "sad_id").":".Option::get($module_id, "sad_password");
        $authorization = base64_encode($authorization_string);

        $url = "http://".Option::get($module_id, "sad_url");

        $httpClient = new HttpClient(); 
        $httpClient->setHeader('Content-Type', 'application/xml');
        $httpClient->setHeader('Authorization', $authorization);
        $result = $httpClient->get($url);//Сменить на POST и добавить тело!
        $result = simplexml_load_string($result);
        $documents = $result->getDocumentsOnConsideration->response;
        $documentsCount = count($documents->document);

        echo $documentsCount;
    }
}
?>