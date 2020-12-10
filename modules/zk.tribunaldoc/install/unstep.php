<?php
    use Bitrix\Main\Localization\Loc;

    Loc::loadMessages(__FILE__);

    #Проверка этот ли элемент пытаемся удалить
    if(!check_bitrix_sessid()){
        return;
    }

    echo(CAdminMessage::ShowNote(Loc::getMessage("ZK_TRIBUNALDOC_UNSTEP_BEFORE")." ".Loc::getMessage("ZK_TRIBUNALDOC_UNSTEP_AFTER")));
?>

<form action="<?php echo($APPLICATION->GetCurPage()); ?>">
    <input type="hidden" name="lang" value="<?php echo(LANG); ?>">
    <input type="submit" value="<?php echo(Loc::getMessage("ZK_TRIBUNALDOC_UNSTEP_SUBMIT_BACK")); ?>">
</form>