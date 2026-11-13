<header>
   <nav id="nbar">
      <div class="container">
         <div class="d-flex align-items-center justify-content-between w-100">
            <a href="#" class="d-flex align-items-center gap-2"
               style="font-size:1.2rem;font-weight:700;color:var(--tx)">
               <div class="logo-i"> <img src="{{ asset('logo.png') }}" alt="Logo"></div>
               <span>MWork</span>
            </a>

            <div class="d-none d-lg-flex align-items-center gap-1 mx-auto">
               <a href="#" class="nav-link">Beranda</a>
               <a href="#fitur" class="nav-link">Fitur</a>
               <a href="#harga" class="nav-link">Paket Harga</a>
               <a href="#tutorial" class="nav-link">Tutorial</a>
               <a href="#alasan" class="nav-link">Alasan</a>
               <a href="#tentang" class="nav-link">Pertanyaan Umum</a>
               <a href="#kontak" class="nav-link">Kontak</a>
            </div>

            <div class="d-flex align-items-center gap-2">
               <button class="boc d-flex align-items-center justify-content-center" id="thbtn"
                  style="width:38px;height:38px;padding:0;border-radius:12px" aria-label="Toggle theme">
                  <i class="fa-solid fa-sun" id="suni" style="display:none"></i>
                  <i class="fa-solid fa-moon" id="mooni"></i>
               </button>
               <a href="{{ route('login') }}" 
   class="bgrd btn px-3 py-2 d-none d-sm-flex align-items-center gap-1">
   <i class="fa-regular fa-user fa-sm"></i> Log in
</a>

            </div>
         </div>
      </div>
   </nav>

   <div id="mbmenu">
      <a href="#fitur" class="nav-link d-block py-3 border-bottom" style="border-color:var(--bd)!important">Fitur</a>
      <a href="#harga" class="nav-link d-block py-3 border-bottom" style="border-color:var(--bd)!important">Paket
         Harga</a>
      <a href="#tutorial" class="nav-link d-block py-3 border-bottom"
         style="border-color:var(--bd)!important">Tutorial</a>
      <a href="#alasan" class="nav-link d-block py-3 border-bottom" style="border-color:var(--bd)!important">Alasan</a>
      <a href="#tentang" class="nav-link d-block py-3">Pertanyaan Umum</a>
      <a href="#kontak" class="nav-link d-block py-3 border-bottom" style="border-color:var(--bd)!important">Kontak</a>
      <div class="d-flex gap-2 mt-3">
         <button class="boc flex-fill py-2 btn" data-bs-toggle="offcanvas" data-bs-target="#lofc"
            onclick="swTab('login')">Log in</button>
      </div>
   </div>
</header>