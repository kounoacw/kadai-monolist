<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Item;

class RankingController extends Controller
{
    /**
     * want のランキング
     * 
     */
    public function want()
    {
        $items = [];
        if (Item::exists()) {
            $items = \DB::table('item_user')
                            ->join('items', 'item_user.item_id', '=', 'items.id')
                            ->select('items.*', \DB::raw('COUNT(*) as count'))
                            ->where('type', 'want')
                            ->groupBy('items.id')
                            ->orderBy('count', 'DESC')
                            ->take(10)
                            ->get();
        }
        
        return view('ranking.want', [
            'items' => $items,
        ]);
    }
    
    /**
     * have のランキング
     * 
     */
    public function have()
    {
        $items = [];
        if (Item::exists()) {
            $items = \DB::table('item_user')
                            ->join('items', 'item_user.item_id', '=' , 'items.id')
                            ->select('items.*', \DB::raw('COUNT(*) as count'))
                            ->where('type', 'have')
                            ->groupBy('items.id')
                            ->orderBy('count', 'DESC')
                            ->take(10)
                            ->get();
        }
        
        return view('ranking.have', [
            'items' => $items,
        ]);
    }
}
