/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        _ = require('underscore'),
        Backbone = require('backbone'),
        templates = require('templates');
    
    return Backbone.View.extend({
        
        tagName     : 'div',
        className   : 'audio',
        template    : _.template(templates.audio),
        
        events: {
            "click span.control"    :       "togglePlayer"
        },
        
        initialize: function(options) {
            this.model = options.model;
            this.render();
        },
        
        togglePlayer: function() {
            if(this.audio.paused) {
                this.play();
            }
            else if(this.audio.played) {
                this.pause();
            }
        },
        
        play: function() {
            this.audio.play();
            this.$player.addClass("play");
        },
        
        pause: function() {
            this.audio.pause();
            this.$player.removeClass("play");
        },
        
        showDuration: function() {
            var duration = this.audio.duration, 
                time = this.audio.currentTime, 
                fraction = time / duration, 
                percent = fraction * 100;
            
            if (percent){
                this.$progressbar.css('width', percent + '%');
            }
        },
        
        setOnStart: function() {
            this.$player.removeClass("play");
            this.$progressbar.css('width', 0);
        },
        
        render: function() {
            this.$el.html(this.template());
            this.$audio = this.$("audio").first();
            this.audio = this.$audio.get(0);
            this.$audio.attr('src', this.model.getUrl());
            this.$audio.on('ended', $.proxy(this, "setOnStart"));
            this.$audio.on('timeupdate', $.proxy(this, "showDuration"));
            
            this.$player = this.$("div.player").first();
            this.$progressbar = this.$("span.progressbar").first();
            
            return this;
        }
    });
});
