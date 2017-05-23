/* 
 * 
 */
define(function(require){
    "use strict";
    
    var Backbone = require('backbone');
    
    return Backbone.View.extend({
        
        defaultCityZoom : 8,
        
        initialize: function(options) {
            //parent - VApp.js
            this.parent = options.parent;
            this.model = this.parent.model;
            this.router = this.parent.router;
            this.topControl = this.parent.topControl;
            
            this.parent.createSidebar();
            this.parent.createMapMain();
            
            this.map = this.parent.mainMap;
            this.sidebar = this.parent.sidebar;
            
            this.listenTo(this.sidebar, 'defineLocation', this.defineLocation);
        },
        defineLocation: function(coord) {
            this.setByIp(coord);
        },
        create: function(options) {
            var city = options.pageData.city;
            if(!city){
                console.log("VCityPage:create/setByIp");
                var self = this;
                this.sidebar.defineLocation(false, function(coord){
                    self.setByIp(coord, true);
                });
//                this.model.defineLocation("getByIp");
            }
            else if(options.appInfo && options.appInfo.cityPage){
                console.log("VCityPage:create/setByPageData");
                this.setByPageData(options.appInfo.cityPage);
            }
            else {
                console.log("VCityPage:create/setByCityName");
                //this.setByCityName(city);
                this.model.location.getBySearchString(city);
            }
        },
        
        set: function(options) {
            if(options.appInfo && options.appInfo.cityPage){
//                console.log("VCityPage:set/setByPageData");
                this.setByPageData(options.appInfo.cityPage);
            }
            else if(options.location){
//                console.log("VCityPage:set/setByLocation");
                this.setByLocation();
            }
            else {
//                console.log("VCityPage:set/getByIp");
                this.setByIp();
            }
        },
        
        /**
         * Автоопределение города на главной странице (когда не вводится название города)
         */
        setByIp: function(coord, isCenter) {
            var self = this;
            this.model.getByIp(function(err){
                if(err){
                    self.model.trigger("changePage", {
                        pageName: "error", options: {message: "Не найден город"}
                    });
                }
                else {
                    self.show();
                    // Установка города по умолчанию
                    if (typeof (self.model) != 'undefined') {
                        var navigateCity = self.model.location.city.get('name_en');
                        self.router.navigate(navigateCity, {trigger: false});
                    }
                    if (typeof coord !== 'undefined') self.parent.mainMap.addMyLocation(coord, isCenter);
                }
            });
        },
        
        /**
         * Если сервер вернул данные из бд
         */
        setByPageData: function(data) {
            this.model.setDataForCityPage(data);
            this.show();
        },
        
        setByCityName: function(city) {
            var self = this;
            this.model.fetchCollectionsByCityName(city, function(err){
                if(err){
                    self.model.trigger("changePage", {
                        pageName: "error", options: {message: "Не найден город"}
                    });
                }
                else {
                    self.show();
                }
            });
        },
        
        setByLocation: function() {
            var self = this;
            this.model.fetchCollectionsByCityAddress(function(){
                self.show();
            });
        },
        
        clear: function() {
//            console.log("VCityPage/clear");
            this.model.clearCityPageData();
            this.map.removeMarksFromMap();
        },
        
        showState: function(city) {
//            console.log("VCityPage/showState");
            city = city || this.model.location.city;
            var self = this,
                name_ru = city.get("name_ru"),
                name_en = city.get("name_en");
            if(name_ru && name_ru !== ""){
                this.topControl.setCityName(name_ru);
                
                if(name_en && name_en !== ""){
                    // **************************** ВРЕМЕННО ****************************
//                    this.router.showCityState(name_en);
                }
                else {
                    this.model.rusEngTranslate(name_ru, function(err, result){
                        if(err){
                            self.topControl.removeCityName();
                        }
                        if(result){
                            result = result.replace(/\s/g, "_");
                            self.router.showCityState(result);
                        }
                    });
                }
            }
        },
        
        
        /*
         * 0: 48.5852671: 35.987334
         * 0: 48.3557251: 34.757974
         * 
         * 0: 48.4738161: 35.8007491
         * 0: 48.5690321: 35.242732
         * 
         * 0: 48.5852671: 35.987334
         * 0: 48.4738161: 35.8007491
         */
        show: function() {
            /*if(this.model.marks.length === 0){
                this.map.removeMarksFromMap();
                
                this.map.setBounds(this.model.location.boundedBy);
//                console.log(this.model.location.getBounds());
//                this.map.setBounds(this.model.location.getBounds());
                /*this.map.setBounds([
//                  [48.5852, 35.9873], [48.4738, 35.8007]
//                  [48.5852671, 35.987334], [48.4738161, 35.800749]
                  [48.3557251, 34.757974], [48.5690321, 35.242732]
                ]);*
            }
            else {
                this.map.addMarksToMap(this.model.marks);
            }*/
            
            this.map.addMarksToMap(this.model.marks);
            this.map.setBounds(this.model.location.boundedBy);
            this.sidebar.renderCityPage();
            this.showState();
        }
    });
});
