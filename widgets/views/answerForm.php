
<?php
use yii\widgets\ActiveForm;

$form=ActiveForm::begin(array(
    'action' => yii\helpers\url::toRoute('answer/create')
)); ?>
<div class="panel panel-default panel-answer">
    <div class="panel-heading">
        <strong>Your</strong> answer
    </div>
    <div class="panel-body">
        <?php echo $form->field($answer, 'post_text')->textarea(array( 'rows' => '5', 'style' => 'height: auto !important;', "class" => "form-control contentForm", "tabindex" => "2", "placeholder" => "Your answer..."))->label(false); ?>
        <?php echo $form->field($answer,'question_id')->hiddenInput(array('value' => $question->id))->label(false); ?>
        <div class="pull-left">
            <?php
            // Creates Uploading Button
            echo \humhub\modules\file\widgets\FileUploadButton::widget(array(
                'uploaderId' => 'contentFormFiles',
                'fileListFieldName' => 'fileList',
            ));
            ?>
            <script>
                $('#fileUploaderButton_contentFormFiles').bind('fileuploaddone', function (e, data) {
                    $('.btn_container').show();
                });

                $('#fileUploaderButton_contentFormFiles').bind('fileuploadprogressall', function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    if (progress != 100) {
                        // Fix: remove focus from upload button to hide tooltip
                        $('#post_submit_button').focus();

                        // hide form buttons
                        $('.btn_container').hide();
                    }
                });
            </script>
            <?php
            // Creates a list of already uploaded Files
            echo \humhub\modules\file\widgets\FileUploadList::widget(array(
                'uploaderId' => 'contentFormFiles'
            ));
            ?>
        </div>

        <?php
        echo \yii\helpers\Html::hiddenInput("containerGuid", Yii::$app->user->guid);
        echo \yii\helpers\Html::hiddenInput("containerClass",  get_class(new \humhub\modules\user\models\User()));
        ?>
        <?php echo \yii\helpers\Html::submitButton('Submit', array('class' => ' btn btn-info pull-right', 'style' => 'margin-top: 5px;')); ?>
    </div>
</div>
<?php \yii\widgets\ActiveForm::end(); ?>