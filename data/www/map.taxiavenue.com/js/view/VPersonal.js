/* 
 * 
 */
define([
    'underscore',
    'backbone',
    'templates'
], 
function(_, Backbone, templates){
    "use strict";
    
    return Backbone.View.extend({
        
        tagName     : "div",
        className   : "personal",
        
        events: {
            "click button[name='profile']"  : "profile",
            "click button[name='logout']"   : "logout"
        },
        
        initialize: function(options){
            this.parent = options.parent;
            this.render();
        },
        
        render: function() {
            var tmpl = _.template(templates.personal);
            this.$el.html(tmpl()).appendTo("body");
            return this;
        },
        
        profile: function() {
            this.parent.showProfile();
            this.remove();
        },
        
        logout: function() {
            this.parent.logout();
            this.remove();
        }
    });
    
});
