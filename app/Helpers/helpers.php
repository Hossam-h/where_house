<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;


if (!function_exists('returnError')) {

    function returnError($msg, $status = 400)
    {
        return response()->json([
            'data' => ['error' => $msg]
        ], $status);
    }
}

if (!function_exists('returnSuccess')) {
    function returnSuccess($msg, $status = 200)
    {
        return response()->json([
            'data' => ['msg' => $msg]
        ], $status);
    }
}

if (!function_exists('returnData')) {
    function returnData($data, $status = 200)
    {
        return response()->json([
            'data' => $data
        ], $status);
    }
}

if (!function_exists('returnPaginatedData')) {
    function returnPaginatedData($data,$extra= null, $status = 200)
    {
        $paginatedData =  $data[0]->toArray();
        $data = [
            'data' => $paginatedData['data'],
            'paginate' => [
                'currentPage' => $paginatedData['current_page'],
                'lastPage'    => $paginatedData['last_page'],
                'perPage'     => $paginatedData['per_page'],
                'total'       => $paginatedData['total']
            ],
        ];

        $extra ? $data += $extra : '';

        return response()->json([
            'data' => $data
        ], $status);
    }
}
if (!function_exists('returnPaginatedResourceData')) {
    function returnPaginatedResourceData($data,$extra= null, $status = 200)
    {
        

        return $data;
        $paginatedData =  (array)$data[0];

        return $paginatedData;
        $data = [
            'data' => $paginatedData['data'],
            'paginate' => [
                'currentPage' => $paginatedData['meta']->current_page,
                'lastPage' => $paginatedData['meta']->last_page,
                'perPage' => $paginatedData['meta']->per_page,
                'total' => $paginatedData['meta']->total
            ],
        ];

        $extra ? $data += $extra : '';

        return response()->json([
            'data' => $data
        ], $status);
    }
}

if (!function_exists('returnPaginatedDataForCollection')) {
    function returnPaginatedDataForCollection($data,$paginatedObject,$extra= null, $status = 200)
    {
        $paginatedData = $paginatedObject->toArray();
        $data = [
            'data' => $data,
            'paginate' => [
                'currentPage' => $paginatedData['current_page'],
                'lastPage' => $paginatedData['last_page'],
                'perPage' => $paginatedData['per_page'],
                'total' => $paginatedData['total']
            ],
        ];

        $extra ? $data += $extra : '';

        return response()->json([
            'data' => $data
        ], $status);
    }
}



if (!function_exists('save_file')) {

    function save_file($file,$path)
    {

        $file_name = uniqid() . '.' .$file->getClientOriginalExtension();
        $dist = public_path($path);                     // $path = 'users/'

        $file->move($dist, $file_name);

        return $file_name;
    }
}



if (!function_exists('send_sms')) {

    function send_sms($number,$message){

        $username = 'morzaq.app@gmail.com';
        $password = '1234679@morzaq';
        $sender = 'YandM Trade';

        $url = "https://smssmartegypt.com/sms/api/?username=$username&password=$password&sendername=$sender&message=$message&mobiles=$number";
        $response = Http::get($url);


        return $response->body();
    }

}


if(!function_exists('convertQuantity')){
    function convertQuantity($quantity , $convert){
        return $quantity * $convert;
    }
}
