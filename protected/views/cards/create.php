<?php
/* @var $this CardsController */
/* @var $model Cards */

$this->breadcrumbs=array(
	Yii::t('app','Create card'),
);

$this->renderPartial('_form', array('model'=>$model,'legend'=>Yii::t('app','Create card'))); 