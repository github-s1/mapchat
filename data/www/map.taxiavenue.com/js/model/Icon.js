define(['backbone', 'config'], function(Backbone, Config){
    "use strict";
    
    var Icon = Backbone.Model.extend({
        
        defaults: {
            name: "", 
            width: 0,
            height: 0
        },
        
        getSrc: function(){
            return Config.MarkIconPath + this.get("name");
        },
        
        getSizeForMap: function(){
            var width = parseInt(this.get("width"));
            var height = parseInt(this.get("height"));
            return [width, height];
        },
        
        getOffsetForMap: function(){
            var width = parseInt(this.get("width")),
                height = parseInt(this.get("height")), 
                offsetX = Math.ceil(width/2), 
                offsetY = Math.ceil(height/2);
            return [-offsetX, -offsetY];
        }
    });
    
    Icon.MAX_SIZE = 30;
    
    return Icon;
});
