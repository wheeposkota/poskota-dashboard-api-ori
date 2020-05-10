@can('menu-epaper')
    <li class="m-menu__item m-menu__item--submenu  {{strstr(Route::current()->getName(), 'commodity') ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
        <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
            <i class="m-menu__link-icon flaticon-box-1"></i>
                <span class="m-menu__link-text">Commodity</span>
            <i class="m-menu__ver-arrow la la-angle-right"></i>
         </a>
         <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
            <ul class="m-menu__subnav">
                <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true"><span class="m-menu__link"><span class="m-menu__link-text">Commodity</span></span></li>
                <li class="m-menu__item  {{strstr(Route::current()->getName(), 'commodity.resource') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{route('commodity.resource.master')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Master</span></a></li>
                <li class="m-menu__item  {{strstr(Route::current()->getName(), 'commodity.categories') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{route('commodity.categories.master')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Categories</span></a></li>
            </ul>
        </div>
    </li>
@endcan