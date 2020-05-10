@can('menu-gallery')
    <li class="m-menu__item m-menu__item--submenu {{in_array(Route::current()->getName(), ['gallery.photo', 'gallery.video']) ? 'm-menu__item--expanded m-menu__item--open' : ''}}" aria-haspopup="true" m-menu-submenu-toggle="hover">
        <a href="javascript:void(0)" class="m-menu__link m-menu__toggle">
            <i class="m-menu__link-icon flaticon-photo-camera"></i>
                <span class="m-menu__link-text">Gallery</span>
            <i class="m-menu__ver-arrow la la-angle-right"></i>
         </a>
        <div class="m-menu__submenu "><span class="m-menu__arrow"></span>
            <ul class="m-menu__subnav">
                <li class="m-menu__item  m-menu__item--parent" aria-haspopup="true"><span class="m-menu__link"><span class="m-menu__link-text">Gallery</span></span></li>
                <li class="m-menu__item  {{Route::current()->getName() ==  'gallery.photo' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Gallery\Http\Controllers\PhotoController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Photos</span></a></li>
                <li class="m-menu__item  {{Route::current()->getName() ==  'gallery.video' ? 'm-menu__item--active' : ''}}" aria-haspopup="true"><a href="{{action('\Modules\Gallery\Http\Controllers\VideoController@index')}}" class="m-menu__link "><i class="m-menu__link-bullet m-menu__link-bullet--dot"><span></span></i><span class="m-menu__link-text">Videos</span></a></li>
            </ul>
        </div>
    </li>
@endcan