


-Wyscig aut - losuje typ auta z 3 rodzaji: {wolne, duze przyspieszenie, duza predkosc maksymalna).
-Nadaje losowe własciwosci każdemu pojazdowi (losuje po 2 każdego rodzaju)
-Porównuje wyniki na losowym dystansie
-Sortuje listę




<?php
header('Content-Type:text/plain');

class Car
{
    private $name;
    private $acceleration;
    private $max_speed;
    
    public function __construct($name, $acceleration, $max_speed) {
        $this->name = $name;
        $this->acceleration = $acceleration;
        $this->max_speed = $max_speed;
    }
    
    public function getName() {
        return $this->name;
    }
	
    public function getAcceleration() {
        return $this->acceleration;
    }
	
    public function getMaxSpeed() {
        return $this->max_speed;
    }
    
    public function calculateTravelTime($distance) {
        $current_speed = 0;
        $time = 0;
        while($distance > 0) {
            $time++;
            $distance -= $current_speed;
            if ($current_speed < $this->max_speed) {
                $current_speed += $this->acceleration;
            }
            if ($current_speed > $this->max_speed) {
                $current_speed = $this->max_speed;
            }
        }
        
        return $time;
    }
}

abstract class CarGeneratorType
{
    const SPORT = 1;
    const HI_ACCELERATION = 2;
    const HI_MAX_SPEED = 3;
    const SLOW = 4;    
}

abstract class CarGenerator
{
    const MIN_ACCELERATION = 10;
    const NORMAL_ACCELERATION = 30;
    const MAX_ACCELERATION = 50;
    
    const MIN_MAX_SPEED = 60;
    const NORMAL_MAX_SPEED = 180;
    const MAX_MAX_SPEED = 360;
    
    public static function generate($type) {
        switch($type) {
            case CarGeneratorType::SPORT:
                return new Car(
                    'SPORT', 
                    mt_rand(CarGenerator::NORMAL_ACCELERATION, CarGenerator::MAX_ACCELERATION),
                    mt_rand(CarGenerator::NORMAL_MAX_SPEED, CarGenerator::MAX_MAX_SPEED));
                    break;
                    
            case CarGeneratorType::HI_ACCELERATION:
                return new Car(
                    'HI_ACCELERATION', 
                    mt_rand(CarGenerator::NORMAL_ACCELERATION, CarGenerator::MAX_ACCELERATION),
                    mt_rand(CarGenerator::MIN_MAX_SPEED, CarGenerator::NORMAL_MAX_SPEED));
                    break;
                    
            case CarGeneratorType::HI_MAX_SPEED:
                return new Car(
                    'HI_MAX_SPEED', 
                    mt_rand(CarGenerator::MIN_ACCELERATION, CarGenerator::NORMAL_ACCELERATION),
                    mt_rand(CarGenerator::NORMAL_MAX_SPEED, CarGenerator::MAX_MAX_SPEED));
                    break;
                    
            case CarGeneratorType::SLOW:
                return new Car(
                    'SLOW', 
                    mt_rand(CarGenerator::MIN_ACCELERATION, CarGenerator::NORMAL_ACCELERATION),
                    mt_rand(CarGenerator::MIN_MAX_SPEED, CarGenerator::NORMAL_MAX_SPEED));
                    break;
                    
            default:
                throw new Exception("Unknow car type");
            
        }
    }
}

class Race
{
    private $cars;
    private $distance;
    private $result;
    
    public function __construct($distance, $cars) {
        $this->distance = $distance;
        $this->cars = $cars;
    }
    
    public function run() {
        $result = [];
        foreach($this->cars as $car) {
            $result[] = [
                'car' => $car,
                'time' => $car->calculateTravelTime($this->distance)
            ];
        }
        
        return $result;
    }
}


$cars = [];

$assetTypes = [
    CarGeneratorType::SPORT, CarGeneratorType::SPORT, 
    CarGeneratorType::HI_ACCELERATION, CarGeneratorType::HI_MAX_SPEED,
    CarGeneratorType::SLOW, CarGeneratorType::SLOW
];

foreach($assetTypes as $type) {
    $cars[] = CarGenerator::generate($type);
}

$distance = mt_rand(10, 1000);
$race = new Race($distance, $cars);
$raceResult = $race->run();

usort($raceResult, function($a, $b) {
    if ($a['time'] == $b['time']) {
        return 0;
    }
    return ($a['time'] < $b['time']) ? -1 : 1;
});

$format = "%-15s | %-16s | %-4s | %-14s | %s\n";

echo "Dystans: $distance\n\n";

printf($format,
	"Nazwa",
	"Średnia prędkość",
	"Czas",
	"Przyspieszenie",
	"Maksymalna prędkość");
	

foreach($raceResult as $score) {
    printf($format,
		$score['car']->getName(),
		number_format($distance/$score["time"], 2),
		$score["time"],
		$score['car']->getAcceleration(),
		$score['car']->getMaxSpeed());

}