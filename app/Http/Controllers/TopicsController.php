<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;
class TopicsController extends Controller
{
    public function __construct()
    {	
    	//对除了 index() 和 show() 以外的方法使用 auth 中间件进行认证
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request,Topic $topic)
	{
		//$topics = Topic::paginate(30);
		//方法 with() 提前加载了我们后面需要用到的关联属性 user 和 category，并做了缓存
		//$topics = Topic::with('user','category')->paginate();

		//$request->order 是获取 URI http://larabbs.test/topics?order=recent 中的 order 参数
		$topics = $topic->withOrder($request->order)->paginate(20);
		return view('topics.index', compact('topics'));
	}

    public function show(Topic $topic)
    {
        return view('topics.show', compact('topic'));
    }

    //发帖子表单
	public function create(Topic $topic)
	{	
		//获取话题分类
		$categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	//对发布帖子表单进行数据处理
	public function store(TopicRequest $request,Topic $topic)
	{
		//$topic = Topic::create($request->all());
		//echo '<pre>';print_r($request->all());echo Auth::id();exit;
		$topic->fill($request->all());
		$topic->user_id = Auth::id();
		$topic->save();
		return redirect()->route('topics.show', $topic->id)->with('success', '成功创建话题.');
	}

	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
		return view('topics.create_and_edit', compact('topic'));
	}

	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->route('topics.show', $topic->id)->with('success', '更新成功.');
	}

	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('success', '删除成功.');
	}

	//上传图片
	public function uploadImage(Request $request, ImageUploadHandler $uploader)
	{
		// 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];

        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
        	// 保存图片到本地
            $result = $uploader->save($request->upload_file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
	}
}