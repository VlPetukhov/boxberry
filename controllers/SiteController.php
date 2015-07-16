<?php

namespace app\controllers;

use app\models\CalculatorForm;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SiteController extends Controller
{

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Site index
     * @return string
     */
    public function actionIndex()
    {
        $formModel = new CalculatorForm();

        if($formModel->load(Yii::$app->request->post()) && $formModel->validate())
        {
            return $this->render('result', ['formModel' => $formModel]);
        }
        else
        {
            return $this->render('form', ['formModel' => $formModel]);
        }
    }

    public function actionGetpvz(){
        if(Yii::$app->request->isAjax && isset($_POST['cityId']))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return CalculatorForm::getCityPvzArray(intval($_POST['cityId']));
        }
        else
        {
            throw new NotFoundHttpException();
        }
    }
}
