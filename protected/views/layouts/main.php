<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="en">
    <link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>

<div id="mainmenu">
<?php $this->widget('booster.widgets.TbNavbar',
    array(
        'brand' => CHtml::encode(Yii::app()->name),
        'fixed' => 'top',
        'fluid' => TRUE,
        'type'=>'inverse',
        'items' => array(
            array(
                'class' => 'booster.widgets.TbMenu',
                'type' => 'navbar',
                'htmlOptions'=>array('class'=>'pull-right'),
                'items' => array(
                    array(
                        'label'=>Yii::t('app','Login'), 
                        'icon'=>'log-in',
                        'url'=>array('/site/login'), 
                        'visible'=>Yii::app()->user->isGuest
                    ),
                    array(
                        'label'=>Yii::t('app','Logout').' ('.Yii::app()->user->name.')', 
                        'icon'=>'log-out',
                        'url'=>array('/site/logout'), 
                        'visible'=>!Yii::app()->user->isGuest
                    )
                )
            )
        )
    )
);?>
</div><!-- mainmenu -->
    
<div class="container" id="page">

    <?php if(isset($this->breadcrumbs)):            
        $this->widget(
            'booster.widgets.TbBreadcrumbs',
            array(
                'links' => $this->breadcrumbs
            )
        );
    endif?>

	
        <?php echo $content; ?>

	<div class="clear"></div>

</div><!-- page -->

</body>
</html>
