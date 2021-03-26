<?php

namespace App\Services;

use Validator;
use Log;
use Illuminate\Http\Request;
use App\Util\Api;
use App\Component\Auth\AuthComponent;
use App\Util\ComponentHelperTraits;
use App\Exceptions\Handler;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeService extends Service {

    private $authComponent;
    private $fileUpload_ext = ['jpg', 'jpeg', 'png'];

    public function __construct(AuthComponent $auth){

        $this->authComponent = $auth;
    }

    public function userCheck(Request $request) {
        $data = $request->all();        

        $rules = [
            "empCode" => "required|string"
        ];

        $validator = $this->validator($data["data"], $rules);
       
        if ($validator !== false) 
        {
            return $validator;
        }

        $empData = $data["data"];
        $response  = $this->authComponent->employeeCheck($empData);

        return $this->showResponse($response);
    }

    public function login(Request $request) {
        $data = $request->all();
        $action = $data['action'];

        if ($action == "login") {
            $rules = [
                "empCode" => "required|string",
                "password" => "required|string"
               
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false) 
            {
                return $validator;
            }

            $response = $this->authComponent->loginCheck($data['data']);
        } else if ($action == "register") {
            $rules = [
                "empCode" => "required|string",
                "password" => "required|string",
                "confirmPassword" => "required|string",
                "userName" => "required|string"
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false) 
            {
                return $validator;
            }

            $response = $this->authComponent->register($data['data']);
        }
        return $response;
    }

    public function changePassword(Request $request){
        $data = $request->all();
        $action = $data['action'];  

        if ($action == "changePassword") {
            $rules = [
                "empCode" => "required|string",
                "oldPassword" => "required|string",
                "newPassword" => "required|string",
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false)
            {
                return $validator;
            }

            $response = $this->authComponent->changePassword($data['data']);
        }
        return $this->showResponse($response);
    }

    public function forgotPassword(Request $request){
        $data = $request->all();
        $action = $data['action'];  

        if ($action == "forgotPassword") {
            $rules = [
                "empCode" => "required|string",
                "oldPassword" => "required|string",
                "newPassword" => "required|string",
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false)
            {
                return $validator;
            }

            $response = $this->authComponent->changePassword($data['data']);
        }
        return $this->showResponse($response);
    }

    public function getProfileDetails(Request $request){
        $data = $request->all();
        $action = $data['action'];  

        if ($action == "profileDetails") {
            $rules = [
                "empCode" => "required|string",
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false)
            {
                return $validator;
            }
            
            $response = $this->authComponent->getProfileDetails($data['data']);
        }
        return $this->showResponse($response);
    }

    public function profileDetailsUpdate(Request $request){
        $data = $request->all();
        $action = $data['action'];  

        if ($action == "profileUpdate") {
            $rules = [
                "empCode" => "required|string",
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false)
            {
                return $validator;
            }

            $empData['empCode'] = $data['data']['empCode'];
            $response  = $this->authComponent->employeeCheck($empData);
            if($response['status'] <> 3){
                $response = $this->authComponent->profileDetailsUpdate($data['data']);
            }
        } else {
            $response = [ "errors" => ["attribute" => "action", "message" => "Invalid action" ]];
        }

        return $this->showResponse($response);
    }

    public function profileFilter(Request $request){
        $data = $request->all();
        $action = $data['action'];  

        if ($action == "profileFilter") {
            $rules = [
                "empCode" => "string",
                "name" => "string",
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false)
            {
                return $validator;
            }
            
            $response = $this->authComponent->profileFilterData($data['data']);
        }
    }

    public function getAllGroup(Request $request) {
        $data = $request->all();
        $action = $data['action'];   

        $rules = [
            "userId" => "required|string"
        ];

        $validator = $this->validator($data["data"], $rules);
       
        if ($validator !== false) 
        {
            return $validator;
        }

        $empData = $data["data"];
        $response  = $this->authComponent->getCommunDetails($empData);

        return $this->showResponse($response);
    }

    public function userToGroup(Request $request){
        $data = $request->all();
        $action = $data['action'];  

        if ($action == "userToGroup") {
            $rules = [
                "communityType" => "required|string",
                "communityName" => "required|string",
                "adminUserId" => "required|string",
                "accessType" => "required|string",
            ];

            $validator = $this->validator($data["data"], $rules);
       
            if ($validator !== false)
            {
                return $validator;
            }
            
            $response = $this->authComponent->saveuserToGroup($data['data']);
        }
        return $this->showResponse($response);
    }

    private function save_base64_image($base64_image_string, $output_file_without_extension, $path_with_end_slash="" ) {
        $splited = explode(',', substr( $base64_image_string , 5 ) , 2);
        $mime=$splited[0];
        $data=$splited[1];
    
        $mime_split_without_base64=explode(';', $mime,2);
        $mime_split=explode('/', $mime_split_without_base64[0],2);

        if(count($mime_split)==2)
        {
            $extension=$mime_split[1];
            if($extension=='jpeg')$extension='jpg';
            $output_file_with_extension=$output_file_without_extension.'.'.$extension;
        }
        file_put_contents( $path_with_end_slash . $output_file_with_extension, base64_decode($data) );
        return $output_file_with_extension;
    }

    public function profilePictureUpdate(Request $request){
        $data = $request->all();

        $rules = [
            "empCode"   => "required|string",
            "image"     => "required|string"
        ];

        $validator = $this->validator($data, $rules);
       
        if ($validator !== false) 
        {
            return $validator;
        }

        $img = base64_decode($data['image']);
        $picName = $data['empCode'] . '_profilepic';
        $fname = $this->save_base64_image($data['image'], $picName, storage_path('app/public/profilePics')."/");

        //$success = file_put_contents(storage_path('app/public/profilePics')."/".$picName, $data);
        $profile = array();
        $profile['empCode']    = $data['empCode'];
        $profile['profilePic'] = $fname;
        $profile['imageUrl']   = env('IMG_BASE_URL')."/".$fname;
        $response = $this->authComponent->profileDetailsUpdate($profile);
        return $this->showResponse($response);
        exit;
        /*
        $pic = $request->file("image");
        if(!empty($pic)){
            $picName    = $pic->getClientOriginalName();
            $extension  = $pic->extension();
            $picName    = $data['empCode'] . '_profilepic.' . $extension;
            $response   = ["pic" => $picName];
            $folder     = "profilePics";
            try{
                if (!Storage::exists($folder)) {
                    Storage::makeDirectory($folder);
                }

                $profile = array();
                $pic->move(storage_path('app/public/profilePics'), $picName);
                $profile['empCode']    = $data['empCode'];
                $profile['profilePic'] = $picName;
                $response = $this->authComponent->profileDetailsUpdate($profile);
            } catch(FileExcception $e){
                $response = ["status" => "error", "message" => "Image upload failed. ".$e->getMessage()];
            }

        } else {
            $response = [ "errors" => ["attribute" => "image", "message" => "Image is required" ]];
        }
        return $this->showResponse($response);
        */
    }
}