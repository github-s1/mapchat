define([
    'jquery',
    'backbone', 
    'config',
    'model/Region'
], function($, Backbone, Config, Region){
    "use strict";
    
    return Backbone.Collection.extend({
        
        model: Region,
        
        selectedRegion: null,
        
        fetch: function(requestData) {
            return $.ajax({
                type: "POST",
                context: this,
                url: Config.baseUrlJSON + 'region_json/getRegionByCountryId',
                data: requestData,
                success: function(response){
                    var data = eval('(' + response + ')');
                    if(!data || data.response === "false"){
                        this.reset(null, {silent: true});
                        this.selectedRegion = null;
                    }
                    else {
                        this.reset(data.response, {silent: true});
                        this.selectedRegion = this.models[0];
                    }
                    this.trigger("reset");
                }
            });
        },
        
        setSelectedRegion: function(region){
            if(region instanceof Region){
                this.selectedRegion = region;
            }
        },
        
        getSelectedRegion: function(){
            return this.selectedRegion.attributes;
        },
        
        getSelectedRegionId: function(){
            return this.selectedRegion ? this.selectedRegion.get("id") : -5;
        }
    });
});
