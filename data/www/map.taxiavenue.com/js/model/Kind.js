define(function(require){
    "use strict";
    
    var $ = require('jquery'), 
        _ = require('underscore'),
        Config = require('config'), 
        Backbone = require('backbone'),
        AppError = require('appError');
    
    return Backbone.Model.extend({
        
        MAX_DESCRIPTION_LENGTH: 130,
        GENERAL_KIND_ID: -1,
        
        defaults: {
            id_user: 0,
            id_theme: 0, 
            theme: null, 
            id_icon: 0, 
            icon: null, 
            id_type: 0, 
            type: null, 
            name_ru: "",
            code: "",
            description: "",
            site: null,
            lider: null,
            cityPartOfUri: ""
        },
        
        isGeneral: function(){
            return this.GENERAL_KIND_ID === parseInt(this.get('id'), 10);
        },
        
        getLink: function(city){
            var cityPartOfUri = "";
            if(this.get("cityPartOfUri") !== ""){
                cityPartOfUri = Config.baseUrl + this.get("cityPartOfUri");
            }
            else if(city && _.isObject(city)) {
                cityPartOfUri = city.getLink();
            }
            if (cityPartOfUri == '') return false;
            return cityPartOfUri + "/" + this.get('code');
            
//            return city.getLink() + "/" + this.get('code');
        },
        
        getIconUrl: function(){
            return Config.MarkIconPath + this.get('icon').get('name');
        },
        
        toPageKindView: function(totalMarks, city){
//            var city = this.collection.city;
            return {
                title: this.get('name_ru'),
                issetFull: this.issetDescriptionFull(),
                description: this.getDescriptionIntro(),
                themeName: this.get('theme').get('name'),
                imgSrc: Config.MarkIconPath + this.get('icon').get('name'),
                cityName: city.get("name_ru"),
                cityPageHref: city.getLink(),
                site: this.get('site'),
                lider: this.get('lider'),
                totalMarks: totalMarks
            };
        },
        
        toEditKindView: function(city){
            return {
                title: this.get('name_ru'),
                code: this.get('code'),
                description: this.get('description'),
                themeId: this.get('theme').get('id'),
                type: this.get('type'),
                cityName: city.get("name_ru"),
                imgPath: Config.ImgPath,
                imgSrc: Config.MarkIconPath + this.get('icon').get('name'),
                cityPageHref: city.getLink(),
                site: this.get('site'),
                lider: this.get('lider')
            };
        },
        
        getDescriptionIntro: function(){
            var description = this.get('description'),
                len = description ? description.length : 0;
            if(len > this.MAX_DESCRIPTION_LENGTH){
                description = description.substring(0, this.MAX_DESCRIPTION_LENGTH);
                var lastIdx = description.lastIndexOf(" ");
                if(lastIdx !== -1 && lastIdx > 100){
                    description = description.substring(0, lastIdx);
                }
                description = description + " ...";
            }
            return description;
        },
        
        issetDescriptionFull: function(){
            var issetFull = false,
                len = this.get('description') ? this.get('description').length : 0;
            if(len > this.MAX_DESCRIPTION_LENGTH){
                issetFull = true;
            }
            return issetFull;
        },
        
        update: function(data) {
            return $.ajax({
                type: 'POST',
                url: Config.baseUrlJSON + 'kind_json/updateKind',
                data: data
            });
        },
        
        save: function(data, callback) {
            $.ajax({
                type: "POST",
                data: {
                    name_ru: data.name_ru,
                    id_type: data.id_type,
                    id_theme: data.id_theme
                },
                url: Config.baseUrlJSON + 'kind_json/addKind'
            })
            .done(function(result){
                if(!result){
                    return callback(new AppError({
                        type: "ServerDataError",
                        className: "Kind",
                        methodName: "save",
                        message: "Некорректный результат запроса"
                    }));
                }
                var data = eval('(' + result + ')');
                callback(null, data.response);
            })
            .fail(callback);
            
            /*
            var dfd = $.Deferred();
            $.ajax({
                type: 'POST',
                url: Config.baseUrlJSON + 'kind_json/addKind',
                data: this.toJSON(),
                success: function(result){
                    var data = eval('(' + result + ')');
                    dfd.resolve(data.response);
                },
                error: function(e){
                    var err = {
                        className: "Kind",
                        methodName: "save",
                        message: "Ошибка при сохранении нового вида значка",
                        inner: e
                    };
                    dfd.reject(err);
                }
            });
            
            return dfd.promise();
            */
        },
        
        updateIcon: function(data, callback) {
            $.ajax({
                type: "POST",
                //data: {data: data, idKind: this.get('id')},
                data: data,
                context: this,
                url: Config.baseUrlJSON + "kind_json/updateIcon/?id=" + this.get('id'),
                processData: false,  // tell jQuery not to process the data
                contentType: false   // tell jQuery not to set contentType
            }).done(function(result){
                //result = eval('(' + result + ')');
                var avatar = result.response;
                return callback(null, avatar);
                this.set({
                    id_avatar: avatar.id,
                    avatar: avatar,
                    big_photo: avatar.big_photo,
                    small_photo: avatar.small_photo
                });
                callback(null, avatar);
            })
            .fail(function(err){
                new AppError({
                    data: data,
                    inner: err,
                    className: "User",
                    type: "ServerError",
                    methodName: "updateAvatar",
                    message: "Не удалось сохранить изображение на сервер"
                });
            });
        }
    });
});
