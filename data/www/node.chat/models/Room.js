/** 
 * Модуль для работы с данными расположенные в конкретном городе.
 */

var dbHandler = require('./dbHandler');
var _ = require('underscore');
var Message = require('./Message');
var User = require('../models/User');

var util = require('util');
var EventEmitter = require('events').EventEmitter;

//var roomId = 0;
var rooms = {};

var user = new User();

var fieldsForMessage = ['id', 'email', 'login', 'name', 'family', 'sex', 'small_photo', 'big_photo'];

/**
 * Конструктор класса
 */
function Room() {
    EventEmitter.call(this);
    
    this.getRoomId = function(socket){
        return socket.roomId;
    };
    
    /**
     * Получить комнату к которой привязан пользователь
     */
    this.getRoom = function(roomId, callback) {
        if (typeof rooms[roomId] !== 'undefined') return callback(rooms[roomId]);
        this.setRoom(roomId, function(roomData){
            callback(roomData);
        });
    };
    
    this.getRooms = function() {
        return rooms;
    };
    
    /**
     * Взять данные из БД и поместить в кеш
     */
    this.setRoom = function(roomId, callback) {
        var self = this;
        getRoomData(roomId, function(res){
            if (res === false) {
                self.emit('disconnected', 'No data room');
                return;
            }
            rooms[roomId] = res;
            callback(rooms[roomId]);
        });
    };
    
     /**
     * Добавить сообщение в комнату
     * Если нет комнаты - создать и добавить смс
     * @param string message
     * @param obj userData - {id, cityId}
     */
    this.addMessageInRoom = function(message, callback, socket) {
        var dataUser = user.getUserData(socket, fieldsForMessage);

        var self = this;
        Message.insertMessage(message, function(err, res){

            if (err !== null) {
                self.emit('disconnected', 'filed inser message');
                return;
            }

            // Сообщение сохранено в бд. Добавляем сообщение в кеш
            updateRoom(res, dataUser, function(data){
                callback(data);
            }, socket.roomId);

        }, dataUser, socket.roomId);
    };
    
    /**
     * Перебросить пользователя в другую комнату
     */
    this.changeRoom = function(data, callback, socket) {
        
    };
}

util.inherits(Room, EventEmitter);
module.exports = Room;

/*------------private methods---------------*/

/**
 * Добавить комнату в список. Если ее еще нет в списке
 */
function getRoomData(cityId, callback) {
    var limit = 15;
    var fields = 'u.id AS id, u.email AS email, u.login AS login, u.name AS name, u.family AS family, u.sex AS sex, ';
        fields += 'm.id AS mid, m.content AS content, m.date_create AS date_create, ';
        fields += 'a.big_photo AS big_photo, a.small_photo AS small_photo';
    var query = "SELECT " + fields + " FROM `users` AS u " +
                "INNER JOIN `messages` AS m ON u.id=m.user_id " +
                "INNER JOIN `avatar` AS a ON u.id_avatar=a.id " +
                "WHERE m.id_city=? ORDER BY m.`date_create` DESC  LIMIT ?";

    var params = [cityId, limit];
    var self = this;
    dbHandler.getAll(query, function(err, res){
        if(err){
            //console.log(err);
            callback(false);
        } else if(res && _.isObject(res)) {
            callback({
                messages : getMessages(res),
                users : getUniqueUsers(res)
            });
        } else {
            //log.error({error: "Пользователь с id " + user_id + " не найден" });
            callback(false);
        }
    }, params);
}

function getMessages(res) {
    var list = [];
    var fields = ['id', 'mid', 'content', 'date_create'];
    res.forEach(function(user){
        var itemList = {};
        fields.forEach(function(field){
            var trueField = field;
            if (field == 'id') trueField = 'user_id';
            if (field == 'mid') trueField = 'id';
            if (typeof user[field] !== 'undefined') itemList[trueField] = user[field];
        });
        list.push(itemList);
    });
    return list;
}

function getUniqueUsers(res) {
    var list = {};
    var fields = ['id', 'email', 'login', 'name', 'family', 'sex', 'big_photo', 'small_photo'];
    res.forEach(function(user){
        var itemList = {};
        fields.forEach(function(field){
            if (typeof user[field] !== 'undefined') itemList[field] = user[field];
        });
        if (typeof list[user.id] === 'undefined') list[user.id] = itemList;
        //list.push(itemList);
    });
    var result = [];
    var j = 0;
    for (i in list) {
        result[j++] = list[i];
    }
    return result;
}

function updateRoom(message, dataUser, callback, roomId) {
        var room = rooms[roomId];
        room.messages.push(message);
        if (isHaveUser(dataUser, room) === false) room.users.push(dataUser);
        callback({
            message:message,
            user: dataUser
        });
}

/**
 * Есть ли пользователь в комнате
 */
function isHaveUser(user, room) {
    var bool = false;
    room.users.forEach(function(item){
        if (item.id == user.id) {
            bool = true;
            return;
        }
    });
    return bool;
}

/**
 * Получить обьект для работы с сообщениями
 */
function getObjMessage(roomId) {
    
};