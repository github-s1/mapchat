<?php

/**
 * This is the model class for table "city".
 *
 * The followings are the available columns in table 'city':
 * @property integer $id
 * @property integer $id_region
 * @property string $name_ru
 * @property string $name_en
 * @property double $lat
 * @property double $lng
 * @property double $northeast_lat
 * @property double $northeast_lng
 * @property double $southwest_lat
 * @property double $southwest_lng
 *
 * The followings are the available model relations:
 * @property Region $idRegion
 */
class City extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'city';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id_region, name_ru, lat, lng, northeast_lat, northeast_lng, southwest_lat, southwest_lng', 'required'),
            array('id_region', 'numerical', 'integerOnly'=>true),
            array('lat, lng, northeast_lat, northeast_lng, southwest_lat, southwest_lng', 'numerical'),
            array('name_ru, name_en', 'length', 'max'=>145),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, id_region, name_ru, name_en, lat, lng, northeast_lat, northeast_lng, southwest_lat, southwest_lng', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'idRegion' => array(self::BELONGS_TO, 'Region', 'id_region'),
            'idMarks' => array(self::HAS_MANY, 'MarkCity', 'id_city'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'в таблице храняться названия не только городов, но и названия населенных пунктов',
            'id_region' => 'Id Region',
            'name_ru' => 'Name Ru',
            'name_en' => 'Name En',
            'lat' => 'Lat',
            'lng' => 'Lng',
            'northeast_lat' => 'Northeast Lat',
            'northeast_lng' => 'Northeast Lng',
            'southwest_lat' => 'Southwest Lat',
            'southwest_lng' => 'Southwest Lng',
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
        $criteria=new CDbCriteria;

        $criteria->compare('id',$this->id);
        $criteria->compare('id_region',$this->id_region);
        $criteria->compare('name_ru',$this->name_ru,true);
        $criteria->compare('name_en',$this->name_en,true);
        $criteria->compare('lat',$this->lat);
        $criteria->compare('lng',$this->lng);
        $criteria->compare('northeast_lat',$this->northeast_lat);
        $criteria->compare('northeast_lng',$this->northeast_lng);
        $criteria->compare('southwest_lat',$this->southwest_lat);
        $criteria->compare('southwest_lng',$this->southwest_lng);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return City the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function getMarkersForCity() 
    {
        $query = 'SELECT * FROM mark WHERE active="Y" AND id IN (SELECT id_mark FROM mark_city WHERE id_city='.$this->id.')';
        $marker = Mark::model()->findAllBySql($query);

        /*
        $markers_city_array = $this->idMarks;
        $marker = array();
        if(!empty($markers_city_array)) {
                foreach($markers_city_array as $m) {
                        $marker[] = $m->GetMarker();
                }
        }
        */
        return $marker;

    }

    /**
     * Переопределение родительского метода
     * Добавление id страны в возвращаемый результат
     */
    public function getAttributes($names = true, $addCountry = false) {
        $attr = parent::getAttributes();
        if (!$addCountry) return $attr;
        $attr['id_country'] = $this->idRegion->id_country;
        return $attr;
    }
}
