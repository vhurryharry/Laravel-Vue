'use strict';

var os = require('os');
var nodeStatic = require('node-static');
var http = require('http');
var socketIO = require('socket.io');
var _ = require('lodash');

var fileServer = new (nodeStatic.Server)();
var app = http.createServer(function (req, res) {
    fileServer.serve(req, res);
}).listen(9002);

var io = socketIO.listen(app);
const hallway = io.of('/hallway');

hallway.on('connection', function (socket) {
    // convenience function to log server messages on the client

    var peersToAdvertise = _.chain(io.sockets.connected)
        .values()
        .without(socket)
        .value();

    console.log('advertising peers', _.map(peersToAdvertise, 'id'));


    peersToAdvertise.forEach(function (socket2) {
        console.log('Advertising peer %s to %s', socket.id, socket2.id);

        socket2.emit('peer', {
            peerId: socket.id,
            initiator: true
        });

        socket.emit('peer', {
            peerId: socket2.id,
            initiator: false
        });
    });

    socket.on('signal', function (data) {
        var socket2 = io.sockets.connected[data.peerId];
        if (!socket2) { return; }
        console.log('Proxying signal from peer %s to %s', socket.id, socket2.id);

        socket2.emit('signal', {
            signal: data.signal,
            peerId: socket.id
        });
    });

    socket.on('join', function (data) {
        socket.join(data.room_slug);

        console.log('User joined chat room 1');
        // console.log(data);
        socket.to(data.room_slug).emit('joined', {
            room: data.room_slug,
            user: socket.id,
            peer_data: data.peer_data
        });

        // console.log("rooom id");
        // console.log(room);
    });

    function log () {
        var array = ['Message from server:'];
        array.push.apply(array, arguments);
        socket.emit('log', array);
    }

    socket.on('message', function (message) {
        log('Client said: ', message);
        // for a real app, would be room-only (not broadcast)
        socket.broadcast.emit('message', message);
    });

    socket.on('create or join', function (room) {
        log('Received request to create or join room ' + room);

        var clientsInRoom = io.sockets.adapter.rooms[room];
        var numClients = clientsInRoom ? Object.keys(clientsInRoom.sockets).length : 0;
        log('Room ' + room + ' now has ' + numClients + ' client(s)');

        if (numClients === 0) {
            socket.join(room);
            log('Client ID ' + socket.id + ' created room ' + room);
            socket.emit('created', room, socket.id);

        } else if (numClients === 1) {
            log('Client ID ' + socket.id + ' joined room ' + room);
            io.sockets.in(room).emit('join', room);
            socket.join(room);
            socket.emit('joined', room, socket.id);
            io.sockets.in(room).emit('ready');
        } else { // max two clients
            socket.emit('full', room);
        }
    });

    socket.on('ipaddr', function () {
        var ifaces = os.networkInterfaces();
        for (var dev in ifaces) {
            ifaces[dev].forEach(function (details) {
                if (details.family === 'IPv4' && details.address !== '127.0.0.1') {
                    socket.emit('ipaddr', details.address);
                }
            });
        }
    });

    socket.on('bye', function () {
        console.log('received bye');
    });

});
