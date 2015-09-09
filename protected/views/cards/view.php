<?php
/* @var $this CardsController */
/* @var $model Cards */

$this->breadcrumbs=array(
	Yii::t('app','History of the card #').$model->number,
);

Booster::getBooster()->registerPackage('datepicker');
$cs = Yii::app()->clientScript;
$cs->registerScript(__CLASS__.__LINE__, 'jQuery("#CardsHistory_action_date").datepicker({"language":"'.Yii::app()->language.'", format:"dd.mm.yyyy"}); jQuery("#CardsHistory_action_date").on("changeDate", function(ev){
    jQuery(this).datepicker("hide");
});', CClientScript::POS_READY);
?>

<h1><?php echo Yii::t('app','History of the card #').CHtml::encode($model->number); ?></h1>
<div class="row">
    <div class="col-sm-6">
        <?php $this->widget('booster.widgets.TbDetailView', array(
                'data'=>$model,
                'type'=>array('striped','bordered'),
                'attributes'=>array(
                    array(
                        'name'=>'series',
                        'type'=>'raw',
                        'value'=>function($data) {
                            return Cards::getSeriesList($data->series);
                        },
                    ),
                    'number',
                    array(
                        'name'=>'created_at',
                        'type'=>'raw',
                        'value'=>  function($data) {
                            return date("d.m.Y H:i:s", strtotime($data->created_at));
                        },
                    ),
                    array(
                        'name'=>'expired_at',
                        'type'=>'raw',
                        'value'=>  function($data) {
                            return date("d.m.Y H:i:s", $data->expired_at);
                        },
                    ),
                    array(
                        'name'=>'status',
                        'type'=>'raw',
                        'value'=>  function($data) {
                            return $data->cardStatus;
                        },
                    ),
                ),
        )); ?>
    </div>
    <div class="col-sm-6">
        <?php $this->widget('booster.widgets.TbGridView', array(
            'id'=>'history-grid',
            'dataProvider'=>$historyModel->search(),
            'filter'=>$historyModel,
            'afterAjaxUpdate'=>'function(){
jQuery("#CardsHistory_action_date").datepicker({"language":"'.Yii::app()->language.'", format:"dd.mm.yyyy"}); jQuery("#CardsHistory_action_date").on("changeDate", function(ev){
    jQuery(this).datepicker("hide");
});}',
            'type' => 'striped bordered',
            'template'=>"{items}\n{pager}",
            'enablePagination'=>true,
            'pager'=>array(
                'class'=>'booster.widgets.TbPager'
            ),
            'columns'=>array(
                'action_date'=> array(
                    'name'=>'action_date',
                    'value'=>'date("d.m.Y H:i:s", strtotime($data->action_date))',
                    'htmlOptions'=>array('data-title'=>Yii::t('app','Date'))
                ),
                'status'=> array(
                    'name'=>'status',
                    'filter'=>CardsHistory::getStatusList(),
                    'value'=>'$data->cardStatus',
                    'type'=>'raw',
                    'htmlOptions'=>array('data-title'=>Yii::t('app','Status'))
                ),
            ),
        )); ?>
    </div>
</div>
