/* 
 * 
 */
define([ 
    'jquery',
    'view/VMap'
], 
function($, VMap){
    "use strict";
    
    return VMap.extend({
        
        initialize: function(options) {
            this.mark = options.mark;
            
            VMap.prototype.initialize.call(this, {
                parent: options.parent, 
                el: options.el,
                controls: ['zoomControl']
            });
            
            this.addMarkToMap();
        },
        
        addMarkToMap: function() {
            var mapMark = this.mark.get("mapMark");
            this.YandexMap.geoObjects.add(mapMark);
            this.setBounds(mapMark.geometry.getBounds());
        },
        
        remove: function() {
            this.destroy();
            this.undelegateEvents();
            this.stopListening();
            this.$el.remove();
        }
        
    });
    
});
