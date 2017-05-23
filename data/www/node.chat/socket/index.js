/**
 * Инициализация Socket.IO так, чтобы им обрабатывались подключения к серверу Express/HTTP
 */

var config = require('../config');

var RoomService = require('../models/RoomService');
var User = require('../models/User');
var PrivateChat = require('../models/PrivateChat');
//var Message = require('../models/Message');

var user = new User();

// Error handler
process.on('uncaughtException', function(err) {
  console.log(err.stack);
});

module.exports = function(server){
    var io = require('socket.io').listen(server);
    io.set('origins', config.get('io-origin'));
    
    //io.set('transports', ['websocket', 'xhr-polling']);
    
    io.on('connection', function (socket) {
        var room = new RoomService();
        socket.on('join', function(data, callback){
            if (typeof data == 'string') data = eval('(' + data + ')');
            if (typeof data.userId == 'undefined' || typeof data.city == 'undefined') return callback('not autorized');
            user.checkAuthUser(data, function (res) {
                // Пользователь авторизовался

                socket.dataUser = res.dataUser;
                room = new RoomService(res.roomId);
                
                // Разошлем всем остальным, что пользователь добавился в комнату
                socket.broadcast.to('room_' + room.getRoomId()).emit('add_user_in_room', socket.dataUser);

                // Загружаем данные из комнаты
                room.loadRoom(function(roomData){
                    socket.join('room_' + room.getRoomId());
                    PrivateChat.getCountNewPrivate(user.getId(socket), function(res){
                        roomData.countNewPrivate = res;

                        socket.emit('onJoin', roomData);
                    });
                });
            });
        });

        socket.on('leave', function(data, callback){
            delete socket.dataUser;
        });

        /**
         * Перебросить пользователя в другую комнату
         */
        socket.on('change_room', function(obj, callback){
            if (typeof obj.roomId != 'undefined') {
                // Если данные приходят из мобильного
                var roomId = obj.roomId;
                var isMobile = true;
            } else {
                // Если данные приходят из браузера
                var roomId = obj;
                var isMobile = false;
            }
            var oldRoom = room.getRoomId();
            var oldCity = room.getCityId();

            room.changeRoom(roomId, socket);
            
            var newRoom = room.getRoomId(); // куда перещел пользователь
            var newCity = room.getCityId(); // новый город, если поменялся
            // Загружаем данные из комнаты
            room.loadRoom(function(roomData){
                if (oldRoom && oldRoom.indexOf('_') > -1) socket.leave('room_' + oldRoom); // from LS

                if (newRoom.indexOf('_') > -1) {
                    // перешел в ЛС (из ЛС или города)
                    socket.join('room_' + newRoom);
                } else {
                    // перешел в город (из ЛС или города)
                    socket.join('room_' + newRoom);
                    if (oldCity && oldCity !== newCity) {
                        socket.leave('room_' + oldCity);
                        // оповещение пользователей, что кто-то покинул/пришел в комнату
                        if (typeof socket.dataUser !== 'undefined') {
                            socket.broadcast.to('room_' + oldCity).emit('leave_user_from_room', socket.dataUser);
                            socket.broadcast.to('room_' + newCity).emit('add_user_in_room', socket.dataUser);
                        }
                            
                    }
                    
                }
                if (isMobile) {
                    callback(roomData);
                } else {
                    socket.emit('onJoin', roomData);
                    if (typeof callback !== 'undefined') callback(newRoom, room.getOnline(io, socket));
                }
            });
        });
        
        // Подписываемся на событие клиента "Отправить сообщение"
        socket.on('message', function(message, callback){
            if (typeof socket.dataUser === 'undefined') {
                return;
            }
            if (typeof message == 'string') message = eval('(' + message + ')');
            room.addMessageInRoom(message, function(res){
                
                // Отправить сообщение в комнату всем кроме отправителя
                socket.broadcast.to('room_' + room.getRoomId()).emit('message', res);
                if (typeof callback !== 'undefined') callback(res); // Возвратить клиенту сообщение
            }, socket);
        });
        
        /**
         * Получить список переписчиков в ЛС
         */
        socket.on('get_private_list', function(callback){
            if (typeof socket.dataUser === 'undefined') {
                return;
            }
            PrivateChat.getPrivateList(user.getId(socket), function(res){
                // получить всех онлайн пользователей в комнате
                res.onlineUsers = room.getOnline(io, socket);
                callback(res);
            }, room.getCityId());
        });

        socket.on('set_readed', function(obj){
            if (typeof socket.dataUser === 'undefined') {
                return;
            }
            PrivateChat.setReaded(obj.to, obj.from);
        });

        socket.on('disconnect', function() {
            if (typeof socket.dataUser !== 'undefined') {
                io.sockets.in('room_' + room.getCityId()).emit('leave_user_from_room', socket.dataUser);
            }
        });
    });
};