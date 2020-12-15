<?php
use Bitrix\Main\Localization\Loc;

Loc::LoadMessages(__FILE__);

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

global $APPLICATION;

if(CModule::includeModule("zk.tribunaldoc")):
?>
<?$this->SetViewTarget("sidebar");?>
<div class="tribunaldoc_block">
    <div class="tribunaldoc_title"><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_TITLE")); ?></div>
    <div class="tribunaldoc_content">
    <div class="tribunaldoc_item">
        <span><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_DOC_ALL")); ?> </span>
        <span class="tribunaldoc_number" id="tribunalDocCount">
        <?php echo zk\tribunaldoc\Doc::getCount(); ?>
        </span>
    </div>
    <div class="tribunaldoc_item">
        <span><?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_DOC_NEW")); ?> </span>
        <span class="tribunaldoc_number tribunaldoc_new" id="tribunalDocNewCount">
        + <?php echo zk\tribunaldoc\Doc::getNewCount(); ?>
        </span>
    </div>
    <form id="tribunaldoc" class="sidebar-widget-top" action="/ajax/zk/tribunaldoc/zk.tribunaldoc.php" method="POST" onsubmit="window.open('<?php echo zk\tribunaldoc\Doc::getSADPage() ?>')">
        <input type="hidden" name="lang" value="<?php echo(LANG); ?>" />
        <input class="tribunaldoc_button" name="viewsad" type="submit" value="<?php echo(Loc::GetMessage("ZK_TRIBUNALDOC_COMPONENT_BUTTON")); ?>">
    </form>
    </div>
</div>
<script>
    tribunaldoc = document.getElementById("tribunaldoc");
    tribunaldoc.addEventListener('submit',(event)=>{
    event.preventDefault();
   
    let xhr = new XMLHttpRequest();
    xhr.open("POST", tribunaldoc.getAttribute('action'),true);
    let formData = new FormData(tribunaldoc);
    xhr.send(formData);
    xhr.onreadystatechange=()=>{
        if(xhr.readyState===4){
            if(xhr.status == 200 && xhr.status<300){
                var new_info = JSON.parse(xhr.response);

                var tribunalDocCount = document.getElementById("tribunalDocCount");
                var tribunalDocNewCount = document.getElementById("tribunalDocNewCount");

                tribunalDocCount.innerText = new_info["count"];
                tribunalDocNewCount.innerText = new_info["new_count"];
            }
        }
    };
 });
</script>
<?$this->EndViewTarget();?>
<?php
endif;
?>