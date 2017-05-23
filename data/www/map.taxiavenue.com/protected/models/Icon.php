<?php

/**
 * This is the model class for table "icon".
 *
 * The followings are the available columns in table 'icon':
 * @property integer $id
 * @property string $name
 * @property integer $width
 * @property integer $height
 *
 * The followings are the available model relations:
 * @property Kind[] $kinds
 */
class Icon extends CActiveRecord
{
    public $directory_name = 'mark_icons';
    
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'icon';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, width, height', 'required'),
			array('width, height', 'numerical', 'integerOnly'=>true),
           // array('name', 'file', 'types'=>'jpg, gif, png'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, name, width, height', 'safe', 'on'=>'search'),
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
			'kinds' => array(self::HAS_MANY, 'Kind', 'id_icon_kind'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Name',
			'width' => 'Width',
			'height' => 'Height',
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
		$criteria->compare('name',$this->name,true);
		$criteria->compare('width',$this->width);
		$criteria->compare('height',$this->height);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Icon the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
    
    /**
     * Обновить иконку вида
     */
    public function updateFile() {
        $this->deleteFile(true);
        $this->version++;
        
        if (strpos($this->name, '~') !== false) $name = substr($this->name, 0, strpos($this->name, '~'));
            else $name = substr($this->name, 0, strpos($this->name, '.'));

        $ext = substr($this->name, strpos($this->name, '.'));
        $this->name = $name.'~'.$this->version.$ext;
        $this->save();
        
        $ds = DIRECTORY_SEPARATOR;
        $file_name = $this->name;
        if($image = CUploadedFile::getInstance($this, 'iconKind')) {
            
            $fileSrc = Yii::getPathOfAlias('webroot.img').$ds.$this->directory_name.$ds.$file_name;
            $image->saveAs($fileSrc);
            Yii::app()->ih
			->load($fileSrc)
			->resize(40, 40, true)
			->save($fileSrc);
            return true;
        }
        return false;
    }
    
    protected function deleteFile($is_delete = false, $photo = null){
		if($is_delete) {
			$documentPath=Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$this->name;	
		} else {
			$documentPath=Yii::getPathOfAlias('webroot.img').DIRECTORY_SEPARATOR.$this->directory_name.DIRECTORY_SEPARATOR.$photo;
		}
		if(is_file($documentPath)) {
            unlink($documentPath);
		}		
	}
}
