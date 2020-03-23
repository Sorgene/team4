@extends('layouts.app')


@section('css')
{{-- data table --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
@endsection



@section('content')
<div class="container">
    <h2>形象首頁</h2>
    <p>
        <a class="btn btn-primary" data-toggle="collapse" href="#create_collapse" role="button" aria-expanded="false"
            aria-controls="create_collapse">
            new image vedio
        </a>
    </p>
    <div class="collapse" id="create_collapse">
        <div class="card card-body">
            <form method="POST" action="/admin/image_home" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="youtube_url">Youtube URL</label>
                    <input type="text" class="form-control" id="youtube_url" name="youtube_url" required>
                </div>
                <div class="form-group">
                    <label for="video_title">Video Title</label>
                    <input type="text" class="form-control" id="video_title" name="video_title" required>
                </div>
                <div class="form-group">
                    <label for="sort">Sort</label>
                    <input class="form-control" type="number" min="0" name="sort" id="sort" cols="30" rows="10" required
                        style="width:100px">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

    <hr>
    <table id="example" class="table table-striped table-bordered" style="width:100%">

        <thead>
            <tr>
                <th>vedio</th>
                <th>video title</th>
                <th>sort</th>
                <th>sort up/down</th>
                <th>edit/delete</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($all_imagehome_datas as $item)

            <tr>
                <td class="vedio_area">
                    <iframe src="" data-url="{{$item->youtube_url}}">
                    </iframe>
                </td>
                <td>{{$item->video_title}}</td>
                <td data-sort_id='{{$item->id}}'>{{$item->sort}}</td>
                <td>
                    {{-- 權重排序 --}}
                    <a href="#" type="button" class="btn btn-outline-info btn-sm col-12 btn-block"
                        onclick="sort_up({{$item->sort}},{{$item->id}})">Up</a>
                    <a data-btn_id="{{$item->id}}" href="#" type="button"
                        class="btn btn-outline-info btn-sm col-12 btn-block"
                        onclick="too_small({{$item->sort}},{{$item->id}});test({{$item->id}})">Down</a>

                </td>
                <td>
                    {{-- 修改、刪除 --}}
                    <a class="btn col-12 btn-block btn-sm btn-primary" data-toggle="collapse"
                        href="#edit_collapse{{$item->id}}" role="button">修改</a>

                    {{-- 點擊連結→觸發js事件→中斷連結的事件進行，執行指定函式 --}}
                    <a href="#" class="btn col-12 btn-block btn-sm btn-danger"
                        onclick="event.preventDefault();show_confirm(`{{$item->id}}`)">刪除</a>
                    <form id="delete_form{{$item->id}}" action="/home/news/delete/{{$item->id}}" method="POST"
                        style="display: none;">
                        @csrf
                    </form>
                </td>
            </tr>
            <div class="collapse" id="edit_collapse{{$item->id}}">
                <div class="card card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <div class="form-group">
                                <label for="youtube_url">Youtube URL</label>
                                <input class="form-control" id="youtube_url" name="youtube_url"
                                    value="{{$item->youtube_url}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="video_title">Video Title</label>
                            <input type="text" class="form-control" id="video_title" name="video_title"
                                value="{{$item->video_title}}">
                        </div>
                        <div class="form-group">
                            <label for="sort">Sort</label>
                            <input class="form-control" type="number" min="0" value="{{$item->sort}}" name="sort"
                                id="sort" required style="width:100px">
                        </div>

                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>


            @endforeach

        </tbody>

    </table>
</div>
@endsection
@section('js')
{{-- 接入js，並初始化datatables --}}
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable({
            "order": [[ 2, "desc" ]]
            });
        });
</script>


<script>
    // confirm函式，跳出視窗警告使用者正在進行刪除行為，若確認，則送出隱藏的表單，執行刪除
        function show_confirm(k){
            let r = confirm("你即將刪除這筆最新消息!");
            if (r == true){
                document.querySelector(`#delete_form${k}`).submit();
            }
        }
</script>
{{-- 權重、刪除，及時更新 --}}
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
<script>
    function test(d){
        let sort_value_now = $(`td[data-sort_id=${d}]`)[0].innerHTML;
        console.log(sort_value_now);
    }

    // 在index頁更改sort時，及時更新畫面排序
    function too_small(i,k){
        let sort_value_now = $(`td[data-sort_id=${k}]`)[0].innerHTML;
        console.log(sort_value_now);
        // onclick時，傳來現有索引值跟動作物件的id，判斷若索引值小於1，則跳出警告，不進行更改
        if (sort_value_now < 1) {
            alert("the min. sort value is 0,cann't be smaller");
        } else {
            // 若索引值大於1，以ajax送出資料(post)
            $.ajax({
            type:"POST",
            url:`/admin/image_home/sort_down`,
            // 送出的值為點擊到的這筆資料的id
            data:{data_id:k},
            success:function(result){
                console.log(result);
                // 那麼就只需要抓到被點擊到的資料是哪一筆，並以innerHTML的方法更改「網頁中」sort欄位的值
                let on_change_sort = $(`td[data-sort_id=${k}]`)[0];
                on_change_sort.innerHTML = result[0];
                // 若有第三筆，則是代表這次的動作是兩筆資料的索引值互換，第三筆資料為互換對象的id
                // 所以要抓到互換索引值的對象，並同樣以innerHTML的方法更改「網頁中」sort欄位的值
                if (result.length == 3){
                    let target_id = result[2];
                    let on_change_sort2 = $(`td[data-sort_id=${target_id}]`)[0];
                    on_change_sort2.innerHTML = result[1];
                }

                // 最後重新將datatable初始化，以達成即時重新排序的目的
                $('#example').DataTable({
                "order": [[ 2, "desc" ]],
                // 初始化須加上destroy:true，讓datatable將資料刪除重來
                destroy:true,
                });
            }
            });
        }
    }


    function sort_up(i,k){
            // 若索引值大於1，以ajax送出資料(post)
            $.ajax({
            type:"POST",
            url:`/admin/image_home/sort_up`,
            // 送出的值為id
            data:{data_id:k},
            success:function(result){
                console.log("success",result);
                let on_change_sort = $(`td[data-sort_id=${k}]`)[0];
                on_change_sort.innerHTML = result[0];
                let target_id = result[2];

                if (result.length == 3){
                    let on_change_sort2 = $(`td[data-sort_id=${target_id}]`)[0];
                    on_change_sort2.innerHTML = result[1];
                }


                $('#example').DataTable({
                "order": [[ 2, "desc" ]],
                destroy:true,
                });
            }
            });
    }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        let data_url = $('.vedio_area iframe');
        console.log(data_url);

        $.each(data_url, function( index, value ) {
            full_url = $(this).attr('data-url');
            var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
            var match = full_url.match(regExp);
            // return (match&&match[7].length==11)? match[7] : false;
            let vedio_id = match[7];
            $(this).attr('src',`http://www.youtube.com/embed/${vedio_id}`);
        });
    });
</script>
@endsection