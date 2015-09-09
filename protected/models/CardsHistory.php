<?php

/**
 * This is the model class for table "{{cards_history}}".
 *
 * The followings are the available columns in table '{{cards_history}}':
 * @property integer $id
 * @property integer $card_id
 * @property string $action_date
 * @property integer $status
 */
class CardsHistory extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{cards_history}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('card_id, status', 'required'),
            array('card_id, status', 'numerical', 'integerOnly'=>true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, card_id, action_date, status', 'safe', 'on'=>'search'),
        );
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'action_date' => Yii::t('app','Date'),
            'status' => Yii::t('app','Status'),
        );
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CardsHistory the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
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
        $criteria->compare('card_id',$this->card_id);
        $criteria->compare('status',$this->status);
        if (!empty($this->action_date)) {
            $criteria->addBetweenCondition('action_date', 
                date('Y-m-d H:i:s',strtotime($this->action_date)), 
                date('Y-m-d H:i:s',strtotime('+1 day', strtotime($this->action_date))), 'AND');
        }
        return new CActiveDataProvider($this, array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder' => 'action_date ASC',
            ),
            'pagination'=>array(
                'pageSize'=>20
            )
        ));
    }
    
    public function getCardStatus() {
        switch ($this->status) {
            case Cards::STATUS_CREATE: $status = CHtml::tag('span',array('class'=>'label label-primary'),Yii::t('app','Create'),true); break;
            case Cards::STATUS_ACTIVE: $status = CHtml::tag('span',array('class'=>'label label-success'),Yii::t('app','Active'),true); break;
            case Cards::STATUS_INACTIVE: $status = CHtml::tag('span',array('class'=>'label label-default'),Yii::t('app','Inactive'),true); break;   
        }
        return $status;
    }
    
    public static function getStatusList() {
        return array(
            Cards::STATUS_CREATE  => Yii::t('app','Create'),
            Cards::STATUS_ACTIVE   => Yii::t('app','Active'),
            Cards::STATUS_INACTIVE => Yii::t('app','Inactive'),
        );
    }
}
