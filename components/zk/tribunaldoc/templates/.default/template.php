<?php
use Bitrix\Main\Localization\Loc;

Loc::LoadMessages(__FILE__);

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

global $APPLICATION;

if(CModule::includeModule("zk.tribunaldoc")):
    if(!empty($_POST))
    {
        zk\tribunaldoc\Doc::setCount();
    }
?>

<div>
    <div><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_TITLE")); ?></div>
    <div>
        <span><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_DOC_ALL")); ?> </span>
        <span>
        <?php echo zk\tribunaldoc\Doc::getCount(); ?>
        </span>
    </div>
    <div>
        <span><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_DOC_NEW")); ?> </span>
        <span>
        <?php echo zk\tribunaldoc\Doc::getNewCount(); ?>
        </span>
    </div>
    <form class="sidebar-widget-top" action="<?php echo($APPLICATION->GetCurPage()); ?>" method="POST" onsubmit="window.open('<?php echo zk\tribunaldoc\Doc::getSADPage() ?>')">
        <input type="hidden" name="lang" value="<?php echo(LANG); ?>" />
        <input type="submit" value="<?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_BUTTON")); ?>">
    </form>
</div>

<?php
endif;
?>