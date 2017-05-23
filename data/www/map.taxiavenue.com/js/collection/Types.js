/* 
 * 
 */
define(['backbone', 'model/Type'], function(Backbone, Type){
    "use strict";
    
    return Backbone.Collection.extend({
        model: Type
    });
});
