<?php

/**
 * This is the model class for table "cars".
 *
 * The followings are the available columns in table 'cars':
 * @property integer $id
 * @property string $color
 * @property string $photo1
 * @property string $photo2
 * @property string $photo3
 * @property string $photo4
 * @property string $photo5
 * @property string $photo6
 * @property string $photo7
 * @property integer $model
 */
class Cars extends CActiveRecord
{	
	public $directory_name = 'cars';
	
	public $image;
	public $image_old;
	public $is_delete = false;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'cars';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('color, model', 'required'),
			array('model', 'numerical', 'integerOnly'=>true),
			array('color, photo1, photo2, photo3, photo4, photo5, photo6, photo7', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, color, photo1, photo2, photo3, photo4, photo5, photo6, photo7, model', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'model_car' => array(self::BELONGS_TO, 'Models', 'model'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'color' => 'Color',
			'photo1' => 'Photo1',
			'photo2' => 'Photo2',
			'photo3' => 'Photo3',
			'photo4' => 'Photo4',
			'photo5' => 'Photo5',
			'photo6' => 'Photo6',
			'photo7' => 'Photo7',
			'model' => 'Model',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('color',$this->color,true);
		$criteria->compare('photo1',$this->photo1,true);
		$criteria->compare('photo2',$this->photo2,true);
		$criteria->compare('photo3',$this->photo3,true);
		$criteria->compare('photo4',$this->photo4,true);
		$criteria->compare('photo5',$this->photo5,true);
		$criteria->compare('photo6',$this->photo6,true);
		$criteria->compare('photo7',$this->photo7,true);
		$criteria->compare('model',$this->model);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Cars the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beforeSave(){
        if(!parent::beforeSave())
            return false;
        if($this->scenario=='update'){
			if($image=CUploadedFile::getInstance($this,'photo1')) {
				$this->deleteFile(1); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo1.'.$image->getExtensionName());
				$this->photo1 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo2')) {
				$this->deleteFile(2); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo2.'.$image->getExtensionName());
				$this->photo2 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo3')) {
				$this->deleteFile(3); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo3.'.$image->getExtensionName());
				$this->photo3 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo4')) {
				$this->deleteFile(4); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo4.'.$image->getExtensionName());
				$this->photo4 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo5')) {
				$this->deleteFile(5); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo5.'.$image->getExtensionName());
				$this->photo5 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo6')) {
				$this->deleteFile(6); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo6.'.$image->getExtensionName());
				$this->photo6 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo7')) {
				$this->deleteFile(7); // старый документ удалим, потому что загружаем новый
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo7.'.$image->getExtensionName());
				$this->photo7 = $image->getExtensionName();	
			}
		}
        return true;
    }
	
	public function afterSave(){
        if($this->scenario=='insert'){
			if($image=CUploadedFile::getInstance($this,'photo1')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo1.'.$image->getExtensionName());
				$this->photo1 = $image->getExtensionName();
			}
			if($image=CUploadedFile::getInstance($this,'photo2')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo2.'.$image->getExtensionName());
				$this->photo2 = $image->getExtensionName();
			}
			if($image=CUploadedFile::getInstance($this,'photo3')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo3.'.$image->getExtensionName());
				$this->photo3 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo4')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo4.'.$image->getExtensionName());
				$this->photo4 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo5')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo5.'.$image->getExtensionName());
				$this->photo5 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo6')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo6.'.$image->getExtensionName());
				$this->photo6 = $image->getExtensionName();	
			}
			if($image=CUploadedFile::getInstance($this,'photo7')) {
				$this->image=$image;
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR);
				$this->image->saveAs(
					Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo7.'.$image->getExtensionName());
				$this->photo7 = $image->getExtensionName();	
			}
		}
        return true;
    }
	
	protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
		$this->is_delete = true;
        $this->deleteDocument(); // удалили модель? удаляем и файл
        return true;
    }
	
	public function deleteFile($id){
		$documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id.DIRECTORY_SEPARATOR.'photo'.$id.'.'.$this->image_old[$id];
		if(is_file($documentPath)) {
            unlink($documentPath);
		}	
    }
	
	public function deleteDocument(){		
		$documentPath = Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->id;
		$this->removeDirectory($documentPath);
		$this->is_delete = false;
    }
	
	public function removeDirectory($dir) {
		if ($objs = glob($dir."/*")) {
		   foreach($objs as $obj) {
			 is_dir($obj) ? removeDirectory($obj) : unlink($obj);
		   }
		}
		rmdir($dir);
	}

}
