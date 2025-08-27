<div class="list-group list-group-flush my-3">
    <a href="/chef/home" class="list-group-item list-group-item-action bg-transparent second-text"><i class="fas fa-tachometer-alt me-2"></i>Bosh sahifa</a>
    <a href="{{ route('chef.certificates') }}" class="list-group-item list-group-item-action bg-transparent second-text fw-bold {{Request::is('chef/certificates') ? 'active' : null }}">
        <i class="fas fa-certificate"></i> Sertifikatlar
    </a>
</div>
