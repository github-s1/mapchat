<?php

/**
 * This is the model class for table "photo".
 *
 * The followings are the available columns in table 'photo':
 * @property integer $id
 * @property integer $id_mark
 * @property string $name
 * @property integer $position
 *
 * The followings are the available model relations:
 * @property Mark $idMark
 */
class Photo extends CActiveRecord
{	public $directory_name = 'mark_photos';
	public $images;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'photo';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_mark, name', 'required'),
			array('id_mark, position', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_mark, name, position', 'safe', 'on'=>'search'),
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
			'idMark' => array(self::BELONGS_TO, 'Mark', 'id_mark'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'id_mark' => 'Id Mark',
			'name' => 'Name',
			'position' => 'Position',
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
		$criteria->compare('id_mark',$this->id_mark);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('position',$this->position);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Photo the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beforeSave() {
		if(!parent::beforeSave())
            return false;
		
        if($image = CUploadedFile::getInstance($this,'name')) {
			$photo = Photo::model()->findByPk($this->id);
			if(!empty($photo)) {
				$this->deleteFile(false, $photo->name); // старый файл удалим, потому что загружаем новый
			}	
            $this->images = $image;
			@mkdir(Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name);
            
			$file_name = 'photo_'.time();
			$file_src = Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$file_name.'.'.$image->getExtensionName();
			
			$this->images->saveAs(Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$file_name.'.'.$image->getExtensionName());
			
			$this->name = $file_name.'.'.$image->getExtensionName();	
		}
        return true;
    }
	
    protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
        $this->deleteFile(true); // удалили модель? удаляем и файл
        return true;
    }
 
    protected function deleteFile($is_delete = false, $photo = null){
		if($is_delete) {
			$documentPath=Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->photo;
		} else {
			$documentPath=Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$photo;
		}
		if(is_file($documentPath)) {
            unlink($documentPath);
		}	
	}
}
