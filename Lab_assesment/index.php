<?php

echo"task -1<br>";

$width = 10 ;

$length= 20;
$area_of_Rectangle =0;
$area_of_Rectangle = $length * $width;

echo"area of Rectangle : $area_of_Rectangle <br>";

$perimeter = 2 * ($length + $width) ;

echo"perimeter : $perimeter<br>";

echo"task -2<br>";

$amount = 100; 
$vat = $amount * 0.15;
echo "Amount: $" . $amount . "<br>";
echo "VAT (15%): $" . $vat . "<br>";
echo "Total: $" . ($amount + $vat);

echo"task -3<br>";

$number = 25; 
if ($number % 2 == 0) {
    echo $number . " is even";
} else {
    echo $number . " is odd";
}
echo"task-4<br>";

$a = 10; 
$b = 25; 
$c = 15;

if ($a >= $b && $a >= $c) {
    echo $a . " is the largest";
} elseif ($b >= $a && $b >= $c) {
    echo $b . " is the largest";
} else {
    echo $c . " is the largest";
}

echo"task -5<br>";


for ($i = 10; $i <= 100; $i++) {
    if ($i % 2 != 0) {
        echo $i . " ";
    }
}

echo"task -6<br>";

$numbers = [3, 7, 2, 9, 4];
$search = 9;
$found = false;

for ($i = 0; $i < count($numbers); $i++) {
    if ($numbers[$i] == $search) {
        $found = true;
        break;
    }
}

if ($found) {
    echo $search . " found in array";
} else {
    echo $search . " not found";
}



// Triangle
for ($i = 1; $i <= 5; $i++) {
    for ($j = 1; $j <= $i; $j++) {
        echo "*";
    }
    echo "<br>";
}

echo "<br>";

// Square
for ($i = 1; $i <= 4; $i++) {
    for ($j = 1; $j <= 4; $j++) {
        echo "*";
    }
    echo "<br>";
}

?>