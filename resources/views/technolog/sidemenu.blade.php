<div class="list-group list-group-flush my-3">
    <a href="/technolog/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="/technolog/muassasalar" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/muassasalar') ? 'active' : null }}"><i class="fas fa-building"></i> Muassasalar</a>
    <a href="/technolog/bolalar-qatnovi" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/bolalar-qatnovi') ? 'active' : null }}"><i class="fas fa-child"></i> Bolalar qatnovi</a>
    <a href="/technolog/seasons" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/seasons') ? 'active' : null }}"><i class="fas fa-paste"></i> Menyular</a>
    <a href="/technolog/food" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/food') ? 'active' : null }}"><i class="fas fa-hamburger"></i> Taomlar</a>
    <a href="/technolog/allproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/allproducts') ? 'active' : null }}"><i class="fas fa-carrot"></i> Products</a>
    <!-- <a href="/technolog/getbotusers" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/getbotusers') ? 'active' : null }}"><i class="fas fa-comment-dots me-2"></i>Chat bot</a> -->
    <a href="/technolog/shops" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/shops') ? 'active' : null }}"><i class="fas fa-store-alt"></i> Do'konlar</a>
    <a href="/technolog/chefgetproducts" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/chefgetproducts') ? 'active' : null }}"><i class="fas fa-project-diagram me-2"></i>Ishlatilgan</a>
    <a href="/technolog/allchefs" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/allchefs') ? 'active' : null }}"><i class="fas fa-user"></i> Oshpazlar</a>
    <a href="{{ route('technolog.certificates.index') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/certificates*') ? 'active' : null }}"><i class="fas fa-certificate"></i> Sertifikatlar</a>
    <a href="{{ route('technolog.importExcelPage') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('technolog/import-excel') ? 'active' : null }}"><i class="fas fa-file-excel"></i> Excel import</a>
    <!-- <a href="#" class="list-group-item list-group-item-action bg-transparent text-danger fw-bold"><i class="fas fa-power-off me-2"></i>Logout</a> -->
</div>
