<?php

namespace shop\useCases\manage\Shop\Upload;

use shop\entities\Shop\Brand;
use yii\helpers\BaseInflector;
use shop\entities\Meta;

class LukasNProviderParser extends AbstractUploadManageService
{
    public function uploadFile($file)
    {
        $configFile = require(__DIR__ . '/../../../../../backend/config/providers.php');
        $config = $configFile['lukas-n'];
        $delimiter = $configFile['delimiter'];

        $products = $this->getProducts($file, $config, $delimiter);
        $this->updateProducts($products);
        $this->updateBrands($file, $config, $delimiter);
    }

    public function getProducts($file, $config, $delimiter)
    {
        $filecsv = file($file);
        $products = array();
        foreach ($filecsv as $data) {
            $row = explode($delimiter, $data);
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
            $product['provider'] = 'lukas_n';

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
        return $products;
    }

    protected function updateBrands($file, $config, $delimiter)
    {
        $filecsv = file($file);
        $meta = new Meta('','','');

        foreach ($filecsv as $data) {
            $row = explode($delimiter, $data);
            $brandName = $row[$config['brand']];
            if ($brandName == '' || $brandName == 'Производитель') {
                continue;
            }
            if (!Brand::findOne(['name' => $brandName])) {
                $brand = Brand::create($brandName, BaseInflector::slug($brandName, '-', false), $meta);
                $brand->save();
            }
        }


    }
}