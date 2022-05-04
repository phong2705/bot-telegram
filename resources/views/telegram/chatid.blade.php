<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Chat ID</title>
    <link rel="stylesheet" href="assets/style.css">
</head>

<body>
    <div class="wrapper">
        <header>
            <h1 class="title">Form Chat ID</h1>
            <a href="{{ url('') }}">List Chat ID</a>
            <!-- <p class="version">v2.0</p> -->
        </header>
        <section class="input">
            <h2 class="instruction">Create New Chat ID</h2>
            <form class="row" id="task" action="{{ url('add-chat-id') }}" method="post">
                @csrf
                <div class="col-3">
                    <label class="form-label">Chat ID</label><br>
                    <input class="form-enter chat_id" type="text" name="chat_id">
                    <br>
                </div>
                <input class="submit" type="submit" value="Submit">
            </form>
        </section>
        <footer>
            <div class="row">
                <a href="https://api.telegram.org/bot5273274128:AAFQL_ffGoRhzUpN2RPv0r2PNRzBR06UVXk/getupdates">link lấy chat ID mới</a>
            </div>
        </footer>
    </div>
    
</body>
</html>