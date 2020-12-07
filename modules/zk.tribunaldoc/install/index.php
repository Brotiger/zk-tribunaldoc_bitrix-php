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

            $this->MODULE_NAME = Loc::getMessage("MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("MODULE_DESCRIPTION");
            $this->PARTNER_NAME = Loc::getMessage("PARTNER_NAME");
            $this->PARTNER_URI = Loc::getMessage("PARTNER_URI");

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
                Loc::getMessage("BITRIX_VERSION_ERROR")
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("INSTALL_TITLE")." \"".Loc::getMessage("MODULE_NAME")."\"",__DIR__."/step.php"
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
        return false;
    }

    public function DoUninstall(){
        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UninstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage("UNINSTALL_TITLE")." \"".Loc::getMessage("MODULE_NAME")."\"",__DIR__."/unstep.php"
        );

        return false;
    }

    public function UnInstallFiles(){
        return false;
    }

    public function UninstallDB(){
        Option::delete($this->MODULE_ID);

        return false;
    }

    public function UnInstallEvents(){
        return false;
    }
}
?>