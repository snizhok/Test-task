<?php
/* @var $this SiteController */
/* @var $model LoginForm */
/* @var $form CActiveForm  */

$this->pageTitle=CHtml::encode(Yii::app()->name). ' - Login';
?>
<div class="row">
<div class="form col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
<?php $form=$this->beginWidget('booster.widgets.TbActiveForm',
    array(
        'id' => 'login-form',
        'htmlOptions' => array('class' => 'well'), // for inset effect, array(
	'enableClientValidation'=>true,
	'clientOptions'=>array(
		'validateOnSubmit'=>true,
	),
)); ?>
    <legend><?php echo Yii::t('app','Login'); ?></legend>
    <p class="note"><span class="help-block"><?php echo Yii::t('app','Fields with <span class="required">*</span> are required.'); ?></span></p>
    <?php echo $form->textFieldGroup($model,'username'); ?>
    <?php echo $form->passwordFieldGroup($model,'password'); ?>
    <?php echo $form->checkboxGroup($model,'rememberMe'); ?>

    <p class="hint"><span class="help-block">
        <?php echo Yii::t('app','Hint: You may login with <kbd>admin</kbd> / <kbd>admin</kbd>.'); ?>
    </span></p>
    <?php $this->widget('booster.widgets.TbButton', array(
        'buttonType' => 'submit',
        'context'=>'primary',
        'label' => Yii::t('app','Submit')
    )); ?>

<?php $this->endWidget(); ?>
</div><!-- form -->
</div>