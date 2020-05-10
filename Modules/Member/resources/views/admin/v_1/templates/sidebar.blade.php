@can('menu-member')
    <li class="m-menu__item  {{strstr(Route::current()->getName(), 'member') ? 'm-menu__item--active' : ''}}" aria-haspopup="true">
        <a href="{{action('\Modules\Member\Http\Controllers\MemberController@index')}}" class="m-menu__link ">
            <i class="m-menu__link-icon fa fa-address-card"></i>
            <span class="m-menu__link-title"> 
                <span class="m-menu__link-wrap"> 
                    <span class="m-menu__link-text">
                        Member
                    </span>
                 </span>
             </span>
         </a>
    </li>
@endcan