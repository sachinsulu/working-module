<!-- 1. LEFT SIDEBAR -->
<aside class="w-72 glass-sidebar flex flex-col h-full flex-shrink-0 relative overflow-hidden z-20">
    <!-- Floating gradient background for sidebar -->
    <div class="absolute -top-24 -left-24 w-48 h-48 rounded-full bg-indigo-500/10 blur-3xl"></div>
    <div class="absolute -bottom-24 -right-24 w-48 h-48 rounded-full bg-fuchsia-500/10 blur-3xl"></div>

    <!-- Sidebar Header / Logo -->
    <div class="h-20 flex items-center px-6 gap-3 border-b border-slate-800/60 relative z-10">
        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-500 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <div>
            <h1 class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white via-indigo-200 to-indigo-400 bg-clip-text text-transparent">Longtail-WM</h1>
            <p class="text-[10px] uppercase font-semibold text-slate-500 tracking-wider">Access Control Center</p>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 py-6 space-y-1 relative z-10 overflow-y-auto">
        <!-- Dashboard Link -->
        @can('view dashboard')
        <a href="{{ route('admin.dashboard') }}" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition duration-300 {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-indigo-500/15 to-indigo-600/5 border-l-2 border-indigo-500 text-indigo-300' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
            </svg>
            <span>Overview Dashboard</span>
        </a>
        @endcan

        <!-- Users Link -->
        @can('view users')
        <a href="{{ route('admin.users.index') }}" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition duration-300 {{ request()->routeIs('admin.users.index') ? 'bg-gradient-to-r from-indigo-500/15 to-indigo-600/5 border-l-2 border-indigo-500 text-indigo-300' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span>User Management</span>
        </a>
        @endcan

        <!-- Clients Link -->
        @can('view clients')
        <a href="{{ route('admin.clients.index') }}" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition duration-300 {{ request()->routeIs('admin.clients.index') ? 'bg-gradient-to-r from-indigo-500/15 to-indigo-600/5 border-l-2 border-indigo-500 text-indigo-300' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-5-3.87M9 20H2v-2a4 4 0 014-4h3m8-5a4 4 0 11-8 0 4 4 0 018 0zM16 3.13a4 4 0 010 7.75"/>
            </svg>
            <span>Client Management</span>
        </a>
        @endcan

        <!-- Projects Link -->
        @can('view projects')
        <a href="{{ route('admin.projects.index') }}" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition duration-300 {{ request()->routeIs('admin.projects.index') ? 'bg-gradient-to-r from-indigo-500/15 to-indigo-600/5 border-l-2 border-indigo-500 text-indigo-300' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
            </svg>
            <span>Project Management</span>
        </a>
        @endcan

        <!-- Roles Link -->
        @can('view roles')
        <a href="{{ route('admin.roles.index') }}" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition duration-300 {{ request()->routeIs('admin.roles.index') ? 'bg-gradient-to-r from-indigo-500/15 to-indigo-600/5 border-l-2 border-indigo-500 text-indigo-300' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
            <span>Roles & Permissions</span>
        </a>
        @endcan

        <!-- Departments Link -->
        @can('view departments')
        <a href="{{ route('admin.departments.index') }}" class="w-full flex items-center gap-3 px-4 py-3.5 rounded-xl text-sm font-medium transition duration-300 {{ request()->routeIs('admin.departments.index') ? 'bg-gradient-to-r from-indigo-500/15 to-indigo-600/5 border-l-2 border-indigo-500 text-indigo-300' : 'text-slate-400 hover:bg-slate-800/40 hover:text-slate-200' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
            </svg>
            <span>Departments</span>
        </a>
        @endcan
    </nav>

    <!-- Sidebar Profile Footer -->
    <div class="p-4 border-t border-slate-800/60 relative z-10 bg-slate-950/40">
        <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/60 border border-slate-800/40">
            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-fuchsia-500 flex items-center justify-center font-bold text-white text-sm shadow">
               {{ mb_substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="overflow-hidden">
                <h4 class="text-xs font-bold text-slate-200 truncate">{{ Auth::user()->name }}</h4>
                <p class="text-[10px] text-indigo-400 font-semibold truncate uppercase">{{ Auth::user()->getRoleNames()->first() ?? 'User' }}</p>
            </div>
        </div>
    </div>
</aside>
