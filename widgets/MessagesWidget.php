<?php
/**
* This widget displays all of the messages of the user
* 
* @author   dispy   <dispyfree@googlemail.com>
*/
class MessagesWidget extends CWidget{
    
    public function run(){
        $messages = Yii::app()->msg->getMessages();
        
        foreach($messages as $message){
            $this->widget('MessageWidget', array('message' => $message));
            if($message->params->exclusive)
                break;
        }
    }
}
?>
