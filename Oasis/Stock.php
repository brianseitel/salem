<?

namespace Oasis;

class Stock {

  public $name      = '';

  private $columns   = [];
  private $data      = [];

  public function load($raw_data) {
    $this->columns = fetch($raw_data->data, 'column_names');

    $this->name = array_shift(explode('(', $raw_data->data['name']));
    foreach ($raw_data->data['data'] as $row) {
      $this->data[$row[0]] = array_combine($this->columns, $row);
    }
  }

  public function getRowByDate($date) {
    return fetch($this->data, $date, 'Invalid date');
  }

  public function getMovingAverage() {
    $sum = 0;
    foreach ($this->data as $row) {
      $sum += $row['Close'];
    }

    return number_format($sum / count($this->data), 2);
  }

  public function getRange() {
    $max = 0;
    $min = 999999;

    foreach ($this->data as $row) {
      if ($row['Low'] < $min) {
        $min = $row['Low'];
      }

      if ($row['High'] > $max) {
        $max = $row['High'];
      }
    }

    return ['high' => $min, 'low' => $max];
  }

  public function getCurrentPrice() {
    $first = array_shift($this->data);

    array_unshift($this->data, $first);

    return $first['Close'];
  }

  public function getStandardDeviation() {
    $values = [];
    foreach ($this->data as $row) {
      $values[] = $row['Close'];
    }

    return std($values);
  }
}

function std($aValues) {
    $fMean = array_sum($aValues) / count($aValues);
    //print_r($fMean);
    $fVariance = 0.0;
    foreach ($aValues as $i)
    {
        $fVariance += pow($i - $fMean, 2);

    }
    $size = count($aValues) - 1;
    return (float) sqrt($fVariance)/sqrt($size);
}