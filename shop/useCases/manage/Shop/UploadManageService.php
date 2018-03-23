<?php

namespace shop\useCases\manage\Shop;

use shop\entities\Meta;
use shop\entities\Shop\Brand;
use shop\entities\Shop\Product\Product;
use yii\web\UploadedFile;
use yii\helpers\BaseInflector;
use shop\forms\manage\Shop\UploadForm;

class UploadManageService
{
    private $filePath;
    private $provider;
    private $config;
    private $delimiter;

    public function __construct()
    {
    }

    public function init($file)
    {
        $filename = $file->baseName . '.' . $file->extension;
        $upload = $file->saveAs(__DIR__ . '/uploads/' . $filename);
//        $upload = $file->saveAs('@uploadsPath/' . $filename);
        if (!$upload) {
            return false;
        }
        define('CSV_PATH', __DIR__ . '/uploads/');
        $csv_file = CSV_PATH . $filename;
        $this->filePath = $csv_file;
        $filecsv = file($csv_file);
        $this->setProvider($filecsv);
        $configFile = require(__DIR__ . '/../../../../backend/config/providers.php');
        $this->config = $configFile[$this->provider];
        $this->delimiter = $configFile['delimiter'];
    }

    public function uploadCsvFile(UploadForm $form)
    {
        $file = UploadedFile::getInstances($form, 'csvFile');
        $this->init($file);


        $products = $this->getProducts();
        foreach ($products as $data) {
            if (!$product = Product::findOne(['code'=>$data['code']])) {
                $product = Product::create(
                    $data['brandId'],
                    $data['categoryId'],
                    $data['code'],
                    $data['name'],
                    $data['description'],
                    $data['weight'],
                    $data['quantity'],
                    new Meta(
                        $data['meta_title'],
                        $data['meta_description'],
                        $data['meta_keywords']
                    )
                );
                $product->activate();
            } else {
                $product->edit(
                    $data['brandId'],
                    $data['code'],
                    $data['name'],
                    $data['description'],
                    $data['weight'],
                    new Meta(
                        $data['meta_title'],
                        $data['meta_description'],
                        $data['meta_keywords']
                    )
                );
                $product->changeMainCategory($data['categoryId']);
            }
            $product->setPrice($data['price'], $data['old_name']);
            $product->save();
//            $this->products->save($product);

        }
    }

    private function setProvider($filecsv)
    {
        if (strripos($filecsv[1], 'Лукас-Н')) {
            $this->provider = 'lukas-n';
        } elseif (true) {
            $this->provider = 'other_provider';
        }
    }

    public function getProducts()
    {
        $filecsv = file($this->filePath);
        $products = array();
        $config = $this->config;
        switch ($this->provider) {
            case 'lukas-n':
                foreach ($filecsv as $data) {
                    $row = explode($this->delimiter, $data);
                    if ($row[0] == '' || $row[0] == 'Артикул' || $row[0][0] == '<') {
                        continue;
                    }
                    $product = array();
                    $product['name'] = $row[$config['name']];
                    $product['code'] = $row[$config['code']];
                    $product['description'] = $row[$config['description']];
                    $product['price'] = (int)((float)str_replace(',', '.', $row[$config['price']]) * 100);
                    $brand = Brand::findOne(['name' => $row[$config['brand']]]);
                    $product['brandId'] = isset($brand) ? $brand->id : 1;
                    $product['provider'] = $this->provider;

                    $product['old_price'] = 0;
                    $product['weight'] = 0;
                    $product['quantity'] = 'Есть' ? 100 : 0;
                    $product['categoryId'] = 2;
                    //meta info
                    $product['meta_title'] = '';
                    $product['meta_description'] = '';
                    $product['meta_keywords'] = '';
                    $products[] = $product;
                }
                break;
            case 'other_provider':
                //some code
                break;
        }
        return $products;
    }

    public function updateBrands()
    {
        $filecsv = file($this->filePath);
        $meta = new Meta('','','');
        switch ($this->provider) {
            case 'lukas-n':
                foreach ($filecsv as $data) {
                    $row = explode($this->delimiter, $data);
                    $brandName = $row[$this->config['brand']];
                    if ($brandName == '' || $brandName == 'Производитель') {
                        continue;
                    }
                    if (!Brand::findOne(['name' => $brandName])) {
                        $brand = Brand::create($brandName, BaseInflector::slug($brandName, '-', false), $meta);
                        $brand->save();
                    }
                }
                break;
            case 'other_provider':
                //some code
                break;
        }
    }
}
