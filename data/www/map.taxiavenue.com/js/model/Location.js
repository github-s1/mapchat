/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        _ = require('underscore'),
        Backbone = require('backbone'),
        AppError = require('appError'),
        Config = require('config'),  
        City = require('model/City'), 
        Region = require('model/Region'), 
        Country = require('model/Country'),
        GeoService = require('model/GeoService');
    return Backbone.Model.extend({
        
        country: new Country,
        region: new Region,
        city: new City,
        
        geoService  : new GeoService,
        
        coordinates: [],
        boundedBy: [],
        setFromGeolocation: function(location){
            this.clear();
//            console.log(location);
            this.country.set("name_ru", location.country);
            this.region.set("name_ru", location.region);
            this.city.set("name_ru", location.city);
            this.coordinates = location.coordinates;
            this.boundedBy = location.boundedBy;
            this.trigger('changeRegion', this.region);
        },
        
        updateFromSelfServer: function(location){
            var coordinates = [location.city.lat, location.city.lng];
            var bounds = [
                [
                    location.city.southwest_lat,
                    location.city.southwest_lng
                ],
                [
                    location.city.northeast_lat,
                    location.city.northeast_lng
                ],
            ];
            this.setCoordinates(coordinates);
            this.setBounds(bounds);
            this.setCountry(location.country);
            this.setRegion(location.region);
            this.setCity(location.city);
            this.trigger('changeRegion', this.region);
        },
        
        setCoordinates: function(coordinates){
            this.coordinates = [];
            if(_.isArray(coordinates) && !_.isEmpty(coordinates)){
                this.coordinates = coordinates;
            }
            else if(_.isObject(coordinates) && !_.isEmpty(coordinates)) {
                this.coordinates = [coordinates.lat, coordinates.lng];
            }
        },
        
        setBounds: function(bounds){
            if(!_.isArray(bounds) || _.isEmpty(bounds)){
                this.boundedBy = [];
                return;
            }
            if(!_.isArray(bounds[0]) || !_.isArray(bounds[1]) || _.isEmpty(bounds[0]) || _.isEmpty(bounds[1])){
                this.boundedBy = [];
                return;
            }
            
            var northeast = bounds[0], 
                southwest = bounds[1];
            
            northeast[0] = parseFloat(bounds[0][0]);
            northeast[1] = parseFloat(bounds[0][1]);
            southwest[0] = parseFloat(bounds[1][0]);
            southwest[1] = parseFloat(bounds[1][1]);
            this.boundedBy = [ northeast, southwest ];
        },
        
        setCountry: function(country){
            if(_.isObject(country) && !_.isEmpty(country)){
                this.country.set(country);
            }
            else {
                this.country.clear();
            }
        },
        
        setRegion: function(region){
            if(_.isObject(region) && !_.isEmpty(region)){
                this.region.set(region);
            }
            else {
                this.region.clear();
            }
        },
        
        setCity: function(city){
            if(_.isObject(city) && !_.isEmpty(city)){
                this.city.set(city);
            }
            else {
                this.city.clear();
            }
        },
        
        getCityPartOfUri: function(){
            var uri = this.city.get("name_en");
            if(uri !== ""){
                return uri;
            }
            
            uri = this.region.get("name_en");
            if(uri !== ""){
                return uri + "-region";
            }
            
            uri = this.country.get("name_en");
            if(uri !== ""){
                return uri + "-country";
            }
            return "error";
        },
        
        getBounds: function(){
            // 
            return this.boundedBy;
        },
        
        clear: function(){
            this.country.clear();
            this.region.clear();
            this.city.clear();
            this.boundedBy = [];
            this.coordinates = [];
        },
        getBySearchString: function(searchString) {
            $.ajax({
                type: "POST",
                context: this,
                url: Config.baseUrlJSON + 'location/GetLocationByName',
                data: {location: searchString},
                success: function(response) {
                    var result = response.response;
                    if (result !== false) {
                        this.trigger('changeCityBySearchString', result);
                    }
                } 
            });
        },
    });
});
