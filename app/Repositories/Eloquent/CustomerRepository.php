<?php
declare(strict_types=1);
namespace App\Repositories\Eloquent;

use GuzzleHttp\Client;
use Illuminate\Container\Container as App;
 
class CustomerRepository extends Repository
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
		return 'App\\Customer';
	 }

	 public function getUserList($request)
	 {
		$data = $request->all();
		$keyword = isset($data['keyword']) ? $data['keyword'] : "";
		$paginate =  $this->model->where(['deleted' => 0])
			->when($keyword,function ($query) use ($keyword){
				$query->where(function ($query) use ($keyword){
					return $query->where('wx_name','like',"%{$keyword}%")
						->orWhere('username','like',"%{$keyword}%")
						->orWhere('tel','like',"%{$keyword}%");
				});
			})->paginate($request->pageSize);
		return collection($paginate);
	 }

	 public function saveUser($request)
	 {
		$data = $request->all();
		$data['password'] = bcrypt($data['password']);
		//dd($data['password']);die();
		$res = $this->model->create($data);
		
		return $this->respondWith(['created' => !!$res, 'customer' => $res]);
	 }
}