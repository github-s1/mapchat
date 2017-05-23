<?php

/**
 * This is the model class for table "avatar".
 *
 * The followings are the available columns in table 'avatar':
 * @property integer $id
 * @property string $big_photo
 * @property string $small_photo
 * @property string $src
 *
 * The followings are the available model relations:
 * @property Users[] $users
 */
class Avatar extends CActiveRecord
{	public $directory_name = 'users_avatar';
	public $images;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'avatar';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('big_photo, small_photo', 'required'),
			array('big_photo, small_photo', 'length', 'max'=>45),
			array('src', 'length', 'max'=>255),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, big_photo, small_photo, src', 'safe', 'on'=>'search'),
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
			'users' => array(self::HAS_MANY, 'Users', 'id_avatar'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'big_photo' => 'Big Photo',
			'small_photo' => 'Small Photo',
			'src' => 'Src',
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
		$criteria->compare('big_photo',$this->big_photo,true);
		$criteria->compare('small_photo',$this->small_photo,true);
		$criteria->compare('src',$this->src,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Avatar the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	protected function beforeSave() {
		if(!parent::beforeSave())
            return false;
		
        if($image=CUploadedFile::getInstance($this,'avatar')) {
			
			$avatar = Avatar::model()->findByPk($this->id);
			if(!empty($avatar)) {
				$this->deleteFile(false, $avatar->big_photo); // старый файл удалим, потому что загружаем новый
			}	
            $this->images=$image;
			@mkdir(Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name);
            
			$file_name = 'avatar_'.time();
			$this->images->saveAs(Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$file_name.'.'.$image->getExtensionName());
			$this->big_photo = $file_name.'.'.$image->getExtensionName();	
			$this->small_photo = $file_name.'.'.$image->getExtensionName();
			$this->src =  Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$file_name.'.'.$image->getExtensionName();
		}
        return true;
    }
	
    protected function beforeDelete(){
        if(!parent::beforeDelete())
            return false;
        $this->deleteFile(true); // удалили модель? удаляем и файл
        return true;
    }
 
    public function deleteFile($is_delete = false, $photo = null){
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
