<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stat</title>
</head>
<body>
    <div class="container">
        <h3>Статистика</h3>

        <div>
            <p>День {{ $farm->day }}</p>
            (выберите день от 1 до {{ $maxDay }})
            <form action="/stat" method="get">
                @csrf
                <input name="day" placeholder="{{ $farm->day }}" style="width:5%;">
                <button type="submit">Выбрать</button>
            </form> 
        </div>

        <ul>
            <li>общее количество овечек: {{ $farm->sheeps }}</li>
            <li>количество убитых овечек: {{ $farm->killed }}</li>
            <li>количество живых овечек: {{ $farm->activ }}</li>
            <li>номер самого населенного загона: №{{ $farm->max }} ({{ $farm->maxQ }} овечек)</li>
            <li>номер самого менее населенного загона: №{{ $farm->min }} ({{ $farm->minQ }} овечек)</li>
        </ul>
    </div>
</body>
</html>