<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

    <title>Main</title>
<style>
    .zagon {
    border: 1px solid black;
    overflow-y: scroll;
    height: 12em;
    }
</style>
</head>
<body>
    <div class="container">
        <h3>Ферма для овечек</h3>
        <div class="col-md-12">
            <span>
                День: <a id="day">1</a>
                (cекунды:<a id="second">0</a>)
            </span>
            <div class="row">
                @foreach ($farm as $k => $value)
                    <div class="col-md-6">
                        <h4>Загон{{ $k+1 }} </h4> 
                        <div class="zagon" id="z{{ $k+1 }}">
                            @foreach ($value as $v)
                                <div id="sheep{{ $v->id }}">Овечка{{ $v->id }}</div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        <hr>
        <div>
            <a href="/stat">Статистика по дням<a>
        </div>
        <div class="mt-2">
            <input type="text" id="myInput" name="command" placeholder="Введите текст команды*" style="width:90%;">
            <button id="command">Выполнить</button>
            <p>
                * Консоль принимает следующие команды:<br>
                add 1 (Команда добавляет овечку в загон, номер которого нужно указать вместо цифры 1) <br>
                kill 2 (Команда зарубить овечку, номер которой укажете вместо 2) <br>
                move 10 4 (Команда перемещает овечку в другой загон, вместо 10 указать номер овечки, а вместо 4 номер загона)
            </p>
        </div>
        <hr>
        <div>
            <form action="/clear">
                <button id="clear" class="btn btn-danger">Сбросить всё!</button>
            </form>
        </div>
    </div>

<script>
    $(function (){
        var timer = setInterval(function () {
            var second = localStorage.getItem('second') ? localStorage.getItem('second') : 0;
            var day = localStorage.getItem('day') ? localStorage.getItem('day') : 1;
            second = parseInt(second) + 1;
            setDay(day);
            setSecond(second);

            if (second % 10 == 0 && second > 0) {
                day = parseInt(day) + 1;
                setDay(day);
                add(day);
                if (second % 100 == 0 && second > 0) {
                    kill(day);
                    move(day);
                }
            }

        }, 1000);
        
        $('#clear').on('click', function () {
            $.ajax({
                url: '/clear',
                success: function () {
                    window.location.reload();
                }
            });
            setSecond(0);
            setDay(1);
            clearInterval(timer);
        });
        $('#command').on('click', function () {
            $.ajax({
                success: function () {
                    window.location.assign("/command?command=" + $("#myInput").val() + "&d=" + localStorage.getItem('day'));
                }
            });
        });

        function setDay(day) {
            localStorage.setItem('day', day);
            $('#day').html(day);
        }

        function setSecond(second) {
            localStorage.setItem('second', second);
            $('#second').html(second);
        }

        function add(day) {
            $.ajax({
                type: 'GET',
                url: '/love',
                dataType: 'json',
                data: "d=" + day,
                success: function (data) {
                    console.log(data);
                    $('#z' + data.sheepfold).append(
                        '<div id="sheep' + data.sheep_id + '">Овечка' + data.sheep_id + '</div>'
                    );
                    stat(day);
                }
            });
        }

        function kill(day) {
            $.ajax({
                type: 'GET',
                url: '/kill',
                dataType: 'json',
                data: "d=" + day,
                success: function (data) {
                    console.log(data);
                    $('#sheep' + data.kill).remove();
                }
            });
        }

        function move(day) {
            $.ajax({
                type: 'GET',
                url: '/move',
                dataType: 'json',
                data: "d=" + day,
                success: function (data) {
                    console.log(data);
                    $('#sheep' + data.sheep_id).remove();

                    $('#z' + data.to).append(
                        '<div id="sheep' + data.sheep_id + '">Овечка' + data.sheep_id + '</div>'
                    );
                }
            });
        }

        function stat(day) {
            $.ajax({
                type: 'GET',
                url: '/statistic',
                data: "d=" + day
            })
        }
    });
</script>
</body>
</html>