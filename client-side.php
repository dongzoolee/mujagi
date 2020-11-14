<?php
header("Content-Type:text/html;charset=utf-8");
include "../dbInfo/dbConnect.php";
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>livechat</title>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.2.1/dist/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/3.0.0/socket.io.js"></script>
    <script src="https://leed.at/leed/plug-in/highlight/highlight.pack.js"></script>
    <script>
        hljs.initHighlightingOnLoad();
    </script>
    <link rel="stylesheet" href="https://leed.at/leed/plug-in/highlight/styles/railscasts.css">
    <link href="https://leed.at/leed/css/font.css" rel="stylesheet" type="text/css">
</head>
<style>
    li {
        list-style: none;
    }

    html {
        width: 100%;
        height: 100%;
    }

    body {
        width: 100%;
        height: 100%;
        margin: 0;
    }

    p {
        font-family: 'appleNeoT';
        font-size: 6vw;
        height: 8.5vw;
        padding: 2vw 0 0 5vw;
        margin: 0;
        display: inline-block;
    }

    #container {
        height: calc(100% - 12vw);
        box-sizing: border-box;
    }

    textarea {
        right: 0;
        width: 49%;
        height: 37vw;
        box-sizing: border-box;
        display: inline-block;
    }

    #chat {
        height: calc(100% - 12vw);
        width: 54%;
        margin: 0;
        position: fixed;
        bottom: 3vw;
        overflow-y: scroll;
        box-sizing: border-box;
        border: 1px solid #707070;
    }

    button {
        width: 7vw;
        height: 3vw;
        font-size: 1.2vw;
    }

    #form_div {

        position: absolute;
        width: 46%;
        right: 0;

    }

    #not_available {
        font-family: "appleNeoT";
        font-size: 5vw;
        color: red;
        text-align: center;
    }

    #new_message {
        display: none;
        margin-left: 1vw;
    }

    .blink_yes {
        color: white;
    }

    .blink_no {
        color: blue;
    }

    @media(max-width:1024px) {
        #chat {
            height: calc(100% - 30vw);
            bottom: 20vw;
        }

        textarea {
            width: calc(100% - 13vw);
            height: 20vw;
        }

        button {
            width: 12vw;
            height: 20vw;
            font-size: 3vw;
        }
    }
</style>

<body>
    <p>누구나 로그인 없이</p>
    <div id="conn_info" style="display:inline-block;">
        <span>접속자 수 : </span>
        <span id="conn_num"></span>
        <span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;press [CTRL] + [ENTER] to send</span>
        <div id="new_message" class="blink_yes">New Message</div>
    </div>
    <div id="container">
        <ul id="chat">
            <!-- <div id="not_available">Loading . . .</div> -->
            <?php
            $sql = "SELECT * FROM mujagi";
            $res = $mysqli->query($sql);
            while ($row = $res->fetch_array(MYSQLI_ASSOC)) {
                echo '<li>익명' . $row['id'] . ' : ' . $row['msg'] . '</li>';
            }
            ?>
        </ul>
        <div id="form_div">
            <form id="msg_form">
                <textarea id="codearea" placeholder="#include <iostream>
int main(){
    ios::sync_with_stdio(0),cin.tie(0),cout.tie(0);
}"></textarea>
                <textarea id="textarea" placeholder="채팅 메세지를 입력하세요"></textarea>
                <button disabled onclick="submit_form()">로그인중</button>
            </form>
        </div>
    </div>
</body>
<script>
    $('#textarea').bind('keydown', function(e) {
        if (e.keyCode === 13 && e.ctrlKey) {
            $('#msg_form').submit();
        }
    });
    $('#codearea').bind('keydown', function(e) {
        if (e.keyCode === 13 && e.ctrlKey) {
            $('#msg_form').submit();
        }
    });
    // hljs
    document.querySelectorAll('code').forEach((block) => {
        hljs.highlightBlock(block);
    });
    $('#chat').scroll(() => {
        if ((parseInt($('#chat').scrollTop()) + parseInt($('#chat').innerHeight()) + parseInt(1) >= parseInt($('#chat').prop('scrollHeight'))))
            $('#new_message').css('display', 'none');
    });
    window.onload = () => {
        $('#chat').scrollTop($('#chat').prop('scrollHeight'));
    }
    var socket = io("https://livechat.leed.at");
    socket.on('connect', () => {
        // $('#not_available').remove();
        $.getJSON('https://api.ipify.org?format=jsonp&callback=?', function(data) {
            socket.emit('ip', data.ip);
            console.log(data.ip);
        });
    })
        $('#msg_form').submit((e) => {
            var chk = 0;
            e.preventDefault();
            if ($('#codearea').val().trim()) {
                socket.emit('msg', '<pre><code class=\'c++ hljs cpp\'>' + $('<html>').text($('#codearea').val()).html() + "</code></pre>");
                $('#codearea').val('');
                chk = 1;
                $('#codearea').focus();
            }
            if ($('#textarea').val().trim()) {
                socket.emit('msg', $('<html>').text($('#textarea').val()).html());
                $('#textarea').val('');
                chk = 1;
                $('#textarea').focus();
            }
            if (!chk) {
                alert('내용을 입력하세요');
            }
            setTimeout(() => {
                document.querySelectorAll('code').forEach((block) => {
                    hljs.highlightBlock(block);
                });
            }, 0)
            setTimeout(() => {
                document.querySelectorAll('code').forEach((block) => {
                    hljs.highlightBlock(block);
                });
            }, 100)
        })
    socket.on('msg', (data) => {
        if (parseInt($('#chat').scrollTop()) + parseInt($('#chat').innerHeight()) + parseInt(1) >= parseInt($('#chat').prop('scrollHeight'))) {
            $('#chat').append('<li>익명' + data.id + ' : ' + data.msg + '</li>')
            document.getElementById('chat').scrollTop = document.getElementById('chat').scrollHeight;
        } else {
            $('#chat').append('<li>익명' + data.id + ' : ' + data.msg + '</li>')
            if (!(parseInt($('#chat').scrollTop()) + parseInt($('#chat').innerHeight()) + parseInt(1) >= parseInt($('#chat').prop('scrollHeight')))) {
                var interv;
                $('#new_message').css('display', 'inline-block');
                interv = setInterval(() => {
                    if (!(parseInt($('#chat').scrollTop()) + parseInt($('#chat').innerHeight()) + parseInt(1) >= parseInt($('#chat').prop('scrollHeight')))) {
                        $('#new_message').toggleClass('blink_no');
                        $('#new_message').toggleClass('blink_yes');
                    } else {
                        clearInterval(interv);
                        $('#new_message').css('display', 'none');
                    }
                }, 400);

            }
        }
        setTimeout(() => {
            document.querySelectorAll('code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        }, 0)
        setTimeout(() => {
            document.querySelectorAll('code').forEach((block) => {
                hljs.highlightBlock(block);
            });
        }, 100)
    })
    socket.on('query success', () => {
        $('#msg_form button').text('전송');
        $('#msg_form button').removeAttr('disabled');
    });
    socket.on('welcome', (msg) => {
        $('#chat').append('<li>' + msg + '</li>')
    })
    socket.on('new conn', (num) => {
        $('#conn_num').text(num);
    })
</script>

</html>
