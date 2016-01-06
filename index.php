<?
require 'vendor/autoload.php';
require 'Oasis/Stock.php';

const COLUMN_WIDTH      = 18;
const STRONG_MULTIPLIER = 4;
const MULTIPLIER        = 2;

$config = json_decode(file_get_contents('./.config'), 1);

date_default_timezone_set('America/Los_Angeles');

$start_date = date('Y-m-d', strtotime('-50 day'));

$symbols = ['Z', 'BA', 'HOG', 'TSLA', 'AIZ', 'CCE', 'AMZN', 'DIS', 'PCG', 'PG'];
sort($symbols);

$columns = [
  'Name',
  'Current Price',
  'Average',
  'Low',
  'High',
  'Standard Deviation',
  // MULTIPLIER .'x',
  // STRONG_MULTIPLIER.'x',
  'Difference',
  'Recommended Action'];

$line = '';
foreach ($columns as $k) {
  $line .= str_pad($k, COLUMN_WIDTH, ' ', STR_PAD_RIGHT) . ' | ';
}
echo $line."\n";
echo str_repeat('-', strlen($line))."\n";

foreach ($symbols as $symbol) {
  try {
    $q = new Oasis\Quandi($config['api_key'], $symbol, $start_date);
    $stock = new Oasis\Stock;
    $stock->load($q);

    $stats = [
      'name'          => trim($stock->name),
      'current_price' => '$'.number_format($stock->getCurrentPrice(), 2),
      'mean'          => '$'.number_format($stock->getMovingAverage(), 2),
      'low'           => '$'.number_format($stock->getRange()['low'], 2),
      'high'          => '$'.number_format($stock->getRange()['high'], 2),
      'std'           => $stock->getStandardDeviation(),
      // 'x'             => MULTIPLIER * $stock->getStandardDeviation(),
      // 'x^n'           => STRONG_MULTIPLIER * $stock->getStandardDeviation(),
      'diff'          => ($stock->getCurrentPrice() - $stock->getMovingAverage()) . ' (' . (100 - number_format($stock->getCurrentPrice() / $stock->getMovingAverage() * 100, 2)) . '%)'
    ];

    $action = null;

    $current     = $stats['current_price'];
    $diff        = $stock->getCurrentPrice() - $stock->getMovingAverage();
    $strong_sell = STRONG_MULTIPLIER * $stats['std'];
    $sell        = MULTIPLIER * $stats['std'];
    $strong_buy  = STRONG_MULTIPLIER * $stats['std'];
    $buy         = MULTIPLIER * $stats['std'];


    $abs = abs($diff);
    if ($diff > 0) {
      if ($abs > $strong_sell) {
        $action = 'STRONG SELL';
      } else if ($abs > $sell) {
        $action = 'SELL';
      } else {
        $action = 'HOLD';
      }
    } else {
      if ($abs > $strong_buy) {
        $action = 'STRONG BUY';
      } else if ($abs > $buy) {
        $action = 'BUY';
      } else {
        $action = 'HOLD';
      }
    }

    $stats['action'] = $action;

    foreach ($stats as $stat => $value) {
      echo substr(str_pad($value, COLUMN_WIDTH, ' ', STR_PAD_RIGHT), 0, COLUMN_WIDTH) . ' | ';
    }
    echo "\n";
  } catch (Exception $e) {
    pd($e);
  }
}
echo str_repeat('-', strlen($line))."\n";

function fetch($array, $key, $default = '') {
  if (array_key_exists($key, $array)) {
    return $array[$key];
  }

  return $default;
}

function pp($array) {
  print_r($array);
  echo "\n";
}

function pd($array) {
  pp($array); die();
}