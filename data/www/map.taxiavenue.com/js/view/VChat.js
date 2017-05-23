define(function(require){
    "use strict";
    var $ = require('jquery'),
        _ = require('underscore'), 
        Config = require('config'), 
        KEmoji = require('kemoji'),
        Backbone = require('backbone'),
        VAuth = require('view/VAuth'), 
        templates = require('templates'),
        scroll = require('scroll');
    
    return Backbone.View.extend({
        
        tagName     : 'div',
        id          : 'chat',
        className   : 'hide',
        template    : _.template(templates.chatWin),
        
        events: {
            "click #chatEmoji div.chatMessage"          :   "removePlaceholder",
            "blur #chatEmoji div.chatMessage"           :   "showPlaceholder",
            "keypress #chatEmoji div.chatMessage"       :   "keyPress",
            "click button.chat_send_mess"               :   "sendMessage",
            "click div.msg"                             :   "hideUserInfoSubMenu",
            "click button.nick"                         :   "showUserInfo",
            "click button.private_chat_win"             :   "showPrivateMessageWin",
            "click ul.user_info li.chatMessage a"       :   "sendChatMessage",
            "click ul.user_info li.privateMessage a"    :   "sendPrivateMessage",
            "click div.privateList ul.private-list li"  :   "showUserMessages",
            "click button.common_chat_win"              :   "showCommonChat"
        },
        /**********************************************************************/
        initialize: function(options){ 
            this.parent = options.parent;
            this.model = this.parent.chat;
            var cId = this.parent.location.region.get('id');
            if (!cId) {
                if (typeof cityId != 'undefined') {
                    cId = cityId;
                    this.model.changeRoom('city_' + cId);
                }
            } else {
                this.model.changeRoom('city_' + cId);
            }
            this.listenTo( this.model.messages, 'reset', this.renderMessages );
            this.listenTo( this.model.messages, 'add', this.addNewMessage );
            this.listenTo(this.model, 'notify_user', this.notifyUser);
            this.listenTo(this.model, 'room_changed', this.showActiveChat);
            
            this.listenTo(this.model, 'add_user_in_room', this.addToOnlineList);
            
            this.listenTo(this.model, 'leave_user_from_room', this.removeFromOnlineList);
            this.render();
            this.showCountNewPrivate();
            
            this.sc = new scroll({el: this.$el.find('div.msg_container'), triggerName: 'endScrollChat'});
            this.sc.bindScroll();
            this.listenTo(this.sc, 'endScrollChat', $.proxy(this, 'addMessagesAfterScrolling'));
        },
        getUserId: function($elem){
            if($elem.hasClass("msg")){
                return parseInt($elem.attr("data-user-id"), 10);
            }
            else {
                return parseInt($elem.closest("div.msg").attr("data-user-id"), 10);
            }
        },
        sendChatMessage: function(e){
            e.preventDefault();
            var $li = $(e.currentTarget);
            var userId = this.getUserId($li);
            var user = this.model.users.get(userId);
            var userName = user.getName();
            var text = "<span class='targetUser' data-target-user='" + userId + "'>";
            text += userName + "</span>:&nbsp;";
            this.$("div.chatMessage").click().html(text);
        },
        
        /**
         * Показать личную переписку с конкретным пользователем
         */
        showUserMessages: function(e){
            var $li = $(e.currentTarget);
            var userId = $li.attr("data-user-id");
            $(e.currentTarget).find("span.countMes").fadeOut(1000, function(){
                this.remove();
            });
            //console.log("showUserMessages: " + userId);
            this.model.changeRoom('private_' + userId);
        },
        
        /**
         * Показать личную переписку с конкретным пользователем по клику в общем чате
         */
        sendPrivateMessage: function(e){
            e.preventDefault();
            if(!this.parent.selfUser.loggedIn()){
                this.actionInMemory = "showPrivateMessageWin";
                new VAuth({parent: this, user: this.parent.selfUser});
                return;
            }
            var $li = $(e.currentTarget);
            var userId = this.getUserId($li);
            if($li.hasClass('move-to-private')) this.moveToPrivate(userId);
            this.model.changeRoom('private_' + userId);
        },
        moveToPrivate: function(userId) {
            var $oldEl = this.$el.find('ul.other-list div[data-user-id=' + userId + ']');
            var $newEl = this.$el.find('ul.private-list');
            if ($newEl.find('li[data-user-id=' + userId +']').length > 0) return;
            var user = {
                from_id: userId,
                login : $oldEl.find('button.nick').html()
            };
            $newEl.append(this.addToPrivateList(user));
        },
        /**
         * Вернутся в общий чат
         */
        showCommonChat: function() {
            this.model.changeRoom('city_' + this.parent.location.region.id);
        },
        showUserInfo: function(e) {
            var $button = $(e.currentTarget);
            if($button.hasClass("self")){
                return;
            }
            
            e.stopPropagation();
            var $user_info = $button.next("ul.user_info");
            this.showUserInfoSubMenu($user_info);
        },
        
        showUserInfoSubMenu: function($elem) {
            this.$("ul.user_info").removeClass("active");
            $elem.addClass("active");
            if($elem.hasClass("hide")){
                $elem.removeClass("hide");
            }
            else {
                $elem.addClass("hide");
            }
            
            this.$("ul.user_info").each(function(){
                var $user_info = $(this);
                if(!$user_info.hasClass("active")){
                    $user_info.addClass("hide");
                }
            });
        },
        
        hideUserInfoSubMenu: function() {
            this.$("ul.user_info").addClass("hide");
        },
        
        /**
         * Подсасывающийся список при скроллинге (получить остальные сообщения)
         */
        addMessagesAfterScrolling: function() {
            var self = this;
            this.model.messages.addMessFromCache(function(messages){
                if (messages == false) return;
                
                self.renderMessages();
                self.sc.set('gettingData', false);
            });
        },
        /**
         * Подсветка активной комнаты
         */
        showActiveChat: function(roomId, listOnline) {
            this.$el.find('ul.private-list li').removeClass('pressed');
            if (roomId.indexOf('_') > -1) {
                var rooms = roomId.split('_');
                var userId = (this.parent.selfUser.get('id') == rooms[0]) ? rooms[1] : rooms[0];
                this.$el.find('ul.private-list li[data-user-id=' + userId + ']').addClass('pressed');
            } else {
            }
            this.renderOnlineList(listOnline, true);
        },
        showCountNewPrivate: function() {
            var count = this.model.get('countNewPrivate');
            if (count < 1 || typeof count == 'undefined') return;
            var el = this.$el.find('.private_chat_win');
            var countNew = el.find('.new_message');
            if (countNew.length > 0) {
                countNew.html(count);
            } else {
                el.append('<span class="new_message">' + count + '</span>');
            }
        },
        render: function() {
            var view = this.template({baseUrl: Config.baseUrl});
            this.$el.html(view).appendTo("body");
            this.$messageField = this.$("input[name='message']");
            this.$messageContainer = this.$("div.msg_container").first();
            this.renderMessages();
            
            var self= this;
            setTimeout(function(){
                self.initEmoji();
            }, 10);
            
            return this;
        },
        /**
         * Уведомить пользователя, если пришло новое ЛС и он не находится в комнате
         */
        notifyUser: function(fromId) {
            if (this.openedPrivateList()) {
                incUserNotify(this.$el, fromId);
            } else {
                incCommonNotify(this.$el);
            }
        },
        openedPrivateList: function() {
            return !this.$el.find('.privateList').hasClass('hide');
        },
        initEmoji: function() {
            this.kemoji = KEmoji.init('chatEmoji', {
                emojiDir: Config.ImgPath + 'emoji',
                smileContainerWidth: 220,
                smileContainerHeight: 200,
                showSmilesButtonElement: this.$("div.KEmoji_Smiles_Show_Button").get(0)
            });
        },
        afterAuthAction: function() {
            if(this.actionInMemory){
                this[this.actionInMemory]();
            }
        },        
        removePlaceholder: function() {
            if(!this.$placeholder){
                this.$placeholder = this.$("span.placeholder").first();
            }
            if(this.$placeholder && !this.placeholderIsHidden){
                this.$placeholder.addClass("hide");
                this.placeholderIsHidden = true;
            }
        },
        
        showPlaceholder: function() {
            if(this.$placeholder && this.placeholderIsHidden){
                var text  = this.kemoji.getValue();
                text = text.replace(/&nbsp;/g, "");
                if (text === '<br>') text = '';
                text  = $.trim(text);
                if(text === ''){
                    this.kemoji.setValue("");
                    this.placeholderIsHidden = false;
                    this.$placeholder.removeClass("hide");
                }
            }
        },
        
        renderMessages: function() {
            this.$messageContainer.empty();
            var messages = this.model.messages.toChatView();
            messages.forEach(function(message){
                var view = _.template(templates.chatMessage, message);
                this.$messageContainer.append(view);
            }, this);
            return this;
        },
        
        addNewMessage: function(message) {
            var view = _.template(templates.chatMessage, message.toChatViewRow());
            this.$messageContainer.prepend(view);
        },
        keyPress: function(e) {
            if(e.keyCode == 13) {
                this.sendMessage();
            }
        },
        
        sendMessage: function() {
            if(!this.parent.selfUser.loggedIn()){
                this.actionInMemory = "sendMessage";
                new VAuth({parent: this, user: this.parent.selfUser});
                return;
            }
            var message = this.kemoji.getValue(KEmoji.HTML_VALUE);
            
            //var mess2 = message.replace(/<img[^<]*>/g, '12345'); // Допустим - один смайл занимает 5 символов
            var mess2 = message.replace(/<img[^<]*>/g, '1'); // TODO Допустим - один смайл занимает 1 символ
            mess2 = mess2.trim();
            if (mess2.length > 100) {
                //console.log('*' + message + '*');
                //console.log(message.length);
                alert('Длина сообщения не должна превышать 100 символов');
                return;
            }
            this.kemoji.setValue("");
            if(message){
                this.model.sendMessage(message);
            };
        },
        
        toggleChatWin: function() {
            if(this.$el.hasClass("hide")){
                this.showCommonChat();
                this.$el.removeClass("hide");
                //! костыль
                $('#tc_countCity').html('0');
                addSpanNewMessage(null, null, $('#tc_countPrivate').html());
            }
            else {
                if(!this.$("div.privateList").hasClass("hide")){
                    this.$("div.privateList").addClass("hide");
                }
                this.$el.addClass("hide");
            }
        },
        showPrivateMessageWin: function(){
            $('#tc_countPrivate').html('0');//TODO: WTF?
            if(this.$("div.privateList").hasClass("hide")){
                if(!this.parent.selfUser.loggedIn()){
                    this.actionInMemory = "showPrivateMessageWin";
                    new VAuth({parent: this, user: this.parent.selfUser});
                    return;
                }
                var self = this;
                this.model.getPrivateList(function(list){
                    self.renderPravateList(list.privateList);
                    self.renderOnlineList(list.onlineUsers);
                    self.$("div.privateList").removeClass("hide");
                });
            }
            else {
                this.$("div.privateList").addClass("hide");
                this.$el.find('.privateList ul.other-list').html('');
            }
        },
        /**
         * Отобразить выпадающий список личных сообщений
         */
        renderPravateList: function(list) {
            var $el = this.$('.privateList ul.private-list');
            var str = '';
            var self = this;
            list.forEach(function(user){
                str += self.addToPrivateList(user);
                /*str += '<li class="clear" data-user-id="' + user.from_id + '">';
                str += '<span class="name left">' + user.login + '</span>';
                if (user.count > 0) str += '<span class="countMes right">' + user.count + '</span>';
                str += '</li>';*/
            });
            $el.html(str);
        },
        addToPrivateList: function(user) {
            var str = '<li class="clear" data-user-id="' + user.from_id + '">';
            str += '<span class="name left">' + user.login + '</span>';
            if (user.count > 0) str += '<span class="countMes right">' + user.count + '</span>';
            str += '</li>';
            return str;
        },
        renderOnlineList: function(list, clear) {
            var container = this.$el.find('.privateList ul.other-list');
            if (clear) container.html(''); // очистить список
            list.forEach(function(item){
                var userInfo = this.model.getUserInfo(item);
                if (container.find('div[data-user-id=' + userInfo.id + ']').length > 0) return;
                var view = _.template(templates.otherList, userInfo);
                container.append(view);
            }, this);
            return this;
        },
        /**
         * Добавить пользователя в онлайн список
         */
        addToOnlineList: function(user) {
            this.renderOnlineList([user]);
        },
        /**
         * Удалить пользователя из списка онлайн
         */
        removeFromOnlineList: function(user) {
            this.$el.find('.privateList ul.other-list div[data-user-id=' + user.id + ']').remove();
        },
        remove: function() {
            this.undelegateEvents();
            this.stopListening();
            this.$el.remove();
        }
    });
});
/**
 * Если свернут блок со списком переписчиков ЛС
 */
function incCommonNotify(mainEl) {
    var $chat = $('#chat');
    //if ($chat.hasClass('hide')) {
        var count = $('#tc_countPrivate').html();
        $('#tc_countPrivate').html(++count);
    //}
    
    var eButton = mainEl.find('button.private_chat_win');
    var elNewMess = eButton.find('span.new_message');
    addSpanNewMessage(eButton, elNewMess, 1);
    /*if (elNewMess.length < 1) {
        eButton.append('<span class="new_message">1</span>');
        return;
    }*/
    var count = elNewMess.html();
    elNewMess.html(++count);
}
function addSpanNewMessage(btn, el, count) {
    if (btn === null) btn = $('.chat_window').find('button.private_chat_win');
    if (el === null) el = btn.find('span.new_message');
    if (el.length < 1 && count != 0) {
        btn.append('<span class="new_message">' + count + '</span>');
        return true;
    } else if (count > 1) {
        el.html(count);
    }
}
/**
 * Если развернут блок со списком переписчиков ЛС
 */
function incUserNotify(mainEl, fromId) {
    var el = mainEl.find('ul li[data-user-id=' + fromId + ']');
    if (typeof el !== 'undefined') {
        var elNewMess = el.find('span.countMes');
        if (elNewMess.length == 0) {
            el.append('<span class="countMes right">1</span>');
            return;
        }
        var count = elNewMess.html();
        elNewMess.html(++count);
    }
}