<li class="m-menu__item m-menu__item--submenu {{strstr(Route::current()->getName(), 'ads') ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
    <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
        <i class="m-menu__link-icon fab fa-buysellads"></i>
            <span class="m-menu__link-text">Ads</span>
        <i class="m-menu__ver-arrow la la-angle-right"></i>
     </a>
    <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
        <ul class="m-menu__subnav">
            <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true"><span class="m-menu__link"><span class="m-menu__link-text">Ads</span></span></li>
                @canany(['menu-master-ads', 'read-all-transaction-classic-ads', 'create-transaction-classic-ads', 'approving-payment-classic-ads', 'approving-content-classic-ads' , 'layouting-content-classic-ads'])
                    <li class="m-menu__item m-menu__item--submenu {{strstr(Route::current()->getName(), 'ads.classic') ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                <span class="m-menu__link-text">Iklan Umum/Khusus</span>
                            <i class="m-menu__ver-arrow la la-angle-right"></i>
                         </a>
                        <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
                            <ul class="m-menu__subnav">
                                    @canany(['read-all-transaction-classic-ads', 'create-transaction-classic-ads', 'approving-payment-classic-ads', 'approving-content-classic-ads', 'layouting-content-classic-ads'])
                                        <li class="m-menu__item  {{strstr(Route::current()->getName(), 'ads.classic.transaction') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Ads\Http\Controllers\TrxAdsClassicController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Transaksi</span></a></li>
                                    @endcanany
                                    @can('menu-master-ads')
                                        <li class="m-menu__item  {{Route::current()->getName() ==  'ads.classic.pricelist' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Ads\Http\Controllers\RltAdsCategoriesController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Daftar Harga</span></a></li>
                                        <li class="m-menu__item  {{Route::current()->getName() ==  'ads.classic.categories' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Ads\Http\Controllers\MstAdCategoriesController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Kategori</span></a></li>
                                    @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['menu-master-ads', 'read-all-transaction-web-ads', 'create-transaction-web-ads', 'approving-payment-web-ads', 'approving-content-web-ads', 'layouting-content-web-ads'])
                    <li class="m-menu__item m-menu__item--submenu {{strstr(Route::current()->getName(), 'ads.web') ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
                        <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
                            <i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i>
                                <span class="m-menu__link-text">Iklan Web</span>
                            <i class="m-menu__ver-arrow la la-angle-right"></i>
                         </a>
                        <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
                            <ul class="m-menu__subnav">
                                    @canany(['read-all-transaction-web-ads', 'create-transaction-web-ads', 'approving-payment-web-ads', 'approving-content-web-ads', 'layouting-content-web-ads'])
                                        <li class="m-menu__item  {{strstr(Route::current()->getName(), 'ads.web.transaction') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Ads\Http\Controllers\TrxAdsWebController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Transaksi</span></a></li>
                                    @endcanany
                                    @can('menu-master-ads')
                                        <li class="m-menu__item  {{Route::current()->getName() ==  'ads.web.pricelist' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Ads\Http\Controllers\MstAdsWebController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Daftar Harga</span></a></li>
                                    @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
            @can('menu-terms-ads')
                <li class="m-menu__item  {{Route::current()->getName() ==  'ads.terms' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Ads\Http\Controllers\MstAdsController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Terms</span></a></li>
            @endcan
        </ul>
    </div>
</li>