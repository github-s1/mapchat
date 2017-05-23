/* 
 * Модуль для работы с комнатами
 */


function roomService(roomId) {
    var _cityId = 0;
    this.getCityId = function() {
        return this._cityId;
    };
    
    this.initRoom = function(roomId, socket) {
        if (!roomId)
            return null;
        var obj;
        if (roomId.indexOf('private') > -1) obj = require('../models/RoomPrivate');
        if (roomId.indexOf('city') > -1) {
            obj = require('../models/RoomCity');
            var template = 'city_';
            this._cityId =  roomId.substr(template.length);
        }
        return new obj(roomId, socket);
    };
    
    var _room = this.initRoom(roomId);
    //var _roomId = roomId;
    
    this.getRoomId = function() {
        if (!_room) return null;
        return _room.getId();
    };
    
    /**
     * Загрузить данные комнаты
     */
    this.loadRoom = function(callback) {
        if (!_room) return;
        var self = this;
        _room.loadRoom(function(res) {
            res.roomId = self.getRoomId();
            callback(res);
        });
    };
    
    /**
     * Добавить сообщение в комнату
     */
    this.addMessageInRoom = function(message, callback, socket) {
        if (!_room) return;
        _room.addMessage(message, function(err, res) {
            if (err !== null) {
                
            } else {
                callback(res);
            }
        }, socket);
    };
    
    /**
     * Сменить комнату
     */
    this.changeRoom = function(roomId, socket) {
        _room = this.initRoom(roomId, socket);
    };
    
    /**
     * Все онлайн пользователи в комнате
     */
    this.getOnline = function(io, socket) {
        if (!_room) return;
        var clients = io.sockets.adapter.rooms['room_' + this.getCityId()];
        var list = [];
        for (var clientId in clients ) {
            var client_socket = io.sockets.connected[clientId];//Do whatever you want with this
            if (!client_socket.dataUser) continue;
            if (socket && socket.dataUser 
                && client_socket.dataUser.id == socket.dataUser.id) continue;
            var listItem = {
                id: client_socket.dataUser.id,
                name : client_socket.dataUser.name,
                login: client_socket.dataUser.login,
                small_photo: client_socket.dataUser.small_photo,
            };
            list.push(listItem);
        }
        return list;
    };
};

module.exports = roomService;
