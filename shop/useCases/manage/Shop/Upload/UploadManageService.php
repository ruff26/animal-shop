<?php

namespace shop\useCases\manage\Shop;


use shop\entities\Shop\Brand;

class UploadManageService extends AbstractUploadManageService
{
    private $filePath;
//    private $provider;
    private $config;
    private $delimiter;

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

//    private function setProvider($filecsv)
//    {
//        if (strripos($filecsv[1], 'Лукас-Н')) {
//            $this->provider = 'lukas-n';
//        } elseif (true) {
//            $this->provider = 'other_provider';
//        }
//    }

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


}
