<?php

/**
 * This is the model class for table "point".
 *
 * The followings are the available columns in table 'point':
 * @property integer $id
 * @property integer $id_mark
 * @property double $lat
 * @property double $lng
 * @property integer $order
 *
 * The followings are the available model relations:
 * @property Mark $idMark
 */
class Point extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'point';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id_mark, lat, lng, order', 'required'),
            array('id_mark, order', 'numerical', 'integerOnly'=>true),
            array('lat, lng', 'numerical'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, id_mark, lat, lng, order', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
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
            'lat' => 'Lat',
            'lng' => 'Lng',
            'order' => 'Order',
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
        $criteria->compare('id_mark',$this->id_mark);
        $criteria->compare('lat',$this->lat);
        $criteria->compare('lng',$this->lng);
        $criteria->compare('order',$this->order);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Point the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
}
