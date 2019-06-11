<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Eloquent\CustomerRepository as Customer;

class CustomerController extends Controller
{
    private $customer;
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
    
    /**
     * 用户列表
     */
    public function getUserList(Request $request)
    {
        $result = collect($this->customer->getUserList($request))->toJson();
        return $result;
    }

    /**
     * 添加用户
     */
    public function saveUser(Request $request)
    {
        $result = collect($this->customer->saveUser($request))->toJson();
        return $result;
    }

    /**
     * 得到用户信息
     */
    public function getUserInfo(Request $request)
    {
       
        $result = 'aaa';
        return $result;
    }
}
