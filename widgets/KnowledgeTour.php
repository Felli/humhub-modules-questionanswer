<?php

namespace humhub\modules\questionanswer\widgets;

use humhub\components\Widget;

/**
 * ProfileWidget.
 * Displays the user profile
 *
 * @package application.modules.questionanswer.widgets
 * @since 0.5
 * @author Luke
 */
class KnowledgeTour extends Widget {

    /**
     * The user object
     *
     * @var User
     */
    public $user;

    /**
     * Timestamp
     *
     * @var Datetime
     */
    public $timestamp;

    /**
     * Executes the widget.
     */
    public function run() {
       return $this->render('tour/tourPanel');
    }

}

?>
