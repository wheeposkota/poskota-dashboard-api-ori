@can('menu-agent')
    <li class="m-menu__item  {{strstr(Route::current()->getName(), 'agent') ? 'm-menu__item--active' : ''}}" aria-haspopup="true">
        <a href="{{action('\Modules\Agent\Http\Controllers\AgentController@index')}}" class="m-menu__link ">
            <i class="m-menu__link-icon fa fa-user-tie"></i>
            <span class="m-menu__link-title"> 
                <span class="m-menu__link-wrap"> 
                    <span class="m-menu__link-text">
                        Agent
                    </span>
                 </span>
             </span>
         </a>
    </li>
@endcan