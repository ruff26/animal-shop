<?php

namespace shop\forms\manage\Shop;

use yii\web\UploadedFile;
use yii\base\Model;
use shop\entities\Shop\Product\Product;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $csvFile;
    public $providers = [];

    public function rules(): array
    {
        return [
            ['providers', 'required'],
            [['csvFile'],'file','extensions'=>'csv','maxSize'=>1024 * 1024 * 5],
        ];
    }

    public function getProductProviders() :array
    {
        return Product::getProviders();
    }

//    public function beforeValidate(): bool
//    {
//        if (parent::beforeValidate()) {
//            $this->csvFile = UploadedFile::getInstances($this, 'csvFile');
//            return true;
//        }
//        return false;
//    }

}