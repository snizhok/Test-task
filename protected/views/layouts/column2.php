<?php /* @var $this Controller */ ?>
<?php $this->beginContent('//layouts/main'); ?>
<div class="row">
    <div class="col-md-3 col-sm-4">
            <div id="sidebar">
            <?php
                $this->widget(
                    'booster.widgets.TbMenu',
                    array(
                        'type' => 'list',
                        'items' => $this->menu
                    )
                );
            ?>
            </div><!-- sidebar -->
    </div>
    <div class="col-md-9 col-sm-8">
            <div id="content">
                    <?php echo $content; ?>
            </div><!-- content -->
    </div>
</div>
<?php $this->endContent(); ?>