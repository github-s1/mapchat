/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        Config = require('config'),
        Backbone = require('backbone');
    
    return Backbone.Model.extend({
        
        defaults: {
            id: 0,
            id_mark: 0,
            name: ""
        },
        
        getUrl: function() {
            return Config.AudioPath + this.get("name");
        },
        
        isEmpty: function() {
            return "" === this.get("name");
        }
    });
});