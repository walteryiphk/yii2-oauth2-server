<?php

namespace filsh\yii2\oauth2server\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;

class DefaultController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::className()
            ],
        ]);
    }

    public function actionToken()
    {
        $server = $this->module->getServer();
        $request = $this->module->getRequest();
        $response = $server->handleTokenRequest($request);

        return $response->getParameters();
    }

    /**
     * Default authorize endpoint
     */
    public function actionAuthorize()
    {
        $server = $this->module->getServer();
        $request = $this->module->getRequest();
        $response = $this->module->getResponse();
        if(!$server->validateAuthorizeRequest($request,$response))
        {
            return $response->getParameters();
        }
        if(empty($_POST))
        {
            exit('<form method="post"><label>Do You Authorize?</label><br/>
                 <input type="submit" name="authorized" value="yes"><input type="submit" name="authorized" value="no"></form>');
        }
        $is_authorized = ($_POST['authorized'] === 'yes');
        if(!\Yii::$app->user->isGuest)
        {
            $server->handleAuthorizeRequest($request,$response,$is_authorized,\Yii::$app->user->getId());
        }
        else
        {
            $server->handleAuthorizeRequest($request,$response,$is_authorized);
        }
        return $response->getParameters();
    }
}