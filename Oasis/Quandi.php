<?

namespace Oasis;

class Quandi {

  private $baseUrl   = 'https://www.quandl.com/api/v3/datasets/WIKI/:symbol.json';
  private $url       = null;
  private $transport = null;

  public $data = [];

  public function __construct($apiKey, $symbol, $start_date = null) {
    $this->url = str_replace(':symbol', $symbol, $this->baseUrl);
    $this->transport = new \Guzzle\Http\Client();

    $query = http_build_query([
      'api_key' => $apiKey,
      'start_date' => $start_date
    ]);

    $results = $this->transport->get($this->url.'?'.$query)->send();

    $data = json_decode((string) $results->getBody(), 1);

    $this->data = $data['dataset'];
  }

}