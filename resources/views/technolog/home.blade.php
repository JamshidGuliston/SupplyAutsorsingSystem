@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div id="timeline">
        <!-- //date -->
        @if(!empty($date) and count($date)>2)
        <div class="dot" id="one">
            <span>{{ $date[count($date)-1]->day_number }}</span>
            <date>{{ $date[count($date)-1]->month_name }}</date>
        </div>
        @endif
        @if(!empty($date) and count($date)>1)
        <div class="dot" id="two">
            <span>{{ $date[1]->day_number }}</span>
            <date>{{ $date[1]->month_name }}</date>
        </div>
        @endif
        @if(!empty($date) and count($date)>0)
        <div class="dot" id="three">
            <span>{{ $date[0]->day_number }}</span>
            <date>{{ $date[1]->month_name }}</date>
        </div>
        @endif
        @if($date[0]->day_number != date("d", $tomm))
        <div class="dot" id="four" type="button" data-bs-toggle="modal" data-bs-target="#exampleModals">
            <span>{{ date("d", $tomm) }}</span>
            <date>{{ date("F", $tomm) }}</date>
        </div>
        @endif
        @if($date[0]->day_number == date("d", $tomm))
        <div class="dot" id="four2">
            <span>{{ date("d", $tomm) }}</span>
            <date>{{ date("F", $tomm) }}</date>
        </div>
        @endif
        <div class="inside"></div>
    </div>

    <!-- Modals -->
    <!-- Button trigger modal -->
    <!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
    </button> -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModals" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('technolog.newday') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Yangi ish kuni</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="form-control" name="daynum" value="{{ date('d', $tomm) }}" />
                        <input type="hidden" class="form-control" name="daymonth" value="{{ date('F', $tomm) }}" />
                        <input type="hidden" class="form-control" name="dayyear" value="{{ date('Y', $tomm) }}" />
                        {{ date('d', $tomm) ." - ". date("F", $tomm) }}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Yaratish</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- end date -->

    <div class="row g-3 my-2">
        @foreach($kingardens as $item)
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>

                    <a href="#!" class="list-group-item-action bg-transparent second-text fw-bold" class="fs-5" data-bs-toggle="modal" data-bs-target="#exampleModal">{{$item-> kingar_name}}</a>

                    <div class="user-box">
                        <h4 class="text-sizes fs-2 m-0">{{ $item->worker_count}}</h4>
                        <i class="fas user fa-user-alt ml-1"></i>
                    </div>
                </div>
                <i class="fas fa-school fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>
        @endforeach
    </div>

    <!-- <div class="row my-5">
        <h3 class="fs-4 mb-3">Recent Orders</h3>
        <div class="col">
            <table class="table bg-white rounded shadow-sm  table-hover">
                <thead>
                    <tr>
                        <th scope="col" width="50">#</th>
                        <th scope="col">Product</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>Television</td>
                        <td>Jonny</td>
                        <td>$1200</td>
                    </tr>
                    <tr>
                        <th scope="row">2</th>
                        <td>Laptop</td>
                        <td>Kenny</td>
                        <td>$750</td>
                    </tr>
                    <tr>
                        <th scope="row">3</th>
                        <td>Cell Phone</td>
                        <td>Jenny</td>
                        <td>$600</td>
                    </tr>
                    <tr>
                        <th scope="row">4</th>
                        <td>Fridge</td>
                        <td>Killy</td>
                        <td>$300</td>
                    </tr>
                    <tr>
                        <th scope="row">5</th>
                        <td>Books</td>
                        <td>Filly</td>
                        <td>$120</td>
                    </tr>
                    <tr>
                        <th scope="row">6</th>
                        <td>Gold</td>
                        <td>Bumbo</td>
                        <td>$1800</td>
                    </tr>
                    <tr>
                        <th scope="row">7</th>
                        <td>Pen</td>
                        <td>Bilbo</td>
                        <td>$75</td>
                    </tr>
                    <tr>
                        <th scope="row">8</th>
                        <td>Notebook</td>
                        <td>Frodo</td>
                        <td>$36</td>
                    </tr>
                    <tr>
                        <th scope="row">9</th>
                        <td>Dress</td>
                        <td>Kimo</td>
                        <td>$255</td>
                    </tr>
                    <tr>
                        <th scope="row">10</th>
                        <td>Paint</td>
                        <td>Zico</td>
                        <td>$434</td>
                    </tr>
                    <tr>
                        <th scope="row">11</th>
                        <td>Carpet</td>
                        <td>Jeco</td>
                        <td>$1236</td>
                    </tr>
                    <tr>
                        <th scope="row">12</th>
                        <td>Food</td>
                        <td>Haso</td>
                        <td>$422</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div> -->

</div>
@endsection
@section('script')
<script>
    window.addEventListener('load', MyFunc, true);
    var i = 0;
    var j = 0;

    function divchange() {
        var divtag = document.getElementById("four");
        var bgcolor = ["#d2f8e9", "#ee928e"];
        divtag.style.backgroundColor = bgcolor[i];
        i = (i + 1) % bgcolor.length;
    }

    function MyFunc() {
        setInterval(divchange, 1000);
    }
</script>
@endsection