<?php

namespace app\models;

use Yii;
use app\models\ImageUpload;
use app\models\Category;

/**
 * This is the model class for table "article".
 *
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $date
 * @property string $image
 * @property integer $viewed
 * @property integer $user_id
 * @property integer $status
 * @property integer $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'description', 'content'], 'required'],
            [['title', 'description', 'content'], 'string'],
            [['date'], 'date', 'format'=>'php:Y-m-d'],
            [['date'], 'default', 'value'=>date('Y-m-d')],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'content' => 'Content',
            'date' => 'Date',
            'image' => 'Image',
            'viewed' => 'Viewed',
            'user_id' => 'User ID',
            'status' => 'Status',
            'category_id' => 'Category ID',
        ];
    }
    
    public function saveImageName($fileName)
    {
        $this->image = $fileName;
        return $this->save(false);
    }

    // Удаление картинки после удаления статьи
    public function deleteImage()
    {
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);
    }
    // Действие после удаления
    public function beforeDelete()
    {
        // Вызываем удаление картинки
        $this->deleteImage();
        return parent::beforeDelete();
    }
    
    public function getImage()
    {
        if($this->image)
        {
            return '/uploaded/'.$this->image;
        }else{
            return '/uploaded/no-photo.png';
        }
    }
    public function saveCategory($categoryId)
    {
        if(!$categoryId == null)
        {
            //Получаем сатегорию по id
            $category = Category::findOne($categoryId);
            
            $this->link('category',$category);
            
            return true;
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticleTags()
    {
        return $this->hasMany(ArticleTag::className(), ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['article_id' => 'id']);
    }
    
    public function getCategory()
    {
        return $this->hasOne(Category::className(),['id'=>'category_id']);
    }
}
