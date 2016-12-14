<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use humhub\modules\questionanswer\models\QuestionVotes;
use humhub\modules\questionanswer\models\Question;
\humhub\modules\questionanswer\Assets::register($this);
?>

<style>
.vote_control .btn-xs:nth-child(1) {
    margin-bottom:3px;
}

.qanda-panel {
    margin-top:57px;
}

.qanda-header-tabs {
    margin-top:-49px;
}

</style>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h3 class="text-center">Posts Tagged: <?php echo Html::encode($tag->tag); ?></h3>
        </div>
        <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default qanda-panel">
                <?= $this->context->renderPartial('../partials/top_menu_bar'); ?>
                <div class="panel-body">

                <?php foreach ($questions as $question) { ?>
                    <div class="media" style="margin-top: 10px;">
                        <div class="pull-left">
                            <div class="vote_control pull-left" style="padding:5px; padding-right:10px; border-right:1px solid #eee; margin-right:10px;margin-top:-18px">
                                <?php
                                $upBtnClass = ""; $downBtnClass = ""; $vote = ""; $vote_type = "up";
                                $model = (object) $question;

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
                                ?>
                                <?= \humhub\modules\questionanswer\widgets\VoteButtonWidget::widget(array('post_id' => $question['id'], 'model' => new QuestionVotes, 'vote_on' => 'question', 'vote_type' => $vote_type, 'classObj' => $upBtnClass, 'should_open_question' => 0)); ?>
                            </div>

                            <div class="pull-left" style="text-align:center; margin-top:5px; margin-right:8px;">
                                <b><?php echo $question['vote_count']; ?></b>
                                <p>likes</p>
                            </div>
                            <div class="pull-left" style="text-align:center; margin-top:5px; margin-right:8px;">
                                <b><?php echo Question::getViewQuestion($question['id']); ?></b>
                                <p>views</p>
                            </div>
                            <div class="pull-left" style="text-align:center; margin-top:5px;">
                                <b><?php echo $question['answers']; ?></b>
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
                        <div class="media-body" style="padding-top:5px; padding-left:10px;">
                            <h4 class="media-heading">
                                <?php echo Html::a(Html::encode($question['post_title']), Url::toRoute(array('//questionanswer/question/view', 'id' => $question['id']))); ?>
                                <div class="time" style="float:right;margin-left:5px;">
                                    <?= $datetime; ?>
                                </div>
                            </h4>
                            <h5><?php echo Html::encode((strlen($question['post_text']) > 203) ? substr($question['post_text'],0,200).'...' : $question['post_text']); ?></h5>
                        </div>
                    </div>
                <?php } ?>

                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Related</strong> Posts</div>
                <div class="list-group">
                    <a class="list-group-item" href="#">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a>
                    <a class="list-group-item" href="#">Nunc pharetra blandit sapien, et tempor nisi.</a>
                    <a class="list-group-item" href="#">Duis finibus venenatis commodo. </a>
                </div>
                <br>
            </div>
        </div>
    </div>
</div>
<!-- end: show content -->