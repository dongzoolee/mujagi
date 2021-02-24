const express = require('express');
const app = express();
const dotenv = require('dotenv');
dotenv.config();
// mysql2
const mysql = require('mysql2');
const connection = mysql.createConnection({
    host: process.env.MYSQL_HOST,
    user: process.env.MYSQL_USER,
    password: process.env.MYSQL_PASSWORD,
    database: process.env.MYSQL_DB
});
connection.connect();

const server = app.listen(2222, () => {
    console.log('2222포트에서 mujagi 서버 실행됨');
});

var idx = 0;
var socketio = require('socket.io');
var io = socketio();
io.attach(server);
var conn_num = 0;
io.on('connection', (socket) => {
    io.emit('new conn', ++conn_num);
    console.log('new login from id : ' + socket.id);

    socket.on('ip', (ip) => {
        connection.query('SELECT EXISTS (SELECT id FROM mujagi_user WHERE ip = "' + ip + '") AS chk', (err, res, field) => {
            // console.log(res[0].chk)
            if (err) throw err;
            if (!res[0].chk) { // 없다면 
                connection.query('INSERT INTO mujagi_user(ip) VALUES(?)', [ip], () => {
                    connection.query('SELECT id FROM mujagi_user WHERE ip = "' + ip + '"', (err, res, field) => {
                        socket.ip = ip;
                        socket.id = res[0].id;
                        socket.emit('query success');
                    });
                });
            } else {
                connection.query('SELECT id, ip FROM mujagi_user WHERE ip = "' + ip + '"', (err, res, field) => {
                    // console.log(res[0].id);
                    socket.id = res[0].id;
                    socket.ip = res[0].ip;
                    socket.emit('query success');
                })
            }
        });
    })
    // socket.broadcast.emit('new connection', '익명' + ++idx + '이 접속하셨습니다.');
    // var welcome = '익명' + ++idx + '이 접속하셨습니다.';
    // io.emit('welcome', welcome);
    // q.push(welcome);

    // socket.on('login', (data) => {
    // })

    socket.on('disconnect', () => {
        io.emit('new conn', --conn_num);
        console.log('disconnection from id : ' + socket.id);
        // socket.broadcast.emit('new connection', '누군가 떠나셨습니다');
        //q.push('누군가 떠나셨습니다');
    })

    socket.on('msg', (msg) => {
        console.log('익명' + socket.id + ' : ' + msg);
        connection.query('INSERT INTO mujagi(id, ip,msg,date) VALUES(?, ?, ?, ?)', [socket.id, socket.ip, msg, new Date().toLocaleString()])
        io.emit('msg', {
            id: socket.id,
            msg: msg
        });
    })
})

// require('dns').lookup(require('os').hostname(), function (err, add, fam) {
//     console.log('addr: '+add);
// })
