<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImageUpload extends Model{
    public $image;

    
    public function rules()
    {
        return [
        [['image'],'required'],
        [['image'],'file','extensions'=>'jpg,png'],
        ];
    }
    
    public function FileUpload(UploadedFile $file, $currentImage)
    {
        $this->image = $file;
        if($this->validate())
        {
            $this->deleteCurrentImage($currentImage);
            
            //Создаём уникальное название для картинки
            $fileName = $this->generateFileName($file);
            
            //Загружаем картнку в папку uploaded
            $file->saveAs(Yii::getAlias('@web').'uploaded/'.$fileName);
            return $fileName;
        }
    }

    public function pathUploadedImage(){
        return $path = Yii::getAlias('@web').'uploaded/';
    
    }
    
    public function generateFileName($file)
    {
        return strtolower(md5(uniqid($file->baseName)).'.'.$file->extension);
    }
    
    public function deleteCurrentImage($currentImage)
    {
        //Если до этого уже была загружена картинка для этой статьи
        if(is_file($this->pathUploadedImage().$currentImage)){
            //удаляем текущую картинку
            unlink($this->pathUploadedImage().$currentImage);
        }
    }
}

?> 
