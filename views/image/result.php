<?php

use yii\helpers\Html;
use app\models\Image;

/* @var $this yii\web\View */
/* @var $model app\models\Image */

$this->title = 'Search Results';
$this->params['breadcrumbs'][] = ['label' => 'Images', 'url' => ['/image']];
$this->params['breadcrumbs'][] = $this->title;
Image::clearDir();
$model->toFile();
?>
<h1><?= Html::encode($this->title) ?></h1>
<div class="card border-info">
    <div class="card-header bg-info text-light h5">
        <?= Html::encode($model->filename) ?>
    </div>
    <div class="card-body">
        <div class="d-flex bd-highlight">
            <div class="col bd-highlight">
                <img class="w-100" src="/img/<?= $model->filename; ?>" alt="<?= $model->filename; ?>" />
            </div>
            <div class="col flex-shrink-1 bd-highlight">
                <h3>Your image is <?= ($result) ? '' : 'not' ?> found in the search database!</h3>
                <h4><?= count($result) ?> result(s)</h4>
                <p>Searched <?= Image::getCount() ?> images for <?= Html::encode($model->filename) ?></p>   
                <p>Source: <?= (Html::encode($model->source) !== 'local') ? '<a target="_blank" href="'.Html::encode($model->source).'">' : null ?> <?=  Html::encode($model->source) ?> <?= (Html::encode($model->source) !== 'local') ? '</a>' : null ?></p>
                <p>Size: <?= Html::encode($model->size) ?> bytes</p>
                <p>MD-5 hash: <?= Html::encode($model->hash) ?></p>
                <p>Uploaded: <?= Yii::$app->formatter->asDatetime($model->created_at, 'long');?></p>
            </div>
        </div>
    </div>
</div>
