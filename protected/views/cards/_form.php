<?php
/* @var $this CardsController */
/* @var $model Cards */
/* @var $form CActiveForm */
?>

<div class="row">
<div class="form col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
<?php $form=$this->beginWidget('booster.widgets.TbActiveForm',
    array(
        'id'=>'cards-form',
        'htmlOptions' => array('class' => 'well'), // for inset effect, array(
	'enableAjaxValidation'=>false,
)); ?>
    <legend><?php echo CHtml::encode($legend); ?></legend>
    <p class="note"><span class="help-block"><?php echo Yii::t('app','Fields with <span class="required">*</span> are required.'); ?></span></p>
    <?php //echo $form->errorSummary($model); ?>
    <?php echo $form->dropDownListGroup($model,'series',array(
        'widgetOptions'=>array(
            'data'=>Cards::getSeriesList()
        )
    )); ?>
    <?php echo $form->textFieldGroup($model,'number'); ?>
    <?php echo $form->dropDownListGroup($model,'expired_var',array(
        'widgetOptions'=>array(
            'data'=>Cards::getExpiredList()
        )
    )); ?>
    <?php $this->widget('booster.widgets.TbButton', array(
        'buttonType' => 'submit',
        'context'=>'primary',
        'label'=>Yii::t('app','Create')
    )); ?>
<?php $this->endWidget(); ?>

</div><!-- form -->