<?php

use App\Post;
use App\User;
use App\Country;
use App\Photo;
use App\Tag;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
   return view('welcome');
});

// Route::get('/about', function () {
//     return 'Hi, about page';
//  });

//  Route::get('/contact', function () {
//     return 'Hi, I am contact';
// });

// Route::get('/post/{id}/{name}', function ($id, $name) {
//     return 'Hi, I am contact' . $id . " ". $name;
// });

// Route::get('/admin/posts/example', array('as'=>'admin.home',function () {
//     $url = route('admin.home');  //route
//     return 'This url is '. $url;
// }));

//route with controller
//Route::get("/post/{id}", "PostsController@index");

//Route::resource('post','PostsController');

// Route::get('/contact', 'PostsController@contact');

// Route::get('/post/{id}/{name}/{password}','PostsController@show_post');

//insert data
Route::get('/insert', function () {
   DB::insert('insert into posts(title,content) values(?,?)',['Laravel is awesome with Edwin', 'Laravel is the best thing that has happened to PHP, PERIOD']);
});

//read data
Route::get('/read', function(){
   $result = DB::select('select * from posts where id = ?', [1]);

   foreach($result as $post):
      return $post->title;
   endforeach;
});

//update data
// Route::get('/update', function(){
//    $updated = DB::update('update posts set title = "Update title" where id = ?', [1]);
//    return $updated;
// });

//delete data
// Route::get('/delete', function () {
//    $deleted = DB::delete('delete from posts where id = ?', [1]);
//    return $deleted;
// });

Route::get('/find', function() {
   //$posts = Post::all();
   // foreach($posts as $post):
   //    return $post->title;
   // endforeach;

   $posts = Post::find(3);
   return $posts->content; 
});

//find where it is
Route::get('/findwhere', function (){
   $posts = Post::where('id', 2)->orderBy('id', 'desc')->take(2)->get();
   return $posts;
});

//find more
Route::get('/findmore', function() {
   // $posts = findOrFail(1);
   //return $posts;

   //$post = Post::where('users_count', '<', 50)->firstOrFail();
});

Route::get('/basicinsert', function() {
   $post = new Post;
   $post->title = 'new Eloquent title insert';
   $post->content = 'Wow, eloquent is really cool, look at this content';
   $post->save();
});

//Eloquent Create
Route::get('/basicinsert2', function() {
   $post = Post::find(2);
   $post->title = 'new Eloquent title insert 2';
   $post->content = 'Wow, eloquent is really cool, look at this content 2';
   $post->save();
});

//Mass assignment
Route::get('/create', function() {
   Post::create(['title'=>'the create method', 'content' => 'Wow, I am learning a lot with Edwin Diaz']);
});

//Eloquent update
Route::get('/update', function() {
   Post::where('id', 2)->where('is_admin', 0)->update(['title'=>'NEW PHP TITLE', 'content'=>'I love my Cynthia']);
});

//Eloquent delete
Route::get('/delete', function() {
   $post = Post::find(2);
   $post->delete();
});

Route::get('/delete2', function(){
   Post::destroy(3);
});

//multiple delete
Route::get('/delete2', function(){
   Post::destroy(3,4,5);
   //Post::where('is_admin', 0)->delete();
});

Route::get('/softdelete', function() {
   Post::find(7)->delete();
});

//Retrieving deleted/ trashed records
Route::get('/readsoftdelete', function(){
   // $post = Post->find(6);
   // return $post;   //这将返回空白，因为$post虽然还在数据库中，但已经查询不到
   
   //method 1
   $post = Post::withTrashed()->where('is_admin', 0)->get();
   dd($post);
   //method 2
   // $post = Post::onlyTrashed()->where('is_admin', 0)->get();
   // return $post;
});

//Restore trashed records
Route::get('/restore', function() {
   Post::withTrashed()->where('is_admin', 0)->restore();
});

//Delete records permanently - force delete
Route::get('/forcedelete', function() {
   
   //force delete every reord of which is_admin is 0, 慎用！！！
   //Post::withTrashed()->where('is_admin', 0)->forceDelete();

   //only force delete trashed items
   Post::onlyTrashed()->where('is_admin', 0)->forceDelete();

});

/**
 * Eloquent Relationship
 */
//One to one Relationship
 //hasOne
Route::get('/user/{id}/post', function($id) {
   //return User::find($id)->post;
   return User::find($id)->post;
});
//belongs to
Route::get('/post/{id}/user', function($id){
   return Post::find($id)->user->name;
});

//One to many
Route::get('/posts', function() {
   $user = User::find(1);
   
   foreach($user->posts as $post) {
      echo $post->title . "</br>";
   }
});


//Many to many
Route::get('/user/{id}/role', function($id){
   //$user = User::find($id);
   // foreach($user->roles as $role) {
   //    echo $role->name;
   // }
   
   $user = User::find($id)->roles()->orderBy('id','desc')->get();   
   return $user;
});

//Accessing the intermediate table / pivot
Route::get('/user/pivot', function() {
   $user = User::find(1);
   foreach($user->roles as $role) {
      echo $role->pivot->updated_at;
   }
});
// this will return {"user_id":1,"role_id":1}


//hasManyThrough
Route::get('/user/country', function() {
   $country = Country::find(4);
   foreach ($country->posts as $post) {
      return $post->content;
   }
});

//polymorphic relations
$router->get('/user/photos', function() {
   $user = User::find(1);
   foreach($user->photos as $photo) {
      return $photo->path;
   }
});

$router->get('/post/photos', function() {
   $post = Post::find(1);
   foreach($post->photos as $photo) {
      // return $photo->path;
      echo $photo->path."<br/>";
   }
});

$router->get('photo/{id}', function ($id) {
   $photo = Photo::findOrFail($id);
   return $photo->imageable;
});

//polymorphic many to many
$router->get('/post/tag', function() {
   $post = Post::find(1);
   
   foreach($post->tags as $tag) {
      echo $tag->name;
   }
});

$router->get('/tag/post/', function () {
   $tag = Tag::find(2);
   return $tag->posts;
   // foreach($tag->posts as $post) {
   //    echo $post->title;
   // }
});




