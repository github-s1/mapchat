<?php

/**
 * This is the model class for table "cars".
 *
 * The followings are the available columns in table 'cars':
 * @property integer $id
 * @property string $number
 * @property string $marka
 * @property string $model
 * @property string $color
 * @property integer $year
 * @property string $photo1
 * @property string $photo2
 * @property string $photo3
 * @property string $photo4
 * @property string $photo5
 * @property string $photo6
 * @property string $photo7
 * @property integer $id_bodytype
 */
class Cars extends CActiveRecord
{	public $directory_name = 'cars';
	
	public $image;
	
	private $number_translit;
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
			array('number, marka, model, id_bodytype', 'required'),
			array('number', 'unique'),
			array('year, id_bodytype', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>20),
			array('number', 'number_validate'),
			array('marka, model, color', 'length', 'max'=>255),
			array('photo1, photo2, photo3, photo4, photo5, photo6, photo7', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, number, marka, model, color, year, photo1, photo2, photo3, photo4, photo5, photo6, photo7, id_bodytype', 'safe', 'on'=>'search'),
		);
	}
	
	public function number_validate($attribute)
	{	
		$this->$attribute = trim($this->$attribute);

		if(strpos($this->$attribute, 0x20) !== false)
			$this->addError($attribute, 'Номер не должен содержать пробелы.');
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(			'bodytype' => array(self::BELONGS_TO, 'Bodytypes', 'id_bodytype'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'number' => 'Гос. номер',
			'marka' => 'Марка',
			'model' => 'Модель',
			'color' => 'Цвет',
			'year' => 'Год выпуска',
			'photo1' => 'Photo1',
			'photo2' => 'Photo2',
			'photo3' => 'Photo3',
			'photo4' => 'Photo4',
			'photo5' => 'Photo5',
			'photo6' => 'Photo6',
			'photo7' => 'Photo7',
			'id_bodytype' => 'Тип кузова',
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
		$criteria->compare('number',$this->number,true);
		$criteria->compare('marka',$this->marka,true);
		$criteria->compare('model',$this->model,true);
		$criteria->compare('color',$this->color,true);
		$criteria->compare('year',$this->year);
		$criteria->compare('photo1',$this->photo1,true);
		$criteria->compare('photo2',$this->photo2,true);
		$criteria->compare('photo3',$this->photo3,true);
		$criteria->compare('photo4',$this->photo4,true);
		$criteria->compare('photo5',$this->photo5,true);
		$criteria->compare('photo6',$this->photo6,true);
		$criteria->compare('photo7',$this->photo7,true);
		$criteria->compare('id_bodytype',$this->id_bodytype);

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
			
		$this->number_translit = ConversionData::Translit($this->number);
		$file_name = $this->number_translit.'_'.time();
		
		$car = Cars::model()->findByPk($this->id);
		
		if(!empty($car)) {
			$car_photos = array($car->photo1, $car->photo2, $car->photo3, $car->photo4, $car->photo5, $car->photo6, $car->photo7);
		}
		// сохраняем фото прав и машины, а также их миниатюры
		$array_prop = array(); 
		for($i = 1; $i < 8; $i++) {
			if($image=CUploadedFile::getInstance($this,'photo'.$i)) {
				if(!empty($car)) {
					$this->deleteFile($i, $car_photos[$i - 1]); // старый документ удалим, потому что загружаем новый
				}	
				$this->image=$image;
				
				@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
				$file_src = Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$file_name.'_photo'.$i.'.'.$image->getExtensionName();
				
				$this->image->saveAs($file_src);
				
				Yii::app()->ih
				->load($file_src)
				->resize(80, false, true)
				->save(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.'small'.DIRECTORY_SEPARATOR.$file_name.'_photo'.$i.'.'.$image->getExtensionName());
				
				
				$array_prop['photo'.$i] = $file_name.'_photo'.$i.'.'.$image->getExtensionName(); 
			}
		}
		$this->SetProperties($array_prop);
        return true;
    }
	
	protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
        $this->deleteDocument(); // удалили модель? удаляем и файл
        return true;
    }
	
	// удаляет все фото и их миниатюры
	private function deleteDocument(){
		$documentPath = array();
		$small_documentPath = array();
		for($i = 1; $i < 8; $i++) {
			$documentPath[]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i);
			$small_documentPath[]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.'small'.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i);
		}
		foreach($documentPath as $Path) {
			if(is_file($Path)) {
				unlink($Path);
			}	
		}
		
		foreach($small_documentPath as $Path) {
			if(is_file($Path)) {
				unlink($Path);
			}	
		}
    }
	
	// удаляет фото и его миниатюру
	protected function deleteFile($id, $photo){
		$documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$photo;
		$small_documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.'small'.DIRECTORY_SEPARATOR.$photo;
		if(is_file($documentPath)) {
            unlink($documentPath);
		}
		if(is_file($small_documentPath)) {
            unlink($small_documentPath);
		}		
    }
	
	// копирует фотки для можерации
	public function copy_photos(){
		for($i = 1; $i < 8; $i++) {
			if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i))) {
				copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i), Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i));
			}
			if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.'small'.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i))) {
				copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.'small'.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i), Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.'small'.DIRECTORY_SEPARATOR.$this->getAttribute('photo'.$i));
			}
		}	
    }
	
	// копирует фото из буфера
	public function changePhoto(CarsTemp $cars_temp){
		$array_prop = array(); 
		for($i = 1; $i < 8; $i++) {
			$array_prop['photo'.$i] = $cars_temp->getAttribute('photo'.$i); 
		}
		$this->SetProperties($array_prop);
		
		return $this;
    }
	
	public function SetProperties($properties = null)
	{	
		if(!empty($properties)) {
			foreach($properties as $i => $element) {
				if($i == 'photo1' || $i == 'photo2' || $i == 'photo3' || $i == 'photo4' || $i == 'photo5' || $i == 'photo6'|| $i == 'photo7') {
					if($element == null) {
						unset($properties[$i]);
					}		
				} 
			}
			
			$this->attributes = $properties;
		}
		return $this;
	}
}
