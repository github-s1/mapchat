/* 
 * Все, что связанно со скроллингом страницы
 * @params el елемент, на который навешивается событие скроллинга
 * @params triggerName имя триггера, чтоб расспознавать на каком елементе сработало событие скроллинга
 */
define(function(require){
    
    "use strict";
    
    var $ = require('jquery'), 
        _ = require('underscore'), 
        Backbone = require('backbone');
        
    
    
    return Backbone.Model.extend({
        
        gettingData : false, // идет ли выборка данных в текущий момент
        
        currScrollPos: 0, // текущее положение ползунка
        
        initialize: function(options) {
            this.el = options.el;
            this.triggerName = options.triggerName;
        },
        
        /**
         * Связать с элементом событие скроллинга
         */
        bindScroll: function() {
            var self = this;
            this.el.bind('scroll', function(){
                var top = $(this).scrollTop(); // отступ прокрутки сверху для первого элемента в наборе
                var visibleHeight = $(this).height(); // высота видимой области
                var commonHeight = this.scrollHeight; // Общая высота блока
                var sliderDown = (commonHeight - top - visibleHeight) < 15; // 15px условная величина разницы
                // Прокрутка вниз
                if (self.currScrollPos < top && sliderDown && self.gettingData == false) {
                    self.trigger(self.triggerName);
                    self.set('gettingData', true);
                }
                self.currScrollPos = top;
            });
        }
    });
});
