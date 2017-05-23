/* 
 * 
 */
define(['backbone', 'model/Theme'], function(Backbone, Theme){
    "use strict";
    
    return Backbone.Collection.extend({
        model: Theme
    });
});
