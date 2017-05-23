<?php

class Kind_jsonController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/none';

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view','GetKindByMarkId','AddKind',
                    'GetKindByThemeId','GetAllKinds',
                    'GetTypeByKindId','UpdateKind','DeleteKind',
                    'GetKindByIdArray', 'ChangeTypeKind', 'updateIcon'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}


	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
        throw new CHttpException(404,'The requested page does not exist.');
		/*$dataProvider=new CActiveDataProvider('Kind');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));*/
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Kind the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Kind::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

    /**
     * функция возвращает данные вида метки
     * входом служит id метки
     */
    public function actionGetKindByMarkId(){
        $id_mark = Yii::app()->request->getPost('id_mark');
        //$id_mark = $_GET['id_mark'];
        $Kinds = $this->GetKindByMarkId($id_mark);
        $res=array('response'=>$Kinds);
        $res_encode=json_encode($res);
        $this->render('getKindByMarkId',array(
            'data'=>$res_encode
        ));
    }

    /**
     * функция возвращает данные вида метки
     * входом служит id темы
     */
    public function actionGetKindByThemeId(){
        $id_theme = Yii::app()->request->getPost('id_theme');
        //$id_theme = $_GET['id_theme'];
        $limit = Yii::app()->request->getPost('limit');
        $offset = Yii::app()->request->getPost('offset');
        $Kinds=$this->GetKindByThemeId($id_theme,$limit,$offset);
        $res=array('response'=>$Kinds);
        $res_encode=json_encode($res);
        $this->render('getKindByThemeId',array(
            'data'=>$res_encode
        ));
    }

    /**
     * функция возвращает данные всех видов метки
     */
    public function actionGetAllKinds(){
        $arKinds = Kinds::getAllKindsNoStatic();
        if($arKinds){
            $result = $arKinds;
        } else{
            $result = 'failed';
        }		
		
		echo json_encode(array('response'=>$result));
    }

    /**
     * Функция возвращает тип вида метки
     * входом служит id вида
     */
    public function actionGetTypeByKindId(){
        $this->layout='//layouts/none';
        $id_kind = Yii::app()->request->getPost('id_kind');
        //$id_kind = $_GET['id_kind'];
        $Kinds=$this->GetTypeByKindId($id_kind);
        $res = array('response'=>$Kinds);
        $res_encode=json_encode($res);
        $this->render('getTypeByKindId',array(
            'data'=>$res_encode
        ));
    }

    /**
     * функция позволяет добавить вид метки
     * вход
     * id_theme (int) - id темы вида метки, обязательно
     * id_icon (int) - id  иконки вида метки, если иконка не задана берется дефолтная иконка
     * id_type (int) - id  типа вида метки, обязательно
     * name_ru (string) - название вида метки, обязательно
     * code (string) - код вида метки исп. для построения урл, если поле не передается, то оно транслитирируется из названия вида
     * description (string) - описание вида метки
     */
    public function actionAddKind(){
        $id_user = $this->is_authentificate();		
		//$id_user = 3;
		$id_theme = Yii::app()->request->getPost('id_theme');
        $id_icon = Yii::app()->request->getPost('id_icon');
        $id_type = Yii::app()->request->getPost('id_type');
        $name_ru = Yii::app()->request->getPost('name_ru');
        $code = Yii::app()->request->getPost('code');
        $description = Yii::app()->request->getPost('description');
		$color = Yii::app()->request->getPost('color');
        $result = Kinds::addKind($id_theme, $id_icon, $id_type, $name_ru, $code, $description, $id_user, false, $color);
        
		echo json_encode(array('response'=>$result));
    }

    /**
     * метод позволяет изменить вид метки
     * входящие параметры:
     * обязательные поля
     * id_kind (int) - id вида который нужно изменить
     * необязательные поля
     * id_type (int) - id типа к которому относиться вид
     * id_theme (int) - id темык которому относиться вид
     * name_ru (string) - название вида
     * code (string) - поле для отображения в урл.
     * description (string) - описание вида
     * id_icon - id иконки вида, если не задан берется дефолтная иконка
     */
    public function actionUpdateKind(){
        $id_kind = Yii::app()->request->getPost('id_kind');
        $id_user = Yii::app()->request->getPost('id_user');
        $id_theme = Yii::app()->request->getPost('id_theme');
        $id_icon = Yii::app()->request->getPost('id_icon');
        $id_type = Yii::app()->request->getPost('id_type');
        $name_ru = Yii::app()->request->getPost('name_ru');
        $code = Yii::app()->request->getPost('code');
        $site = Yii::app()->request->getPost('site');
        $lider = null;//Yii::app()->request->getPost('lider');
        $description = Yii::app()->request->getPost('description');
        $hash = Yii::app()->request->getPost('hash');
        if (!isset($hash))
                $hash = Yii::app()->session->getSessionID();
        if (!isset($id_user))
            $id_user = Yii::app()->user->id;
        $update = new updateData;
        $result = $update->updateKind($id_kind, $id_user, $id_theme, $id_icon, $id_type, $name_ru, $code, $description, $site, $lider, $hash);
        if (($result!='false')&&(!array_key_exists('error', $result))){
            $icon = $this->GetIconByKindId($id_kind);
            $result['icon_url']='http://' . $_SERVER['SERVER_NAME'] . Yii::app()->request->baseUrl . '/img/mark_icons/' . $icon['name'];
        }
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('updateKind',array(
            'data'=>$res_encode
        ));
    }

    /**
     * Изменить тип заначка (из общего)
     */
    public function actionChangeTypeKind() {
        $id_kind = Yii::app()->request->getPost('id_kind');
        $id_mark = Yii::app()->request->getPost('id_mark');
        $mark = Mark::model()->findByPk($id_mark);
        if (!$mark) {
            $result = 'error';
        } else {
            $mark->id_kind = $id_kind;
            $mark->save();
            $result = $mark;
        }
        $this->renderJSON($result);
    }
    
    /**
     * метод позволяет удалить вид метки с заданым id
     * вход:
     * id_kind (int) - id вида который нужно удалить
     */
    public function actionDeleteKind(){
        $id_kind = Yii::app()->request->getPost('id_kind');
        //$id_kind = $_GET['id_kind'];

        $delete = new deleteData;
        $result = $delete->deleteKind($id_kind);
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('deleteKind',array(
            'data'=>$res_encode
        ));
    }

    /** Возвращает информацию по виду метки
     *  @param idArray массив id видов по которым нужно вернуть данные
     */
    public function actionGetKindByIdArray(){
        $idString = Yii::app()->request->getPost('idString');
        //$idString = $_GET['idString'];
        $idArray = explode(',', $idString);

        //$idArray = array(1, 2, 3, 4, 5, 122);
            if (isset($idArray) && (is_array($idArray))){
            foreach ($idArray as $id){
                $kind= $this->GetKindById($id);
                if ($kind!='false'){
                    $result[] =$kind;
                }
                else
                    $result='false';
            }
        }
        else
            $result = 'false';
        $res = array('response'=>$result);
        $res_encode=json_encode($res);
        $this->render('getKindByIdArray',array(
            'data'=>$res_encode
        ));

    }

    /**
     * Изменить иконку вида
     */
    public function actionUpdateIcon() {
        $idUser = $this->is_authentificate();
        $idKind = Yii::app()->request->getParam('id');
        $add = new addData();
        if (empty($idKind)) $this->renderJSON(array('error'=>array('error_code'=>2,'error_msg'=>'Не передан id вида.')));
        if (empty($_FILES)) $this->renderJSON(array('error'=>array('error_code'=>2,'error_msg'=>$add::ERROR_FILE)));
        
        $_FILES = FilesOperations::FilesProcessing("Icon", "iconKind");
        $model = Kind::model()->findByPk($idKind);
        
        if (empty($model)) $this->renderJSON(array('error'=>array('error_code'=>2,'error_msg'=>'Вид не существует.')));
        if ($model->id_user != Yii::app()->user->id) $this->renderJSON(array('error'=>array('error_code'=>2,'error_msg'=>'У Вас нет прав редактировать вид.')));
        
        $idIcon = $model->idIcon->id;
        $iconModel = $model->idIcon;
        if ($idIcon == 1 and $model->id != 1) {
            $iconModel = Icons::addIconToNewKind($model->code, true);
            $model->id_icon = $iconModel->id;
            $model->save();
        }
        
        if ($iconModel->updateFile()) {
            $this->renderJSON(array('response' => 'success'));
        } else {
            $this->renderJSON(array('response' => 'failed'));
        }
        //$this->renderJSON($model->idIcon->updateFile());
    }

}
