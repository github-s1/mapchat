define(['backbone', 'model/User'], function(Backbone, User){
    "use strict";
    
    return Backbone.Collection.extend({
        model: User
    });
});
