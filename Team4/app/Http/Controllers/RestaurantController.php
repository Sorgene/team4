<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Restaurant;
use Illuminate\Http\Request;
use Symfony\Component\VarDumper\VarDumper;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $all_restaurant_datas = Restaurant::with('customer')->get();
        return view('admin/restaurant/index', compact("all_restaurant_datas"));
    }

    // store
    public function store(Request $request)
    {
        $request_data = $request->all();

        $validatedData = $request->validate([
            'customer_id' => 'exists:customers,id',
        ]);

        // 判斷時段
        $Breakfast_time = Carbon::create('11:00'); //1100前為早餐
        $Lunch_time = Carbon::create('15:00');  //1500前為午餐
        $order_time = Carbon::create($request_data['time']);
        $comparison1 = $Lunch_time->diffInHours($order_time,false);  //後減前
        $comparison2 = $Breakfast_time->diffInHours($order_time,false);

        // 以new創建新資料
        $restaurant = new Restaurant;
        $restaurant->customer_id = $request_data['customer_id'];
        $restaurant->total_number = $request_data['total_number'];
        $restaurant->vegetarian_number = $request_data['vegetarian_number'];
        $restaurant->date = $request_data['date'];
        $restaurant->time = $request_data['time'];
        if ($comparison1 >= 0) {
            $restaurant->time_session = "Dinner";
        } else {
            if ($comparison2 >= 0) {
                $restaurant->time_session = "Lunch";
            } else {
                $restaurant->time_session = "Breakfast";
            }
        }
        $restaurant->price = 500 * $request_data['total_number'];
        $restaurant->remark = $request_data['remark'];
        $restaurant->customer_id = $request_data['customer_id'];
        //save data
        $restaurant->save();

        return redirect('/admin/booking/restaurant');
    }
    public function update(Request $request,$id)
    {
        $request_data = $request->all();
        $item = Restaurant::find($id);
        $item->price = 500 * $request_data['total_number'];
        // 判斷時段
        $Breakfast_time = Carbon::create('11:00'); //1100前為早餐
        $Lunch_time = Carbon::create('15:00');  //1500前為午餐
        $order_time = Carbon::create($request_data['time']);
        $comparison1 = $Lunch_time->diffInHours($order_time,false);  //後減前
        $comparison2 = $Breakfast_time->diffInHours($order_time,false);
        if ($comparison1 >= 0) {
            $item->time_session = "Dinner";
        } else {
            if ($comparison2 >= 0) {
                $item->time_session = "Lunch";
            } else {
                $item->time_session = "Breakfast";
            }
        }
        $item->update($request_data);
        return redirect('admin/booking/restaurant');
    }
    // delete
    public function delete($id)
    {
        $item = Restaurant::find($id);
        $item->delete();
        return ;
    }
}
