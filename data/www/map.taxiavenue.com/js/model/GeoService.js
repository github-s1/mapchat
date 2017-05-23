/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        _ = require('underscore'),
        Backbone = require('backbone'),
        AppError = require('appError'),
        Config = require('config');
    
    return Backbone.Model.extend({
        
        parseGeoObjects: function(res) {
            var geoObjects = res.geoObjects;
            try {
                var countryData = geoObjects.get(0).properties.get("metaDataProperty").GeocoderMetaData.AddressDetails.Country,
                    data = {
                        region: countryData.AdministrativeArea.AdministrativeAreaName,
                        country: countryData.CountryName,
                        city: geoObjects.get(0).properties.get('name'),
                        boundedBy: geoObjects.get(0).properties.get('boundedBy'),
                        coordinates: geoObjects.get(0).geometry.getCoordinates()
                    };
                    
                return { success: data };
            }
            catch(e){
                return {
                    error: "Не удалось определить местоположение"
                };
            }
        },
        
        getLocationByIp: function(callback){
            Config.YMapNS.geolocation.get({
                // Выставляем опцию для определения положения по ip
                provider: 'yandex',
                // Автоматически геокодируем полученный результат.
                autoReverseGeocode: true
            }).then(function (res) {
                var data = this.parseGeoObjects(res);
                if(data.error){
                    callback(new AppError({
                        type: "YandexParseError",
                        className: "GeoService",
                        methodName: "getLocationByIp",
                        message: "Не удалось определить положение по IP"
                    }));
                }
                else {
                    callback(null, data.success);
                }
                
            }, function(err){
                callback(new AppError({
                    inner: err,
                    type: "YandexError",
                    className: "GeoService",
                    methodName: "getLocationByIp",
                    message: "Не удалось определить положение по IP"
                }));
            }, this);
        },
        
        getMyLocation: function(callback) {
            Config.YMapNS.geolocation.get({
                // Выставляем опцию для определения положения по ip
                provider: 'browser',
                // Автоматически геокодируем полученный результат.
                autoReverseGeocode: false,
                
            }).then(function (res) {
                var data = res.geoObjects.get(0).geometry.getCoordinates();
                if(data.error){
                    callback(new AppError({
                        type: "YandexParseError",
                        className: "GeoService",
                        methodName: "getLocationByIp",
                        message: "Не удалось определить положение по IP"
                    }));
                }
                else {
                    callback(null, data);
                }
                
            }, function(err){
                callback(new AppError({
                    inner: err,
                    type: "YandexError",
                    className: "GeoService",
                    methodName: "getLocationByIp",
                    message: "Не удалось определить положение по IP"
                }));
            }, this);
        },
        
        /// Dnepr
        /// Павлоград
        /// Верховцево
        /// Кривой Рог
        /// Днепропетровск
        /// Москва
        getBySearchString: function(callback) {
            this._getBySearchString(this.searchString, callback);
            this.searchString = "";
        },
        
        getByAddress: function(callback){
            var searchString = this.getSearchAddress();
            this._getBySearchString(searchString, callback);
        },
        
        _getBySearchString: function(searchString, callback) {
//            console.log(searchString);
            // Если результат есть в хэше, то возвращаем его
            var data = false;//this.findAtHashBySerchString(searchString);
            if(data){
                callback(null, data);
                return;
            }
            
            // Если нет - получаем
            Config.YMapNS.geocode(searchString).then(function(result){
                
                result = this.parseGeoObjects(result);
//                console.log(result);
                if(result.error){
                    callback(new AppError({
                        inner: result.error,
                        type: "YandexParseError",
                        className: "Location",
                        methodName: "_getBySearchString",
                        message: "Не удалось разобрать данные геолокации"
                    }));
                }
                else {
                    this.addToHash(searchString, result.success.city, result.success);
                    callback(null, result.success);
                }
                
            }, function(err){
                callback(new AppError({
                    inner: err,
                    data: searchString,
                    type: "YandexError",
                    className: "Location",
                    methodName: "_getBySearchString",
                    message: "Не удалось найти запрошенный адрес"
                }));
            }, this);
        },
        
        rusEngTranslate: function(word, callback){
            var queryStr = "https://translate.yandex.net/api/v1.5/tr.json/translate?key=" + Config.YandexTranslateKey;
                queryStr += "&text=" + word + "&lang=ru-en";
            $.ajax({
                type: "GET",
                url: queryStr
            })
            .done(function(result){
                if(parseInt(result.code, 10) === 200){
//                    result = result.text[0].replace(/\s/g, "_");
                    callback(null, result.text[0]);
                }
                else {
                    callback(result);
                }
            })
            .fail(function(err){
                callback(err);
            });
        }
    });
});
