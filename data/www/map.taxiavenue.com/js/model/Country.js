/* 
 * 
 */
define([
    'backbone',
    'config'
], function(Backbone, Config){
    "use strict";
    
    return Backbone.Model.extend({
        
        defaults: {
            id: 0,
            name_ru: "",
            name_en: "",
            lat: 0, 
            lng: 0
        },
        
        getLink: function(){
            return Config.baseUrl + this.get("name_en");
        },
        
        clear: function(){
            for (var key in this.attributes){
                delete this.attributes[key];
            }
            this.set(this.defaults);
        }
    });
});