<?php
/* @var $this CommentController */
/* @var $model \humhub\modules\questionanswer\models\QAComment */
use yii\helpers\Url;
use yii\helpers\Html;
use humhub\compat\CHtml;
use humhub\libs\HHtml;
use humhub\modules\questionanswer\models\Tag;

?>

<div class="form">

    <?php $form = \yii\bootstrap\ActiveForm::begin(array(
        'id' => 'comment-form',
        'enableAjaxValidation' => false,
    )); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <div class="col-xs-12">
            <?php echo $form->field($model, 'post_title')->textInput(array('rows' => 6, 'cols' => 50)); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <?php echo $form->field($model, 'post_text')->textArea(array('rows' => 6, 'cols' => 50)); ?>
        </div>
    </div>

    <?php
    $allTags = "";
    $allTags = [];
    foreach ($model->tags as $tag) {
        $allTags[] = Html::encode($tag->tag->tag);
    }
    $tags = implode(', ', $allTags);
    ?>
    <div class="row">
        <div class="col-xs-12">
            <label class="control-label">Tags</label>
            <?php
            echo \yii\helpers\Html::textInput('Tags', $tags, array('class' => 'form-control autosize contentForm', 'placeholder' => "Enter comma separated tags here...")); ?>
            <p class="help-block">Enter comma separated tags</p>
        </div>
    </div>
    <div class="row buttons">
        <div class="col-xs-12">
            <?php echo \yii\helpers\Html::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
        </div>
    </div>

    <?php \yii\bootstrap\ActiveForm::end(); ?>

</div><!-- form -->