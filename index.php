<?php

//Abstract class that implements parkable and defines default configuration for parkable vehicles
abstract class Parkable //implements Parkable
{
    private $registry;
    private $model;
    private $parkHour;

    function __construct($licensePlate, $model, $time)
    {
        $this->registry = $licensePlate;
        $this->model = $model;
        $this->parkHour = $time;
    }

    function getDetails()
    {
        $carDetails = [
            'registry' => $this->registry,
            'model' => $this->model,
            'parkHOur' => $this->parkHour,
        ];

        return $carDetails;
    }

    function getTime()
    {
        return $this->parkHour;
    }
}

// Class that stores car information
class Car extends Parkable
{
    private $type = "car";

    /*OTHER CAR SPECIFIC METHODS HERE*/
}

//Class that stores motorcycle information
class Motorcycle extends Parkable
{
    private $type = "motorcycle";

    /*OTHER MOTORCYCLE SPECIFIC METHODS HERE*/
}

// Class that defines the state of a spot
class Spot
{
    public $status;
    private $carDetails;

    function __construct()
    {
        $this->status = true;
        $this->carDetails = null;
    }

    function parkVehicle(Parkable $newVehicle)
    {
        $this->carDetails = $newVehicle;
        $this->status = false;
    }

    function getParkTime()
    {
        return $this->carDetails->getTime();
    }

    function unParkCar()
    {
        $this->carDetails = null;
        return true;
    }

    function getVehicleDetails()
    {
        return $this->carDetails->getDetails();
    }

    function isVacant()
    {
        return $this->status;
    }
}

class Prices
{
    private $rules;

    function __construct($rules)
    {
        $this->rules = $rules;
    }

    function getAmount($totalTime)
    {
        /*CHECK RULES AND TOTAL PARKED TIME TO GENERATE TOTAL AMOUNT TO BE PAID*/
        return 'R$50,00';
    }

    function printReceipt($carDetails, $totalTime)
    {
        return "O VEICULO " . $carDetails['registry'] . " PAGOU " . $this->getAmount($totalTime) . "PELO TEMPO " . $totalTime;
    }
}

// Class that defines the parking lot space
class ParkingLot
{
    private $totalSpots;
    private $emptySpots;
    private $spotsDetails;
    private $prices;

    function __construct($numberOfSpots, $prices)
    {
        $this->emptySpots = $numberOfSpots;
        $this->totalSpots = $numberOfSpots;
        $this->prices = $prices;

        foreach (range(0, $numberOfSpots - 1) as $i) {
            $this->spotsDetails[$i] = new Spot();
        }
    }

    function parkVehicle(Parkable $newVehicle)
    {
        foreach (range(0, $this->totalSpots - 1) as $i) {
            if ($this->spotsDetails[$i]->isVacant()) {
                $this->spotsDetails[$i]->parkVehicle($newVehicle);
                echo "CARRO ESTACIONADO NA VAGA " . $i . " ! <br>";

                $this->emptySpots--;

                return true;
            }
        }
    }

    function getTotalAmount($registry)
    {
        foreach (range(0, $this->totalSpots - 1) as $i) {
            if ($this->spotsDetails[$i]->checkHevicle($registry)) {
                $totalTime = $this->spotsDetails[$i]->getParkTime();
                $totalAmount = $this->prices->getAmount($totalTime);

                $paymentDetails = [
                    'vehicle' => $registry,
                    'spot' => $i,
                    'timeSpent' => $totalAmount,
                ];

                return $paymentDetails;
            }
        }

        return null;
    }

    function confirmPayment($spot)
    {
        $carDetails = $this->spotsDetails[$spot]->getVehicleDetails();
        $totalTime = $this->spotsDetails[$spot]->getParkTime();
        $receipt = $this->prices->printReceipt($carDetails, $totalTime);

        $this->spotsDetails[$spot]->freeSpot();
        echo "VAGA " . $spot . " LIBERADA ! <br>";

        $this->emptySpots++;

        return $receipt;
    }
}

$meuEstacionamento = new ParkingLot(5, 'PRICING RULES');

echo "TESTING SPOTS CREATION <br> <pre>";
print_r($meuEstacionamento);
echo "</pre>";
