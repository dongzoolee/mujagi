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
}

var socket = io("https://livechat.leed.at");
socket.on('connect', () => {
    // $('#not_available').remove();
    $.getJSON('https://api.ipify.org?format=jsonp&callback=?', function(data) {
        socket.emit('ip', data.ip);
        console.log(data.ip);
    });
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
