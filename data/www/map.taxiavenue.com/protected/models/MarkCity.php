<?php

/**
 * This is the model class for table "mark_city".
 *
 * The followings are the available columns in table 'mark_city':
 * @property integer $id
 * @property integer $id_mark
 * @property integer $id_city
 */
class MarkCity extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'mark_city';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id_mark, id_city', 'required'),
            array('id_mark, id_city', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, id_mark, id_city', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(			
            'mark' => array(self::BELONGS_TO, 'Mark', 'id_mark'),
            'city' => array(self::BELONGS_TO, 'City', 'id_city'),
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
            'id_city' => 'Id City',
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
        $criteria->compare('id_city',$this->id_city);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return MarkCity the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function GetMarker() {
        return $this->mark;
    }
}
