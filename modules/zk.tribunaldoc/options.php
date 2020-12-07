<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;

Loc::LoadMessages(__FILE__);

$request = HttpApplication::getInstance()->getContext()->getRequest();

$module_id = htmlspecialcharsbx($request["mid"] != ""? $request["mid"] : $request["id"]);

Loader::includeModule($module_id);

$aTabs = array(
    array(
        "DIV" => "edit",
        "TAB" => Loc::getMessage("OPTIONS_TAB_NAME"),
        "TITLE"   => Loc::getMessage("OPTIONS_TAB_NAME"),
        "OPTIONS" => array(
            Loc::getMessage("OPTIONS_TAB_COMMON"),
            array(
                "switch_on",
                Loc::getMessage("OPTIONS_TAB_SWITCH_ON"),
                "Y",
                array("checkbox")
            ),
            Loc::getMessage("ACCESS_RIGHTS"),
            array(
                "sad_id",
                Loc::getMessage("SAD_ID"),
                "",
                array("text")
            ),
            array(
                "sad_password",
                Loc::getMessage("SAD_PASSWORD"),
                "",
                array("password")
            )
        )
    )
);

if($request->isPost() && check_bitrix_sessid()){

    foreach($aTabs as $aTab){

       foreach($aTab["OPTIONS"] as $arOption){

            if(!is_array($arOption)){
                continue;
            }

            if($arOption["note"]){
                 continue;
            }

            if($request["apply"]){
                $optionValue = $request->getPost($arOption[0]);
                if($arOption[0] == "switch_on"){
                    if($optionValue == ""){
                        $optionValue = "N";
                    }
                }

                Option::set($module_id, $arOption[0], $optionValue);
            }elseif($request["default"]){
                Option::set($module_id, $arOption[0], $arOption[2]);
            }
        }
    }
}

$tabSadControl = new CAdminTabControl(
    "tabSadControl",
    $aTabs
);

$tabSadControl->Begin();
?>

<form action="<?php echo($APPLICATION->GetCurPage()); ?>?mid=<?php echo($module_id); ?>&lang=<?php echo(LANG); ?>" method="post">

<?php
   foreach($aTabs as $aTab){

       if($aTab["OPTIONS"]){

         $tabSadControl->BeginNextTab();

         __AdmSettingsDrawList($module_id, $aTab["OPTIONS"]);
      }
   }

   $tabSadControl->Buttons();
?>

<input type="submit" name="apply" value="<?php echo(Loc::GetMessage("INPUT_APPLY")); ?>" class="adm-btn-save" />
<input type="submit" name="default" value="<?php echo(Loc::GetMessage("INPUT_DEFAULT")); ?>" />

<?php
    echo(bitrix_sessid_post());
?>

</form>

<?php
    $tabSadControl->End();
?>