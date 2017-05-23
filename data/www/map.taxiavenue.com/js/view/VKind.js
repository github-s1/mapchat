define(function(require){
    "use strict";
    
    var $ = require('jquery'),
        _ = require('underscore'),
        Backbone = require('backbone'), 
        Config = require('config'),  
        VLoader = require('view/VLoader'), 
        VAuth = require('view/VAuth'), 
        AppError = require('appError'),
        
        ThirdPartyMarks = require('model/ThirdPartyMarks').getInstance(),
        templates = require('templates');
    
    return Backbone.View.extend({
        
        tagName         : 'div',
        id              : 'sidebar_kind',
        className       : 'view',
        templateView    : _.template(templates.sidebarKind),
        templateEdit    : _.template(templates.sidebarKindEdit),
        
        $shareBlock     : null,
        $editButton     : null,
        $editForm       : null,
        
        events: {
            "click a.back_map"                  :       "toCityPage",
            "click p.learn_more"                :       "toggleDescriptionBlock",
            "click button.edit"                 :       "toEditKindPage",
            "click button.save"                 :       "updateKind",
            "click input[name='save']"          :       "updateKind",
            "click button.cancel"               :       "cancelEditView",
            "change input[name='titleKind']"    :       "translateTitle",
            "click div.share"                   :       "share",
            
            "click div.icon_info.edit"          :       "changeKindIcon",
            "change input[name='iconKind']"     :       "updateIconKind",
        },
        
        share: function(e) {
            var $elem = $(e.target), 
                className = $elem.attr("class");
            console.log(className);
        },
        
        initialize: function(options) {
            this.parent = options.parent;
            this.marks = options.marks;
            this.model = options.kind;
            this.city = options.city;
            this.selfUser = this.parent.selfUser;
            this.baseModel = this.parent.parent.model;
            this.render();
            this.getAllThemes();
        },
        
        render: function() {
            var view = this.templateView(this.model.toPageKindView(this.marks.length, this.city));
            this.$el.html(view).removeClass("edit");
            
            this.$shareBlock = this.$("div.share").first();
            this.$editButton = this.$("button.edit").first();
            this.$descriptionBriefBlock = this.$("div.border div.description p.brief").first();
            this.$editForm = null;
            return this;
        },
        
        getAllThemes: function(callback) {
            var self = this;
            this.baseModel.fetchAllThemes(function(err, res){
                if(err){
                    Backbone.trigger("logger:error", new AppError({
                        inner: err,
                        type: "ServerDataError",
                        className: "VKind",
                        methodName: "getAllThemes",
                        message: "Ошибка при возврате из функции MApp.fetchAllThemes"
                    }));
                }
                else {
                    self.themes = res;
                    if(callback){
                        callback();
                    }
                }
            });
        },
        
        toggleDescriptionBlock: function(e) {
            var $el = $(e.target),  
                $prevEl = $el.prev("p.brief");
            var text = $el.text();
            if(text === "Скрыть"){
                $el.text("Узнать больше >>");
                $prevEl.removeClass("full").addClass("intro").text(this.model.getDescriptionIntro());
            }
            else {
                $el.text("Скрыть");
                $prevEl.removeClass("intro").addClass("full").text(this.model.get("description"));
            }
        },
        
        toCityPage: function(e) {
            e.preventDefault();
            ThirdPartyMarks.showMarks = true,
            this.parent.showCityPage(this.city);
        },
        
        toEditKindPage: function(e) {
            e.preventDefault();
            if(!this.selfUser.loggedIn()){
                new VAuth({parent: this, user: this.selfUser});
                return;
            }
            if(!this.selfUser.hasPrivilegesToEdit(this.model.get("id_user"))){
                alert("Вы не можете редактировать этот вид значков");
                return;
            }
            
            var self = this, 
                data = this.model.toEditKindView(this.city);
            if(!this.themes){
                this.getAllThemes(function(){
                    data.themes = self.themes;
                    self.renderEdit(data);
                });
            }
            else {
                data.themes = this.themes;
                this.renderEdit(data);
            }
        },
        
        afterAuthAction: function() {
            this.$("button.edit").click();
        },
        
        renderEdit: function(data) {
            var view = this.templateEdit(data);
            this.$el.addClass("edit").html(view);
            this.$editForm = this.$("form").first();            
            return this;
        },
        
        cancelEditView: function(e) {
            e.preventDefault();
            this.render();
        },
        
        translateTitle: function(e) {
            var self = this,
                $title = $(e.target), 
                title = $.trim($title.val());
            $title.val(title);
            if(title === ''){
                return;
            }
            this.baseModel.rusEngTranslate(title, function(err, res){
                if(res){
                    res = res.replace(/\s/g, "-");
                    self.$("input[name='aliasKind']").val(res.toLowerCase());
                }
            });
        },
        
        updateKind: function(e) {
            e.preventDefault();
            var formData = this.getFormData();            
            if(!formData){
                this.render();
                return;
            }
            
            var self = this,
                loader = new VLoader({
                    data: {text: "Сохраняем ... "} 
                });
            $.when(this.model.update(formData)).then(function(res){
                loader.remove();
                res = eval('(' + res + ')').response;
                if(res.error){
                    alert(res.error.error_msg);
                }
                else {
                    self.model.set(res);
//                    console.log(self.model.toJSON());
                    self.parent.updateUri({
                        city: self.city.get("name_en"),
                        kind: self.model.get("code")
                    });
                    self.render();
                }
            });
        },
        
        getFormData: function() {
            var alias = $.trim(this.$("input[name='aliasKind']").val()),
                name = $.trim(this.$("input[name='titleKind']").val()),
                themeId = $.trim(this.$("select[name='theme'] option:selected").val()),
                description = $.trim(this.$("textarea[name='descriptionKind']").val()),
                site = $.trim(this.$("input[name='site']").val()),
                lider = $.trim(this.$("input[name='lider']").val());
            
            if(!alias || !name || !themeId || !description){
                return false;
            }
            
            return [
                {name: "id_kind", value: this.model.get("id")},
                {name: "id_type", value: this.model.get("type").get("id")},
                {name: "id_theme", value: themeId},
                {name: "name_ru", value: name},
                {name: "code", value: alias},
                {name: "description", value: description},
                {name: "site", value: site},
                {name: "lider", value: lider}
            ];
        },
        
        createField: function(data) {
            var $label = $("<label></label>");
            $("<span></span>").text(data.name).appendTo($label);
            $("<input />").attr("type", "text").css({
                "margin-left": "14px",
                "margin-right": "4px"
            }).val(data.value).appendTo($label);
            $("<img />").attr({
                class: "delField",
                title: "Удалить поле",
                src: Config.ImgPath + 'close.png'
            }).appendTo($label);
            $label.insertBefore(this.$("p.clear").first());
        },
        
        remove: function() {
            this.undelegateEvents();
            this.stopListening();
            this.$el.remove();
        },
        
        changeKindIcon: function() {
            $('input[name="iconKind"]').click();
        },
        
        updateIconKind: function(e) {
            var files = e.target.files;
            if(files.length === 0) return;
            var self = this,
                data = new FormData(this.$("form").get(0)),
                loader = new VLoader({data: {text: "Сохраняем изображение ... "}});
            this.model.updateIcon(data, function(err, res){
                if (res == 'success') window.location.reload();
                loader.remove();
            });
        }
    });
});
