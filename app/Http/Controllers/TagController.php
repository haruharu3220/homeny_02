<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Validator;
use App\Models\Tag;

class TagController extends Controller
{
    //
    public function index(){
        $team = Auth::user()->team_id;
        // $tags = Tag::where('team_id', $team)->orderBy('updated_at', 'desc')->get();
        $tags = Tag::where('team_id', $team)->orderBy('updated_at', 'asc')->get();
        
        return response()->view('tag.index', compact('tags'));
    }
    
    public function store(Request $request){
        // dd($request);
        $tagNames = $request->tagName;
        // dd($tagNames);
        if (is_array($tagNames)) {
            foreach ($tagNames as $tagName) {
                $tag = new Tag();
                $tag->team_id = Auth::user()->team_id;
                $tag->name = $tagName;
                $tag->save();
            }
        }else{
                $tag = new Tag();
                $tag->team_id = Auth::user()->team_id;
                $tag->name = $tagNames;
                $tag->save();
        }

        return redirect()->route('tag.store');
    }
    
    
    
    public function destroy($id){
        $result = Tag::find($id)->delete();
        return redirect()->route('tag.index');

    }
    
    public function memoedit($id){
        $tag = Tag::find($id);
        return response()->view('tag.edit', compact('tag'));
    }
    
    public function create(){
        return response()->view('tag.create');
    }
    
    public function update(Request $request, $id){
    //     //バリデーション
    //   $validator = Validator::make($request->all(), [
    //     'name' => 'required | max:191',
    //   ]);
    //   //バリデーション:エラー
    //   if ($validator->fails()) {
    //     return redirect()
    //       -> route('memoedit', $id)
    //       ->withInput()
    //       ->withErrors($validator);
    //     }
        // dd($request);
        $result = Tag::find($id)->update($request->all());
        // dd($result);
        return redirect()->route('dashboard');
    }
    
}
