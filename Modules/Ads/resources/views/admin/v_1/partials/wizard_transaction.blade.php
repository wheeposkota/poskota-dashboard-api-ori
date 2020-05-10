<!--begin: Form Wizard Progress -->
<div class="m-wizard__progress">
    <div class="progress">
        @if(strstr(Route::current()->getName(), 'create'))
            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        @elseif(strstr(Route::current()->getName(), 'payment'))
            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
        @elseif(strstr(Route::current()->getName(), 'invoice'))
            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
        @endif
    </div>
</div>

<div class="m-wizard__nav">
    <div class="m-wizard__steps">
        <div class="m-wizard__step {{strstr(Route::current()->getName(), 'create') ? 'm-wizard__step--current' : 'm-wizard__step--done'}}">
            <a href="#" class="m-wizard__step-number">
                <span><i class="fab fa-buysellads"></i></span>
            </a>
            <div class="m-wizard__step-info">
                <div class="m-wizard__step-title">
                    1. Informasi Iklan
                </div>
                <div class="m-wizard__step-desc">
                   Isi Semua Informasi Ikllan
                </div>
            </div>
        </div>
        <div class="m-wizard__step {{strstr(Route::current()->getName(), 'payment') ? 'm-wizard__step--current' : 'm-wizard__step--done'}}">
            <a href="#" class="m-wizard__step-number">
                <span><i class="fa fa-money-check-alt"></i></span>
            </a>
            <div class="m-wizard__step-info">
                <div class="m-wizard__step-title">
                    2. Pembayaran
                </div>
                <div class="m-wizard__step-desc">
                    Periksan Data Iklan dan Lakukan Pembayaran
                </div>
            </div>
        </div>
        <div class="m-wizard__step {{strstr(Route::current()->getName(), 'invoice') ? 'm-wizard__step--current' : 'm-wizard__step--done'}}">
            <a href="#" class="m-wizard__step-number">
                <span><i class="fa  flaticon-line-graph"></i></span>
            </a>
            <div class="m-wizard__step-info">
                <div class="m-wizard__step-title">
                    3. Bukti Pembayaran
                </div>
                <div class="m-wizard__step-desc">
                    Cetak Bukti Pembayaran
                </div>
            </div>
        </div>
    </div>
</div>
