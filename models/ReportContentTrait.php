<?php
namespace humhub\modules\questionanswer\models;

use humhub\modules\user\models\User;
use Yii;

trait ReportContentTrait
{

    /**
     * Checks to see if the reportcontent module is enabled
     */
    public function reportModuleEnabled()
    {
        return Yii::$app->hasModule('reportcontent');
    }
    /**
     * Checks if the given or current user can report post with given id.
     *
     * @param (optional) Int $userId
     * @return bool
     */
    public function canReportPost($userId = "")
    {


        if(!Yii::$app->hasModule('reportcontent'))
            return false;

        if ($userId == "")
            $userId = Yii::$app->getUser()->id;

        $user = User::findOne(['id' => $userId]);

        if ($user->super_admin)
            return false;

        if ($this->created_by == $user->id)
            return false;


        if (Yii::$app->getUser()->isGuest)
            return false;

        if (User::findOne(['id' => $this->created_by, 'super_admin' => 1]))
            return false;

        return true;

    }

}
?>