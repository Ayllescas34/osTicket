<?php

$BUTTONS = isset($BUTTONS) ? $BUTTONS : true;
?>
    <div class="bg-slate-400">
<?php if ($BUTTONS) { ?>
        <div class=" mx-auto w-full flex justify-center ">
<p class="text-white w-full text-center">
<?php
    if ($cfg->getClientRegistrationMode() != 'disabled'
        || !$cfg->isClientLoginRequired()) { ?>
            <a href="open.php" style="display:block" class="bg-blue-200 hover:bg-blue-400 text-white font-bold py-2 px-4 border border-blue-950 rounded text-lg"><?php
                echo __('Open a New Ticket');?></a>
</p>
<?php } ?>
<p class="text-white w-full text-center ">
            <a href="view.php" style="display:block" class="bg-green-300 hover:bg-green-400 font-bold py-2 px-4 border border-green-100 rounded text-lg"><?php
                echo __('Check Ticket Status');?></a>
</p>
        </div>
<?php } ?>
        <div class="content"><?php
    if ($cfg->isKnowledgebaseEnabled()
        && ($faqs = FAQ::getFeatured()->select_related('category')->limit(5))
        && $faqs->all()) { ?>
            <section><div class="header"><?php echo __('Featured Questions'); ?></div>
<?php   foreach ($faqs as $F) { ?>
            <div><a href="<?php echo ROOT_PATH; ?>kb/faq.php?id=<?php
                echo urlencode($F->getId());
                ?>"><?php echo $F->getLocalQuestion(); ?></a></div>
<?php   } ?>
            </section>
<?php
    }
    $resources = Page::getActivePages()->filter(array('type'=>'other'));
    if ($resources->all()) { ?>
            <section><div class="header"><?php echo __('Other Resources'); ?></div>
<?php   foreach ($resources as $page) { ?>
            <div><a href="<?php echo ROOT_PATH; ?>pages/<?php echo $page->getNameAsSlug();
            ?>"><?php echo $page->getLocalName(); ?></a></div>
<?php   } ?>
            </section>
<?php
    }
        ?></div>
    </div>

