/* 
 * Модуль для обработки данных по городам
 */

var dbHandler = require('./dbHandler');
var _ = require('underscore');
var Message = require('./Message');
var User = require('../models/User');

var user = new User();

/**
 * Тут хранятся данные по всем комнатам
 * синхронизировано с БД
 */
var _rooms = {};


//var user = new User();

var fieldsForMessage = ['id', 'email', 'login', 'name', 'family', 'sex', 'small_photo', 'big_photo'];

function RoomCity(cityId) {
    var _cityId = extractId(cityId); // cityId = "city_*" extract *
    
    this.getId = function() {
        return _cityId;
    };
    
    this.loadRoom = function (callback) {
        var casheRoom = getCacheRoom(_cityId);
        if (casheRoom !== false) return callback(casheRoom);
        
        var limit = 15;
        var fields = 'u.id AS id, u.email AS email, u.login AS login, u.name AS name, u.family AS family, u.sex AS sex, ';
            fields += 'm.id AS mid, m.content AS content, m.date_create AS date_create, ';
            fields += 'a.big_photo AS big_photo, a.small_photo AS small_photo';
        query = "SELECT " + fields + " FROM `users` AS u " +
                    "INNER JOIN `messages` AS m ON u.id=m.user_id " +
                    "INNER JOIN `avatar` AS a ON u.id_avatar=a.id " +
                    "WHERE m.id_city=? ORDER BY m.`id` DESC  LIMIT ?";
        //var query = "SELECT * FROM `messages` WHERE id_city=? ORDER BY `date_create` DESC LIMIT ?";

        params = [_cityId, limit];
        var self = this;
        dbHandler.getAll(query, function(err, res){
            if(err){
                //console.log(err);
                callback(false);
            } else if(res && _.isObject(res)) {
                var data = {
                    messages : getMessages(res),
                    users : getUniqueUsers(res)
                };
                setCasheRoom(data, _cityId);
                callback(data);
            } else {
                //log.error({error: "Пользователь с id " + user_id + " не найден" });
                callback(false);
            }
        }, params);

        


    };
    
    /**
     * Добавить сообщение в комнату
     */
    this.addMessage = function(message, callback, socket) {
        if(message.content.trim() == '') {
            return;
        }
        
        var dataUser = user.getUserData(socket, fieldsForMessage);
        var query = "INSERT INTO `messages` SET ?";

        var params = {
            date_create: message.date_create,
            user_id: dataUser.id,
            id_city: _cityId,
            content: message.content
        };
        // Добавляем сообщение в БД
        // В случае успешного добавления - возвращаем добавленное собщения (с last_inset_id)
        dbHandler.insert(query, function(err, insertId) {
            if (err) {
                callback(err.message);
                return;
            }
            message.id = insertId;
            message.user_id = parseInt(message.user_id);
            var data = {
                message : message,
                user : dataUser
            }
            setCasheRoom(data, _cityId, dataUser, true);
            callback(null, data);
        }, params);
    };
};

module.exports = RoomCity;

function extractId(cityId) {
    var template = 'city_';
    return cityId.substr(template.length);
}

/**
 * Получить данные из кеша
 */
function getCacheRoom(id) {
    if (typeof _rooms[id] !== 'undefined') return _rooms[id];
    return false;
}

/**
 * Сохранить данные в кеш
 */
function setCasheRoom(data, id, dataUser, append) {
    if (append === true) {
        _rooms[id].messages.push(data.message);
        if (isHaveUser(dataUser, _rooms[id]) === false) _rooms[id].users.push(data.user);
    } else {
        _rooms[id] = data;
    }
    
}
/*-----------------В ОТДЕЛЬНЫЙ КЛАСС-------------------------------*/
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