<?php

namespace shop\forms\manage\Shop;

use yii\web\UploadedFile;
use yii\base\Model;

class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $csvFile;
    public $updateItems = [];

    public function __construct($config = [])
    {
        $this->updateItems = [
            'Products',
            'Brands'
        ];
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
//            ['updateItems', 'each', 'rule' => ['integer']],
            [['csvFile'],'file','extensions'=>'csv','maxSize'=>1024 * 1024 * 5],
        ];
    }

//    public function beforeValidate(): bool
//    {
////        if (parent::beforeValidate()) {
//            $this->csvFile = UploadedFile::getInstances($this, 'csvFile');
//            return true;
////        }
////        return false;
//    }

}