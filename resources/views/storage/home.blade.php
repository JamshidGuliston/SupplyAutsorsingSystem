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
        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">720</h3>
                    <p class="fs-5">Products</p>
                </div>
                <i class="fas fa-gift fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">4920</h3>
                    <p class="fs-5">Sales</p>
                </div>
                <i
                    class="fas fa-hand-holding-usd fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">3899</h3>
                    <p class="fs-5">Delivery</p>
                </div>
                <i class="fas fa-truck fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm d-flex justify-content-around align-items-center rounded">
                <div>
                    <h3 class="fs-2">%25</h3>
                    <p class="fs-5">Increase</p>
                </div>
                <i class="fas fa-chart-line fs-1 primary-text border rounded-full secondary-bg p-3"></i>
            </div>
        </div>
    </div>

    <div class="row my-5">
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
    </div>

</div>
@endsection