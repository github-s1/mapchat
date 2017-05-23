define(['backbone', 'model/Icon'], function(Backbone, Icon){
    "use strict";
    
    return Backbone.Collection.extend({
        model: Icon,
        
        setIcons: function(objIcons){
            var icons = [];
            for(var i in objIcons){
                icons.push(objIcons[i]);
            }
            this.set(icons);
        }
    });
});
