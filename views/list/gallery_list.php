<?php

use \humhub\modules\gallery\assets\Assets;
use \humhub\modules\gallery\widgets\GalleryList;
use \yii\helpers\Html;

$bundle = Assets::register($this);
$this->registerJsVar('galleryMediaUploadUrl', 'unused');
?>

<div id="gallery-container" class="panel panel-default">
    <?php echo Html::beginForm(null, null, ['data-target' => '#globalModal', 'id' => 'gallery-form']); ?>
    <div class="panel-body">
        <?php if ($this->context->canWrite(false)): ?>
            <div class="row button-action-menu">
                <div class="col-sm-4">
                    <a class="btn btn-default" data-target="#globalModal"
                       href="<?php echo $this->context->contentContainer->createUrl('/gallery/custom-gallery/edit'); ?>"><i
                            class="glyphicon glyphicon-plus"></i> Add
                        Gallery</a>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div id="logContainer" class="col-sm-12" style="display: none">
                <ul class="alert alert-danger">
                </ul>
            </div>
            <?php echo GalleryList::widget(['entryList' => array_merge($stream_galleries, $custom_galleries)]); ?>
        </div>
    </div>
    <?php echo Html::endForm(); ?>
</div>