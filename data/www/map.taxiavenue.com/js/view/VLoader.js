/* 
 * 
 */
define(function(require){
    "use strict";
    
    var _ = require('underscore'), 
        Config = require('config'), 
        Backbone = require('backbone'), 
        loaderTmpl = require('text!templates/loader.html');
    
    return Backbone.View.extend({
        
        tagName     : 'figure',
        id          : 'loader',
        className   : 'clear',
        template    : _.template(loaderTmpl),
        
        data        : {
            imgSrc: Config.ImgPath + "loader.gif"
        },
        
        initialize: function(params) {
            _.extend(this.data, params.data);
            this.css = params.css || {};
            
            this.render();
        },
        
        setText: function(text) {
            this.$("figcaption").first().text(text);
        },
        
        render: function() {
            var view = this.template(this.data);
            
            if(! _.isEmpty(this.css) ){
                this.$el.css(this.css);
            }
            
            this.$el.html(view).appendTo("body");
            return this;
        }
    });
});
