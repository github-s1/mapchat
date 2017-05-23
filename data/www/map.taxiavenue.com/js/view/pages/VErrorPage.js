/* 
 * 
 */
define(function(require){
    "use strict";
    
    var _ = require('underscore'),
        Backbone = require('backbone'), 
        Config = require('config'),
        templates = require('templates');
    
    return Backbone.View.extend({
        
        tagName : 'section',
        id      : "errorPage",
        template    : _.template(templates.errorPage),
        
        initialize: function(options) {
            this.parent = options.parent;
            this.parent.removeSidebar();
            this.parent.createMapMain();
            this.map = this.parent.mainMap;
//            this.map.setBounds();
            this.render();
        },
        
        create: function() {
//            console.log("VError/create");
        },
        
        set: function() {
//            console.log("VError/set");
        },
        
        clear: function() {
//            console.log("VError/clear");
            this.remove();
        },
        
        render: function() {
            var view = this.template({
                url: Config.baseUrl
            });
            this.$el.html(view);
            this.parent.$el.append(this.$el);
            return this;
        }
    });
});
