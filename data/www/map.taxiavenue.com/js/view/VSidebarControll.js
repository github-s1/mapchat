/* 
 * 
 */
define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        _ = require('underscore'),  
        Backbone = require('backbone'),
        templates = require('templates');
    
    var VSidebarControll = Backbone.View.extend({
        
        tagName         : 'div',
        id              : 'sidebar_controll',
        template        : _.template(templates.sidebarControl),
        
        $listWhere      : null,
        $listThemes     : null,
        $listInterest   : null,
        
        events: {
            "click button.add_icon"             :       "addMark",
            "click button.toggle"               :       "toggleMarks",
            "change select[name='where']"       :       "changeWhere",
            "change select[name='themes']"      :       "changeTheme",
            "change select[name='interest']"    :       "changeInterest"
        },
        
        initialize: function(options) {
            this.parent = options.parent;
            this.themes = this.parent.themes;            
            this.render();
        },
        
        render: function() {
            this.$el.html(this.template());
            this.$listWhere = this.$("select[name='where']");
            this.$listThemes = this.$("select[name='themes']");
            this.$listInterest = this.$("select[name='interest']");
            return this;
        },
        
        renderThemes: function() {
            this.$listThemes.empty();
            var view = _.template(templates.sidebarThemeItem, {
                themes:  this.themes.models
            });
            this.$listThemes.append(view);
        },
        
        toggleMarks: function(e) {
            var $button = $(e.currentTarget);
            if($button.hasClass("show_icons")){
                $button.removeClass("show_icons").addClass("hide_icons")
                        .css("background-image", "url('img/hide_icons.png')")
                        .text("Показать всё");
                this.parent.marks.hideMapMarks();
            }
            else {
                $button.removeClass("hide_icons").addClass("show_icons")
                        .css("background-image", "url('img/show_icons.png')")
                        .text("Скрыть всё");
                this.parent.marks.showMapMarks();
            }
        },
        
        addMark: function() {
            this.parent.addMark();
        },
        
        changeWhere: function(e) {
            var selected = $(e.currentTarget).val();
            this.parent.changeWhere(selected);
        },
        
        changeTheme: function(e) {
            var themeId = $(e.currentTarget).val();
            themeId = (themeId === VSidebarControll.DEFAULT_THEME_ID) ? false : themeId;
            this.parent.changeTheme(themeId);
        },
        
        changeInterest: function(e) {
            var selected = $(e.currentTarget).val();
            this.parent.changeInterest(selected);
        },
        
        remove: function() {
            this.undelegateEvents();
            this.stopListening();
            this.$el.remove();
        }
    });
    
    VSidebarControll.DEFAULT_THEME_ID = "-2";
    
    return VSidebarControll;
});
