<?php
/* @var $this CardsController */
/* @var $model Cards */


Booster::getBooster()->registerPackage('datepicker');
$cs = Yii::app()->clientScript;
$cs->registerScript(__CLASS__.__LINE__, 'jQuery("#Cards_created_at").datepicker({"language":"'.Yii::app()->language.'", format:"dd.mm.yyyy"}); jQuery("#Cards_created_at").on("changeDate", function(ev){
    jQuery(this).datepicker("hide");
});', CClientScript::POS_READY);

$cs->registerScript(__CLASS__.__LINE__, 'jQuery("#Cards_expired_at").datepicker({"language":"'.Yii::app()->language.'", format:"dd.mm.yyyy"}); jQuery("#Cards_expired_at").on("changeDate", function(ev){
    jQuery(this).datepicker("hide");
});', CClientScript::POS_READY);

?>

<h1><?php echo Yii::t('app','Manage cards'); ?></h1>

<hr>
<?php 
$this->widget('booster.widgets.TbButtonGroup', array(
    'buttons' => array(
        array(
            'label'=>Yii::t('app','Create card'),
            'buttonType'=>'link',
            'url'=>array('create'),
            'icon'=>'plus',
            'context'=>'success',
            'htmlOptions'=>array(
                'title'=>Yii::t('app','Create card'),
            ),
        )
    ),
));
echo '<hr>';
$this->widget('booster.widgets.TbGridView', array(
	//'id'=>'cards-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
        'type' => 'striped bordered',
        'template'=>"{items}\n{pager}",
        'enablePagination'=>true,
        'afterAjaxUpdate'=>'function(){
jQuery("#Cards_created_at").datepicker({"language":"'.Yii::app()->language.'", format:"dd.mm.yyyy"}); jQuery("#Cards_created_at").on("changeDate", function(ev){
        jQuery(this).datepicker("hide");
    });
jQuery("#Cards_expired_at").datepicker({"language":"'.Yii::app()->language.'", format:"dd.mm.yyyy"}); jQuery("#Cards_expired_at").on("changeDate", function(ev){
    jQuery(this).datepicker("hide");
});    
}',
        'pager'=>array(
            'class'=>'booster.widgets.TbPager'
        ),
	'columns'=>array(
            'series'=> array(
                'name'=>'series',
                'filter'=>Cards::getSeriesList(),
                'type'=>'raw',
                'value'=>'Cards::getSeriesList($data->series)',
                'htmlOptions'=>array('data-title'=>Yii::t('app','Series'))
            ),
            'number'=> array(
                'name'=>'number',
                'htmlOptions'=>array('data-title'=>Yii::t('app','Number'))
            ),
            'created_at'=> array(
                'name'=>'created_at',
                'value'=>'date("d.m.Y H:i:s", strtotime($data->created_at))',
                'htmlOptions'=>array('data-title'=>Yii::t('app','Issue date'))
            ),
            'expired_at'=> array(
                'name'=>'expired_at',
                'value'=>'date("d.m.Y H:i:s", $data->expired_at)',
                'htmlOptions'=>array('data-title'=>Yii::t('app','Expiration date'))
            ),
            'status'=> array(
                'name'=>'status',
                'filter'=>Cards::getStatusList(),
                'value'=>'$data->cardStatus',
                'type'=>'raw',
                'htmlOptions'=>array('data-title'=>Yii::t('app','Status'))
            ),
            array(
                'class' => 'booster.widgets.TbToggleColumn',
                'toggleAction' => 'update',
                'filter'=>false,
                'name' => 'status',
                'value'=>'$data->status==1',
                'uncheckedIcon'=>'ok-circle',
                'uncheckedButtonLabel'=>Yii::t('app','Activation'),
                'checkedIcon'=>'ban-circle',
                'checkedButtonLabel'=>Yii::t('app','Deactivation'),
                'header' => Yii::t('app','Change status'),
                'htmlOptions'=>array('data-title'=>Yii::t('app','Change status'),'style'=>'text-align:center;')
            ),
            array(
                'class'=>'booster.widgets.TbButtonColumn',
                'updateButtonOptions'=>array('style'=>'display:none'),
                'viewButtonIcon'=>'time',
                'viewButtonOptions'=>array('title'=>Yii::t('app','View history')),
                'htmlOptions'=>array('data-title'=>Yii::t('app','Action'))
            ),
	),
)); 
