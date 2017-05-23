/**
 * Модель для обработки значков из сторонних сервисов
 * panoramio, inlanger, instagram, yandex-пробки...
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        _ = require('underscore'), 
        Backbone = require('backbone'),
        Config = require('config'),
        templates = require('templates');
    var arrMarks = window.thirdPartyMarks || [];
    var ThirdPartyMarks = Backbone.Model.extend({
        
        width: 50,
        height: 50,
        showMarks: true,
        
        markBalloonTemplate: _.template(templates.markBalloon),
        clusterBalloonTemplate: _.template(templates.clusterBalloon),
        initialize: function(fromSelf) {
            //if (!fromSelf) throw new Error('use method getThirdPartyMarks() for instance this class.');
        },
        /**
         * Получить массив геообьектов значков
         */
        getGeoObjects: function() {
            var geoObjects = [];
            if (this.showMarks == false) return geoObjects;
            arrMarks.forEach(function(item){
                geoObjects.push(this.createPoint(item));
            }, this);
            return geoObjects;
        },
        /**
         * Изменить массив arrMarks. Если изменился город
         */
        setMarks: function(marks) {
            arrMarks = marks;
        },
        
        createPoint: function(mark) {
            var geoObject = null;
            try {
                geoObject = new Config.YMapNS.GeoObject({
                    geometry: {
                        type: "Point",
                        coordinates: [mark.lat,mark.lng]
                    },
                    properties: {
                        hintContent: mark.name,
                        link: mark.mark_url,
                        clusterCaption: mark.name,
                        /*id: this.get('id'),
                        diffDate: this.getDateDifferenceForMapMark(),
                        link: kind.getLink(),
                        hintContent: kind.get('name_ru'),
                        description: this.getDescription(),*/
                        //clusterCaption: kind.get('name_ru'),
                        balloonContentBody: this.getClusterBalloon(mark)
                    }
                }, {
                    visible: true,
                    //balloonLayout: this.createBalloonForMark(),
                    iconLayout: 'default#image',
                    hideIconOnBalloonOpen: false,
                    iconImageHref: mark.img_url,
                    iconImageSize: [50,50],
                    iconImageOffset: this.getOffsetOther()
                });
            }
            catch(e){
                throw new AppError({
                    type: "ScriptError",
                    className: "Mark",
                    methodName: "createPointMark",
                    message: "Ошибка при создании значка. ID значка #: " + this.get("id"),
                    inner: e
                });
            }
            return geoObject;
        },
        
        getOffsetOther: function(){
            var width = parseInt(this.width),
                height = parseInt(this.height), 
                offsetX = Math.ceil(width/2), 
                offsetY = Math.ceil(height/2);
            return [-offsetX, -offsetY];
        },
        
        getClusterBalloon: function(mark) {
            //var clusterBalloonTemplate = _.template(templates.clusterBalloon);
            return this.clusterBalloonTemplate({
                diffDate: '34',//this.getDateDifferenceForMapMark(),
                title: mark.name,
                kindLink: mark.img_url,
                markLink: mark.mark_url,
                iconSrc: mark.img_url,
                description: mark.name//this.getDescription()
            });
        },
        
    }, {
        singleton: null,
        getInstance: function() {
            ThirdPartyMarks.singleton =
                ThirdPartyMarks.singleton || new ThirdPartyMarks(true);
            return ThirdPartyMarks.singleton;
        }
    });
    return ThirdPartyMarks;
});
