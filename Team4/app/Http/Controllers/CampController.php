<?php

namespace App\Http\Controllers;

use App\Camp;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CampController extends Controller
{
    public function index(Request $request)
    {
        $all_camp_datas = Camp::all();
        return view('admin/camp/index', compact("all_camp_datas"));
    }

    // store
    // public function store(Request $request)
    // {
    //     $request_data = $request->all();
    //     // 以new創建新資料
    //     $camp = new Camp;
    //     $camp->date = $request_data['date'];
    //     $camp->title = $request_data['title'];
    //     $camp->content = $request_data['content'];
    //     //save data
    //     $camp->save();

    //     return redirect('/admin/camp');
    // }
    public function update(Request $request,$id)
    {
        $request_data = $request->all();
        $item = Camp::find($id);
        if ($request_data["campsite_type"] == "Grass") {
            $one_dat_price = 300;
        }
        if ($request_data["campsite_type"] == "Small Pavilion") {
            $one_dat_price = 400;
        }
        if ($request_data["campsite_type"] == "Big Pavilion") {
            $one_dat_price = 2500;
        }

        $check_in_date = Carbon::parse($request_data["check_in_date"]);
        $striking_camp_date = Carbon::parse($request_data["striking_camp_date"]);
        $camp_days = $striking_camp_date->diffInDays($check_in_date);
        $item->price = $camp_days * $one_dat_price;
        $item->update($request_data);
        return redirect('admin/booking/camp');
    }
    // delete
    public function delete($id)
    {
        $item = Camp::find($id);
        $item->delete();
        return ;
    }
}
