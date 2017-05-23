/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
//        _ = require('underscore'), 
        Config = require('config'),
        Backbone = require('backbone');
    
    return Backbone.Model.extend({
        
        defaults: {
            user_id: 0,
            user: null,
            current_user_id: 0,
            isSelf: false,
            content: "",
            date_create: Math.floor(new Date().getTime() / 1000)
//            date_create: new Date().getTime()
        },
        
        toChatViewRow: function() {
            return {
                userAvatar: Config.AvatarPath + this.get('user').get('big_photo'),
                userProfileLink: this.get('user').getLink(),
                userName: this.get('user').getName(),
                userId: this.get('user_id'),
                content: this.getContent(),
                isSelf: this.get('isSelf')
            };
        },
        
        getContent: function() {
            var $wrap = $("<div></div>"),
                $content = $wrap.html(this.get('content')),
                $targetUser = $content.find("span.targetUser");
            if($targetUser.length > 0){
                var targetUserId = parseInt($targetUser.attr("data-target-user"), 10),
                    selfUserId = parseInt(this.get('current_user_id'), 10);
                if(targetUserId === selfUserId){
                    $targetUser.addClass("selected");
                }
            }
            return $wrap.html();
        }
        
    });
});