<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Team;
use App\Models\User;

class TeamController extends Controller
{
    

    // protected $teamModel;
    

  
    
    public function create(){
        return view('team.team');
    }
    
    public function option(){
        return view('team.option');
    }
    
    public function join(){
        return view('team.join');
    }
    
    public function store(Request $request){
        
        $user = User::find(Auth::id());
        $team = Team::where('original_id',$request->team_id)->first();

        
        // 家族IDが見つからない場合
        if (!$team) {
            // エラーメッセージをセッションに追加
            $request->session()->flash('error', 'エラー：家族IDが見つかりませんでした。');
        
            // join.blade.php にリダイレクト
            return redirect()->route('team.join');
        }
        
        
        $user->team_id = $team->id;
        $user->save();
        return redirect()->route('dashboard');
    }
    
    public function register(Request $request){
    
        $request->validate([
            'familyName' => 'required|string|max:255',
            'familyID' => 'required|string|min:4|max:8|unique:teams,original_id',
        ]);

        $team = Team::create([
            'original_id' => $request->input('familyID'),
            'team_name' => $request->input('familyName'),
        ]);

        $user = User::find(Auth::id());
        $user->team_id = $team->id;
        $user->save();
        
        // dd($team,$user);
        return redirect()->route('tag.create');
        // return view('tag.create');
    }
}
