/* 
 * 
 */
define(['jquery'], function($){
    "use strict";
    
    var localStorage = (function(){
//            window.localStorage.clear();
        var _storageName = "MapChat",
            _storage = window.localStorage,
            _defaults = {
                "hideWelcomePage": false
            };
            if(_storage.length === 0){
                _storage.setItem(_storageName, JSON.stringify(_defaults));
            }
//        var _data = JSON.parse(_storage.getItem(_storageName));
        var _data = $.extend(_defaults, JSON.parse(_storage.getItem(_storageName)));
        
        return {
            save: function(fieldName, fieldValue){
//                    console.log("fieldName: " + fieldName + "; fieldValue: " + fieldValue);
                _data[fieldName] = fieldValue;
                _storage.setItem(_storageName, JSON.stringify(_data));
            },
            get: function(fieldName){
                return _data[fieldName];
            },
            remove: function(fieldName){
                if(_data[fieldName]){
                    delete _data[fieldName];
                    _storage.setItem(_storageName, JSON.stringify(_data));
                }
            }
        };
    })();
    
    var storage = function(params) {
        
        var _data = params || {};
        
        function getData(){
            return _data;
        }
        function setData(data){
            _data = data;
        }
        Object.defineProperties(this, {
            data: {get: getData, set: setData, enumerable:true, configurable:false}
        });
    };
    
    storage.prototype.constructor = storage;
    
    storage.prototype.setField = function(name, value){
        this.data[name] = value;
    };
    storage.prototype.getField = function(name){
        return this.data[name];
//        return this.getData[name];
    };
    storage.prototype.removeField = function(name){
        if(this.data[name]){
            delete this.data[name];
        }
    };
    
    storage.prototype.saveLocal = function(fieldName, fieldValue){
        localStorage.save(fieldName, fieldValue);
    };
    
    storage.prototype.getLocal = function(fieldName){
        return localStorage.get(fieldName);
    };
    
    return storage;
});
