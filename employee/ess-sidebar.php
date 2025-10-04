<!-- sidebar.html -->
<aside 
  x-data="{ sidebarOpen: true }"
  class="flex flex-col bg-[#0b1b3b] text-white transition-all duration-300 min-h-screen"
  :class="sidebarOpen ? 'w-64' : 'w-16'"
>

  <!-- Sidebar Header -->
  <div class="flex items-center justify-between px-4 py-4 bg-gradient-to-r from-[#0b1b3b] to-[#102650]">
    <div class="flex items-center gap-3" x-show="sidebarOpen" x-transition>
      <div class="h-10 w-10 rounded-full grid place-items-center border-2 border-[#d4af37] bg-white">
        <img src="../assets/img/mainlogo.png" alt="HM" class="w-6 h-6" />
      </div>
      <p class="font-semibold">H VILL</p>
    </div>
    <!-- Toggle Button -->
    <button @click="sidebarOpen = !sidebarOpen" class="text-white hover:text-[#d4af37]">
      <span x-show="sidebarOpen" x-transition>⮜</span>
      <span x-show="!sidebarOpen" x-transition>⮞</span>
    </button>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-2 text-sm">
    <p class="uppercase text-xs text-white/50 mb-1" x-show="sidebarOpen" x-transition>Main</p>
    
    <!-- Dashboard -->
    <a href="employeedashboard.php" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
       :class="{ 'bg-white/10': window.location.pathname.includes('employeedashboard.php') }">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v11a1 1 0 01-1 1h-3m-6 0h6"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Overview / Dashboard</span>
    </a>

    <p class="uppercase text-xs text-white/50 mb-1" x-show="sidebarOpen">Employee</p>

    <!-- Employee self-service -->
    <a href="ess.php" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
       :class="{ 'bg-white/10': window.location.pathname.includes('ess.php') }">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Employee Self-Service</span>
    </a>

    <!-- Training Management -->
    <a href="ess-train.php" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
       :class="{ 'bg-white/10': window.location.pathname.includes('ess-train.php') }">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Training Management</span>
    </a>

    <!-- Succession Planning -->
    <a href="ess-suc.php" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
       :class="{ 'bg-white/10': window.location.pathname.includes('ess-suc.php') }">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Succession Planning</span>
    </a>

    <!-- Learning Management -->
    <a href="ess-learn.php" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
       :class="{ 'bg-white/10': window.location.pathname.includes('ess-learn.php') }">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Learning Management</span>
    </a>

    <!-- Competency Management -->
    <a href="ess-comp.php" 
       class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
       :class="{ 'bg-white/10': window.location.pathname.includes('ess-comp.php') }">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Competency Management</span>
    </a>
  </nav>

  <!-- Footer -->
  <div class="px-3 py-4 border-t border-white/20">
    <a href="logout.php" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5m0 6H3"/>
      </svg>
      <span x-show="sidebarOpen" x-transition>Logout</span>
    </a>
    <p class="text-[10px] text-white/50 mt-2" x-show="sidebarOpen" x-transition>© 1999–2025 HMVH</p>
  </div>
</aside>

<!-- Import Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>