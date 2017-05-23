/* 
 * 
 */
define([
    'jquery',
    'backbone', 
    'config',
    'model/Country'
], function($, Backbone, Config, Country){
    "use strict";
    
    return Backbone.Collection.extend({
        
        model: Country,
        
        selectedCounty: null,
        
        fetch: function(requestData) {
            return $.ajax({
                type: "POST",
                context: this,
                url: Config.baseUrlJSON + 'country_json',
                data: requestData,
                success: function(response){
                    var data = eval('(' + response + ')');
                    if(!data || data.response === "false"){
                        this.reset(null, {silent: true});
                        this.selectedCounty = null;
                    }
                    else {
                        this.reset(data.response, {silent: true});
                        this.selectedCounty = this.models[0];
                    }
                    this.trigger("reset");
                }
            });
        },
        
        setSelectedCountry: function(country){
            if(country instanceof Country){
                this.selectedCounty = country;
            }
        },
        
        getSelectedCountry: function(){
            return this.selectedCounty.attributes;
        },
        
        getSelectedCountryId: function(){
            return this.selectedCounty ? this.selectedCounty.get("id") : -5;
        }
    });
});
