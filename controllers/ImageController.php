<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\UploadedFile;
use app\models\Image;
use app\models\Category;
use app\models\User;
use app\models\UploadForm;
use yii\filters\AccessControl;

class ImageController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['own', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['own', 'update', 'delete'],
                        'roles' => ['admin'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['own', 'update', 'delete'],
                        'roles' => ['updateImage'],
                        'roleParams' => function($rule) {
                            if (Yii::$app->request->get('user_id')) {
                                $image = Image::findOne(['user_id' => Yii::$app->request->get('user_id')]);
                            }
                            else {
                                $image = Image::findOne(Yii::$app->request->get('id'));
                            }
                            return ['image' => $image];
                        },
                    ],                                
                ],
            ],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function actionIndex($category_id = null)
    {
        $models = array();
        if($category_id) {
            $category = Category::findOne($category_id);
            if($category) {
                foreach($category->images as $image) {
                    $models[] = Image::findOne($image->id);
                }
            }
            
        }
        else {
            $models = Image::find()->orderBy('id')->all();
            $category = null;
        }
        return $this->render('index', [
            'models' => $models,
            'category' => $category,
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function actionOwn($user_id = null)
    {
        if($user_id) {
            return $this->render('index', [
            'models' => Image::findAll(['user_id' => $user_id]),
        ]);
        }
        return $this->redirect('/image');
    }
    
    /**
     * {@inheritdoc}
     */
    public function actionView($id)
    {
        $model = Image::findOne($id);
        $user = User::findOne($model->user_id);
        $owner = $user->lastname.' '.$user->firstname;
        return $this->render('view', [
            'model' => $model,
            'owner' => $owner,
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function actionResult($id)
    {
        return $this->render('result', [
            'model' => Image::findOne($id),
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function actionUpdate($id)
    {
        $model = Image::findOne($id);
        if (isset($model)) {
            if ($model->load(Yii::$app->request->post()))
            {
                if ($model->validate()) {
                    $category_id = Yii::$app->request->post('Image')['categories'];
                    if ($category_id) {
                        $model->addCategory($category_id);
                    }
                    $model->save(false);
                    return $this->redirect(['/image/update', 'id' => $id]);
                }
            }
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    public function actionUpload()
    {
        $model = new UploadForm();
        if (Yii::$app->request->isPost) {
            $url = Yii::$app->request->post('UploadForm')['imageFile'];
            if($url) {
                Image::loadFromUrl($url, 'UploadForm', 'imageFile');
            }
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            $dir = sys_get_temp_dir();
            if ($model->upload()) {
                // image is uploaded successfully
                $image = new Image();
                $image->hash = Image::toHash($model->content);                
                $image->user_id = Yii::$app->user->getId();
                $image->filename = $model->imageFile->name;
                $image->source = ($url) ? $url : 'local';
                $image->size = $model->imageFile->size;
                $image->content = ($url) ? '' : $model->content;
                $image->created_at = date('Y-m-d H:i:s');
                $result = Image::searchMd5($image->hash);
                if(!$result) {
                    $image->save(false);
                    $image->addCategory(1);
                }
                return $this->render('/image/result',[
                    'model' => $image,
                    'result' => $result,
                ]);
            }
        }
        return $this->redirect('/');
    }
        
    /**
     * {@inheritdoc}
     */        
    public function actionDelete($id)
    {
        $model = Image::findOne($id);
        if ($model)
        {
            $model->delete();
            return $this->redirect('/image'); 
        }
        return $this->goBack();       
    }
     
    /**
     * {@inheritdoc}
     */        
    public function actionRmcat($category_id, $id)
    {
        if($category_id != 1) {
            $image = Image::findOne($id);
            if ($image)
            {
                $image->removeCategory($category_id);
            }
        }
        return $this->redirect('/image/update?id='.$id);       
    }
}
