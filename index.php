<?php


interface Bookmaker{
    public function getAllResults();
    public function getResultByMatchId($id);
    public function setBet($user_id, $match_id,$betOn,$result,$time);
    public function getAllMatches();
    public function getMatchById($id);
    public function getAllBetsUsers($id);
}
interface Sort{
    public function sortWinners($users);
}
interface ParimatchDraw{
    public function outputResult($array);
}
class ParimatchSort {
    public function sortWinners($users){
        $masters= [];
        $winners = [];
        $losers = [];
        foreach ($users as $user){
            if($user['result'] == 'Вы выиграли'){
                if($user['betOn'] == 'Точный счет'){
                    $masters[] = $user;
                }
                else{
                    $winners[] = $user;
                }
            }
            else{
                $losers[] = $user;
            }
        }
        return ['masters' => $masters , 'winners' => $winners, 'losers' => $losers];
    }
}
class ParimatchDrawer implements ParimatchDraw{
    public function outputResult($array){
        foreach ($array['masters'] as $master){
            echo 'Самый большой приз получил пользователь с ID = ' . $master['user_id'] . '<br>';
        }
        foreach ($array['winners'] as $winner){
            echo 'Маленький приз получил пользователь с ID = ' . $winner['user_id'] . '<br>';
        }
        foreach ($array['losers'] as $loser){
            echo 'Не повезло пользователю с ID = ' . $loser['user_id'] . '<br>';
        }
    }
}
class Parimatch implements Bookmaker{

    protected $bets = [];
    protected $results = [
        0 =>
            [
                'team01' => 'Барселона',
                'team02' => 'Ювентус',
                'startTime' => 18,
                'result' => [
                    'Точный счет' =>  '2-1',
                    'Победа' => 'Барселона',
                ]

            ],

        1 =>
            [
                'team01' => 'Реал Мадрид',
                'team02' => 'Арсенал',
                'startTime' => 18,
                'result' => [
                    'Точный счет' =>  '3-1',
                    'Победа' => 'Реал Мадрид'
                ],
            ]
    ];
    protected $matches = [
        0 =>
        [
            'team01' => 'Барселона',
            'team02' => 'Ювентус',
            'startTime' => 18,
            'result' => 'Ожидание',
        ],
    ];

    public function getAllResults(){
        return $this->results;
    }
    public function getResultByMatchId($match_id){
        return $this->results[$match_id]['results'];
    }
    public function setBet($user_id, $match_id, $betOn, $result, $betTime){
        if($this->matches[$match_id]['startTime'] > $betTime){
            return array_push($this->bets,['user_id' => $user_id , 'match_id' => $match_id, 'betOn' => $betOn , 'result' => $result]);
        }
        return false;

    }
    public function getAllMatches(){
        return $this->matches;
    }
    public function getMatchById($match_id){
        if(!$this->matches[$match_id]){
            return $this->getResultByMatchId($match_id);
        }
        return $this->matches[$match_id];
    }
    public function getAllBetsUsers($match_id){
        $result = [];
        foreach ($this->bets as $bet){
            if($bet['match_id'] === $match_id){
                if($this->results[$match_id]){
                    array_push($result, ['user_id' => $bet['user_id'], 'betOn' => $bet['betOn']
                    ,'result' => $bet['result'] === $this->results[$match_id]['result'][$bet['betOn']] ? 'Вы выиграли' : 'Вы проиграли']);
                }
                else{
                    array_push($result, $bet);
                }
            }
        }
        return $result;
    }

}

 // Создаем класс париматч
$parimatch = new Parimatch();

// Cовершаем ставки
$human_01 = $parimatch->setBet(0 ,  0,'Точный счет','3-1', 17);
$human_02 = $parimatch->setBet(1 ,  0,'Победа','Барселона' , 17);
$human_03 = $parimatch->setBet(2 ,  0,'Точный счет','2-1', 17);

// Получаем ставки всех пользователей по матчу на который они ставили
$results = $parimatch->getAllBetsUsers(0);

//Cортируем всех победителей и проигравших
$parimatchSort = new ParimatchSort();
$sortWinners = $parimatchSort->sortWinners($results);;

//Отображаем результат ставочников
$parimatchDrawer = new ParimatchDrawer();
$parimatchDrawer->outputResult($sortWinners);