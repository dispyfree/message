<?php

/**
 * This is the model class for table "{{trigger}}".
 *
 * The followings are the available columns in table '{{trigger}}':
 * @property integer $id
 * @property integer $message_id
 * @property string $created            The timestamp of creation
 * @property string $session_identifier A session-identifier if it's bound to a session
 * @property integer $user_id           A user-identifier, if he's logged in 
 * @property integer $show_on_time      If set, the message will show up after that time
 * @property integer $time_out          A window in seconds after the message was supped to be shown after which the message will be timed out
 */
class ExtTrigger extends CActiveRecord
{
        
    /**
     * Defaults, can be overwritten using ExtMessageComponent::postMessage fourth parameter
     * @see ExtMessageComponent::postMessage
     * @return array
     */
        public static function getDefaults(){
            return array(
                /**
                 * The timstamp denotes after what time the message will be shown
                 * (can be the future, of course)
                 * see the timeout below 
                 */
                'show_on_time'  => time(),
                /**
                 * Limits the visibility of a message: if more than timeout seconds
                 * have elapsed after the message should have been shown (see show_on_time),
                 * the message times out and won't be shown any more.
                 * If set to zero, the timeout is not active
                 */
                'timeout'       => 0,
            );
        }
        
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ExtTrigger the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{trigger}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
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
                    'message' => array(self::BELONGS_TO, 'ExtMessage', 'message_id')
		);
	}
        
        public function defaultScope(){
            return array(
                /**
                 * Triggers do only apply if 
                 * a) the message should already be shown
                 * b) they haven't had a timeout yet 
                 */
              'condition' => 'show_on_time < NOW() AND IF(time_out != 0, ((NOW() - show_on_time) < time_out), TRUE)'  
            );
        }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'message_id' => 'Message',
			'created' => 'Created',
			'session_identifier' => 'Session Identifier',
			'user_id' => 'User',
			'show_on_time' => 'Show On Time',
			'time_out' => 'Time Out',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('message_id',$this->message_id);
		$criteria->compare('created',$this->created,true);
		$criteria->compare('session_identifier',$this->session_identifier,true);
		$criteria->compare('user_id',$this->user_id);
		$criteria->compare('show_on_time',$this->show_on_time,true);
		$criteria->compare('time_out',$this->time_out);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}