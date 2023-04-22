<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\Photo;
use App\Models\Type;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;



class PhotoController extends Controller
{
    
    //index
    public function index(Request $request){
        
        $team_id = Auth::user()->team_id;
        
        $startDate = "2000-01-01";
        $endDate_pre = date('Y-m-d');
        // 日付をDateTimeオブジェクトに変換
        $endDateObject = new \DateTime($endDate_pre);
        // 1日を追加
        $endDateObject->modify('+1 day');
        $endDate = $endDateObject->format('Y-m-d');
        
        if($request->input('start') != null){
            $startDate = $request->input('start');
        }
        if($request->input('end') != null){
            $endDate = $request->input('end');
        }

        $query = Photo::query()
            ->where('team_id', $team_id)
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->orderBy('created_at', 'desc');

        $photos = $query->paginate(25);
        
        // dd($photos);
        foreach($photos as $photo){
            $type = Type::where('id', $photo->type_id)->first()->name;
            // $tags = Tag::where('team_id', $team)->get();
            $results = Photo::with("tags")->where('id', $photo->id)->get();
            // dd($results);
            // dd($results->$tags);
            $classname ="";
            $tagnames = [];
            foreach($results as $result){
                foreach($result->tags as $tag) {
                    $classname .= "tag_no_";
                    $classname .= $tag->id." ";
                    array_push($tagnames,$tag->name);
                }
            }
            // dd($classname);
            $photo ->type_name = $type;
            $photo ->tag_no = $classname;
            $photo ->tag_names = $tagnames;
            // dd($photo);
            
        }
        
        // dd($photos);
        return response()->view('photo.index',compact('photos','startDate','endDate'));
    }
    
    //create
    public function create()
    {
        return response()->view('photo.create');
    }

    
    
    //store
    public function store(Request $request)
    {
        
        $photo = new photo();
        // バリデーション 今回は特に不要
        // バリデーション:エラー
        // if ($validator->fails()) {
        //   return redirect()
        //     ->route('tweet.create')
        //     ->withInput()
        //     ->withErrors($validator);
        // }
        // dd($request->image,$request->tags);
        // dd($request->file("image"));
        if(request('image')){
            $original = $request->file("image")->getClientOriginalName();
            $name = date("Ymd_His")."_".$original;
            request() ->file("image")->move("storage/image",$name);
            $photo -> image = $name;
        }
        
        // 編集 フォームから送信されてきたデータとユーザIDをマージし，DBにinsertする
        $data = $request->merge(['user_id' => Auth::user()->id])->all();
        // $result = Photo::create($data);

        $photo -> user_id = Auth::user() -> id;
        $photo -> team_id = Auth::user() -> team_id;
        
        // dd($request->type);
        $photo -> type_id = $request->type;
        $photo -> save();
    
        //photoに紐づくtagを中間テーブルに登録
        $photo -> tags()->attach($request->tags);
        
        // dd($request);
        // photo.index」にリクエスト送信（一覧ページに移動）
        return redirect()->route('photo.index');
    }
    
    
    
    public function destroy($id)
    {
        $result = Photo::find($id)->delete();
        return redirect()->route('photo.index');
    }
    
    
    public function edit($id)
    {
        $photo = Photo::find($id);

        $results = Photo::with("tags")->where('id', $photo->id)->get();
        $tagnames = [];
            foreach($results as $result){
                foreach($result->tags as $tag) {
                    array_push($tagnames,$tag->name);
                }
            }
        
        $photo ->tag_names = $tagnames;
        // dd($photo);
        // dd($photo->tag_names);
        return response()->view('photo.edit', compact('photo'));
    }


    public function update(Request $request, $id)
    {
        //バリデーション
        // $validator = Validator::make($request->all(), [
        //     'tweet' => 'required | max:191',
        //     'description' => 'required',
        // ]);
        //バリデーション:エラー
        // if ($validator->fails()) {
        //     return redirect()
        //     ->route('tweet.edit', $id)
        //     ->withInput()
        //     ->withErrors($validator);
        // }
        //データ更新処理
        
        // dd($request); 
        if($request->type){
            $photo = Photo::find($id);
            $photo->type_id = $request->type;
            $photo->save();
        }
        
        if($request->tags){
            $photo = Photo::find($id);
        
            // 既存のタグ関連を削除
            DB::table('photo_tag')->where('photo_id', $id)->delete();
        
            // 新しいタグ関連を作成
            foreach ($request->tags as $tag_id) {
                DB::table('photo_tag')->insert([
                    'photo_id' => $id,
                    'tag_id' => $tag_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        return redirect()->route('photo.index');
    }
    
    public function favorite($id)
    {
        $photo =Photo::find($id);
        $photo->iine = true;
        $photo->save();
        return redirect()->route('photo.index');
    }
    public function unfavorite($id)
    {
        $photo =Photo::find($id);
        $photo->iine = false;
        $photo->save();
        return redirect()->route('photo.index');
    }
    
    
    public function memory(){
        return response()->view('photo.memory' );
    }
    
    public function memoryindex(){
        
        $team_id = Auth::user()->team_id;
        

        $query = Photo::query()
            ->where('team_id', $team_id)
            ->orderBy('created_at', 'desc');

        $photos = $query->get();
        
        // dd($photos);
        foreach($photos as $photo){
            $type = Type::where('id', $photo->type_id)->first()->name;
            // $tags = Tag::where('team_id', $team)->get();
            $results = Photo::with("tags")->where('id', $photo->id)->get();
            // dd($results);
            // dd($results->$tags);
            $classname ="";
            $tagnames = [];
            foreach($results as $result){
                foreach($result->tags as $tag) {
                    $classname .= "tag_no_";
                    $classname .= $tag->id." ";
                    array_push($tagnames,$tag->name);
                }
            }
            // dd($classname);
            $photo ->type_name = $type;
            $photo ->tag_no = $classname;
            $photo ->tag_names = $tagnames;
            // dd($photo);
            
        }
        
        // dd($photos);
        return response()->view('photo.memory',compact('photos'));
    }
    
}
