<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ApiRepositoryInterface;
use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use DB;
/**
 * Repository 抽象类
 *
 * Class Repository
 * @package App\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface, ApiRepositoryInterface
{
    private $app;
    protected $model;
    protected $userInfo;
    // protected $carrierInfo;
    // protected $facilitatorInfo;
    protected $statusCode = 200;

    /**
     * 依赖注入 Container与创建模型
     *
     * Repository constructor.
     * @param $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->userInfo = auth('api')->user();
        // $this->carrierInfo = auth('carrier')->user();
        // $this->facilitatorInfo = auth('facilitator')->user();
        $this->makeModel();
    }

    /**
     * 指定模型名称
     *
     * @return mixed
     */
    abstract function model();

    /**
     * 根据模型名创建Eloquent ORM 实例
     *
     * @return bool|\Illuminate\Database\Eloquent\Builder
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());
        if (!$model instanceof Model) {
            return false;
        }
        return $this->model = $model;
    }

    /*
    |--------------------------------------------------------------------------
    | 数据库相关
    |--------------------------------------------------------------------------
    |
    | 含有数据库的CRUD操作,分页等
    |
    |
    */

    /**
     * 根据主键查找数据
     *
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        return $this->model->find($id, $columns);
    }

    /**
     * 根据指定键与值查找数据
     *
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*'))
    {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * 获取所有数据
     *
     * @param array $columns
     * @return mixed
     */
    public function all($columns = array('*'))
    {
        return $this->model->get($columns);
    }

    /**
     * 预加载
     *
     * @param $relations
     * @return mixed
     */
    public function with($relations)
    {
        return $this->model->with(is_string($relations) ? func_get_args() : $relations);
    }

    /**
     * 批量创建
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * 根据主键更新
     *
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute = 'id')
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * 根据主键删除数据
     *
     * @param $ids
     * @return mixed
     */
    public function delete($ids)
    {
        return $this->model->destroy($ids);
    }

    /**
     * @param $id
     * Description : 查找单条记录
     * User : kesongbing@qq.com
     * Time : 2018/9/6 0:18
     */
    public function getById($id,$columns = array('*'))
    {
        $row = $this->model->whereIdAndDeleted($id,0)->first($columns);
        return $row;
    }

    /**
     * 获取分页数据
     *
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 10, $columns = array('*'))
    {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param $id
     * @return array|\Illuminate\Database\Query\Builder[]|Collection
     * Description : 查询指定区域下所有的区域
     * User : kesongbing@qq.com
     * Time : 2018/9/14 14:18
     */
    public function findRegionById($id)
    {
        //查询指定区域下所有的区域
        $regionRow = DB::table("da_region")->whereDeletedAndId(0,$id)->first();
        $depth = $regionRow->depth . "-" . $regionRow->id;
        $list = DB::table("da_region")->where('deleted',0)->Where("depth","like",$depth . "%")->get();
        return $list;
    }

    /*
    |--------------------------------------------------------------------------
    | API相关
    |--------------------------------------------------------------------------
    |
    |
    |
    |
    */

    /**
     * 获取状态码
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * 设置状态码
     *
     * @param $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * 根据数据类型来产生响应
     *
     * @param $data
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function respondWith($data, array $headers = [])
    {
        if (!$data) {
            return $this->errorNotFound('Requested response not found。');
        } elseif ($data instanceof Collection || $data instanceof LengthAwarePaginator || $data instanceof Model) {
            return $this->respondWithItem($data, $headers);
        } elseif (is_string($data) || is_array($data)) {
            return $this->respondWithArray($data, $headers);
        } else {
            return $this->errorInternalError();
        }
    }

    /**
     * 产生响应并处理Collection对象或Eloquent模型
     *
     * @param $item
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithItem($item, array $headers = [])
    {
        $response = response()->json($item->toArray(), $this->statusCode, $headers);
        return $response;
    }

    /**
     * 产生响应并处理数组或字符串
     *
     * @param array $array
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithArray(array $array, array $headers = [])
    {
        $response = response()->json($array, $this->statusCode, $headers);
        return $response;
    }

    /**
     * 产生响应并且返回错误
     *
     * @param $message
     * @param $errorCode
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($message, $errorCode)
    {
        return $this->respondWithArray([
            'error' => [
                'code' => $errorCode,
                'http_code' => $this->statusCode,
                'message' => $message
            ]
        ]);
    }

    /**
     * 请求不允许
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorForbidden($message = 'Forbidden')
    {
        return $this->setStatusCode(403)->respondWithError($message, self::CODE_FORBIDDEN);
    }

    /**
     * 服务器内部产生错误
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorInternalError($message = "Internal Error")
    {
        return $this->setStatusCode(500)->respondWithError($message, self::CODE_INTERNAL_ERROR);
    }

    /**
     * 没有找到指定资源
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorNotFound($message = 'Resource Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($message, self::CODE_NOT_FOUND);
    }

    /**
     * 请求授权失败
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnauthorized($message = "Unauthorized")
    {
        return $this->setStatusCode(401)->respondWithError($message, self::CODE_UNAUTHORIZED);
    }

    /**
     * 请求错误
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorWrongArgs($message = 'Wrong Arguments')
    {
        return $this->setStatusCode(400)->respondWithError($message, self::CODE_WRONG_ARGS);
    }

    /**
     * 无法处理的请求实体
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorUnprocessableEntity($message = "Unprocessable Entity")
    {
        return $this->setStatusCode(422)->respondWithError($message, self::CODE_UNPROCESSABLE_ENTITY);
    }

    /**
     * 自定义验证数据
     *
     * @param Request $request
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     * @return mixed|void
     */
    public function validate(Request $request, array $rules, array $messages = [], array $customAttributes = [])
    {
        Validator::make($request->all(), $rules, $messages, $customAttributes)->validate();
    }

    /*
    |--------------------------------------------------------------------------
    | 杂项
    |--------------------------------------------------------------------------
    |
    | 
    |
    |
    */
    /**
     * 格式化时间
     *
     * @param $date
     * @return mixed
     */
    public function transformTime($date)
    {
        return \Carbon\Carbon::parse($date)->diffForHumans();
    }

    /**
     * @param $file
     * @return string
     * Description : 图片上传
     * User : kesongbing@qq.com
     * Time : 2018/9/14 11:37
     */
    function upload_img($file)
    {
        $url_path = 'uploads/cover';
        $rule = ['jpg', 'png', 'gif'];
        if ($file->isValid()) {
            $clientName = $file->getClientOriginalName();
            $tmpName = $file->getFileName();
            $realPath = $file->getRealPath();
            $entension = $file->getClientOriginalExtension();
            if (!in_array($entension, $rule)) {
                return '图片格式为jpg,png,gif';
            }
            $newName = md5(date("Y-m-d H:i:s") . $clientName) . "." . $entension;
            $path = $file->move($url_path, $newName);
            $namePath = $url_path . '/' . $newName;
            return $namePath;
        }
    }

    //地址解析
    public function addressToLatLng($address)
    {
        $url = "http://api.map.baidu.com/geocoder/v2/?address=$address&output=json&ak=L8LQC8UQlksP2bR7MKAqV3zn";
        $res = do_curl($url,false,0);
        $res = json_decode($res);
        return $res;
    }

    public function geocoder($lat,$lng)
    {
        $url = "http://api.map.baidu.com/geocoder/v2/?location=$lat,$lng&output=json&pois=0&ak=L8LQC8UQlksP2bR7MKAqV3zn";
        $res = do_curl($url,false,0);
        $res = json_decode($res);
        return $res;
    }

    /**
     * @param $data
     * @Time: 2018/11/13 09:59:01
     * @User: kesongbing
     * @Description:小程序模板消息
     */
    public function templateMessage($data,$appid)
    {
        $access_token = $this->returnAsskey($appid);
        if(!$access_token){
            return '';
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
        $res = do_curl($url,$data,1,0,'json');//将data数组转换为json数据
        return $res;
    }

    public function returnAsskey($appid)
    {
        //根据appid获取对应的秘钥
        if($appid == getenv("CBS_APP_ID")){
            $appsecert = getenv("CBS_APP_SECRET");
        }else if($appid == getenv("FWS_APP_ID")){
            $appsecret = getenv("FWS_APP_SECRET");
        }else{
            $appsecret = getenv("KHD_APP_SECRET");
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret='. $appsecret;
        $ass_key = do_curl($url);
        $ass_key = json_decode($ass_key);
        if(isset($ass_key->errcode)){
            return '';
        }
        $access_token = $ass_key->access_token;
        return $access_token;
    }

    //客户版模板消息
    public function templateMessageByCustomer($data,$appid,$appSecert)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret='. $appSecert;
        $ass_key = do_curl($url);
        $ass_key = json_decode($ass_key);
        if(isset($ass_key->errcode)){
            return '';
        }
        $access_token = $ass_key->access_token;
        $url = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token='.$access_token;
        $res = do_curl($url,$data,1,0,'json');//将data数组转换为json数据

        return $res;
    }

    //微信token
    public function returnWxToken($appid,$appsecret)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appid . '&secret='. $appsecret;
        $ass_key = do_curl($url);
        $ass_key = json_decode($ass_key);
        if(isset($ass_key->errcode)){
            return '';
        }
        $access_token = $ass_key->access_token;
        return $access_token;
    }

    //获取微信模板列表
    public function getWxTemplateList($access_token,$offset = 0, $count = 20)
    {
        $url = "https://api.weixin.qq.com/cgi-bin/wxopen/template/list?access_token=" . $access_token;
        $data = [
            "offset" =>  $offset,
            "count" => $count
        ];

        $res = do_curl($url,$data,1,1,'json');
        $res = json_decode($res);
        if($res->errcode != 0){
            return '';
        }
        return $res->list;
    }

}