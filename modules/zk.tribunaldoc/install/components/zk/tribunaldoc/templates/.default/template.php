<?php
use Bitrix\Main\Localization\Loc;

Loc::LoadMessages(__FILE__);

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

global $APPLICATION;

if(CModule::includeModule("zk.tribunaldoc")):
    if($_POST["viewsad"])
    {
        zk\tribunaldoc\Doc::setCount();
    }
?>
<?$this->SetViewTarget("sidebar");?>
<div class="tribunaldoc_block">
    <div class="tribunaldoc_title"><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_TITLE")); ?></div>
    <div class="tribunaldoc_content">
    <div class="tribunaldoc_item">
        <span><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_DOC_ALL")); ?> </span>
        <span class="tribunaldoc_number">
        <?php echo zk\tribunaldoc\Doc::getCount(); ?>
        </span>
    </div>
    <div class="tribunaldoc_item">
        <span><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_DOC_NEW")); ?> </span>
        <span class="tribunaldoc_number tribunaldoc_new">
        + <?php echo zk\tribunaldoc\Doc::getNewCount(); ?>
        </span>
    </div>
    <form class="sidebar-widget-top" action="<?php echo($APPLICATION->GetCurPage()); ?>" method="POST" onsubmit="window.open('<?php echo zk\tribunaldoc\Doc::getSADPage() ?>')">
        <input type="hidden" name="lang" value="<?php echo(LANG); ?>" />
        <input class="tribunaldoc_button" name="viewsad" type="submit" value="<?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_BUTTON")); ?>">
    </form>
    </div>
</div>
<?$this->EndViewTarget();?>
<?php
endif;
?>