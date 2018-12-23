<?php
declare(strict_types=1);
namespace App\Repositories\Eloquent;

use GuzzleHttp\Client;
use Illuminate\Container\Container as App;
 
class TestRepository extends Repository
{
  
 	public function __construct(Client $http, App $app)
    {
        parent::__construct($app);
        $this->http = $http;
    }
	 /**
	  * Specify Model class name
	  * 
	  * @return string
	  */
	 public function model()
	 {
	  	//set model name in here, this is necessary!
	 }
}