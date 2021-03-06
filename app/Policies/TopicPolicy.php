<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Topic;

class TopicPolicy extends Policy
{
    public function update(User $user, Topic $topic)
    {
    	//只允许作者对话题有编辑权限
        return $topic->user_id == $user->id;
        //return true;
    }

    //只允许作者删除话题
    public function destroy(User $user, Topic $topic)
    {
        return $topic->user_id == $user->id;
    }
}
