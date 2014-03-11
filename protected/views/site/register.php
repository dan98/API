<?php
$this->pageTitle = Yii::app()->name . ' - Register';
?>
<div class="container">

    <h1>Register</h1>

    <?php if (Yii::app()->user->hasFlash('register')): ?>

        <div class="flash-success">
            <?php echo Yii::app()->user->getFlash('register'); ?>
        </div>

    <?php else: ?>

        <p>
            To obtain a consumer key and a consumer key to use this API, please fill in this form. Thank you.
        </p>

        <div style="margin-left:20px;">
            <?php $form = $this->beginWidget('CActiveForm'); ?>

            <?php echo $form->errorSummary($model); ?>

            <div class="row">
                <?php echo $form->labelEx($model, 'name'); ?>
                <?php echo $form->textField($model, 'name'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($model, 'email'); ?>
                <?php echo $form->textField($model, 'email'); ?>
            </div>

            <div class="row">
                <?php echo $form->labelEx($model, 'description'); ?>
                <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 50)); ?>
            </div>

            <div class="row submit">
                <?php echo CHtml::submitButton('Submit'); ?>
            </div>

            <?php $this->endWidget(); ?>
        </div>
    <?php endif; ?>
</div>