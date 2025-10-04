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
  <a href="dashboard.php" 
     class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
     :class="{ 'bg-white/10': window.location.pathname.includes('dashboard.php') }">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h3m10-11v11a1 1 0 01-1 1h-3m-6 0h6"/>
    </svg>
    <span x-show="sidebarOpen" x-transition>Overview / Dashboard</span>
  </a>

  <p class="uppercase text-xs text-white/50 mb-1" x-show="sidebarOpen">Modules</p>

  <!-- Competency Management -->
  <a href="Competencymanagement.php" 
     class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
     :class="{ 'bg-white/10': window.location.pathname.includes('Competencymanagement.php') }">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <span x-show="sidebarOpen" x-transition>Competency Management</span>
  </a>

  <!-- Learning Management -->
  <a href="Learningmanagement.php" 
     class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
     :class="{ 'bg-white/10': window.location.pathname.includes('Learningmanagement.php') }">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
    </svg>
    <span x-show="sidebarOpen" x-transition>Learning Management</span>
  </a>

  <!-- Succession Planning -->
  <a href="Successionplanning.php" 
     class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
     :class="{ 'bg-white/10': window.location.pathname.includes('Successionplanning.php') }">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 01-8 0M12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
    </svg>
    <span x-show="sidebarOpen" x-transition>Succession Planning</span>
  </a>

  <!-- Training Management -->
  <a href="Trainingmanagement.php" 
     class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
     :class="{ 'bg-white/10': window.location.pathname.includes('Trainingmanagement.php') }">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
    </svg>
    <span x-show="sidebarOpen" x-transition>Training Management</span>
  </a>

  <!-- Employee self-service -->
  <a href="Employeeself-service.php" 
     class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-white/10"
     :class="{ 'bg-white/10': window.location.pathname.includes('Employeeself-service.php') }">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#d4af37]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
    </svg>
    <span x-show="sidebarOpen" x-transition>Employee self-service</span>
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
