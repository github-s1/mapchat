/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'), 
        _ = require('underscore'), 
        Dialog = require('dialog'), 
        Storage = require('storage'), 
        Backbone = require('backbone'), 
        welcomeTmpl = require('text!templates/welcomePage.html');
    
    return Backbone.View.extend({
        
        tagName     : 'div',
        className   : 'welcome',
        template    : _.template(welcomeTmpl),
        
        dialog      : null,
        storage     : new Storage(),
        hidePage    : false,
        
        events: {
            "change .no_more input"     :       "notShowWelcomePage",
            "click button.learn_more"   :       "learnMore",
            "click button.not_now"      :       "closeWin"
        },
        
        initialize: function() {
            this.createDialog();
            this.render();
        },
        
        render: function() {
            var view = this.template();
            this.$el.html(view);
            return this;
        },
        
        // Вызов при иннициализации
        createDialog: function() {
            var self = this;
            var options = {
                modal   : true,
                title   : "Добро пожаловать!",
//                position: "left center",
                width   : 670,
                height  : 545,
                close   : function(){ 
                    self.storage.saveLocal("hideWelcomePage", self.hidePage);
                    self.remove(); 
                    $(this).dialog('destroy');
                },
                buttons : null
            };
            this.dialog = new Dialog({widget: this, options: options});
        },
        
        notShowWelcomePage: function(e) {
            this.hidePage = $(e.currentTarget).prop("checked");
        },
        
        learnMore: function() {
            this.dialog.close();
        },
        
        closeWin: function() {
            this.dialog.close();
        },
        
        remove: function() {
            this.undelegateEvents();
            this.stopListening();
            this.$el.remove();
        }
    });
});
