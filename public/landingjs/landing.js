// ======================== ANIMASI OBSERVER ========================
document.addEventListener("DOMContentLoaded", function () {
   const elements = document.querySelectorAll(".rv");

   const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
         if (entry.isIntersecting) {
            entry.target.classList.add("in");
         }
      });
   }, { threshold: 0.1 });

   elements.forEach(el => observer.observe(el));
});

// ======================== SLIDER PRICING ========================
document.addEventListener("DOMContentLoaded", function () {
   const track = document.getElementById("pricingTrack");
   const prevBtn = document.getElementById("pricingPrev");
   const nextBtn = document.getElementById("pricingNext");
   const dotsContainer = document.getElementById("pricingDots");
   const items = document.querySelectorAll(".pricing-item");

   let currentIndex = 0;

   // Jumlah card yang terlihat
   function getItemsPerView() {
      if (window.innerWidth <= 768) return 1;
      if (window.innerWidth <= 992) return 2;
      return 3;
   }

   // Buat dot indikator
   function createDots() {
      dotsContainer.innerHTML = "";
      const itemsPerView = getItemsPerView();
      const totalSlides = Math.max(1, items.length - itemsPerView + 1);

      for (let i = 0; i < totalSlides; i++) {
         const dot = document.createElement("span");
         dot.classList.add("pricing-dot");
         if (i === currentIndex) dot.classList.add("active");

         dot.addEventListener("click", function () {
            currentIndex = i;
            updateSlider();
         });

         dotsContainer.appendChild(dot);
      }
   }

   // Update slider
   function updateSlider() {
      const itemsPerView = getItemsPerView();
      const maxIndex = Math.max(0, items.length - itemsPerView);
      if (currentIndex > maxIndex) currentIndex = maxIndex;

      const itemWidth = items[0].getBoundingClientRect().width;
      const gap = parseFloat(window.getComputedStyle(track).gap) || 0;
      const move = currentIndex * (itemWidth + gap);

      track.style.transform = "translateX(-" + move + "px)";
      prevBtn.disabled = currentIndex === 0;
      nextBtn.disabled = currentIndex === maxIndex;

      const dots = dotsContainer.querySelectorAll(".pricing-dot");
      dots.forEach((dot, index) => {
         dot.classList.toggle("active", index === currentIndex);
      });
   }

   // Tombol next
   nextBtn.addEventListener("click", function () {
      const itemsPerView = getItemsPerView();
      const maxIndex = Math.max(0, items.length - itemsPerView);
      if (currentIndex < maxIndex) {
         currentIndex++;
         updateSlider();
      }
   });

   // Tombol prev
   prevBtn.addEventListener("click", function () {
      if (currentIndex > 0) {
         currentIndex--;
         updateSlider();
      }
   });

   // Resize window
   window.addEventListener("resize", function () {
      createDots();
      updateSlider();
   });

   // Initial load
   createDots();
   updateSlider();
});
