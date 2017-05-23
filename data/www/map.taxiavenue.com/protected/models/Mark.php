<?php

/**
 * This is the model class for table "mark".
 *
 * The followings are the available columns in table 'mark':
 * @property integer $id
 * @property integer $id_kind
 * @property integer $id_user
 * @property string $description
 * @property string $address
 * @property integer $createDatatime
 * @property string $active
 * @property string $anonymous
 * @property integer $click_spam
 * @property integer $views
 * @property string $active_balloon
 * @property integer $period
 *
 * The followings are the available model relations:
 * @property Audio[] $audios
 * @property Comments[] $comments
 * @property Kind $idKind
 * @property Users $idUser
 * @property Photo[] $photos
 * @property Point[] $points
 */
class Mark extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'mark';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('id_kind, id_user', 'required'),
            array('id_kind, id_user, createDatatime, click_spam, views, period', 'numerical', 'integerOnly'=>true),
            array('description', 'length', 'max'=>5000),
            array('address, color', 'length', 'max'=>255),
            array('active, anonymous, active_balloon', 'length', 'max'=>1),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, id_kind, id_user, description, address, createDatatime, active, anonymous, click_spam, views, active_balloon, period, color', 'safe', 'on'=>'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'audios' => array(self::HAS_MANY, 'Audio', 'id_mark'),
            'comments' => array(self::HAS_MANY, 'Comments', 'id_mark'),
            'idKind' => array(self::BELONGS_TO, 'Kind', 'id_kind'),
            'idUser' => array(self::BELONGS_TO, 'Users', 'id_user'),
            'photos' => array(self::HAS_MANY, 'Photo', 'id_mark'),
            'points' => array(self::HAS_MANY, 'Point', 'id_mark'),
            'idCity' => array(self::HAS_ONE, 'MarkCity', 'id_mark'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'id_kind' => 'Id Kind',
            'id_user' => 'Id User',
            'description' => 'Description',
            'address' => 'Address',
            'createDatatime' => 'Create Datatime',
            'active' => 'Active',
            'anonymous' => 'Anonymous',
            'click_spam' => 'Click Spam',
            'views' => 'Views',
            'active_balloon' => 'Active Balloon',
            'period' => 'Period',
			'color' => 'color',
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
        $criteria->compare('id_kind',$this->id_kind);
        $criteria->compare('id_user',$this->id_user);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('address',$this->address,true);
        $criteria->compare('createDatatime',$this->createDatatime);
        $criteria->compare('active',$this->active,true);
        $criteria->compare('anonymous',$this->anonymous,true);
        $criteria->compare('click_spam',$this->click_spam);
        $criteria->compare('views',$this->views);
        $criteria->compare('active_balloon',$this->active_balloon,true);
        $criteria->compare('period',$this->period);
		$criteria->compare('color',$this->color,true);

        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Mark the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    public function ViewsIncrement() 
    {
        $this->views += 1;
        $this->save();	
    }
    
    /*
     * <?=$city->name_en?>/<?=$mark['kind']['code'];?>
     */
    public function getUrl()
    {
//        $city = $this->_getCity();
        return Yii::app()->createUrl('mark/index', array(
            'id'=>$this->id
        ));
    }
    
    public function getKindUrl()
    {
        $city = $this->_getCity();
        return Yii::app()->createUrl('post/view', array(
            'id'=>$this->id,
            'title'=>$this->title,
        ));
    }
    
    private function _getCity() {
        $criteria = new CDbCriteria;
        $criteria->select='id_city';
        $criteria->condition='id_mark=:id_mark';
        $criteria->params=array(':id_mark'=>$this->id);
        return MarkCity::model()->find($criteria);
    }


    public function SetParams($attr = null)
    {	
        if(!empty($attr)) {
            foreach($attr as $i => $element) {
                if(empty($element)) {
                    unset($attr[$i]);
                }
            }

            if(isset($attr['anonymous'])) {
                if ($attr['anonymous'] != 'y' && $attr['anonymous'] != 'n'){  
                    unset($attr['anonymous']); 
                }
            } else {
                unset($attr['anonymous']); 
            }

            if(isset($attr['active'])) {
                if ($attr['active'] != 'Y' && $attr['active'] != 'N'){  
                    unset($attr['active']); 
                }
            } else {
                unset($attr['active']); 
            }

            $this->attributes = $attr;
        }	
        return $this;
    }

}
