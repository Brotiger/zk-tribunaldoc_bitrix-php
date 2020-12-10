<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

class zk_tribunaldoc extends CModule{
    public function __construct(){
        if(file_exists(__DIR__."/version.php")){
            $arModuleVersion = array();

            include_once(__DIR__."/version.php");

            $this->MODULE_ID = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];

            $this->MODULE_NAME = Loc::getMessage("ZK_TRIBUNALDOC_MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("ZK_TRIBUNALDOC_MODULE_DESCRIPTION");
            $this->PARTNER_NAME = Loc::getMessage("ZK_TRIBUNALDOC_PARTNER_NAME");
            $this->PARTNER_URI = Loc::getMessage("ZK_TRIBUNALDOC_PARTNER_URI");

            return false;
        }
    }

    public function DoInstall(){
        global $APPLICATION;

        if(CheckVersion(ModuleManager::getVersion("main"), "14.00.00")){
            $this->InstallFiles();
            $this->InstallDB();

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallEvents();
        }else{

            $APPLICATION->ThrowException(
                Loc::getMessage("ZK_TRIBUNALDOC_BITRIX_VERSION_ERROR")
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("ZK_TRIBUNALDOC_INSTALL_TITLE")." \"".Loc::getMessage("ZK_TRIBUNALDOC_MODULE_NAME")."\"",__DIR__."/step.php"
        );

        return false;
    }
    
    public function InstallEvents(){
        return false;
    }

    public function InstallFiles(){
        return false;
    }

    public function InstallDB(){
        global $DB, $DBType;

        $DB->RunSQLBatch(__DIR__."/db/".strtolower($DBType)."/install.sql");

        return false;
    }

    public function DoUninstall(){
        global $APPLICATION;

        if($_REQUEST["step"] < 2){
            $APPLICATION->IncludeAdminFile(
                Loc::getMessage("ZK_TRIBUNALDOC_UNINSTALL_TITLE")." \"".Loc::getMessage("ZK_TRIBUNALDOC_MODULE_NAME")."\"",__DIR__."/unstep1.php"
            );
        }else if($_REQUEST["step"] == 2){
            $this->UnInstallFiles();
            $this->UnInstallDB();
            $this->UnInstallEvents();

            ModuleManager::unRegisterModule($this->MODULE_ID);

            $APPLICATION->IncludeAdminFile(
                Loc::getMessage("ZK_TRIBUNALDOC_UNINSTALL_TITLE")." \"".Loc::getMessage("ZK_TRIBUNALDOC_MODULE_NAME")."\"",__DIR__."/unstep2.php"
            );
        }
        return false;
    }

    public function UnInstallFiles(){
        return false;
    }

    public function UnInstallDB(){
        global $DB, $DBType;

        if($_REQUEST["save_data"] != "Y"){
            Option::delete($this->MODULE_ID);
            $DB->RunSQLBatch(__DIR__."/db/".strtolower($DBType)."/uninstall.sql");
        }

        return false;
    }

    public function UnInstallEvents(){
        return false;
    }
}
?>