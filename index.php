</DOCTYPE html>
<html>
    <head>
        <title> Caht </title>
        <link rel="stylesheet" href="css/main.css" >
    </head>
    <body>
        <div class="chat" >
                <input type="text" class="chat-name" placeholder="Enter your name" >
                <div class="chat-messages">
                    <div class="chat-message"></div>

                </div>
                <textarea class="chat-textarea" placeholder="Type your message"></textarea>
            <div class="chat-status"> Status:<span>Idle</span> </div>
        </div>
    <script src="http://127.0.0.1:8080/socket.io/socket.io.js"></script>
    <script>
        (function () {
            var getNode =function (s) {
                return document.querySelector(s);
            };
            // Get required nods
            statusValue=getNode('.chat-status span');
            messages=getNode('.chat-messages');
            textarea=getNode('.chat textarea');
            chatName=getNode('.chat-name');

            statusDefault=statusValue.textContent;

            setStatus= function (s) {
                statusValue.textContent=s;

                if(s!== statusDefault){
                    var delay=setTimeout(function () {
                        setStatus(statusDefault);
                        clearInterval(delay);
                    },3000)
                }

            };

            try {
                var socket =io.connect('http://127.0.0.1:8080')
            }catch (e) {
                //set status to warn user
            }
            if(socket!==undefined){

                //listen for output
                socket.on('output',function (data) {
                    if (data.length){
                        //loop through results
                        for(var x=0 ; x < data.length ; x=x+1){
                            var message=document.createElement('div');
                            message.setAttribute('class','chat-message');
                            message.textContent=data[x].name +" : "+data[x].message;

                            //Append
                            messages.appendChild(message);

                        }
                    }
                });


                //listen for a status
                socket.on('status',function (data) {
                   setStatus((typeof data==='object') ? data.message : data);

                   if(data.clear===true ) {
                       textarea.value='';
                   }

                });


               // Listen for keydown
                textarea.addEventListener('keydown',function (event) {
                   var self;
                   var name = chatName.value;
                   var message = textarea.value;


                   if(event.which===13&&event.shiftKey===false)
                   {
                       socket.emit('input',{
                           name:name,
                           message:message
                       })
                   }
                });
            }
        })();
    </script>
    </body>
</html>
