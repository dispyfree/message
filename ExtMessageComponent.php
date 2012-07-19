<?php

/**
* This is the Message-Component
* You can retrieve and save messages using this component
* 
* @author   dispy   <dispyfree@googlemail.com>
*/
class ExtMessageComponent extends CApplicationComponent{
    
    public function init(){
        //Publish the bootstrap-js and the css-file
        $cs = Yii::app()->getAssetManager();
        $assetPath = $cs->publish(Yii::getPathOfAlias('ext.message.assets'));
        Yii::app()->clientScript->registerScriptFile(
                $assetPath.'/js/bootstrap-modal.js');
        Yii::app()->clientScript->registerCssFile($assetPath.'/css/messages.css');
        
        //Set Import paths
        Yii::import('ext.message.models.*');
        Yii::import('ext.message.widgets.*');
    }
    
    /**
     * Post a message into the database (this is the most convenient variant)
     * @param string    $type   supported types: '' (is a warning), 'error', success, 'info'
     * @param string    $msg    The message you want to display in plain text
     * @param array     $messageOptions Options: see ExtMessage::$defaults
     * @param array     $triggerOptions Options: see ExtTrigger::getDefaults 
     */
    public function postMessage($type, $msg, $messageOptions = array(), $triggerOptions = array()){
       
        //create Message and set attributes
        $message        = new ExtMessage();
        $message->type  = $type;
        $message->text  = $msg;
        $message->params= $messageOptions;
        
        //delegate to lower level
        $this->postExtMessage($message, $triggerOptions);
    }
    
    /**
     * This is the direct variant, you won't use generally
     * @param string    $message    the message to post
     * @param array     $options    the trigger options (see ExtTrigger::getDefaults)
     * @return boolean  true/false
     * @throws RuntimeException 
     */
    public function postExtMessage($message, $options){
        
        //Assure that the message is saved
        if(!$message->save()){
            $errors = $message->getErrors();
            throw new RuntimeException(
                    Yii::t('message', 
                            'Unable to save message:{err}', 
                            array('{err]' => $errors[0]))
                    );
        }
            
        //Merge the options for the trigger
        $options = array_merge(ExtTrigger::getDefaults(), $options);
        
        //Create Trigger itself
        $trigger = new ExtTrigger();
        $trigger->message_id = $message->id;
        $trigger->setAttributes($options);
        
        //Set the user or the session this trigger is assigned to
        if(!Yii::app()->user->isGuest){
            $trigger->user_id               = Yii::app()->user->id;
        }
        $trigger->session_identifier    = $this->getSessionIdentifier();
        
        return $trigger->save();
    
    }
    
    /**
     * Gets all the messages for the current user
     * if the user isn't logged in, it will use the cookie
     * @see getSessionIdentifier
     * @return array    array of Message-Instances
     */
    public function getMessages(){
        $criteria = new CDbCriteria();
        
        //If there's no filter set, assume that the filtering is done otherwise 
        // e.g.: ACL
        $filterDisabled = 'user_id = NULL AND session_identifier = NULL';
        $conditions     = array($filterDisabled);
        
        //If the user is logged in we search for his User-ID
        if(!Yii::app()->user->isGuest){
            $conditions[] = 'user_id = '.Yii::app()->user->id;
        }
        
        //Non-exclusive: he may have gotten a message before he logged in 
        $conditions[] = 'session_identifier = :sessId';
        $criteria->params[':sessId'] = $this->getSessionIdentifier();
        
        $criteria->addCondition($conditions, 'OR');
        
        //We want only Messages for which at least one trigger for the current user
        //matched
        $relatedModels = array(
            'triggers' => array(
              'joinType'=>'INNER JOIN',
            )
        );
        $messages = ExtMessage::model()->with($relatedModels)->findAll($criteria);
        
        return $messages;
    }
    
    /**
     * Returns a unique session identifier
     * This assumes that you actually use the PHPSESSID-cookie. If you don't, 
     * adjust this line
     * @return string   the unique session-identifier
     */
    protected function getSessionIdentifier(){
        return $_COOKIE['PHPSESSID'];
    }
}
?>
