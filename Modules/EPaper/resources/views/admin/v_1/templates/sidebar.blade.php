@can('menu-epaper')
    <li class="m-menu__item m-menu__item--submenu  {{strstr(Route::current()->getName(), 'epaper') ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
        <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
            <i class="m-menu__link-icon fa fa-file-pdf"></i>
                <span class="m-menu__link-text">E-Paper</span>
            <i class="m-menu__ver-arrow la la-angle-right"></i>
         </a>
         <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
            <ul class="m-menu__subnav">
                <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true"><span class="m-menu__link"><span class="m-menu__link-text">E-Paper</span></span></li>
                <li class="m-menu__item  {{strstr(Route::current()->getName(), 'epaper.subscription.master') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{route('epaper.subscription.master')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Subscription</span></a></li>
                <li class="m-menu__item  {{strstr(Route::current()->getName(), 'epaper.resource') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{route('epaper.resource.master')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Master</span></a></li>
                <li class="m-menu__item  {{strstr(Route::current()->getName(), 'epaper-package') ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{route('epaper-package.resource.master')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Package</span></a></li>
            </ul>
        </div>
    </li>
@endcan