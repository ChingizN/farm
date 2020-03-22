<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    public function index()
    {
        //Проверяем есть ли в базе данных записи, если нет, вводятся 10 овец, их количество в загонах задается рандомно 
        if (!\App\Sheeps::first()) {
            //изназально распологаем 10 овечек по 4 загонам, количество овец в загоне определяется рандомно
            $sheepfolds[] = rand(1, 7);
            $sheepfolds[] = rand(1, 8 - array_sum($sheepfolds));
            $sheepfolds[] = rand(1, 9 - array_sum($sheepfolds));
            $sheepfolds[] = 10 - array_sum($sheepfolds);

            $sheeps = $this->addSheep($sheepfolds);
            $this->statAdd($_GET['d'] = 1);
        }
        $sheepfolds = 4;
        for ($i=1; $i<=$sheepfolds; $i++) {
            $farm[] = \App\Sheeps::where([['status', 1], ['sheepfold', $i]])->orderBy('sheepfold')->get();
        }
        
        return view('main', ['farm' => $farm]);
    }

    public function addSheep($farm = false)
    {
        if (is_array($farm)) {
            foreach ( $farm as $key => $value ) {
                for ( $i = 1; $i <= $value; $i++ ) {
                    $sheeps = \App\Sheeps::create([
                        'sheepfold' => $key+1,
                        'day' => 1
                    ]);
                    $sheep = \App\Sheeps::all('id')->last()->id;
                    $sheepfold = $key + 1;
                    $log[0] = "Пополнение - Овечка$sheep появилась в Загоне№$sheepfold";
                    $log[1] = 1;
                    \App\Logs::record($log);
                }
            }
        } else {
            $sheeps = \App\Sheeps::create([
                'sheepfold' => $farm,
                'day' => $_GET['d']
            ]);
            $sheep = \App\Sheeps::all('id')->last()->id;
            $log[0] = "Пополнение - Овечка$sheep появилась в Загоне№$farm";
            $log[1] = $_GET['d'];
            \App\Logs::record($log);
        }
        return redirect('/');
    }

    public function sheepLove()
    {
        for ($i=1; $i<=4; $i++) {
            $sheepfolds[$i] = \App\Sheeps::where([['status', 1], ['sheepfold', $i]])->get();
            $sheepfolds[$i] = $sheepfolds[$i]->count();
            if ($sheepfolds[$i] < 2) {
                unset($sheepfolds[$i]);
            }
        }
        $sheepfolds = array_rand($sheepfolds);
        $this->addSheep($sheepfolds);
        // return redirect('/');
        echo json_encode(['sheepfold' => $sheepfolds, 'sheep_id' => \App\Sheeps::all('id')->last()->id]);        
    }

    public function sheepKill($id = false)
    {
        if ($id) {
            \App\Sheeps::where('id', $id)->update(['status' => 0]);
        } else {
            for ($i=1; $i<=4; $i++) {
                $sheepfolds[$i] = \App\Sheeps::where([['status', 1], ['sheepfold', $i]])->get();
                $sheepfolds[$i] = $sheepfolds[$i]->count();
                if ($sheepfolds[$i] < 2) {
                    unset($sheepfolds[$i]);
                }
            }
            if (count($sheepfolds)) {
                $sheepfolds = array_rand($sheepfolds);
                $sheep = \App\Sheeps::where([['status', 1], ['sheepfold', $sheepfolds]])->first();
                $sheepkill = \App\Sheeps::where('id', $sheep->id)->update(['status' => 0]);
                $id = $sheep->id;
            } 
            // return redirect('/');
        }
        echo json_encode(['kill' => $id]);
        $log[0] = "Овечку$id забрали сами знаете куда...";
        $log[1] = $_GET['d'];
        \App\Logs::record($log);
    }

    public function sheepMove($id = false, $into = false)
    {
        if ($id) {
            $sheep = \App\Sheeps::where([['id', $id], ['status', 1]])->first();
            $from = $sheep->sheepfold;
            $sheep->update(['sheepfold' => $into]);
        } else {
            for ($i=1; $i<=4; $i++) {
                $sheepfolds[$i] = \App\Sheeps::where([['status', 1], ['sheepfold', $i]])->get();
                $sheepfolds[$i] = $sheepfolds[$i]->count();

                if ($sheepfolds[$i] == 1) {
                    $into[$i] = $i;
                    unset($sheepfolds[$i]);
                }
            }
            $max = max($sheepfolds);
            if (($into )AND $max > 2) {
                $into = array_rand($into);
                $max = array_keys($sheepfolds, max($sheepfolds));
                $sheep = \App\Sheeps::where([['sheepfold', $max[0]], ['status', 1]])->first();
                $sheep->update(['sheepfold' => $into]);
                echo json_encode(['sheep_id' => $sheep->id, 'from' => $max[0], 'to' => $into]);
                $from = $max[0];
                $id = $sheep->id;
            }
        }
        $log[0] = "Овечку$id пересадили из Загона№$from в Загон№$into";
        $log[1] = $_GET['d'];
        \App\Logs::record($log);
        // return redirect('/');
    }

    public function clearAll()
    {
        \App\Sheeps::truncate();
        \App\Logs::truncate();
        \App\Stat::truncate();
        return redirect('/');
    }

    public function stat()
    {
        if (!$_GET) {
            $farm = \App\Stat::all()->last();
        } else {
            $farm = \App\Stat::where('day', $_GET['day'])->first();
        }
        if ($farm) {
            $maxDay = \App\Stat::all()->last()->day;
            return view('stat', ['farm' => $farm, 'maxDay' => $maxDay]);
        } else {
            echo "Введены недопустимые данные";
        }
    }

    public function statAdd()
    {
        $stat[0] = $_GET['d'];
        $stat[1] = \App\Sheeps::all()->count();
        $stat[2] = \App\Sheeps::where('status', 0)->count();
        $stat[3] = \App\Sheeps::where('status', 1)->count();
        for ($i=1; $i<=4; $i++) {
            $sheepfolds[$i] = \App\Sheeps::where([['status', 1], ['sheepfold', $i]])->get();
            $sheepfolds[$i] = $sheepfolds[$i]->count();
        }
        $stat[4] = max($sheepfolds);
        $stat[5] = min($sheepfolds);
        $max = array_keys($sheepfolds, max($sheepfolds));
        $min = array_keys($sheepfolds, min($sheepfolds));
        $stat[6] = $max[0];
        $stat[7] = $min[0];
        \App\Stat::record($stat);
    }

    public function command()
    {
        $array = explode(' ', $_GET['command']);
        if ($array[0] == 'add') {
            if (!(count($array) > 2)) {
                if ($array[1] == 1 or $array[1] == 2 or $array[1] == 3 or $array[1] == 4) {
                    $this->addSheep($array[1]);
                    return redirect('/');
                } else {
                    echo "Введены некорректные данные - Номер загона";
                }
            } else {
                echo 'Введено недопустимое количество аргументов';
            }
        } elseif ($array[0] == 'kill') {
            if (!(count($array) > 2)) {
                $sheeps = \App\Sheeps::all('id', 'status')->where('status', 1); 
                foreach ($sheeps as $value) {
                    $id[] = $value->id;
                }
                if (in_array($array[1], $id)) {
                    $this->sheepKill($array[1]);
                    return redirect('/');
                } else {
                    echo "Введены некорректные данные - Номер овечки";
                }
            } else {
                echo 'Введено недопустимое количество аргументов';
            }
        } elseif ($array[0] == 'move') {
            if (!(count($array) > 3)) {
                $sheeps = \App\Sheeps::all('id', 'status')->where('status', 1); 
                foreach ($sheeps as $value) {
                    $id[] = $value->id;
                }
                if (in_array($array[1], $id)) {
                    if ($array[2] == 1 or $array[2] == 2 or $array[2] == 3 or $array[2] == 4) {
                        $this->sheepMove($array[1], $array[2]);
                        return redirect('/');
                    } else {
                        echo "Введены некорректные данные - Номер загона";
                    }
                } else {
                    echo "Введены некорректные данные - Номер овечки";
                }
            } else {
                echo 'Введено недопустимое количество аргументов';
            }
        } else {
            echo "Введена недопустимая команда!";
        }
    }
}