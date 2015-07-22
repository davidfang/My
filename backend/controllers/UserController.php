<?php
/**
 *      ┏┓　　　┏┓
 *    ┏┛┻━━━┛┻┓
 *    ┃　　　　　　　┃
 *    ┃　　　━　　　┃
 *    ┃　┳┛　┗┳　┃
 *    ┃　　　　　　　┃
 *    ┃　　　┻　　　┃
 *    ┃　　　　　　　┃
 *    ┗━┓　　　┏━┛
 *        ┃　　　┃   神兽保佑
 *        ┃　　　┃   代码无BUG！
 *         ┃　　　┗━━━┓
 *        ┃　　　　　　　┣┓
 *        ┃　　　　　　　┏┛
 *        ┗┓┓┏━┳┓┏┛
 *          ┃┫┫　┃┫┫
 *          ┗┻┛　┗┻┛
 */

namespace backend\controllers;

use backend\models\forsearch\AdminUserSearch;
use backend\models\LoginForm;
use Yii;
use backend\models\AdminUser;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class UserController extends BackendController
{
    /*
     * 用户管理
     */
    public function actionIndex()
    {
        $searchmodel = new AdminUserSearch();
        $dataprovider = $searchmodel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'model'        => new AdminUser(['scenario' => 'create']),
            'dataprovider' => $dataprovider,
            'searchmodel'  => $searchmodel,
        ]);
    }

    /**
     * 登陆
     * @return null|string
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post(),'') && $model->login()) {
            return $this->goBack();
        } else {
            $this->layout = 'main-login';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除用户
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = AdminUser::findOne($id);
        if ($model->delete()) {
            Yii::$app->session->setFlash('success');
        } else {
            Yii::$app->session->setFlash('fail', '删除失败');
        }
        return $this->redirect(['user/index']);
    }

    /**
     * 登出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * 添加用户
     * @return null|string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdduser()
    {
        $model = new AdminUser(['scenario' => 'create']);
        if (Yii::$app->request->isPost) {
            $model->load($_POST);
            if ($model->validate() && $model->save(false)) {
                Yii::$app->session->setFlash('success');
            } else {
                Yii::$app->session->setFlash('fail', '添加失败');
            }
            return $this->redirect(['user/index']);
        }
    }

    public function actionLoadhtml()
    {
        if ($id = Yii::$app->request->post('id')) {
            $model = AdminUser::findOne($id);
        } else {
            $model = new AdminUser();
        }
        return $this->renderPartial('loadhtml', [
            'model' => $model,
        ]);
    }

    /**
     * ajax验证是否存在
     * @return array
     */
    public function actionAjaxvalidate()
    {
        $model = new AdminUser();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'username');
        }
    }

    /**
     * 设置头像
     * @return string|Response
     * @throws \Exception
     */
    public function actionSetphoto()
    {
        $up = UploadedFile::getInstanceByName('photo');
        if ($up && !$up->getHasError()) {
            $userid = Yii::$app->user->id;
            $filename = $userid . '-' . date('YmdHis') . '.' . $up->getExtension();
            $path = Yii::getAlias('@backend/web/upload') . '/user/';
            FileHelper::createDirectory($path);
            $up->saveAs($path . $filename);
            $model = AdminUser::findOne($userid);
            $oldphoto = $model->userphoto;
            $model->userphoto = $filename;
            if ($model->update()) {
                Yii::$app->session->setFlash('success');
                //删除旧头像
                if (is_file($path . $oldphoto))
                    unlink($path . $oldphoto);
                return $this->goHome();
            } else {
                print_r($model->getErrors());
                exit;
            }
        }
        return $this->render('setphoto', [
            'preview' => Yii::$app->user->identity->userphoto,
        ]);
    }

    /**
     * 修改密码
     * @return string|Response
     */
    public function actionChangepwd()
    {
        $model = AdminUser::findOne(Yii::$app->user->id);
        $model->scenario = 'chgpwd';
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('AdminUser');
            if($model->validatePassword($post['oldpassword'])) {
                if ($model->validatePassword($post['password'])) {
                    Yii::$app->session->setFlash('fail', '新密码不可与原始密码一样');
                } else {
                    $model->setPassword($post['password']);
                    Yii::$app->session->setFlash('success');
                }
            }else{
                Yii::$app->session->setFlash('fail', '原密码错误');
            }

            return $this->goHome();
        }
        return $this->render('changepwd', [
            'model' => $model,
        ]);
    }
}