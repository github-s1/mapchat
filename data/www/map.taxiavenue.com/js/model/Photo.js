/* 
 * 
 */
define(['backbone'], function(Backbone){
    "use strict";
    
    return Backbone.Model.extend({
        
        defaults: {
            id_mark: 0, 
            name: ""
        }
    });
});
