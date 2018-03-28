<?php
namespace shop\useCases\manage\Shop;

use shop\repositories\Shop\BrandRepository;
use shop\repositories\Shop\ProductRepository;
use shop\entities\Shop\Product\Product;
use yii\web\UploadedFile;
use yii\helpers\BaseInflector;
use shop\forms\manage\Shop\UploadForm;
use shop\entities\Meta;

abstract class AbstractUploadManageService
{
    protected $products;
    protected $brands;

    protected function __construct(
        ProductRepository $products,
        BrandRepository $brands
    )
    {
        $this->products = $products;
        $this->brands = $brands;
    }

    protected function updateProducts(UploadForm $form)
    {
        $file = UploadedFile::getInstance($form, 'csvFile');
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

    protected function updateBrands()
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

    abstract public function getProducts();

}