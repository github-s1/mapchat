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
 * @property integer $bodytype
 */
class Cars extends CActiveRecord
{
	public $directory_name = 'cars';

	public $image;
	public $image_old;
	public $is_delete = false;

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
			array('number, marka, model, bodytype', 'required'),
			array('number', 'unique'),
			array('year, bodytype', 'numerical', 'integerOnly'=>true),
			array('number', 'length', 'max'=>20),
			array('marka, model, color', 'length', 'max'=>255),
			array('photo1, photo2, photo3, photo4, photo5, photo6, photo7', 'length', 'max'=>45),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, number, marka, model, color, year, photo1, photo2, photo3, photo4, photo5, photo6, photo7, bodytype', 'safe', 'on'=>'search'),
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
			'bodytype' => array(self::BELONGS_TO, 'Bodytypes', 'id_bodytype'),
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
		$criteria->compare('bodytype',$this->bodytype);

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

		$this->number_translit = preg_replace("/[^0-9A-zА-я -]+/u", "", $this->_translit($this->number));

		if($image=CUploadedFile::getInstance($this,'photo1')) {
			$this->deleteFile(1); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo1.'.$image->getExtensionName());

			$this->photo1 = $this->number_translit.'_photo1.'.$image->getExtensionName();
		}
		if($image=CUploadedFile::getInstance($this,'photo2')) {
			$this->deleteFile(2); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo2.'.$image->getExtensionName());
			$this->photo2 = $this->number_translit.'_photo2.'.$image->getExtensionName();
		}
		if($image=CUploadedFile::getInstance($this,'photo3')) {
			$this->deleteFile(3); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo3.'.$image->getExtensionName());
			$this->photo3 = $this->number_translit.'_photo3.'.$image->getExtensionName();
		}
		if($image=CUploadedFile::getInstance($this,'photo4')) {
			$this->deleteFile(4); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo4.'.$image->getExtensionName());
			$this->photo4 = $this->number_translit.'_photo4.'.$image->getExtensionName();
		}
		if($image=CUploadedFile::getInstance($this,'photo5')) {
			$this->deleteFile(5); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo5.'.$image->getExtensionName());
			$this->photo5 = $this->number_translit.'_photo5.'.$image->getExtensionName();
		}
		if($image=CUploadedFile::getInstance($this,'photo6')) {
			$this->deleteFile(6); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo6.'.$image->getExtensionName());
			$this->photo6 = $this->number_translit.'_photo6.'.$image->getExtensionName();
		}
		if($image=CUploadedFile::getInstance($this,'photo7')) {
			$this->deleteFile(7); // старый документ удалим, потому что загружаем новый
			$this->image=$image;
			@mkdir(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name);
			$this->image->saveAs(
				Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->number_translit.'_photo7.'.$image->getExtensionName());
			$this->photo7 = $this->number_translit.'_photo7.'.$image->getExtensionName();
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

	public function deleteDocument(){
		$documentPath = array();
		$documentPath[0]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo1;
		$documentPath[1]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo2;
		$documentPath[2]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo3;
		$documentPath[3]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo4;
		$documentPath[4]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo5;
		$documentPath[5]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo6;
		$documentPath[6]=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo7;

		foreach($documentPath as $Path) {
			if(is_file($Path)) {
				unlink($Path);
			}
		}
		$this->is_delete = false;
    }

	protected function deleteFile($id){
		$documentPath=Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->image_old[$id];
		if(is_file($documentPath)) {
            unlink($documentPath);
		}
    }

	public function copy_photos(){
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo1))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo1, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo1);
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo2))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo2, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo2);
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo3))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo3, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo3);
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo4))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo4, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo4);
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo5))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo5, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo5);
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo6))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo6, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo6);
		if(is_file(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo7))
			copy(Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.'cars_temp'.DIRECTORY_SEPARATOR.$this->photo7, Yii::getPathOfAlias('webroot.images').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo7);
    }

	protected function _translit($string){
		  $table = array(
                'А' => 'A',
                'Б' => 'B',
                'В' => 'V',
                'Г' => 'G',
                'Д' => 'D',
                'Е' => 'E',
                'Ё' => 'YO',
                'Ж' => 'ZH',
                'З' => 'Z',
                'И' => 'I',
                'Й' => 'J',
                'К' => 'K',
                'Л' => 'L',
                'М' => 'M',
                'Н' => 'N',
                'О' => 'O',
                'П' => 'P',
                'Р' => 'R',
                'С' => 'S',
                'Т' => 'T',
                'У' => 'U',
                'Ф' => 'F',
                'Х' => 'H',
                'Ц' => 'C',
                'Ч' => 'CH',
                'Ш' => 'SH',
                'Щ' => 'CSH',
                'Ь' => '',
                'Ы' => 'Y',
                'Ъ' => '',
                'Э' => 'E',
                'Ю' => 'YU',
                'Я' => 'YA',

                'а' => 'a',
                'б' => 'b',
                'в' => 'v',
                'г' => 'g',
                'д' => 'd',
                'е' => 'e',
                'ё' => 'yo',
                'ж' => 'zh',
                'з' => 'z',
                'и' => 'i',
                'й' => 'j',
                'к' => 'k',
                'л' => 'l',
                'м' => 'm',
                'н' => 'n',
                'о' => 'o',
                'п' => 'p',
                'р' => 'r',
                'с' => 's',
                'т' => 't',
                'у' => 'u',
                'ф' => 'f',
                'х' => 'h',
                'ц' => 'c',
                'ч' => 'ch',
                'ш' => 'sh',
                'щ' => 'csh',
                'ь' => '',
                'ы' => 'y',
                'ъ' => '',
                'э' => 'e',
                'ю' => 'yu',
                'я' => 'ya',
				'\\' => '_'
		);

		$output = str_replace(
			array_keys($table),
			array_values($table),$string
		);

		return $output;
	}
}
