<?php

namespace App\Repositories;

use App\Entities\Users;
use App\Entities\UsersProfile;
use App\Entities\CommunityGroup;
use App\Entities\CommunityGroupDetails;
use Prettus\Repository\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

/**
 * @package namespace App\Repository;
 */

class AuthRepository extends BaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Users::class;
    }

    public function getEmployee($data) {
        $user = $this->model->where('empCode', $data['empCode'])->first();

        return $user;
    }

    public function getLoginDetails($data) {
        $response = []; 
        $resData = $this->model->where('empCode', $data['empCode'])->first();

        if (empty($resData) === false) {
            $response['_id'] = $resData['_id'];
            $response['passwordCheck'] = Hash::check($data['password'], $resData->password);
            $response['empCode'] = $resData['empCode'];
            $response['userName'] = empty($resData['userName']) === false ? $resData['userName'] : "";
            $response['imageUrl'] = empty($resData['imageUrl']) === false ? $resData['imageUrl'] : "";
            $response['role'] = empty($resData['role']) === false ? $resData['role'] : "";
            $response['location'] = empty($resData['location']) === false ? $resData['location'] : "";
            
            $token = JWTAuth::fromUser($resData);
            $response['token'] = $token;
            
            // $tok = JWTAuth::getToken();
            // $parsed = $token = JWTAuth::getPayload($tok);
            // $ur = JWTAuth::toUser($tok);

        } else {
            $response['passwordCheck'] = false;
        }
        return $response;
    }

    public function registerDetails($data){
        
        if(!empty($this->getEmployee($data))){
            $response['userCheck'] = false;
        }

        $user             = new $this->model;
        $user->empCode    = $data['empCode'];
        $user->userName   = $data['userName'];
        $user->password   = Hash::make($data['password']);
        $user->created_at    = Carbon::now()->toDateTimeString();
        $user->updated_at    = Carbon::now()->toDateTimeString();
        $user->save();

        if(!empty($this->getEmployee($data))){
            $response['_id'] = $user['_id'];
            $response['empCode'] = $user['empCode'];
            $response['userName'] = empty($user['userName']) === false ? $user['userName'] : "";
            $response['role'] = empty($user['role']) === false ? $user['role'] : "";
            $response['location'] = empty($user['location']) === false ? $user['location'] : "";
            $response['userCheck'] = true;
        }

        return $response;
    }

    public function updateProfile($data){
        $update = UsersProfile::where('empCode',$data['employeeCode'])->update(['profileDesc'=>$data['profileDesc'],'education'=>$data['education'],'skills'=>$data['skills'],'rewards'=>$data['rewards']]);
        return true;
    }

    public function updateProfileImage($data){
        echo $data['profileImage'];
        $update = UsersProfile::where('empCode',$data['employeeCode'])->update(['profileDesc'=>$data['profileDesc'],'education'=>$data['education'],'skills'=>$data['skills'],'rewards'=>$data['rewards']]);
        return true;
    }

    public function changePasswordDetails($data){
        $changePassword = $this->model->where('empCode', $data['empCode'])->first();
        return $changePassword;
    }

    public function updatechangePassword($data){
        $update = Users::where('empCode',$data['empCode'])->update(['password'=>Hash::make($data['newPassword'])]);
        return true;
    }

    public function getEmailDetailsUser($data){
        $forgotPassword = Users::where('empCode', $data['empCode'])->where('email', $data['email'])->first();
        if(empty($forgotPassword) === false){
            return true;
        }else{
            return false;
        }  
    }

    public function getEmployeeprofile($data){
        $profiledetails = $this->model->where('empCode', $data['empCode'])->first();
        if(empty($profiledetails) === false){
            $profiledetails['profilePic'] = url('profilePics')."/".$profiledetails['profilePic'];
            return $profiledetails;
        }else{
            return false;
        }        
    }

    public function getProfiles($data) {
        $getProfiles = Users::where('userId', $data['empCode'])->first();
        $getProfiles['profilePic'] = url('profilePics').$getProfiles['profilePic'];
        return $getProfiles;
    }

    public function insertprofileDetails($data){

        if(!empty($this->getProfiles($data))){
            return false;
        }

        $UsersProfile                 = new Users;
        $userProfile->empCode         = $data['empCode'];
        $userProfile->Name            = $data['name'];
        $userProfile->MobileNumber    = $data['mobileNumber'];
        $userProfile->PersonalEmailID = $data['personalEmailID'];
        $userProfile->created_at      = Carbon::now()->toDateTimeString();
        $userProfile->updated_at      = Carbon::now()->toDateTimeString();
        $userProfile->save();

        if(!empty($this->getProfiles($data))){  
            return true;
        }
    }

    public function profileUserUpdate($data) {
        $profile_update = [];
        $userProfile = Users::where('empCode',$data['empCode'])->first();
        $userData = json_decode(json_encode($userProfile), true);
        $postData = $data;
        $empCode = $userProfile['empCode'];
        if($userData != null){
            $skillEdu = $this->compareSkillsEducation($userData, $postData);
            unset($postData['skills']);
            unset($userData['skills']);
            unset($postData['education']);
            unset($userData['education']);
            unset($userData['created']);
            unset($userData['updated']);
            unset($userData['created_at']);
            unset($userData['updated_at']);
            unset($userData['rewards']);
            unset($postData['rewards']);


            $array_value = array_unique(array_merge($userData,$postData));
            $array_value = array_merge($array_value, $skillEdu);
            
            $array_json = json_encode($array_value);
            $profile_update = json_decode($array_json, true);
            unset($profile_update['_id']);
        }

        if(!empty($empCode)){
            $profileInsert = Users::where('empCode',$data['empCode'])->update($profile_update);
            // return true;
        }else{
            // $userIdCheck = Users::where('empCode',$data['empCode'])->first();
            // if($userIdCheck == null){
                $data['created_at'] = Carbon::now()->toDateTimeString();
                $profileUpdate = Users::insert($data);
                // return true;
            // }

        }
        return true;

    }

    public function compareSkillsEducation($userData, $postData){
        $diff = array();
        if(isset($postData['skills']) && isset($userData['skills']) && count($postData['skills']) == count($userData['skills'])){
            //$diff['skills'] = array_merge($userData['skills'], $postData['skills']);
            $diff['skills'] = $postData['skills'];
        } else {
            $diff['skills'] = empty($postData['skills']) ? $userData['skills'] : $postData['skills'];
        }
        if(isset($postData['education']) && isset($userData['education']) && count($postData['education']) == count($userData['education'])){
            $diff['education'] = $postData['education'];
        } else {
            $diff['education'] = empty($postData['education']) ? $userData['education'] : $postData['education'];
        }
        return $diff;
    }

    public function profileFilterData($data){
        $empCode = $data['empCode'];
        $name = $data['name'];
        $filterProfile = Users::where('empCode', 'LIKE', "%{$empCode}%")->orwhere('userName', 'LIKE', "%{$name}%")->get();
        if(empty($filterProfile) === false){
            return $filterProfile;
        }else{
            return false;
        }

    }
    public function saveuserToGroup($data){

        $communitygroup =new CommunityGroup;
        $communitygroup->communityType = $data['communityType'];
        $communitygroup->communityName = $data['communityName'];
        $communitygroup->adminUserId   = $data['adminUserId'];
        $communitygroup->accessType    = $data['accessType'];
        $communitygroup->is_active     = 1;
        $communitygroup->created_at    = Carbon::now()->toDateTimeString();
        $communitygroup->updated_at    = Carbon::now()->toDateTimeString();
        $communitygroup->save();

        $communitygroup = communitygroup::select('_id','adminUserId')->orderBy('_id','desc')->first();
        return $this->CommunityGroupDetail($communitygroup);
    }

    public function CommunityGroupDetail($data){
        $communitygroup_details =new CommunityGroupDetails;
        $communitygroup_details->communityId   = $data['_id'];
        $communitygroup_details->userId        = $data['adminUserId'];
        $communitygroup_details->isBlocked     = 1;
        $communitygroup_details->is_active     = 1;
        $communitygroup_details->created_at    = Carbon::now()->toDateTimeString();
        $communitygroup_details->updated_at    = Carbon::now()->toDateTimeString();
        $communitygroup_details->save();

        return true;

    }
}