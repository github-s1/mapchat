/* 
 * 
 */
define (function(require){
    
    "use strict";
    
    var Backbone = require('backbone');
    
    var SocialAuth = Backbone.Model.extend({
            
            urls : {
                VK_URL : 'https://oauth.vk.com/authorize?client_id=4696521&scope=friends,video&redirect_uri=http://map.taxiavenue.com/&display=popup&v=5.27&response_type=token',
            
                ODKL_URL : '',
            
                FACEBOOK_URL : ''
            },
            
            makeUrlBySystem : function(system) {
                return this.urls[system + '_URL'];
            }
    });
    
    return SocialAuth;
    
});