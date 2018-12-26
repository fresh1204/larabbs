<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Handlers\ImageUploadHandler;
class UsersController extends Controller
{
	//身份验证（Auth）中间件来过滤未登录用户的 edit, update 动作
	public function __construct(){
		$this->middleware('auth',['except'=>['show']]);
	}

    //个人页面的展示
    public function show(User $user){

    	return view('users.show',compact('user'));
    }

    //编辑表单
    public function edit(User $user){
    	//检验用户是否授权
    	$this->authorize('update',$user);
    	
    	return view('users.edit',compact('user'));
    }

    //对编辑表单提交的数据进行处理
    public function update(UserRequest $request,ImageUploadHandler $uploader,User $user){
    	//dd($request->avatar);
    	//检验用户是否授权
    	$this->authorize('update', $user);

    	$data = $request->all();
    	if($request->avatar){
    		$result = $uploader->save($request->avatar,'avatars',$user->id,362);
    		if($result){
    			$data['avatar'] = $result['path'];
    		}
    	}

    	$user->update($data);

    	return redirect()->route('users.show',$user->id)->with('success','个人资料更新成功');
    }
}
