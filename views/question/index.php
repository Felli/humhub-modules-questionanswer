<?php
/* @var $this QuestionController */
/* @var $dataProvider CActiveDataProvider */
?>

<link rel="stylesheet" type="text/css"
         href="<?php echo $this->module->assetsUrl; ?>/css/questionanswer.css"/>
         
<script type="text/javascript"
            src="<?php echo $this->module->assetsUrl; ?>/js/typeahead/typeahead.bundle.js"></script>
            
<div class="container">

	<!-- Top Banner -->
    <div class="row qanda-banner">
        <div class="col-md-12">
            <div class="panel panel-default panel-profile">
    			<div class="panel-profile-header">
        			<div class="image-upload-container">
            			<img class="img-profile-header-background img-profile-header-background-qanda" id="space-banner-image" src="<?php echo Yii::app()->theme->baseUrl; ?>/img/tc-qanda-banner.png" width="100%">
            
                        <div class="img-profile-data">
                            <h1 class="space">Community Knowledge</h1>
                            <h2 class="space">A searchable repository of teaching knowledge.</h2>
                        </div>
        			</div>

                    <div class="image-upload-container profile-user-photo-container">
                        <img class="img-rounded profile-user-photo" id="space-profile-image" src="<?php echo Yii::app()->theme->baseUrl; ?>/img/tc-profile-qanda.png" data-src="holder.js/140x140" alt="140x140">
                    </div>

    			</div>
			</div>
        </div>
    </div>
    
    <div id="qanda-search" class="text-center">
      <span class="tt-input-span">
          <div id="scrollable-dropdown-menu">
              <input class="form-control typeahead searchInput fullwidth" type="text" placeholder="Search, Ask a Question or Share Something">
          </div>
      </span>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default qanda-panel">
                <?php $this->renderPartial('../partials/top_menu_bar'); ?>
                <div class="panel-body">
                    <?php
                        $this->widget('zii.widgets.CListView', array(
                            'dataProvider'=>$dataProvider,
                            'id'=>'customDataList',
                            'ajaxUpdate'=>true,
                            'itemView'=>'_view',
                        ));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-4 layout-sidebar-container">
            <div class="row">
                <div class="col-xs-12" id="quotes">
					<div class="panel panel-default panel-teachingquotes">
                        <img src="<?php echo Yii::app()->theme->baseUrl; ?>/img/tc-apple.png" style="">
                        <?php $this->renderPartial('/quotes/quotes', array()); ?>
                    </div>
                </div>
            </div>
            <?php $this->widget('application.modules.questionanswer.widgets.KnowledgeTour'); ?>
			<?php
                $this->widget('application.modules_core.activity.widgets.ActivityStreamWidget', array(
                    'streamAction' => '//dashboard/dashboard/stream',
                ));
            ?>
        </div>
    </div>
</div>
<!-- end: show content -->


<script type="text/javascript">
    // Owl Carousel Script - for rotating quotations
    $(document).ready(function () {

        // Only show welcome modal on first view
        if($.cookie('_viewed_welcome_modal') == undefined) {

            $.cookie('_viewed_welcome_modal', true, { path: '/', expires: 5 * 365 });
            // $.removeCookie('_viewed_welcome_modal', { path: '/' });
            $('#modalFirstUse').modal('show');
        }

        $(".panel-teachingquotes .owl-carousel").owlCarousel({
            animateOut: 'fadeOutDown',
            animateIn: 'fadeInDown',
            items:1,
            margin:30,
            stagePadding:30,
            fluidSpeed:50,
            autoplay:true,
            loop:true,
            dots: true,
            nav: false
        });

        // Owl Carousel for Instructions on first use in modal - initiate when modal is opened
        $('#modalFirstUse').on('shown.bs.modal', function () {
            $(".modal .owl-carousel").owlCarousel({
                items: 1,
                loop: false,
                dots: true,
                nav:false
            });

            // Custom next button on modal
            $('.customNextBtn').click(function () {
                $(".modal .owl-carousel").trigger('next.owl.carousel');
            })
        });

    });

</script>
<!-- Ask Question Modal -->
<div class="modal" id="modalAskNewQuestion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                	<h3 class="text-center" style="margin-bottom:0px;"><strong>Ask</strong> a new question or share something</h3>
	            </div>
	            <div class="panel-body">
	                <div class="col-xs-12">
                        <?php $form=$this->beginWidget('CActiveForm', array(
                            'action' => Yii::app()->createUrl("/questionanswer/question/create"),
                            'id'=>'question-form_create',

                        )); ?>
                        <div class="logErrors"></div>
                        <?= $form->label($question, 'post_title'); ?>
                            <?php echo $form->textArea($question,'post_title',array('class' => 'form-control autosize contentForm post_title', 'rows' => '1', "placeholder" => "Ask or share anything!")); ?>
                            <?php echo $form->error($question,'post_title'); ?>

                            <div class="contentForm_options">
                                <?= $form->label($question, 'post_text'); ?>
                                <?php echo $form->textArea($question,'post_text',array('rows' => '5', 'style' => 'height: auto !important;', "class" => "form-control contentForm", "placeholder" => "What is it about teaching that is confusing or exciting you today?")); ?>
                                <?php echo $form->error($question,'post_text'); ?>
                                <br />
                                <?php echo CHtml::textField('Tags', null, array('class' => 'form-control autosize contentForm', "placeholder" => "Enter comma separated tags here...")); ?>
                                <p class="help-block">Example: teaching, students, lesson planning ...</p>
                            </div>

                            <div class="row" style="padding-bottom:20px;">
                                <div class="col-xs-12">
                                <div class="pull-left">
                                    <?php
                                    // Creates Uploading Button
                                    $this->widget('application.modules_core.file.widgets.FileUploadButtonWidget', array(
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
                                    $this->widget('application.modules_core.file.widgets.FileUploadListWidget', array(
                                        'uploaderId' => 'contentFormFiles'
                                    ));

                                    ?>

                                </div>

                                <?php
                                echo CHtml::hiddenField("containerGuid", Yii::app()->user->guid);
                                echo CHtml::hiddenField("containerClass",  get_class(new User()));
                                ?>

                                <?php echo CHtml::submitButton('Submit', array('class' => ' btn btn-info pull-right')); ?>
                            </div>
                        </div>
                        <?php $this->endWidget(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Ask Question Modal -->

<script type="text/javascript">
	$(document).ready(function () {

	// Initiate Q&A typeahead searchbar
	var substringMatcher = function(strs) {
          return function findMatches(q, cb) {
            var matches, substringRegex;
            // an array that will be populated with substring matches
            matches = [];
            // regex used to determine if a string contains the substring `q`
            substrRegex = new RegExp(q, 'i');
            // iterate through the pool of strings and for any string that
            // contains the substring `q`, add it to the `matches` array
            $.each(strs, function(i, str) {
              if (substrRegex.test(str)) {
                matches.push(str);
              }
            });
            cb(matches);
          };
        };

        var questions = '<?= $resultSearchData ?>';
        $('#qanda-search .typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'questions',
            source: substringMatcher(JSON.parse(questions)),
            limit: 1000,
            templates: {
                footer: '<btn class="btn btn-info btn-new-post" data-toggle="modal" data-target="#modalAskNewQuestion">Ask new question</button>',
                empty: '<p>No results found matching your query.</p><btn class="btn btn-info btn-new-post" data-toggle="modal" data-target="#modalAskNewQuestion">Ask a new question or share something</button>',
            }
        });

        $('.searchInput').on("keyup", function() {
            var dataSearch = $(".tt-dataset .tt-suggestion").detach();
            if(dataSearch.length) {
                var html = "<div class='scrollSearchData'>";
                    $.each(dataSearch,function(index, value) {
                        html+=$(this)[0].outerHTML;
                    })
                html+="</div>";

                $(".tt-dataset .btn").before(html);
            }
        })

        $('.tt-suggestion').live("click", function() {
            var text = $(this).text();
            $.ajax({
                    data: {text:text},
                    type: "POST",
                    url: '<?= Yii::app()->createUrl("/questionanswer/question/getLocationOneSelectItem") ?>',
                    success: function (data) {
                        if(data) {
                            window.location.href = data;
                        }
                    }
                }
            );
        });

        $("#question-form_create").submit(function() {
                $.ajax({
                    data: $(this).serialize(),
                    type: "POST",
                    url: '<?= Yii::app()->createUrl("/questionanswer/question/create") ?>',
                    success: function (data) {
                        var res = JSON.parse(data);
                        if(res.flag) {
                            $(".logErrors").html(res.errors);
                        } else {
                            $(".logErrors").empty();
                            window.location.href = res.location;
                        }
                    }
                });

            return false;
        });

        $("body").on("click",".btn-new-post",function() {
            var text = $(".searchInput[value!='']").val();
            $(".post_title").val(text);
        });

    });
</script>