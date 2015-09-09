<?php

/**
 * This is the model class for table "{{cards}}".
 *
 * The followings are the available columns in table '{{cards}}':
 * @property integer $id
 * @property integer $number
 * @property integer $status
 * @property string $series
 * @property string $created_at
 * @property string $expired_at
 * @property string $expired_var
 * @property double $amount
 */
class Cards extends CActiveRecord
{
    const SERIES_GENERAL = 1;
    const SERIES_VIP     = 2;
    
    const STATUS_CREATE   = 0;
    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 2;
    const STATUS_EXPIRED  = 3;
    
    const EXPIRED_YEAR      = 1;
    const EXPIRED_HALF_YEAR = 2;
    const EXPIRED_MONTH     = 3;
    
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
            return '{{cards}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('number, series, expired_var', 'required'),
            array('number, status, series, expired_var', 'numerical', 'integerOnly'=>true),
            array('amount', 'numerical','allowEmpty'=>true),
            array('number','unique'),
            array('created_at, updated_at, expired_at', 'safe'),
            array('number, status, series, created_at, expired_at', 'safe', 'on'=>'search'),
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
            'history'=> array(self::HAS_MANY, 'CardsHistory', 'card_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('app','Number'),
            'status' => Yii::t('app','Status'),
            'series' => Yii::t('app','Series'),
            'created_at' => Yii::t('app','Issue date'),
            'expired_at' => Yii::t('app','Expiration date'),
            'expired_var' => Yii::t('app','Expiration date'),
            'amount' => Yii::t('app','Amount'),
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
        $criteria->compare('series',$this->series);
        $criteria->compare('number',$this->number,true);
        if (!empty($this->created_at)) {
            $criteria->addBetweenCondition('created_at', 
                    date('Y-m-d H:i:s',strtotime($this->created_at)), 
                    date('Y-m-d H:i:s',strtotime('+1 day', strtotime($this->created_at))), 'AND');
        }
        if (!empty($this->expired_at)) {
            $criteria->addBetweenCondition('expired_at', 
                    strtotime($this->expired_at), 
                    strtotime('+1 day', strtotime($this->expired_at)), 'AND');
        }
        if (!empty($this->status)) {
            if ($this->status == self::STATUS_EXPIRED) {
                $criteria->addCondition('expired_at <= :time'); 
                $criteria->params = CMap::mergeArray($criteria->params, array(':time'=>time()));
            } else {
                $criteria->compare('status',$this->status); 
                $criteria->addCondition('expired_at > :time'); 
                $criteria->params = CMap::mergeArray($criteria->params, array(':time'=>time()));
            }
        }
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'pagination'=>array(
                'pageSize'=>20
            )
        ));
    }
        
    public function behaviors()
    {
        return array(
            'CTimestampBehavior' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'created_at',
                'updateAttribute' => 'updated_at',
                'setUpdateOnCreate' => true,
            ),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Cards the static model class
     */
    public static function model($className=__CLASS__)
    {
            return parent::model($className);
    }
    
    public static function getSeriesList($value=false) {
        $list = array(
            self::SERIES_GENERAL => Yii::t('app','General'),
            self::SERIES_VIP     => Yii::t('app','VIP'),
        );
        if (!$value) {
            return $list;
        } else {
            return $list[$value];
        }
    }
    
    public static function getStatusList() {
        return array(
            self::STATUS_ACTIVE   => Yii::t('app','Active'),
            self::STATUS_INACTIVE => Yii::t('app','Inactive'),
            self::STATUS_EXPIRED  => Yii::t('app','Expired'),
        );
    }
    
    public static function getExpiredList($value=false) {
        $list = array(
            self::EXPIRED_YEAR      => Yii::t('app','1 year'),
            self::EXPIRED_HALF_YEAR => Yii::t('app','6 months'),
            self::EXPIRED_MONTH     => Yii::t('app','1 month'),
        );
        if (!$value) {
            return $list;
        } else {
            return $list[$value];
        }
         
    }
    
    public function beforeSave() {
        if (empty($this->status)) {
            $this->status = self::STATUS_ACTIVE;
        }
        if (empty($this->expired_at)) {
            $this->expired_at = strtotime('+'.self::getExpiredList($this->expired_var));
        }
        return parent::beforeSave();
    }
    
    public function getCardStatus() {
        if ($this->expired_at < time()) {
            return CHtml::tag('span',array('class'=>'label label-danger'),Yii::t('app','Expired'),true);
        } else {
            if ($this->status == self::STATUS_ACTIVE) {
                return CHtml::tag('span',array('class'=>'label label-success'),Yii::t('app','Active'),true);
            } else {
                return CHtml::tag('span',array('class'=>'label label-default'),Yii::t('app','Inactive'),true);
            }
        }
    }
    
    public function changeStatus() {
        if ($this->status == self::STATUS_ACTIVE) {
            $this->status = self::STATUS_INACTIVE;
        } else {
            $this->status = self::STATUS_ACTIVE;
        }
        return $this->save();
    }
    
    public function afterSave() {
        $sql  = "INSERT INTO {{cards_history}} ";
        $sql .= "(card_id, status) ";
        $sql .= "VALUES(:card_id, :status)";
        $cardHist = new CardsHistory;
        $cardHist->status = $this->isNewRecord ? 0 : $this->status;
        $cardHist->card_id = $this->id;
        $cardHist->save();
        return parent::afterSave();
    }
}
