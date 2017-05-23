/* 
 * 
 */
define([
    'jqueryui',
    'underscore'
], 
function($, _){
    "use strict";
    
    var Dialog = function(params){
        
        var _widget = params.widget;
        var _options = {
            modal   : true,
            title   :  '',
            width   : Math.floor($(window).innerWidth() * 0.9),
            height  : Math.floor($(window).innerHeight() * 0.95),
            close   : function(){ 
                _widget.remove(); 
                $(this).dialog('destroy');
            },
            buttons : { "Отмена": function(){ $(this).dialog("close"); } }
        };
        
        var _$el = $('<div></div>').addClass('dialog').append(_widget.$el).dialog(_.extend(_options, (params.options || {})));
        
        var showMessage = function(title, message, reload){
            $('<div></div>').text(message).dialog({
                modal   : true,
                title   : title, 
                width   : 300,
                height  : 180,
                buttons : { 
                    "OK": function(){ 
                        $(this).dialog('destroy');
                        if(reload){
                            window.location.reload();
                        }
                        else {
                            setTimeout(function(){
                                _$el.dialog("close");
                            }, 300);
                        }
                    } 
                }
            });
        };
        
        this.setStyle = function(style){
            _$el.css(style);
        };
        
        this.close = function(result){
            if(result && typeof result === 'object'){
                var title = result.title || "";
                var message  = result.message || "";
                showMessage(title, message, result.reload);
            }
            else {
                _$el.dialog("close");
            }
        };
    };
    Dialog.prototype.constructor = Dialog;
    
    return Dialog;
    
});
