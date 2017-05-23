/**
 * Приватные сообщения
 */
 
var dbHandler = require('./dbHandler');
var _ = require('underscore');
var Message = require('./Message');
var User = require('../models/User');

var Chat = require('../models/PrivateChat');

var user = new User();
/**
 * Тут хранятся данные по всем комнатам
 * синхронизировано с БД
 */
 var _rooms = {};

//var user = new User();

var fieldsForMessage = ['id', 'email', 'login', 'name', 'family', 'sex', 'small_photo', 'big_photo'];

/**
 * privateId = "private_*", где * - id собеседника
 */
function roomPrivate(privateId, socket) {
    
    var _privateId = extractId(privateId, socket); // cityId = "city_*" extract *
    
    var _opponentId = privateId.substr(8);
    var _userId = user.getId(socket);
    
    setStatusReaded(_userId, _opponentId);

    this.getId = function() {
        return _privateId;
    };

    

    this.loadRoom = function(callback) {
        var casheRoom = getCacheRoom(_privateId);
        if (casheRoom !== false) return callback(casheRoom);
        
        var limit = 15;
        var fields = 'u.id AS id, u.email AS email, u.login AS login, u.name AS name, u.family AS family, u.sex AS sex, ';
            fields += 'pm.id AS mid, pm.text AS content, pm.date_create AS date_create, ';
            fields += 'a.big_photo AS big_photo, a.small_photo AS small_photo';
        var query = "SELECT " + fields + " FROM `users` AS u " +
                    "INNER JOIN `private_message` AS pm ON pm.from_id=u.id " +
                    "INNER JOIN `avatar` AS a ON u.id_avatar=a.id " +
                    "WHERE (pm.from_id=? AND pm.to_id=?) OR (pm.from_id=? AND pm.to_id=?) ORDER BY pm.`id` DESC  LIMIT ?";
        //var query = "SELECT * FROM `messages` WHERE id_city=? ORDER BY `date_create` DESC LIMIT ?";

        var params = [_opponentId, _userId, _userId, _opponentId, limit];
        var self = this;
        dbHandler.getAll(query, function(err, res){
            if(err){
                console.log(err);
                callback(false);
            } else if(res && _.isObject(res)) {
                var data = {
                    messages : getMessages(res),
                    users : getUniqueUsers(res)
                };
                setCasheRoom(data, _privateId);
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
        var dataUser = user.getUserData(socket, fieldsForMessage);
        var query = "INSERT INTO `private_message` SET ?";

        var params = {
            date_create: message.date_create,
            from_id: _userId,
            to_id: _opponentId,
            text: message.content
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
            };
            setCasheRoom(data, _privateId, dataUser, true);
            
            
            Chat.getCountNewPrivate(_opponentId, function(count){
                socket.broadcast.emit('received_new_private', {
                    room: _privateId, 
                    from: _userId, 
                    to: _opponentId,
                    countNewPrivate: count
                });
            });
            
            callback(null, data);
        }, params);
    };
}

module.exports = roomPrivate;

function extractId(cityId, socket) {
    var template = 'private_';
    if (typeof _rooms[cityId.substr(template.length) + '_' + user.getId(socket)] !== 'undefined') return cityId.substr(template.length) + '_' + user.getId(socket);
    return user.getId(socket) + '_' + cityId.substr(template.length);
    
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
        if (user.content == '') return;
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

function setStatusReaded(to_id, from_id) {
    var query = "UPDATE `private_message` SET status=? WHERE to_id=? AND from_id=?";

        var params = ['read', to_id, from_id];
        // Добавляем сообщение в БД
        // В случае успешного добавления - возвращаем добавленное собщения (с last_inset_id)
        dbHandler.execute(query, function(err, insertId) {
            if (err) {
                //callback(err.message);
                return;
            }
        }, params);
}