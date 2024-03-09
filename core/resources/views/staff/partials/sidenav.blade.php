<div class="sidebar {{ sidebarVariation()['selector'] }} {{ sidebarVariation()['sidebar'] }} {{ @sidebarVariation()['overlay'] }} {{ @sidebarVariation()['opacity'] }}"
    data-background="{{ getImage('assets/admin/images/sidebar/2.jpg','400x800') }}">
    <button class="res-sidebar-close-btn"><i class="las la-times"></i></button>
    <div class="sidebar__inner">
        <div class="sidebar__logo">
            <a href="{{ route('home') }}" class="sidebar__main-logo"><img
                    src="{{ getImage(imagePath()['logoIcon']['path'] .'/logo.png') }}"
                    alt="@lang('image')"></a>
            <a href="{{ route('home') }}" class="sidebar__logo-shape"><img
                    src="{{ getImage(imagePath()['logoIcon']['path'] .'/favicon.png') }}"
                    alt="@lang('image')"></a>
            <button type="button" class="navbar__expand"></button>
        </div>

        <div class="sidebar__menu-wrapper" id="sidebar__menuWrapper">
            <ul class="sidebar__menu">
                <li class="sidebar-menu-item {{ menuActive('staff.dashboard') }}">
                    <a href="{{ route('staff.dashboard') }}" class="nav-link ">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">@lang('Dashboard')</span>
                    </a>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('staff.loan*',3)}}">
                        <i class="menu-icon las la-comment-dollar"></i>
                        <span class="menu-title">@lang('Loan') </span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('staff.loan*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{menuActive(['staff.loan.plan', 'staff.loan.apply'])}} ">
                                <a href="{{route('staff.loan.plan')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Apply for loan')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.loan.pending')}} ">
                                <a href="{{route('staff.loan.pending')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.loan.active')}} ">
                                <a href="{{route('staff.loan.active')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Active')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.loan.paid')}} ">
                                <a href="{{route('staff.loan.paid')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Paid')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.loan.closed')}} ">
                                <a href="{{route('staff.loan.closed')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Closed')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.loan.all')}} ">
                                <a href="{{route('staff.loan.all')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>
                            
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('staff.savings*',3)}}">
                        <i class="menu-icon las la-coins"></i>
                        <span class="menu-title">@lang('Savings') </span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('staff.savings*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{menuActive(['staff.savings.plan', 'staff.savings.apply'])}} ">
                                <a href="{{route('staff.savings.plan')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Apply for savings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.savings.pending')}} ">
                                <a href="{{route('staff.savings.pending')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.savings.active')}} ">
                                <a href="{{route('staff.savings.active')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Active')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.savings.paid')}} ">
                                <a href="{{route('staff.savings.paid')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Paid')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.savings.closed')}} ">
                                <a href="{{route('staff.savings.closed')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Closed')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.savings.all')}} ">
                                <a href="{{route('staff.savings.all')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('staff.payment*',3)}}">
                        <i class="menu-icon las la-history"></i>
                        <span class="menu-title">@lang('Payment History') </span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('staff.payment*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{menuActive('staff.payment.history')}} ">
                                <a href="{{route('staff.payment.history')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('All')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.payment.loan.history')}} ">
                                <a href="{{route('staff.payment.loan.history')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Loan')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.payment.savings.history')}} ">
                                <a href="{{route('staff.payment.savings.history')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Savings')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('staff.collection*',3)}}">
                        <i class="menu-icon las la-credit-card"></i>
                        <span class="menu-title">@lang('Collection')</span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('staff.collection*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{menuActive('staff.collection.loan.pending')}} ">
                                <a href="{{route('staff.collection.loan.pending')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Loan')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.collection.savings.pending')}} ">
                                <a href="{{route('staff.collection.savings.pending')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Pending Savings')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.collection.loan.paid')}} ">
                                <a href="{{route('staff.collection.loan.paid')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Paid Loan')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.collection.savings.paid')}} ">
                                <a href="{{route('staff.collection.savings.paid')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Paid Savings')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item sidebar-dropdown">
                    <a href="javascript:void(0)" class="{{menuActive('staff.ticket*',3)}}">
                        <i class="menu-icon las la-envelope"></i>
                        <span class="menu-title">@lang('Support Ticket')</span>
                    </a>
                    <div class="sidebar-submenu {{menuActive('staff.ticket*',2)}} ">
                        <ul>
                            <li class="sidebar-menu-item {{menuActive('staff.ticket.open')}} ">
                                <a href="{{route('staff.ticket.open')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('Open Ticket')</span>
                                </a>
                            </li>
                            <li class="sidebar-menu-item {{menuActive('staff.ticket')}} ">
                                <a href="{{route('staff.ticket')}}" class="nav-link">
                                    <i class="menu-icon las la-dot-circle"></i>
                                    <span class="menu-title">@lang('My Tickets')</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="sidebar-menu-item {{ menuActive('staff.profile') }}">
                    <a href="{{ route('staff.profile') }}" class="nav-link ">
                        <i class="menu-icon las la-user"></i>
                        <span class="menu-title">@lang('Profile')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('staff.twofactor') }}">
                    <a href="{{ route('staff.twofactor') }}" class="nav-link ">
                        <i class="menu-icon las la-shield-alt"></i>
                        <span class="menu-title">@lang('2FA Security')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('staff.change.password') }}">
                    <a href="{{ route('staff.change.password') }}" class="nav-link ">
                        <i class="menu-icon las la-lock"></i>
                        <span class="menu-title">@lang('Chnage Password')</span>
                    </a>
                </li>
                <li class="sidebar-menu-item {{ menuActive('staff.logout') }}">
                    <a href="{{ route('staff.logout') }}" class="nav-link ">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">@lang('logout')</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- sidebar end -->
