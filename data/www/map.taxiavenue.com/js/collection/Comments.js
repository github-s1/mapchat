/* 
 * 
 */
define(['backbone', 'model/Comment'], function(Backbone, Comment){
    "use strict";
    
    return Backbone.Collection.extend({
        
        model: Comment
    });
});
