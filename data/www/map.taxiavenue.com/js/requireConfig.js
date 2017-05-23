/*
 * 
 */
require.config({
    baseUrl: baseUrl + '/js',
    
    // Предотвращает кэширование браузером скриптов. (Отключить в рабочей версии)
    //urlArgs: "v=" +  (new Date()).getTime(),
    
    paths: {
        'async'         : 'libs/async/async',
        'jquery'        : 'libs/jquery/jquery-2.1.1',
        'jqueryForm'    : 'libs/jquery/jquery.form',
        'jqueryui'      : 'libs/jquery/jquery-ui.min',
//        'audioPlayer'   : 'libs/audioplayer',
        'underscore'    : 'libs/underscorejs/underscore',
        'backbone'      : 'libs/backbonejs/backbone',
        'text'          : 'libs/requirejs/text'
    },
    
    shim: {
//        jqueryui : {
//            deps    : ['jquery', 'jqueryForm', 'audioPlayer'],
//            exports : '$'
//        },
        jqueryui : {
            deps    : ['jquery', 'jqueryForm'],
            exports : '$'
        },
        underscore : {
            exports : '_'
        },
        backbone : {
            deps    : ['underscore', 'jquery'],
            exports : 'Backbone'
        }
    }
});
