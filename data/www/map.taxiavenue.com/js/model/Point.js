/* 
 * 
 */
define(['backbone'], function(Backbone){
    "use strict";
    
    return Backbone.Model.extend({
        
        defaults: {
            id_mark: 0, 
            id_city: 0,
            lat: 0, 
            lng: 0
        }
    });
});