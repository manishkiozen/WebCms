<?php namespace App\Http\Controllers\CMS;

use Config;
use Request;
use App\Http\Controllers\Controller;
use Auth;


class PostCMSController extends BaseCMSController {

	public static $endpoint = 'api/post';

	/**
	 * Halaman CMS Post
	 * @return Response
	 */
	public function index()
	{
		$data = [
			'endpoint' => self::$endpoint
		];
		return view('cms.post.post')->with($data);
	}

	/**
	 * Halaman CMS Post by Category
	 * @return Response
	 */
	public function category($slug)
	{
		$data = [
			'endpoint' => self::$endpoint.'/category/'.$slug
		];
		return view('cms.post.post')->with($data);
	}

	/**
	 * Halaman CMS Post by Tag
	 * @return Response
	 */
	public function tag($slug)
	{
		$data = [
			'endpoint' => self::$endpoint.'/tag/'.$slug
		];
		return view('cms.post.post')->with($data);
	}

	/**
	 * Halaman CMS Post Add
	 * @return Response
	 */
	public function add()
	{
		$tags = json_decode(@file_get_contents(url('api/tag')));
		$categories = json_decode(@file_get_contents(url('api/category')));

		$data = [
			'user_id' => Auth::user()->id, 
			'categories' => $categories, 
			'tags' => $tags
		];
		return view('cms.post.post_add')->with($data);
	}

	/**
	 * Halaman CMS Post Edit
	 * @return Response
	 */
	public function edit($id)
	{
		$url = url(self::$endpoint.'/'.$id);
		$post = json_decode(@file_get_contents($url));
		$available_tag_ids = array_map(function($x){return $x->id;}, $post->tags);

		$tags = json_decode(@file_get_contents(url('api/tag')));
		$tags = array_map(function ($x) use ($available_tag_ids) {
			$x->available = in_array($x->id, $available_tag_ids);
			return $x;
		}, $tags);

		$categories = json_decode(@file_get_contents(url('api/category')));
		$categories = array_map(function ($x) use ($post) {
			$x->available = $x->id == $post->category_id;
			return $x;
		}, $categories);

		$data = [
			'post' => $post, 
			'categories' => $categories, 
			'tags' => $tags
		];
		return view('cms.post.post_edit')->with($data);
	}

	/**
	 * Post Update
	 * @return Response
	 */
	public function store()
	{
		$params = Request::all();
		unset($params['_wysihtml5_mode']);
		$url = url(self::$endpoint.'/');
		$this->_post($url, $params);
		
		return redirect('cms/post');
	}

	/**
	 * Post Update
	 * @return Response
	 */
	public function update($id)
	{
		$params = Request::all();
		unset($params['_wysihtml5_mode']);
		$url = url(self::$endpoint.'/'.$params['id']);
		$this->_put($url, $params);
		
		return redirect('cms/post');
	}

}
