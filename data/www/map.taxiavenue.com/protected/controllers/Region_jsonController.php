<?php







class Region_jsonController extends Controller



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



				'actions'=>array('index','view','getRegionByCountryId'),



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



   		$dataProvider=new CActiveDataProvider('Region');



		$this->render('index',array(



			'dataProvider'=>$dataProvider,



		));



	}







	/**



	 * Returns the data model based on the primary key given in the GET variable.



	 * If the data model is not found, an HTTP exception will be raised.



	 * @param integer $id the ID of the model to be loaded



	 * @return Region the loaded model



	 * @throws CHttpException



	 */



	public function loadModel($id)



	{



		$model=Region::model()->findByPk($id);



		if($model===null)



			throw new CHttpException(404,'The requested page does not exist.');



		return $model;



	}







    /**



     *  Метод возвращает список всех регионов которые на ходяться в стране с id=id_country



     * входящие параметры:



     * id_country (int) - id страны по которой нужно вернуть список регионов



     * limit (int) - кол-во выводимых стран



     * offset (int) - с какой записи делать вывод (нумирация начинается с 0)



     */



    public function actionGetRegionByCountryId(){







        $id_country = Yii::app()->request->getPost('id_country');



        //$id_country = $_GET['id_country'];



        if (isset($id_country)):



              $criteria = new CDbCriteria;



              $criteria->condition='id_country=:id_country';

              

              $criteria->group = 'name_ru';

              $criteria->order = '`id` ASC';



              $criteria->params=array(':id_country'=>$id_country);



              $limit = Yii::app()->request->getPost('limit');



              if (isset($limit)){



                 $criteria->limit=$limit;



              }



              $offset = Yii::app()->request->getPost('offset');



              if (isset($offset)){



                  $criteria->offset=$offset;



              }



            $Regions = Region::model()->findAll($criteria);



            $conv = new Converting;



            if (!empty($Regions)){



                foreach($Regions as $objRegion){



                    $arRegions[]=$conv->convertModelToArray($objRegion);



                }
				
				// Убираем повторы
				
				$ids = "";
				if(count($arRegions) > 1) {
					for($i = 0; $i < count($arRegions)-1; $i++) {
						for($j = $i+1; $j < count($arRegions); $j++) {
							if($arRegions[$i]['lat'] == $arRegions[$j]['lat'] && $arRegions[$i]['lng'] == $arRegions[$j]['lng']) {
								$ids[] = $j;
							}
						}
						if(isset($arRegions[$i]['id']) && $arRegions[$i]['id'] == 4) {
							$ids[] = $i;
						}
					}
					if($ids) {
						foreach($ids as $i) {
							unset($arRegions[$i]);
						}
						$temp = $arRegions;
						unset($arRegions);
						foreach($temp as $v) {
							$arRegions[] = $v;
						}
						unset($temp);
					}
				}

                $res=array('response'=>$arRegions);



            }



            else



                $res=array('response'=>'false');







        else:



            $res=array('response'=>'false');



        endif;



            $res_encode=json_encode($res);



            $this->render('getRegionByCountryId',array(



                'data'=>$res_encode



            ));







    }







}



