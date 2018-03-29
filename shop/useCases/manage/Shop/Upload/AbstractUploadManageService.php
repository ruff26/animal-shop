<?php
namespace shop\useCases\manage\Shop\Upload;

use shop\repositories\Shop\BrandRepository;
use shop\repositories\Shop\ProductRepository;
use shop\entities\Shop\Product\Product;
use yii\web\UploadedFile;
use shop\forms\manage\Shop\UploadForm;
use shop\entities\Meta;

abstract class AbstractUploadManageService
{
    protected $products;
    protected $brands;

    public function __construct(
        ProductRepository $products,
        BrandRepository $brands
    )
    {
        $this->products = $products;
        $this->brands = $brands;
    }

    protected function updateProducts($products)
    {
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
            $this->products->save($product);
        }
    }

    abstract public function uploadFile($file);

}