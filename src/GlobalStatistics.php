<?php
namespace Jinas\Covid19;

use Jinas\Covid19\Http\Client;
use Tightenco\Collect\Support\Arr;

class GlobalStatistics
{
    public $api_response;
    public $updated_at;
    public $api_statuscode;
    public const CASES_ENDPOINT = "https://api.covid19api.com/summary";


     /**
     * FetchCases
     *
     *  Fetch the cases from API
     *
     * @return object
     */
    public function FetchCases() : object
    {
        $client = new Client;
        $data = $client->get($this::CASES_ENDPOINT);
        $this->updated_at = Arr::get($data,'data.Date');
        $this->api_response = Arr::get($data,'data.Countries');
        $this->api_statuscode = Arr::get($data,'status_code');

        return $this;
    }
    
    /**
     * GetTotal
     *
     *  Get Total number of confirmed cases,recovered and deaths globally
     *  
     *  From covid19api API. 
     * @return array
     */
    public function GetTotal() : array
    {
        $cases = collect($this->GetAll());

        $data = [
            'total_confirmed' => $cases->sum('TotalConfirmed'),
            'total_recovered' => $cases->sum('TotalRecovered'),
            'total_deaths' => $cases->sum('TotalDeaths'),
            'total_active' => ($cases->sum('TotalConfirmed') - $cases->sum('TotalRecovered') - $cases->sum('TotalDeaths'))
        ];

        return $data;
    }
    
    /**
     * GetAll
     * 
     *  Get all the country data from covid19api. 
     *
     * @return array
     */
    public function GetAll() : array
    {
        $countries = collect($this->api_response);

        $countries->shift();

        return $countries->toArray();
    }

}