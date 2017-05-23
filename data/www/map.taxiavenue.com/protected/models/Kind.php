<?php

/**
 * This is the model class for table "kind".
 *
 * The followings are the available columns in table 'kind':
 * @property integer $id
 * @property integer $id_theme
 * @property integer $id_icon
 * @property integer $id_type
 * @property string $name_ru
 * @property string $code
 * @property string $description
 *
 * The followings are the available model relations:
 * @property FieldsKind[] $fieldsKinds
 * @property Icon $idIcon
 * @property Theme $idTheme
 * @property Type $idType
 * @property Mark[] $marks
 */
class Kind extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'kind';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('id_user, id_theme, id_icon, name_ru, code', 'required'),
			array('id_theme, id_icon, id_type', 'numerical', 'integerOnly'=>true),
			array('name_ru, code', 'length', 'max'=>45),
            array('lider', 'length', 'max'=>150),
            array('site, color', 'length', 'max'=>60),
			array('description', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, id_theme, id_icon, id_type, name_ru, code, description, color', 'safe', 'on'=>'search'),
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
			'fieldsKinds' => array(self::HAS_MANY, 'FieldsKind', 'id_kind'),
            'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
			'idIcon' => array(self::BELONGS_TO, 'Icon', 'id_icon'),
			'idTheme' => array(self::BELONGS_TO, 'Theme', 'id_theme'),
			'idType' => array(self::BELONGS_TO, 'Type', 'id_type'),
			'marks' => array(self::HAS_MANY, 'Mark', 'id_kind'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
            'id_user'=>'id user',
			'id_theme' => 'Id Theme',
			'id_icon' => 'Id Icon',
			'id_type' => 'Id Type',
			'name_ru' => 'Name Ru',
			'code' => 'исп в урл',
			'description' => 'Description',
            'lider' => 'Лидер города',
            'site' => 'Сайт',
			'color' => 'Цвет линии',
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
		$criteria->compare('id_user',$this->id_user);
		$criteria->compare('id_theme',$this->id_theme);
		$criteria->compare('id_icon',$this->id_icon);
		$criteria->compare('id_type',$this->id_type);
		$criteria->compare('name_ru',$this->name_ru,true);
		$criteria->compare('code',$this->code,true);
		$criteria->compare('description',$this->description,true);
        $criteria->compare('lider',$this->lider,true);
        $criteria->compare('site',$this->site,true);
		$criteria->compare('color',$this->site,true);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Kind the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function SetParams($attr = null)
	{	
		if(!empty($attr)) {
			foreach($attr as $i => $element) {
				if(empty($element)) {
					unset($attr[$i]);
				}
			}
			
			if(isset($attr['name_ru']) && !isset($attr['code'])){
				$attr['code'] = Transliteration::file($attr['name_ru']);
			}
			$this->attributes = $attr;
		}	
		return $this;
	}
}
