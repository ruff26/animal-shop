<?php

namespace backend\controllers\shop;

use yii;
use yii\web\Controller;
use shop\useCases\manage\Shop\UploadManageService;
use shop\forms\manage\Shop\UploadForm;

class UploadController extends Controller
{
    private $service;

    public function __construct($id, $module, UploadManageService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function actionIndex()
    {
        $form = new UploadForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->uploadCsvFile($form);
                return $this->redirect(['index']);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('upload', ['model'=>$form]);
    }
}