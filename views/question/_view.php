<?php
/* @var $this QuestionController */
/* @var $data Question */

use humhub\modules\questionanswer\models\QuestionVotes;
use yii\helpers\Html;
use yii\helpers\Url;
use humhub\modules\questionanswer\models\Question;
?>

<div class="media" style="position:relative;margin-top: 10px;">
    <div class="pull-left">
        <div class="vote_control pull-left" style="padding:5px; padding-right:10px; border-right:1px solid #eee; margin-right:10px;margin-top:-18px">
            <?php 
            $upBtnClass = ""; $downBtnClass = ""; $vote = ""; $vote_type = "up";
            $model = (object) $model;
            // Change the button class to 'active' if the user has voted
            $vote = QuestionVotes::find()->andWhere(['post_id' => $model->id, 'created_by' => \Yii::$app->user->id])->one(); // post($data->id)->user(Yii::app()->user->id)
            if($vote) {
                if($vote->vote_type == "up") {
                    $upBtnClass = "active btn-info";
                    $downBtnClass = "";
                }

                if($vote->created_by == Yii::$app->user->id && $vote->vote_type == "up") {
                    $vote_type = 'down';
                } else {
                    $vote_type = "up";
                }
            }
            echo \humhub\modules\questionanswer\widgets\VoteButtonWidget::widget(array('post_id' => $model->id, 'model' => new QuestionVotes, 'vote_on' => 'question', 'vote_type' => $vote_type, 'classObj' => $upBtnClass, 'should_open_question' => 0));

            ?>
        </div>
        <?php
        $stats = Question::stats($model->id);
        ?>

        <div class="pull-left" style="text-align:center; margin-top:5px; margin-right:8px;">
            <b><?php echo (int) $stats['score']; ?></b>
            <p>likes</p>
        </div>
        <div class="pull-left" style="text-align:center; margin-top:5px; margin-right:8px;">
            <b><?php echo (int) Question::getViewQuestion($model->id); ?></b>
            <p>views</p>
        </div>
        <div class="pull-left" style="text-align:center; margin-top:5px;">
            <b><?php echo (int) $stats['answers']; ?></b>
            <p>responses</p>
        </div>

    </div>
    <?php
    $question = Question::findOne($model->id);
    $timeZone = \Yii::$app->user->identity->time_zone;
    $date = new \DateTime($question->created_at, new \DateTimeZone('UTC'));
    $timestamp = $date->getTimestamp();
    if(empty($timeZone)) {
        $timeZone = 'UTC';
    }
    $date->setTimezone(new \DateTimeZone($timeZone));
    $datetime = $date->format('F j, Y, g:i a');
    ?>
    <div class="media-body" style="position:relative;padding-top:5px; padding-left:10px;">
        <h4 class="media-heading">
            <?php echo Html::a(Html::encode($model->post_title), array('view', 'id'=>$model->id)); ?>
            <div class="time" style="float:right;margin-left:5px;">
                <?= $datetime; ?>
            </div>
        </h4>
        <h5><?php echo Html::encode(\humhub\libs\Helpers::truncateText($model->post_text, 200)); ?></h5>
    </div>

</div>

