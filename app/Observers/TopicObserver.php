<?php

namespace App\Observers;

use App\Models\Topic;
//use App\Handlers\SlugTranslateHandler;
use App\Jobs\TranslateSlug;
// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

//Topic模型监控器类
class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    //在 Topic 模型保存时触发的 saving 事件
    public function saving(Topic $topic)
    {
        //在数据入库前对话题内容进行 XSS过滤
        $topic->body = clean($topic->body, 'user_topic_body');

        // 生成话题摘录
    	$topic->excerpt = make_excerpt($topic->body);

        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        /*
        if(! $topic->slug){
            //$topic->slug = app(SlugTranslateHandler::class)->translate($topic->title);
            //推送任务到队列
            dispatch(new TranslateSlug($topic));
        }
        */
        
    }

    //模型监控器的saved()方法对应Eloquent的 saved 事件,此事件发生在创建和编辑时、数据入库以后
    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if(! $topic->slug){
            //推送任务到队列
            dispatch(new TranslateSlug($topic));
        }

    }
}