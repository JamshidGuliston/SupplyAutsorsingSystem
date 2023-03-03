<div class="list-group list-group-flush my-3">
    <a href="/boss/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="/boss/cashe" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('boss/cashe') ? 'active' : null }}"><i class="fas fa-coins"></i> Kassa</a>
    <a href="/boss/report" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('boss/report') ? 'active' : null }}"><i class="fas fa-cogs"></i> Hisobot</a>
    <a href="/boss/incomereport" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('boss/incomereport') ? 'active' : null }}"><i class="fas fa-cogs"></i> Daromat Hisobot</a>
</div>
