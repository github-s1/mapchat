/* 
 * 
 */
define(['backbone'], function(Backbone){
    "use strict";
    
    return Backbone.Model.extend({
        
        defaults: {
            name_en: "Point", 
            name_ru: "Точка"
        }
    });
});
