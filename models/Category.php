<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 *
 * @property Imagehascategory[] $imagehascategories
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 512],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImages()
    {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])
            ->viaTable('imagehascategory', ['category_id' => 'id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImagesCount()
    {
        return $this->hasMany(Image::className(), ['id' => 'image_id'])
            ->viaTable('imagehascategory', ['category_id' => 'id'])->count();
    }
        
    /**
     * 
     */    
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->created_at = date("Y-m-d H:i:s");;
            }
            return true;
        }
        return false;
    }
        
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }
}
