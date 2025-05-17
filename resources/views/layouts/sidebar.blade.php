<nav class="pcoded-navbar">
    <div class="nav-list">
        <div class="pcoded-inner-navbar main-menu">
            <div class="pcoded-navigation-label">Navigasi</div>
            <ul class="pcoded-item pcoded-left-item">
                <li class="{{ Request::is('dashboard') ? 'active' : '' }}">
                    <a href="{{ route('dashboard') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="feather icon-grid"></i></span>
                        <span class="pcoded-mtext">Dasbor</span>
                    </a>
                </li>
                <li class="{{ Request::is('attendance/history*') ? 'active' : '' }}">
                    <a href="{{ route('attendance.history') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="feather icon-calendar"></i></span>
                        <span class="pcoded-mtext">Riwayat Absensi</span>
                    </a>
                </li>
                <li class="{{ Request::is('excuses/history*') ? 'active' : '' }}">
                    <a href="{{ route('excuses.history') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="feather icon-calendar"></i></span>
                        <span class="pcoded-mtext">Riwayat Perizinan</span>
                    </a>
                </li>
                @if (Auth::user()->role == 'admin')
                    <li class="{{ Request::is('positions*') ? 'active' : '' }}">
                        <a href="{{ route('positions.index') }}" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="feather icon-map-pin"></i></span>
                            <span class="pcoded-mtext">Posisi</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('users*') ? 'active' : '' }}">
                        <a href="{{ route('users.index') }}" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                            <span class="pcoded-mtext">Pengguna</span>
                        </a>
                    </li>
                    <li class="{{ Request::is('settings*') ? 'active' : '' }}">
                        <a href="{{ route('settings.index') }}" class="waves-effect waves-dark">
                            <span class="pcoded-micon"><i class="feather icon-sliders"></i></span>
                            <span class="pcoded-mtext">Pengaturan Website</span>
                        </a>
                    </li>
                @endif
                <li class="{{ Request::is('profile') ? 'active' : '' }}">
                    <a href="{{ route('profile.show') }}" class="waves-effect waves-dark">
                        <span class="pcoded-micon"><i class="feather icon-settings"></i></span>
                        <span class="pcoded-mtext">Pengaturan Profil</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
