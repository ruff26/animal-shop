<?php

namespace shop\useCases\manage\Shop;

use shop\repositories\Shop\BrandRepository;
use shop\repositories\Shop\ProductRepository;
use yii\web\UploadedFile;
use shop\entities\Shop\Product\Product;
use shop\forms\manage\Shop\UploadForm;
use shop\entities\Shop\Brand;
use shop\useCases\manage\Shop\Upload\LukasNProviderParser;

class UploadManageService
{
    public function __construct(
        ProductRepository $products,
        BrandRepository $brands
    )
    {
        $this->products = $products;
        $this->brands = $brands;
    }

    public function upload(UploadForm $form)
    {
        $providerId = $form->providers;
        $providerNames = Product::getProviders();
        $providerName = $providerNames[$providerId] . 'Parser';

        switch ($providerNames[$providerId]) {
            case 'Lukas-N':
                $provider = new LukasNProviderParser($this->products, $this->brands);
                break;
            case 'other_1':
                $provider = new OtherProviderParser($this->products, $this->brands);
                break;
        }
        $file = UploadedFile::getInstance($form, 'csvFile');
        $filePath = __DIR__ . '/uploads/' . $file->baseName . '.' . $file->extension;
        $file->saveAs($filePath);
        $provider->uploadFile($filePath);
    }
}
