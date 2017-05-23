define(function(require){
    "use strict";
    
    var $ = require('jquery'), 
        _ = require('underscore'),
        Backbone = require('backbone'), 
        Storage = require('storage'),
        VApp = require('view/pages/VApp');
    
    return Backbone.Router.extend({
        
        app     : null,
        appInfo : null,
        
        routes: {
            ""                  : "index",
            "/"                 : "index",
            "index.php"         : "index",
            "user/:id"          : "profile",
            ":city"             : "city",
            ":city/"            : "city",
            ":city/:kind"       : "kind",
            ":city/:kind/"      : "kind",
            ":city/:kind/:id"   : "mark",
            "*other"            : "error"
        },
        
        initialize: function() {
            this.showWelcomePage();
            this._getAppInfo();
            var appOptions = {router: this};
            if(this.appInfo && _.isObject(this.appInfo)){
                appOptions.selfUser = this.appInfo.selfUser;
            }
            this.app =  new VApp(appOptions);
        },
        
        _getAppInfo: function(){
            this.appInfo = window.appInfo;
//            console.log(this.appInfo);
//            $(".appInfo").remove();
        },
        
        showWelcomePage: function() {
            var storage = new Storage();
            if(! storage.getLocal("hideWelcomePage")){
                var Welcome = require('view/VWelcome');
                new Welcome();
            }
        },
        
        index: function(){
            this.app.createPage({pageName: "city", pageData: {city: null}});
        },
        
        city: function(city){
            var options = {pageName: "city", pageData: {city: city}};
//            console.log(options);
            if(this.appInfo){
                options.appInfo = this.appInfo;
                this.appInfo = false;
            }
            this.app.createPage(options);
        },
        
        kind: function(city, kind){ 
            var options = {pageName: "kind", pageData: {city: city, kind: kind}};
            if(this.appInfo){
                options.appInfo = this.appInfo;
                this.appInfo = false;
            }
            this.app.createPage(options);
        },
        
        mark: function(city, kind, id){
            var options = {pageName: "mark", pageData: {city: city, kind: kind, id: id}};
            if(this.appInfo){
                options.appInfo = this.appInfo;
                this.appInfo = false;
            }
            this.app.createPage(options);
        },
        
        profile: function(id){
            var options = {pageName: "profile", pageData: {userId: id}};
            if(this.appInfo){
                options.appInfo = this.appInfo;
                this.appInfo = false;
            }
            this.app.createPage(options);
        },
        
        error: function(){
            this.app.createPage({pageName: "error"});
        },
        
        showCityState: function(city){
            this.navigate(city);
        },
        
        showKindState: function(options){
            var uri = options.city + "/" + options.kind;
            this.navigate(uri);
        },
        
        showMarkState: function(options){
            var uri = options.city + "/" + options.kind + "/" + options.id;
            this.navigate(uri);
        },
        
        showProfileState: function(options){
            this.navigate("user/" + options.id);
        }
    });
});
/*
define([
    'backbone', 
    'view/VApp'
], 
function(Backbone, VApp){
    "use strict";
    
    return Backbone.Router.extend({
        
        app: null,
        
        routes: {
            "index.php"         : "index",
            ":city"             : "city",
            ":city/:kind"       : "kind",
            ":city/:kind/:id"   : "mark",
            "*other"            : "index"
        },
        
        initialize: function() {
            this.app = new VApp({router: this});
        },
        
        // Default route
        index: function(){
//            console.log("Router/index: " + Date.now());
            this.app.setIndexPageState();
        },
        
        city: function(city){
//            console.log("Router/city. city: " + city);
            this.app.setCityPageState({city: city});
        },
        
        kind: function(city, kind){
//            console.log("Router/kind. city: " + city + "; kind: " + kind);  
            this.app.setKindPageState({ city: city, kind: kind });
        },
        
        mark: function(city, kind, id){
//            console.log("Router/mark. city: " + city + "; kind: " + kind + "; id: " + id);
            this.app.setMarkPageState({ city: city, kind: kind, id: id });
        },
        
        setCityState: function(city){
            this.navigate(city);
        },
        
        setKindState: function(options){
            var uri = options.city + "/" + options.kind;
            this.navigate(uri);
        },
        
        setMarkState: function(options){
            var uri = options.city + "/" + options.kind + "/" + options.id;
            this.navigate(uri);
        }
    });
});
*/
