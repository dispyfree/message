
##Requirements
This extension requires Bootstrap and a Database. 


##Rationale

### Message
Messages have certain types('' (is a warning), 'successs', 'error', 'info'), contain a message-text, some actions and a bunch of options. A message is shown using the widget (and can optionally be destroyed upon its display). 

### Trigger
Messages are only shown if at least a trigger applies to the current user (Messages have one or several triggers). A trigger stores the session and/or the userID of the current User (or none of both). A trigger matches if:

1.  the user is logged in and the user-id matches the id saved in the trigger
2.  the session of the user matches the session saved in the trigger
3.  neither the session nor the user-id of the trigger are set - this trigger will match all users in the first place. (Later more on this)

In addition to that, each trigger stores a timestamp and once that timestamp has been reached, the trigger will apply. On the other hand, you can specify a timeout so that the trigger will only apply for a certain span of time after he has been activated. 

If all those conditions of a trigger are met, the trigger applies and it's associated message will be shown. 
 


##Installation

1. Copy the files into your extension-folder

2. Import the component:

~~~
[php]

'msg' => array(
        'class' => 'application.extensions.message.ExtMessageComponent'
    ),
~~~

3. Import the tables using the install.sql

4. In the end, you must of course include the widget which displays the messages somwhere - preferrably in your layout.

~~~
[php]
echo CHtml::openTag('div', array('class' => 'messages'));
            $this->widget('application.extensions.message.widgets.MessagesWidget');
        echo CHtml::closeTag('div');;
~~~


##Usage

~~~
[php]
//Most Simplest usage
Yii::app()->msg->postMessage('info', 'This is a sample info');

//To set Options, use the second parameter
Yii::app()->msg->postMessage('', 'This is a warning!', array('modal' => true));

//To modify the trigger, use the last one
Yii::app()->msg->postMessage('', 'This is only visible for 24h', array(), 
array('timeout' => 24 * 3600));

//For detailed parameters, see the classes

//In the end, an advanced example:
 $text = "This is quite an advanced example";
 $actions = array(array(
      'caption' => 'This is a primary button',
      'url'    => '#',
      'type'   => 'primary'
    ),
    array(
      'caption' => 'This is a warning button',
      'url'     => '#',
      'type'    => 'warning',
      'size'    => 'small'
       ),
                );
Yii::app()->msg->postMessage('', $text, 
  array('title' => 'Warning!', 
        'actions' => $actions, 
        'modal' => true,
        'closAble' => true,
        'customView' => 'demo'
 ));

~~~

The advanced example above leads to this:
![Advanced Example](https://dl.dropbox.com/u/70134012/Bildschirmfoto%20vom%202012-07-19%2020%3A47%3A29.png "Advanced Example")


The advanced example without modal:
![non-modal advanced](https://dl.dropbox.com/u/70134012/Bildschirmfoto%20vom%202012-07-19%2020%3A49%3A04.png "Advanced Example without modal")


## Advanced Usage with a all-matching trigger

Trigger matching every user are primarly useful if you confine the messages to users in another way - for example ACL. You can achieve this easily if you make the Triggers themselves Aco-Objects so they are bound to the regular restrictions you impose on them. 
Please do not try the following if you're not familiar with ACL- it's advanced. 


Example:

~~~
[php]

//This shows only users of group X the message, if they haven't already seen it
//For this purpose, we'll use Business-Rules
//This script assumes that you have given only that group permission to view this
//trigger. That can be done easily using something like that:
$group = CGroup::model()->find('alias = :alias', array(':alias' => 'Allowed'));
$group->grant('view', $myTrigger);

//Now, the rule itself
class BusinessRules{
...
public static function isAllowed($aro, $aco, $action){
    $restr = 'hasAlreadySeen';
    //Checks whether he hasn't seen it yet
    if(!$aro->is($restr)){
        $aro->join($restr);
        return true;
    }

    return false;
}


}

~~~



##Resources

...external resources for this extension...

 * [This project resides on github](https://github.com/dispyfree/message "Github Repository")
