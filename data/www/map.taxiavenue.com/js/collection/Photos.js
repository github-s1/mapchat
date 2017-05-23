define(['backbone', 'model/Photo'], function(Backbone, Photo){
    "use strict";
    
    return Backbone.Collection.extend({
        model: Photo
    });
});
