// var mongo  = require('mongodb').MongoClient;
var mysql  = require('mysql');
var client = require('socket.io')(8080).sockets;


var con = mysql.createConnection({
    host: "localhost",
    user: "root",
    password: "",
    database: "node_chat"
});

con.connect(function(err) {
    if (err) throw err;
    console.log("Connected!");

    client.on('connection',function (socket) {
        //Emit all messages

        con.query(
            "SELECT * FROM `messages` ORDER BY `id` DESC LIMIT 20  ", function (err, result) {
                if (err) throw err;
                socket.emit('output',result);
            });
        //Wait for input
        socket.on('input',function (data) {
            var sendStatus = function (s) {
                  socket.emit('status',s)
            };

            var name=data.name;
            var message=data.message;
            var whitespacePattern=/^\s*$/;

            if(whitespacePattern.test(name)||whitespacePattern.test(message))
            {
                sendStatus('Name and message is required')
            }
            else {
                con.query(
                    "insert into messages (name,message) value (?,?)", [name, message], function (err, result) {
                    if (err) throw err;

                        //Emit latest message to All clients
                        client.emit('output',[data]);
                        sendStatus({
                            message:"message sent",
                            clear:true
                        });

                    console.log("Result: " + result);
                });

            }
        })
    });
});

