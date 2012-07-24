<?php

/**
* This widget displays a single message
* 
* @author   dispy   <dispyfree@googlemail.com>
*/
class MessageWidget extends CWidget{
    
    public $message = NULL;
    
    /**
     * Default Options you cannot overwrite in another place
     * @var array
     */
    public $config = array(
        // Use more padding for the box => equals alert-block of bootstrap
        'morePadding' => true,
        // displays the title. If you switch this off, the title is ignored
        'useHeading'  => true,
    );
    
    /**
     * This is the place to overwrite the options the modal plugin receives - directly
     * @var array
     */
    public $modalOptions = array();
    
    
    //Cache for the ID of the used modal form, if used
    public $modalId = NULL;
    
    
    public function run(){
        
        //Wrap the bare message in the modal box, if requested
        if($this->message->params->modal){
            $this->modalBox('start');
            $this->outputMessage();
            $this->modalBox('end');
        }
        else
            $this->outputMessage();
        
    }

    /**
     * Displays the parts of the modal box
     * @param string    $mode   'start' or 'end' denoting the part to display   
     */
    protected function modalBox($mode){
        $this->modalId = 'modal_'.$this->message->type.'_'.$this->message->id;
        
        if($mode == 'start'){
            echo CHtml::openTag('div', array(
                'class' => 'modal', 
                'id' => $this->modalId));
            echo CHtml::openTag('div', array(
               'class' => 'modal-body' 
            ));
        }
        else{
            echo CHtml::closeTag('div');
            echo CHtml::closeTag('div');
            $options = json_encode($this->modalOptions);
            echo CHtml::script("$(function(){ $('#".$this->modalId."').modal(".$options.");});");
        }
            
    }
    
    /**
     * Outputs the mssage itself (revelation!) 
     */
    protected function outputMessage(){
        
        $class = 'alert '. ($this->config['morePadding'] ?
                'alert-block' : '');
        $class .= ' alert-'.$this->message->type;

        //Build the wrapping div
        echo CHtml::openTag('div', array(
            //Whether to add a more padding to the whole thing
            'class' => $class,
            'id'    => $this->message->type.'_'.$this->message->id
            ));   
        
        //Output the close link, if this behavior is enabled
        if($this->message->params->closAble)
            $this->outputCloseLink();
        
        //If the title shall be displayed
        if($this->config['useHeading'])
            echo CHtml::tag('h4', array('class' => 'alert-heading'), $this->message->params->title);
        
        /**
         * Use a custom view, if specified
         * Custom views are stored under the views-directory 
         */
        $customView = $this->message->params->customView;
        if($customView != false){
            $viewPath = $this->getViewFile($customView);
            
            //Only render if it really exists
            if(file_exists($viewPath))
                $this->render($customView, array('message' => $this->message));
            else
                echo $this->message->text;
        }
        else
            echo $this->message->text;
        
        //Now, show the buttons
        $this->outputButtons($this->message);
        
        echo CHtml::closeTag('div');
        
        //Remove the message if requested
        if($this->message->params->removalUponDisplay)
            $this->message->delete();
    }
    
    
    public function outputCloseLink(){
        
        //Depending on whether or not the modal mode is used we must close 
        //different parents
        $close = !$this->message->params->modal ? 
                 '$(this).closest("div.alert").hide();'
                  :'$("#'.$this->modalId.'").modal("toggle");';
            
            echo CHtml::tag('a', array(
                    'class'         => 'close',
                    'data-dismiss'  => $this->message->type,
                    'href'          => '#',
                    'onclick'       => $close,
              ), 'x');
    }
    
    /**
     * Displays the buttons of the given message
     * @param Message $message 
     */
    protected function outputButtons($message){
        
        //We need a bit of space between the text and the actions
        echo "<p /><br />";
        
        //Container - we want to have them at the right
        echo CHtml::openTag('div', array('class' => 'pull-right'));

            foreach($message->params->actions as $action){

                //Set Defaults (you'll overwrite them normally)
                $defaults = array(
                'type'        => 'default',
                'size'        => '',
                'url'         => '#',
                'caption'      => Yii::t('message', 'OK')
                );

                //Merge with already existent settings
                foreach($defaults as $key=>$value){
                    if(!isset($action->$key))
                        $action->$key = $value;
                }

                $classes = array('btn');
                $classes[] = 'btn-'.$action->type;
                //Add size
                $classes[] = $action->size ;

                echo CHtml::tag('a', array(
                'class' => implode(" ", $classes),
                    //If the message isn't deleted automatically - leave a chance
                    //to do that in the called action
                    'href' => str_replace('{messageId}', $message->id, $action->url)
                ), Yii::t('message', $action->caption));
            }
        
         echo CHtml::closeTag('div');
         echo "<br />";
    }
    
}
?>
