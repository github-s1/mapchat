/* 
 * 
 */
define(function(require){
    "use strict";
    
    var _ = require('underscore'), 
        Backbone = require('backbone');
    
    return Backbone.Model.extend({
        
        defaults: {
            message: ""
        },
        
        initialize: function() {
            Backbone.on("logger:error", this.errHandler, this);
            Backbone.on("logger:notice", this.noticeHandler, this);
            Backbone.on("logger:warning", this.warningHandler, this);
        },
        
        errHandler: function(messages) {
            var date = new Date();
            var header = "Logger/errHandler: " + date.getDate() + "/" + (date.getMonth() + 1) + "/" + date.getFullYear();
            header += " " + date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
            if(!messages){
                messages = [header, "Не передано сообщение об ошибке"];
            }
            else if(Array.isArray(messages)) {
                messages = _.flatten(messages);
                messages.splice(0, 0, header);
            }
            else {
                messages = [header, messages];
            }
            
            console.log(messages);
        },
        
        noticeHandler: function(message) {
            console.log("Notice:");
            console.log(message);
        },
        
        warningHandler: function(message) {
            console.log("Warning:");
            console.log(message);
        }
        
    });
});
