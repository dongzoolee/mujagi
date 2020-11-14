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
        <script src = "client-side.js"></script>
        <script>
        hljs.initHighlightingOnLoad();
        </script>
        <link rel="stylesheet" href="https://leed.at/leed/plug-in/highlight/styles/railscasts.css">
        <link href="https://leed.at/leed/css/font.css" rel="stylesheet" type="text/css">
        <link href="client-side.css" rel="stylesheet" type="text/css">
    </head>

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

    </html>
