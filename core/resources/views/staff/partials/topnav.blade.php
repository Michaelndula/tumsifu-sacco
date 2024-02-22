<!-- navbar-wrapper start -->
<nav class="navbar-wrapper">
    <form class="navbar-search" onsubmit="return false;">
        <button type="submit" class="navbar-search__btn">
            <i class="las la-search"></i>
        </button>
        <input type="search" name="navbar-search__field" id="navbar-search__field" placeholder="@lang('Search')">
        <button type="button" class="navbar-search__close"><i class="las la-times"></i></button>

        <div id="navbar_search_result_area">
            <ul class="navbar_search_result"></ul>
        </div>
    </form>

    <div class="navbar__right">
        <a href="{{ route('staff.profile') }}" class="dropdown-menu__item d-flex align-items-center px-3 py-2 border-0">
            <i class="dropdown-menu__icon las la-user-circle"></i>
            <span class="dropdown-menu__caption">@lang('Profile')</span>
        </a>
    </div>
</nav>
<!-- navbar-wrapper end -->
