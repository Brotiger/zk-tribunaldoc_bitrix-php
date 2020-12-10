<?php
    use Bitrix\Main\Localization\Loc;
    
    Loc::loadMessages(__FILE__);
    
    if(!check_bitrix_sessid()){
       return;
    }
    
    if($errorException = $APPLICATION->GetException()){
        echo(CAdminMessage::ShowMessage($errorException->GetString()));
    }else{
        echo(CAdminMessage::ShowNote(Loc::getMessage("ZK_TRIBUNALDOC_STEP_BEFORE")." ".Loc::getMessage("ZK_TRIBUNALDOC_STEP_AFTER")));
    }
?>
    
<form action="<?php echo($APPLICATION->GetCurPage()); ?>">
    <input type="hidden" name="lang" value="<?php echo(LANG); ?>" />
    <input type="submit" value="<?php echo(Loc::getMessage("ZK_TRIBUNALDOC_STEP_SUBMIT_BACK")); ?>">
</form>