<?php

/**
 * This is the model class for table "{{message}}".
 *
 * The followings are the available columns in table '{{message}}':
 * @property integer $id        the primary id
 * @property string $type       the type (supported: '' (is a warning), 'successs', 'error', 'info'
 * @property string $params     Anything you can serialize
 * @property string $created    the creation-time
 */
class ExtMessage extends CActiveRecord
{
        
    /**
     * Defaults of the message you can overwrite using the third parameter of 
     * ExtMessageComponent::postMessage
     * @see ExtMessageComponent::postMessage
     * @var array
     */
        public static $defaults = array(
           //If enabled a cross will be displayed at the top right edge
          'closAble'            => true, 
            
           // If the message should be deleted if it has been shown
          'removalUponDisplay'  => true,
            
           //Show a grey backdrop, the user has to choose something 
           // (This doesn't disable closAble)
          'modal'               => false,
            
          //The title of the message, will be ignored if 'useHeading' 
          // in ExtMessageWidget is disabled
          'title'               => '',
            
          /**
           * Format:
           * array(
           *    array(
           *        'type' => ''(default) | 'primary' | 'info' | 'success'
           *                     | 'warning' | 'danger' | 'inverse'
           *        'size' => '' (default, middle) | 'large', 'small', 'mini'
           *        'url'  => an absolute URL (string). {messageId} will replaced with the ID of the current message
           *                default:'#'
           *        'caption' => string, default: 'OK'
           *    )
           * ) 
           */  
          'actions'             => array(),
          
            /**
             * Custom Views can be used to modify the message-text itself
             * see views/demo.php for an example
             * Example: 'demo' 
             */
          'customView'          => false
        );
        
        /**
         * Merges with default params and encodes for the database
         * @return boolean 
         */
        public function beforeSave(){
            if(!is_array($this->params))
                $this->params = array();
                
            $this->params = array_merge(self::$defaults, $this->params);
            $this->params = json_encode($this->params);
            
            return true;
        }
        
        /**
         * Decodes the params from the database
         * @return boolean 
         */
        public function afterFind(){
            $this->params = json_decode($this->params);
            
            return true;
        }
        
        
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return ExtMessage the static model class
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
		return '{{message}}';
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
                    'triggers' => array(self::HAS_MANY, 'ExtTrigger', 'message_id')
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'type' => 'Type',
			'params' => 'Params',
			'created' => 'Created',
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
		$criteria->compare('type',$this->type,true);
		$criteria->compare('params',$this->params,true);
		$criteria->compare('created',$this->created,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}