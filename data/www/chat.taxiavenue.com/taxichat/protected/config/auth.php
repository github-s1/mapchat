<?php

return array(
    'guest' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Guest',
        'bizRule' => null,
        'data' => null
    ),
    '2' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Сustomer',
        'children' => array(
            'guest', // унаследуемся от гостя
        ),
        'bizRule' => null,
        'data' => null
    ),
    '3' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Administrator',
       /* 'children' => array(
            'moderator',         // позволим админу всё, что позволено модератору
        ), */
        'bizRule' => null,
        'data' => null
    ),
    '4' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Dispatcher',
        /* 'children' => array(
            'user',          
        ), */
        'bizRule' => null,
        'data' => null
    ),
	'6' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Agent',
        'bizRule' => null,
        'data' => null
    ),
    '1' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Driver',
        /* 'children' => array(
            'user',          
        ), */
        'bizRule' => null,
        'data' => null
    ),
    
     '5' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Operator',
        'bizRule' => null,
        'data' => null
    ),
      '7' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Founder',
        'bizRule' => null,
        'data' => null
    ),
     '8' => array(
        'type' => CAuthItem::TYPE_ROLE,
        'description' => 'Dispatcher(agent)',
        'bizRule' => null,
        'data' => null
    ),


);
