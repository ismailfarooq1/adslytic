<aside class="relative bg-sidebar h-screen w-64 hidden sm:block shadow-xl">
    <div class="p-6">
        <a href="{{route('dashboard')}}" class="text-white text-3xl font-semibold uppercase hover:text-gray-300">
            {{\Illuminate\Support\Facades\Auth::user()->name}}
        </a>
    </div>
    <nav class="text-white text-base font-semibold pt-3">
        <a href="{{route('dashboard')}}" class="flex items-center active-nav-link text-white py-4 pl-6 nav-item">
            <i class="fas fa-tachometer-alt mr-3"></i>
            Dashboard
        </a>
    </nav>
</aside>
