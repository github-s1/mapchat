/* 
 * 
 */
define(function(require){
    "use strict";
    
    var VCityPage = require('view/pages/VCityPage');
    
    return VCityPage.extend({
        
        initialize: function(options) {
            options = options || {};
            VCityPage.prototype.initialize.call(this, options);
        },
        
        create: function(options) {
            var cityName = options.pageData.city,
                kindName = options.pageData.kind;
            
            if(options.appInfo && options.appInfo.kindPage){
//                console.log("VKindPage/setFromPage");
                this.model.setDataForCityPage(options.appInfo.kindPage);
                this.show({
                    city: cityName,
                    kind: this.model.getKindFromRequestString(kindName)
                });
            }
            else {
//                console.log("VKindPage/setByCityName");
                this.setByCityName(cityName, kindName);
            }
        },
        
        setByCityName: function(cityName, kindName) {
            var self = this;
            this.model.fetchCollectionsByCityName(cityName, function(err){
                if(err){
                    self.model.trigger("changePage", {
                        pageName: "error", options: {message: "Страница не найдена"}
                    });
                    return;
                }
                var kind = self.model.getKindFromRequestString(kindName);
                self.show({
                    city: cityName,
                    kind: kind
                });
            });
        },
        
        show: function(data) {
//            console.log(data);
            var marks = this.model.getMarksForKind(data.kind.get("id"));
            this.sidebar.showKindPage(data);
            this.parent.showKindPageState({
                city: data.city,
                kind: data.kind.get("code"),
                marks: marks
            });
        }
    });
});
