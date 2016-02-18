<?php

namespace humhub\modules\questionanswer\controllers;

use humhub\modules\questionanswer\models\Answer;
use humhub\modules\questionanswer\models\QuestionTag;
use humhub\modules\questionanswer\models\Tag;
use humhub\modules\questionanswer\models\Question;
use humhub\modules\questionanswer\models\QuestionSearch;
use humhub\modules\user\models\User;
use Yii;
//use humhub\modules\content\components\ContentContainerController;
use humhub\components\Controller;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;

class QuestionController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'acl' => [
                'class' => \humhub\components\behaviors\AccessControl::className(),
                'guestAllowedActions' => ['index', 'view']
            ]
        ];
    }

	/**
	 * ReIndex:
	 *  - Questions
	 *  - Answers
	 *  - Comments
	 */
	public function actionReindex()
	{

		// Remove all questions from search index then index again
		echo ">>>>>> STARTING QUESTION REINDEX >>>>>>>><br>";
		foreach (Question::find()->all() as $obj) {
			echo "[#".$obj->id."] " . $obj->post_title . "<br>";

			// Add a content container to the object if there isn't one
//			if($obj->content->container == null) {
				$containerClass = User::className();
				$contentContainer = $containerClass::findOne(['guid' => User::findIdentity($obj->created_by)->guid]);
				$obj->content->container = $contentContainer;

				\humhub\modules\content\widgets\WallCreateContentForm::create($obj, $contentContainer);
				$obj->save();
//			}


			\Yii::$app->search->delete($obj);
			\Yii::$app->search->add($obj);
		}
		echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><br><br>";


		// Remove all answers from search index then index them again
		/*echo ">>>>>> STARTING ANSWER REINDEX >>>>>>>><br>";
		foreach(Answer::find()->all() as $obj) {
			echo "[#".$obj->id."] " . $obj->post_text . "<br>";
			\Yii::$app->search->delete($obj);
			\Yii::$app->search->add($obj);
		}
		echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><br><br>";*/


		// Remove all tags from search index then index them again
		/*echo ">>>>>> STARTING TAG REINDEX >>>>>>>><br>";
		foreach(Tag::find()->all() as $obj) {
			echo "[#".$obj->id."] " . $obj->tag . "<br>";
			\Yii::$app->search->delete($obj);
			\Yii::$app->search->add($obj);
		}
		echo ">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>><br><br><br>";*/
		echo "Reindex complete";
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		$model = $this->loadModel($id);

		return $this->render('view',array(

    		'author' => $model->user->id,
    		'question' => $model,
            'answers' => Answer::overview($model->id),
    		'related' => Question::related($model->id),

			'model'=> $model,
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{

        $question = new Question();

        if(isset($_POST['Question'])) {

            $question->load(Yii::$app->request->post());
            $question->post_type = "question";

            $containerClass = User::className();
            $contentContainer = $containerClass::findOne(['guid' => Yii::$app->getUser()->guid]);
            $question->content->container = $contentContainer;
			
			$question->content->attachFileGuidsAfterSave = Yii::$app->request->post('fileList');

            if ($question->validate()) {

				// Save and Index the `content` object the search engine
				// NOTE: You could probably do this by adding in container->visibility yourself
				// NOTE2: it's also worth looking at doing this the right way and making Q&A a module
				//			which can be enabled on Spaces and Users.
				// 		This will free us from needing to do work arounds like below :)
				\humhub\modules\content\widgets\WallCreateContentForm::create($question, $contentContainer);
				$question->save();

                if(isset($_POST['Tags'])) {
                    // Split tag string into array
                    $tags = explode(", ", $_POST['Tags']);
                    foreach($tags as $tag) {

                        $tag = Tag::firstOrCreate($tag, $contentContainer);
						$question_tag = new QuestionTag();
                        $question_tag->question_id = $question->id;
                        $question_tag->tag_id = $tag->id;
                        $question_tag->save();
                    }
                }


                $this->redirect(Url::toRoute(['question/view', 'id' => $question->id]));

            }

        }

		return $this->render('create',array(
			'model'=>$question,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate()
	{
		
		$id = Yii::$app->request->get('id');
		$model = Question::findOne(['id' => $id]);

		$model->content->object_model = Question::class;
		$model->content->object_id = $model->id;

		$containerClass = User::className();
		$contentContainer = $containerClass::findOne(['guid' => Yii::$app->getUser()->guid]);
		$model->content->container = $contentContainer;

		if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
			$this->redirect(array('view','id'=>$model->id));
		}

		return $this->render('update',array(
			'model'=>$model,
		));


	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{

        $searchModel = new QuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->setSort([
            'defaultOrder' => [
                'created_at'=>SORT_DESC
            ]
        ]);

        return $this->render('index', array(
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => Question::find()
        ));

    }

	/** 
	 * Find unanswered questions
	 */
	public function actionUnanswered()
	{

		$criteria = Question::find();
		$criteria->select("question.id, question.post_title, question.post_text, question.post_type, COUNT(DISTINCT answers.id) as answers, (COUNT(DISTINCT up.id) - COUNT(DISTINCT down.id)) as score, (COUNT(DISTINCT up.id) + COUNT(DISTINCT down.id)) as vote_count, COUNT(DISTINCT up.id) as up_votes, COUNT(DISTINCT down.id) as down_votes");
		$criteria->from('question');
		$criteria->join('LEFT JOIN', 'question_votes up', "question.id = up.post_id AND up.vote_on = 'question' AND up.vote_type='up'");
		$criteria->join('LEFT JOIN', 'question_votes down', "question.id = down.post_id AND down.vote_on = 'question' AND down.vote_type = 'down'");
		$criteria->join('LEFT JOIN', 'question answers', "question.id = answers.question_id AND answers.post_type = 'answer'");
		$criteria->where(['question.post_type' => 'question']);
		$criteria->groupBy("question.id");
		$criteria->having("answers = 0");
		$criteria->orderBy("score DESC, vote_count DESC, question.created_at DESC");

		$dataProvider = new ActiveDataProvider([
			'query' => $criteria,
		]);

		return $this->render('index',array(
			'dataProvider'=>$dataProvider,
		));

	}

	public function actionPicked()
	{


		$sql = 'SELECT question.id, question.post_title, question.post_text, question.post_type, COUNT(*) as tag_count
				FROM question
				LEFT JOIN question_tag ON (question.id = question_tag.question_id)
				WHERE question_tag.tag_id IN (
					SELECT id as tag_id FROM (
						SELECT tag.id
						FROM tag, question_votes, question
						LEFT JOIN question_tag ON (question.id = question_tag.question_id)
						WHERE question_votes.post_id = question.id
						AND question_tag.tag_id = tag.id
						AND tag.tag != ""
						AND question_votes.vote_on = "question"
						AND question_votes.vote_type = "up"
						AND question_votes.created_by = :user_id
						UNION ALL
						SELECT tag.id
						FROM tag, question_tag LEFT JOIN question ON (question_tag.question_id = question.id)
						WHERE question_tag.tag_id = tag.id
						AND tag.tag != ""
						AND question.created_by = :user_id
					) as c
					GROUP BY id
					ORDER BY COUNT(tag_id) DESC, question.created_at DESC
				)';



		$foo = Yii::$app->db->createCommand($sql)->bindValue('user_id',  Yii::$app->user->id)->getSql();
		$bar = Question::findBySql($sql, ['user_id' => Yii::$app->user->id]);

		$dataProvider = new ActiveDataProvider([
			'query' => $bar,
		]);

		return $this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
        $searchModel = new QuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('admin', array(
            'dataProvider'  => $dataProvider,
            'searchModel'   => $searchModel,
            'model'         => Question::find()
        ));
	}

	/**
	 * Report content using reportcontent module
	 */
	public function actionReport()
	{

        $this->forcePostRequest();

        $json = array();
        $json['success'] = false;

        // Only run if the reportcontent module is available
        if(isset(Yii::app()->modules['reportcontent'])) {

            $form = new ReportReasonForm();

            if (isset($_POST['ReportReasonForm'])) {
                $_POST['ReportReasonForm'] = Yii::app()->input->stripClean($_POST['ReportReasonForm']);
                $form->attributes = $_POST['ReportReasonForm'];

                if ($form->validate() && Question::model()->findByPk($form->object_id)->canReportPost()) {

                    $report = new ReportContent();
                    $report->created_by = Yii::app()->user->id;
                    $report->reason = $form->reason;
                    $report->object_model = 'Question';
                    $report->object_id = $form->object_id;

                    $report->save();

                    $json['success'] = true;
                }
            }

        }

		echo CJSON::encode($json);
		Yii::app()->end();
	}


	/**
	 * Controller for viewing a
	 * tag and loading up all questions
	 * from within that tag
	 */
	public function actionTag() {

		$tag = Tag::findOne(['id' => Yii::$app->request->get('id')]);

		return $this->render('tags', array(
			'tag' => $tag,
			'questions' => Question::tag_overview($tag->id)
		));

	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Question the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Question::findOne(['id' => $id]);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Question $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='question-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
