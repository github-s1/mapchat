/* 
 * 
 */
define(['backbone'], function(Backbone){
    "use strict";
    
    return Backbone.View.extend({
        
        tagName     : 'li',
        
        events: {
            "click"    :    "selectRegion"
        },
        
        initialize: function(params) {
            this.model = params.model;
            this.selected = params.selected;
            
            this.render();
        },
        
        render: function() {
            this.$el.text(this.model.get('name_ru'));
            if(this.selected){
                this.$el.addClass("active");
            }
            return this;
        },
        
        selectRegion: function() {
            this.model.trigger("selectRegion", this.model.toJSON());
            this.$el.closest("ul").find("li.active").removeClass("active");
            this.$el.addClass("active");
        }
    });
    
});
