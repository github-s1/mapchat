define(['backbone', 'model/Point'], function(Backbone, Point){
    "use strict";
    
    return Backbone.Collection.extend({
        model: Point
    });
});
