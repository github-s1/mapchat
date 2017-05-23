/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'), 
        Config = require('config'), 
        Backbone = require('backbone'),
        AppError = require('appError');
    
    return Backbone.Model.extend({
        
        defaults: {
            id: "",
            id_mark: "",
            id_user: "",
            text: "",
//            createDatatime: Date.create().format("{yyyy}-{MM}-{dd} {hh}:{mm}"),
            createDatatime: Math.floor(new Date().getTime() / 1000)
        },
        
        save: function(callback){
            $.ajax({
                type: "POST",
                data: {
                    id_mark: this.get("id_mark"),
                    text: this.get("text")
                },
                url: Config.baseUrlJSON + 'comments_json/AddCommentWeb'
            })
            .done(function(result){
                if(!result){
                    Backbone.trigger("logger:error", new AppError({
                        type: "ServerDataError",
                        className: "Comment",
                        methodName: "save",
                        message: "Не удалось добавить комментарий"
                    }));
                    return callback("Не удалось добавить комментарий");
                }
                callback(null, result.response);
            })
            .fail(function(err){
                Backbone.trigger("logger:error", new AppError({
                    inner: err,
                    type: "ServerError",
                    className: "Comment",
                    methodName: "save",
                    message: "Не удалось добавить комментарий"
                }));
                callback("Не удалось добавить комментарий");
            });
        }
    });
});
