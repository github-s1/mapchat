define(function(require){
    "use strict";
    var $ = require('jquery'), 
        async = require('async'),
        _ = require('underscore'),
        Config = require('config'), 
        Backbone = require('backbone'),
        AppError = require('appError'),
        Icon = require('model/Icon'), 
        Type = require('model/Type'), 
        Kind = require('model/Kind'),
        Comment = require('model/Comment'), 
        Mark = require('model/Mark'), 
        Audio = require('model/Audio'),
        User = require('model/User'),
        City = require('model/City'),  
        Region = require('model/Region'), 
        Country = require('model/Country'),   
        Chat = require('model/Chat'), 
        Themes = require('collection/Themes'), 
        Kinds = require('collection/Kinds'), 
        Marks = require('collection/Marks'), 
        Icons = require('collection/Icons'), 
        Types = require('collection/Types'), 
        Location = require('model/Location'),
        VNotice = require('view/VNotice'),
        ThirdPartyMarks = require('model/ThirdPartyMarks').getInstance(),
        GeoService = require('model/GeoService');
        
    return Backbone.Model.extend({
        YMapNS      : Config.YMapNS,
        selfUser    : new User(),
        location    : new Location(),
        geoService  : new GeoService(),
        marks       : new Marks,
        icons       : new Icons,
        themes      : new Themes,
        kinds       : new Kinds,
        types       : new Types,
        initialize: function(options) {
            if(_.has(options, "selfUser")){
                if(!_.isEmpty(options.selfUser)){
                    this.selfUser.setDataForSelfUser(options.selfUser);
                }
//                console.log(this.selfUser.toJSON());
            }
            else {
                this.selfUser.getDataForSelfUser();
            }
            this.chat = new Chat({selfUser: this.selfUser, location: this.location});
            this.listenTo(this.location, 'change', this.changeLocation);
            this.listenTo(this.location, 'changeCityBySearchString', this.changeLocationBySearchString);
            this.showPopupForgotVhecked();
        },
        /**
         * Показывать ли уведомление при успешном восстановлении пароля
         */
        showPopupForgotVhecked: function() {
            if ((typeof window.forgotChecked == 'undefined') || window.forgotChecked == '') return;
            var data = eval('('+window.forgotChecked+')');
            var mess = 'На Ваш электронный адрес ' + data.login + ' выслан новый<br /> сгенерированый системой пароль.<br /><br />';
                mess += 'Вы всегда сможете изменить его в своем <a href="' + baseUrl + '/user/' + data.id + '">личном кабинете</a>';
            new VNotice({
                html: "<p>" + mess + "</p>",
                addClassName: "error",
                css: {"z-index": 10000},
                autoHidden: false,
            });
        },
        /**
         * Изменение положения карты относительно города (в поисковой строке)
         */
        changeLocationBySearchString: function(result) {
            // Обработка в VApp.js
            this.location.set({
                country: result.location.country ? result.location.country : null,
                region: result.location.region ? result.location.region : null,
                city: result.location.city ? result.location.city : null,
            });
            this.trigger("changePage", {
                pageName: "city",
                pageData: {city:''},
                appInfo : {
                    cityPage : result,
                    selfUser : {}
                }
            });
        },
        /**
         * Изменение положения карты относительно города (в выпадающем списке)
         */
        changeLocation: function(){
            var options = {
                    type: "POST",
                    data: {
                        id_city : (typeof(this.location.get) != 'undefined') ? this.location.get('city').id : window.changeLocationCityId
                    },
                    context: this,
                    url: Config.baseUrlJSON + "mark_json/GetMarksByCityId"
                };
            $.ajax(options).done(function(response){
                
                if(response.error){
                }
                else {
                    ThirdPartyMarks.setMarks(response.otherMarks);
                    var cityPage = response;
                    cityPage.location = this.location.attributes;
					if(this == window) {
						window.changePage({
							pageName: "city",
							pageData: {city:''},
							appInfo : {
								cityPage : cityPage,
								selfUser : {}
							}
						});
					} else {
						this.trigger("changePage", {
							pageName: "city",
							pageData: {city:''},
							appInfo : {
								cityPage : cityPage,
								selfUser : {}
							}
						});
					}                
                }
            })
            .fail(function(err){
                callback(new AppError({
                    inner: err,
                    type: "ServerError",
                    className: "MApp",
                    methodName: "fetchCollectionsByCoordinates",
                    message: "Не удалось выполнить запрос к серверу"
                }));
            });
        },
        
        defineLocation: function(startAction){
            var self = this;
            this.location[startAction](function(err, result){
                if(err){
                    self.defineLocationErrorHandler(err);
                }
                else {
                    self.location.changeFromGeolocation(result);
                }
            });
        },
        
        // Обработать ошибку
        defineLocationErrorHandler: function(err){
            var type = err.type;
            switch(type){
                case "YandexError":
                case "YandexParseError":
                    break;
            }
            
            this.trigger("changePage", {
                pageName: "error", options: {message: err}
            });
        },
        
        /**
         * yandex.api
         * получить данные про регион по ip
         * результат возвращается в РУССКОМ виде 
         */
        getByIp: function(callback){
            var self = this;
            this.geoService.getLocationByIp(function(err, result){
                if(err){
                    self.trigger("changePage", {
                        pageName: "error", options: {message: err}
                    });
                }
                else {
                    self.location.setFromGeolocation(result);
//                    self.fetchCollectionsByCoordinates({
//                        lat: result.coordinates[0],
//                        lng: result.coordinates[1]
//                    }, callback);
                    self.fetchCollectionsByCoordinates(result, callback);
                }
            });
        },
        
        // Получаем коллекции значков, иконок, тем и т.д. по координатам
        fetchCollectionsByCoordinates: function(geoLocation, callback) {
            var options = {
                    type: "POST",
//                    data: coordinates,
//                    data: {lat: 488888.637120917949744, lng: 38865.17576922338177},
//                    data: {lat: 48.637120917949744, lng: 35.17576922338177}, // Novomoskovsk
//                    data: {lat: 48.3169290863063, lng: 35.51768036332626},  // Synel'nykove
//                    data: {lat: 48.53664126783818, lng: 35.86585011566465},  // Павлоград
//                    data: {lat: 55.7522200, lng: 37.6155600},  // Moscow
                    data: {
                        country_ru : geoLocation.country,
                        region_ru : geoLocation.region,
                        city_ru : geoLocation.city,
                        coordinates: geoLocation.coordinates,
                        bounds: geoLocation.boundedBy
                        /*region: geoLocation.region,//"Днепропетровская область", 
                        country: geoLocation.country,//"Украина", 
                        city: geoLocation.city,//"Павлоград", 
                        coordinates: geoLocation.coordinates,//[48.53664126783818, 35.86585011566465],
                        bounds: geoLocation.boundedBy*/
                    },
                    context: this,
                    url: Config.baseUrlJSON + "WebMark/GetPageCityByCoordinates"
                };
//            console.log(options.data);
            $.ajax(options).done(function(response){
                if(response.error){
                    callback(new AppError({
                        data: geoLocation,
                        type: "ServerEmptyData",
                        className: "MApp",
                        methodName: "fetchCollectionsByCoordinates",
                        message: response.error
                    }));
                }
                else {
                    this.setDataForCityPage(response);
                    callback();
                }
            })
            .fail(function(err){
                callback(new AppError({
                    inner: err,
                    type: "ServerError",
                    className: "MApp",
                    methodName: "fetchCollectionsByCoordinates",
                    message: "Не удалось выполнить запрос к серверу"
                }));
            });
        },
        
        // Получаем коллекции значков, иконок, тем и т.д. по адресу
        fetchCollectionsByCityAddress: function(callback) {
//            address = {
//                country :"Украина",
//                region  :"Днепропетровская область",
////                region  :"888",
//                city    :"Днепропетровск"
////                city    :"777"
//            };
            var address = {
                    country: this.location.country.get("name_ru"),
                    region: this.location.region.get("name_ru"),
                    city: this.location.city.get("name_ru")
                },
                options = {
                    type: "POST",
                    data: address,
                    context: this,
                    url: Config.baseUrlJSON + "city_json/GetByAddress"
                };
            $.ajax(options).done(function(response){
                this.fetchCollectionsByCityAddressHandler(address, response, callback);
            })
            .fail(function(err){
                callback(new AppError({
                    inner: err,
                    type: "ServerError",
                    className: "MApp",
                    methodName: "fetchCollectionsByCityAddress",
                    message: "Не удалось выполнить запрос к серверу"
                }));
            });
        },
        
        fetchCollectionsByCityAddressHandler: function(address, response, callback) {
//            console.log(address);
            try {
                response = eval('(' + response + ')');
                if(!response.result || response.result === 'false'){
                    callback(new AppError({
                        data: address,
                        type: "ServerEmptyData",
                        className: "MApp",
                        methodName: "fetchCollectionsByCityAddress",
                        message: "Нет данных по региону"
                    }));
                }
                else {
                    this.setDataForCityPage(response.result);
                    callback();
                }
            }
            catch(e){
                callback(new AppError({
                    inner: e,
                    data: address,
                    type: "ServerDataError",
                    className: "MApp",
                    methodName: "fetchCollectionsByCityAddress",
                    message: "Некорректный результат запроса"
                }));
            }
        },
        
        fetchCollectionsByCityName: function(city, callback) {
            var options = {
                type: "POST",
                data: {cityName: city},
                context: this,
                url: Config.baseUrlJSON + "city_json/GetCityByName"
            };
            $.ajax(options).done(function(response){
                try {
                    response = eval('(' + response + ')');
                    if(!response.result || response.result === 'false'){
                        callback(new AppError({
                            data: city,
                            type: "ServerEmptyData",
                            className: "MApp",
                            methodName: "fetchCollectionsByCityName",
                            message: "Нет данных по городу"
                        }));
                    }
                    else {
                        this.setDataForCityPage(response.result);
                        callback();
                    }
                }
                catch(e){
                    callback(new AppError({
                        data: city,
                        inner: e,
                        type: "ServerDataError",
                        className: "MApp",
                        methodName: "fetchCollectionsByCityName",
                        message: "Некорректный результат запроса"
                    }));
                }
            })
            .fail(function(err){
                callback(new AppError({
                    inner: err,
                    type: "ServerError",
                    className: "MApp",
                    methodName: "fetchCollectionsByCityName",
                    message: "Не удалось выполнить запрос к серверу"
                }));
            });
        },
        
        // Устанавливаем все коллекции (значков, иконок и т.д.) из одного запроса
        setDataForCityPage: function(data){
            if(data){
                if (typeof data.otherMarks != 'undefined') ThirdPartyMarks.setMarks(data.otherMarks);
                this.location.updateFromSelfServer(data.location);
                this.types.set(data.types);
                this.themes.set(data.themes);
                this.icons.setIcons(data.icons);
                var cityPartOfUri = this.location.getCityPartOfUri();
//                console.log(cityPartOfUri);
                this.kinds.createKinds(
                    data.kinds, data.marks, this.icons, this.types, this.themes, cityPartOfUri
                );
                this.marks.createMarks(data.marks, this.kinds);
            }
            else {
                this.clearCityPageData();
            }
        },
        /**
         * Установить значки на карте после сортировки "в городе / на карте / все"
         */
        setAddsMark: function(data) {
            this.setDataForCityPage(data);
            this.trigger('changeMarkFilter', this.marks);
        },
        clearCityPageData: function(){
            this.icons.reset();
            this.types.reset();
            this.themes.reset();
            this.kinds.reset();
            this.marks.reset();
        },
        
        getKindFromRequestString: function(str) {
            for(var i = 0, length = this.kinds.length; i < length; i++){
                if(this.kinds.models[i].get("code") === str){
                    return this.kinds.models[i];
                }
            }
            return null;
        },
        
        getMarksForKind: function(kindId) {
            var marks = [];
            for(var i = 0, length = this.marks.length; i < length; i++){
                if(this.marks.models[i].belongsToKind(kindId)){
                    marks.push(this.marks.models[i]);
                }
            }
            return marks;
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
        },
        
        engRusTranslate: function(word, callback){
            var queryStr = "https://translate.yandex.net/api/v1.5/tr.json/translate?key=" + Config.YandexTranslateKey;
                queryStr += "&text=" + word + "&lang=en-ru";
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
        },
        
        getCity: function() {
            return this.location.city;
        },
        
        getDataForNewMark: function(callback) {
            async.parallel([
                this.fetchAllThemes,
                this.fetchAllKinds,
                this.fetchAllTypes,
                this.fetchAllIcons
            ], function(err, res){
                if(err){
                    callback(err);
                }
                else {
                    var kinds = new Kinds(), 
                        icons = new Icons(res[3]), 
                        types = new Types(res[2]), 
                        themes = new Themes(res[0]);
                    kinds.createKindsWithoutMarks(res[1], icons, types, themes);
//                    console.log(res[3]);
                    callback(null, {
                        kinds: kinds,
                        themes: themes,
                        icons: icons,
                        types: types
                    });
                }
            });
        },
        
        getDataForNewKind: function(callback) {
            async.parallel([
                this.fetchAllThemes,
                this.fetchAllTypes
            ], function(err, res){
                if(err){
                    callback(err);
                }
                else {
                    var themes = new Themes(res[0]), 
                        types = new Types(res[1]);
                    callback(null, {
                        types: types,
                        themes: themes
                    });
                }
            });
        },
        
        fetchAllIcons: function(callback) {
            $.ajax({
                type: "POST",
                url: Config.baseUrlJSON + "icon_json/GetAllIcons"
            })
            .done(function(result){
                if(!result){
                    return callback(new AppError({
                        type: "ServerDataError",
                        className: "MApp",
                        methodName: "GetAllIcons",
                        message: "Некорректный результат запроса"
                    }));
                }
                var data = eval('(' + result + ')');
                callback(null, data.response);
            })
            .fail(callback);
        },
        
        fetchAllThemes: function(callback) {
            $.ajax({
                type: "POST",
                url: Config.baseUrlJSON + "theme_json/getAllThemes"
            })
            .done(function(result){
                if(!result){
                    return callback(new AppError({
                        type: "ServerDataError",
                        className: "MApp",
                        methodName: "fetchAllThemes",
                        message: "Некорректный результат запроса"
                    }));
                }
                var data = eval('(' + result + ')');
                callback(null, data.response);
            })
            .fail(callback);
        },
        
        fetchAllKinds: function(callback) {
            $.ajax({
                type: "POST",
                url: Config.baseUrlJSON + "kind_json/getAllKinds"
            }).done(function(result){
                if(!result){
                    return callback(new AppError({
                        type: "ServerDataError",
                        className: "MApp",
                        methodName: "fetchAllKinds",
                        message: "Некорректный результат запроса"
                    }));
                }
                var data = eval('(' + result + ')');
                callback(null, data.response);
            })
            .fail(callback);
        },
        
        fetchAllTypes: function(callback) {
            $.ajax({
                type: "POST",
                url: Config.baseUrlJSON + "type_json/GetAllTypes"
            })
            .done(function(result){
//                console.log(result);
                if(!result){
                    return callback(new AppError({
                        type: "ServerDataError",
                        className: "MApp",
                        methodName: "fetchAllTypes",
                        message: "Некорректный результат запроса"
                    }));
                }
                var data = eval('(' + result + ')');
                callback(null, data.response);
            })
            .fail(callback);
        },
        
        getMarkData: function(data, callback) {
            $.ajax({
                type: "POST",
                context: this,
                data: {id_mark: data.id, location: data.city},
                url: Config.baseUrlJSON + "mark_json/GetMarkById"
            })
            .done(function(result){
//                result = eval('(' + result + ')');
                if(!result  || result === 'false' || !result.response || result.response === 'false'){
                    // Нет данных 
                    callback(new AppError({
                        data: data,
                        type: "ServerDataError",
                        className: "MApp",
                        methodName: "getMarkData",
                        message: "Некорректный результат запроса"
                    }));
                }
                else {
                    var mark = this.parseMarkData(result.response);
                    callback(null, {
                        mark: mark, city: this.getLocationFromMarkData(result.response)
                    });
                }
            })
            .fail(function(err){
                callback(err);
            });
        },
        
        getLocationFromMarkData: function(data) {
//            console.log(data);
            var location = data.location;
            if(!location || !_.isObject(location)){
                return false;
            }
            
            var cities = location.cities;
            if(cities && _.isArray(cities)){
                return new City(cities[0]);
            }
            
            var regions = location.regions;
            if(regions && _.isArray(regions)){
//                return regions[0];
                return new Region(regions[0]);
            }
            
            var countries = location.countries;
            if(countries && _.isArray(countries)){
                return new Country(countries[0]);
            }
        },
        
        parseMarkData: function(data) {
            var photos = this.setPhotosForMark(data.photos),
                comments = [];
            if(data.comments && _.isArray(data.comments)){
                data.comments.forEach(function(comment){
                    comments.push(new Comment(comment));
                });
            }
            
            var type = new Type;
            if(data.points.length > 1){
                type.set({
                    name_en: "LineString", 
                    name_ru: "Ломаная"
                });
            }
            
            var icon = new Icon(data.icon);
            var kind = new Kind($.extend(data.kind, {
                icon: icon, 
                type: type
            }));
            var user = new User(data.user);
            var audio = null;
            if(data.audio && _.isObject(data.audio)){
//                audio = data.audio.name;
                audio = new Audio(data.audio);
            }
            // 
            var mark = Mark.create($.extend(data.mark, {
                kind: kind,
                user: user,
                photos: photos,
                points: data.points,
                comments: comments,
                audio: audio
            }));            
            return mark;
        },
        
        setPhotosForMark: function(photos){
            var _photos = [];
            if(photos && _.isArray(photos)){
                photos.forEach(function(photo){
                    _photos.push(photo.name);
                });
            }
            return _photos;
        },
        
        getUserById: function(userId, callback) {
            $.ajax({
                type: "POST",
                data: {id_user: userId},
                url: Config.baseUrlJSON + "users_json/getUserById"
            })
            .done(function(result){
                if(!result  || result === 'false' || !result.response || result.response === 'false'){
                    // Нет данных 
                    callback(new AppError({
                        type: "ServerDataError",
                        data: {userId: userId},
                        className: "MApp",
                        methodName: "getUserById",
                        message: "Некорректный результат запроса"
                    }));
                }
                else {
                    callback(null, new User(result.response));
                }
            })
            .fail(function(err){
                callback(err);
            });
        },
        
        /**
         * Кнопка жалобы на странице пользователя
         */
        sendUserBan: function(selfUser, viewUser, callback) {
            var options = {
                type: "POST",
                data: {
                    id_user_ban : viewUser,
                    id_user_sender:selfUser
                },
                context: this,
                url: Config.baseUrlJSON + "users_json/UserBan"
            };
            $.ajax(options).done(function(response){
                if(response.error){
                   callback('error');
                }
                else {
                    callback('ok');
                }
            })
            .fail(function(err){
                callback('error');
            });
        }
    });
});