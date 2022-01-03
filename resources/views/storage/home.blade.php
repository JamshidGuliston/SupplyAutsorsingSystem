@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div id="timeline">
        <!-- //date -->
        <div class="dot" id="one">
            <span>1</span>
            <date>dekabr</date>
        </div>
        <div class="dot" id="two">
            <span>27</span>
            <date>kecha</date>
        </div>
        <div class="dot" id="three">
            <span>28</span>
            <date>bugun</date>
        </div>
        <div class="dot" id="four">
            <span>29</span>
            <date>dekabr</date>
        </div>
        <div class="inside"></div>
    </div>

    <!-- Modals -->

    <article class='modal one'>
        <date>26/06 - 1280</date>
        <h2>Yo les gars</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates magnam excepturi laboriosam minima soluta, quae. Sunt repellat totam non, et sed in veniam fuga odio eius! Nesciunt amet optio recusandae? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum sit dolor sint amet, corporis aperiam nihil, quas, accusantium enim suscipit rem non possimus officiis. Recusandae hic at, fugiat eos eveniet.</p>
    </article>

    <article class='modal two'>
        <date>08/09 - 1649</date>
        <h2>Salut les louzeurs</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates magnam excepturi laboriosam minima soluta, quae. Sunt repellat totam non, et sed in veniam fuga odio eius! Nesciunt amet optio recusandae? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum sit dolor sint amet, corporis aperiam nihil, quas, accusantium enim suscipit rem non possimus officiis. Recusandae hic at, fugiat eos eveniet.</p>
    </article>

    <article class='modal three'>
        <date>21/07 - 1831</date>
        <h2>Eat 'em all !</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates magnam excepturi laboriosam minima soluta, quae. Sunt repellat totam non, et sed in veniam fuga odio eius! Nesciunt amet optio recusandae? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum sit dolor sint amet, corporis aperiam nihil, quas, accusantium enim suscipit rem non possimus officiis. Recusandae hic at, fugiat eos eveniet.</p>
    </article>

    <article class='modal four'>
        <date>03/02 - 1992</date>
        <h2>Mais pourquoi?</h2>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates magnam excepturi laboriosam minima soluta, quae. Sunt repellat totam non, et sed in veniam fuga odio eius! Nesciunt amet optio recusandae? Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsum sit dolor sint amet, corporis aperiam nihil, quas, accusantium enim suscipit rem non possimus officiis. Recusandae hic at, fugiat eos eveniet.</p>
    </article>
    <!-- end date -->
    <div class="row g-3 my-2">
        @foreach($praducts as $row)
        <div class="col-md-2">
            <div class="p-3 bg-white shadow-sm d-flex flex-column justify-content-around align-items-center rounded">
                <i class="fas fa-seedling fs-1 primary-text border rounded-full secondary-bg p-2"></i>
                <div class="text-center">
                    <h5 class="fs-3 mb-0 mt-1">{{$row['weight']}}</h5>
                    <p class="fs-4" style="font-size: 18px !important;">{{$row['product_name']}}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection