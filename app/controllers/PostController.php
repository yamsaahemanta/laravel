<?php




class PostController extends \BaseController {


	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
       $category=Category::all();
        Session::put("category",$category);

       $post=Post::where("author","=",Session::get("users")[0]->username)->get();
       if($post!="[]"){
           $post=Post::where("author","=",Session::get("users")[0]->username)->paginate(4);
       }
//       print($post);

        //exit();
//        Session::put("posts",$post);

        return View::make("post.allPost",array("posts"=>$post));
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        //        $category=Category::all();
//        Session::put("category",$category);

        $category=Category::all();
        Session::put("category",$category);
        return View::make("post.post");
    }


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
    {
//        {
//            $username = Input::get("username");
//            $password = Input::get("password");.

//            $role = Input::get("role");

//            $rules = array(
//                'title' => 'required|max:255',
//                'description' => 'required|min:8|max:255',
//                'category' => 'max:255',
//                'author' => 'required|max:255',
//                'slug' => 'required|alpha_dash|min:5|max:255'
//            );
//            $validator = Validator::make(Input::all(), $rules);
//
//            if ($validator->fails()) {
////                return Redirect::to("post")->withErrors($validator);
//            } else {
               $users=Session::get("users");
                $post = new Post();
                $post->category = Input::get("category");
                $post->description = Input::get("description");
                $post->slug = Input::get("slug");
                $post->author = $users[0]->username;
                $post->title = Input::get("title");
                $post->save();
                return Redirect::to("post")->withErrors("Successfully Added !!");
    }


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

        $posts=Post::where("id","=",$id)->get();
        $category=Category::all();
//        Session::put("category",$category);
//        print(Input::get("flag"));
//        exit();

        return View::make("post.post",array("posts"=>$posts,"category"=>$category,"flag"=>Input::get("flag")));
//            $username = Input::get("username");
//            $password = Input::get("password");
//            $role = Input::get("role");
//            print(Input::get("category"));
//            $rules = array(
//                'title' => 'required|max:255',
//                'description' => 'required|min:8|max:255',
//                'category' => 'max:255',
//                'author' => 'required|max:255',
//                'slug' => 'required|alpha_dash|min:5|max:255'
//            );
//            $validator = Validator::make($posts, $rules);
//
//            if ($validator->fails()) {
//                return Redirect::to("post")->withErrors($validator);
//            } else {
//
//                foreach ($posts as $post)
//                $posts=Post::where(strtolower("slug"),"=",strtolower(Input::get("slug")))->get();
//                if($posts!="[]"){
//                    return Redirect::to("post")->withErrors("Slug Already Exists!!");
//
//                }
//                print("i am here");
//                $post = new Post();
//                $post->category = Input::get("category");
//                $post->description = Input::get("description");
//                $post->slug = Input::get("slug");
//                $post->author = Input::get("author");
//                $post->title = Input::get("title");
//                $post->save();
//                return Redirect::to("post");
//            }

        }




	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
    {

        $rules = array(
            'title' => 'required|max:255',
            'description' => 'required|min:8|max:5000',
            'category' => 'required|max:255',
            'slug' => 'required|min:5|max:255'
        );
        $validator= Validator::make(Input::all(),$rules);
        if ($validator->fails()) {

            return Redirect::to("post/$id/edit")->withErrors($validator);

        } else {
            $postFromId = Post::where("id", "=", $id)->get();
            $posts = Post::where(strtolower("slug"), "=", strtolower(Input::get("slug")))->get();

            if ($posts != "[]") {


                if ($posts[0]->slug == $postFromId[0]->slug) {

                    if (Input::get("description") == $postFromId[0]->description&&Input::get("author") == $postFromId[0]->author&&Input::get("title") == $postFromId[0]->title&&Input::get("category") == $postFromId[0]->category) {
                        if(Input::get("flag")=="y"){
                            return Redirect::to("home/".Session::get("users")[0]->id)->withErrors("No any changes are updated")
                                                                                     ->withPostid(Input::get("postId"));
                        }

                        return Redirect::to("post")->withErrors("No any changes are updated");

                    }else{
                        $all=Input::all();  //input is in #json_array format like  {   } but when taken from db it will be in main_array format which contain json array format inside it like [ { },{ },{ }, ........ ]
//                        $post=new Post();
//                        foreach ($postFromId as $post){
                            $d2=strtotime($postFromId[0]->updated_at);
                            $d1=Carbon\Carbon::now();
                            $d1=strtotime($d1);
                            if($d1-$d2>86400){
                                if(Input::get("flag")=="y"){
                                    return Redirect::to("home/".Session::get("users")[0]->id)->withErrors("You cannot update your post because it crosses 24 hours")
                                        ->withPostid(Input::get("postId"));
                                }

                                return Redirect::to("post")->withErrors("You cannot update your post because it crosses 24 hours");
                            }else{

                                $postFromId[0]->title=$all["title"];
                                $postFromId[0]->description=$all["description"];
                                $postFromId[0]->slug=$all["slug"];
                                $postFromId[0]->category=$all["category"];
                                $postFromId[0]->updated_at=$d1;
                                $postFromId[0]->save();
                                if(Input::get("flag")=="y"){
                                    return Redirect::to("home/".Session::get("users")[0]->id)->withErrors("Successfully Updated")
                                        ->withPostid(Input::get("postId"));
                                }
                                return Redirect::to("post")->withErrors("Successfully Updated");
                        }

                    }

                } else {
                    return Redirect::to("post/$id/edit")->withErrors("Slug Already Exists");
                }

            } else {
                $all=Input::all();  //input is in #json_array format like  {   } but when taken from db it will be in main_array format which contain json array format inside it like [ { },{ },{ }, ........ ]
//                        $post=new Post();
//                        foreach ($postFromId as $post){
                $d2=strtotime($postFromId[0]->updated_at);
                $d1=Carbon\Carbon::now();
                $d1=strtotime($d1);
                if($d1-$d2>86400){
                    print("i am if");
                    return Redirect::to("post")->withErrors("You cannot update your post because it crosses 24 hours");
                }else{
                    print("i am else");

                    $postFromId[0]->title=$all["title"];
                    $postFromId[0]->description=$all["description"];
                    $postFromId[0]->slug=$all["slug"];
                    $postFromId[0]->category=$all["category"];
                    $postFromId[0]->updated_at=$d1;
                    $postFromId[0]->save();
                    return Redirect::to("post")->withErrors("Successfully Updated");
                }

            }


        }

    }


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
	    Post::where("id","=",$id)->delete();
        if(Input::get("flag")=="y"){
            return Redirect::to("home/".Session::get("users")[0]->id)->withErrors("Post Successfully Deleted")
                ->withPostid(Input::get("postId"));
        }

        return Redirect::to("post")->withErrors("Post Successfully Deleted");
	}
	public function check(){
        $slug=Input::get("slug");
        if (strlen($slug) < 5) {
            $messge["mess"] = "Slug should be at least 5 characters";
            $messge["status"] = 0;
        }else{
            $posts=Post::where(strtolower("slug"),"=",strtolower(Input::get("slug")))->get();
            if($posts!="[]"){
                $messge["mess"] = "Slug already exists, Please try next";
                $messge["status"] = 2;

            }else{
//                      $post = new Post();
//                      $post->category = Input::get("category");
//                      $post->description = Input::get("description");
//                      $post->slug = Input::get("slug");
//                      $post->author = Input::get("author");
//                      $post->title = Input::get("title");
                // $post->save();
                //return Redirect::to("post");
                $messge["mess"] = "Slug is valid";
                $messge["status"] = 1;
            }
        }
        echo json_encode($messge);

        //return Response::json($messge);


    }

    /**
     * @return mixed
     */
    public function downloadPdf(){
//        $dompdf = new Dompdf();
//        $dompdf->loadHtml('<h1>Welcome to CodexWorld.com</h1>');
//
//// (Optional) Setup the paper size and orientation
//        $dompdf->setPaper('A4', 'landscape');
//
//// Render the HTML as PDF
//        $dompdf->render();
//
//// Output the generated PDF to Browser
//        $dompdf->stream();
$posts=Post::all();
//View::share('posts',$posts);
$pdf = PDF::loadView('post/downloadPdf',['posts'=>$posts])->setPaper('a4', 'landscape');
return $pdf->download("downloadPdf.pdf");
}

public function getSearch()
    {

        $posts = Post::
                 where('title', 'like', Input::get("title").'%')
                 ->paginate(4);

//print("$posts");
        $category=Category::all();
        return View::make("home.index",array("category"=>$category,"posts"=>$posts));

}



}


