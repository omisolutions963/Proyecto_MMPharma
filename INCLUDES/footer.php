<footer class="bg-primary text-white pt-16 pb-16">
 <div class="max-w-[1369px] mx-auto px-8 flex flex-col items-center text-center space-y-8">
 <!-- Logo -->
 <img src="<?= $base ?? '' ?>logos/mmpharma-logotipo-horizontal-blanco.png" alt="MMPharma" class="h-10 w-auto">
 
 <!-- Navegación -->
 <nav class="grid grid-cols-2 justify-items-center max-w-xs mx-auto sm:flex sm:flex-wrap sm:justify-center sm:max-w-none gap-x-12 gap-y-4 text-xs font-bold uppercase tracking-[0.2em] text-white/60">
 <a href="<?= $base ?? '' ?>index/index.php" class="hover:text-white transition-colors">Inicio</a>
 <a href="<?= $base ?? '' ?>quienes_somos/quienes_somos.php" class="hover:text-white transition-colors">Nosotros</a>
 <a href="<?= $base ?? '' ?>catalogo/catalogo.php" class="hover:text-white transition-colors">Catálogo</a>
 <a href="<?= $base ?? '' ?>contacto/contacto.php" class="hover:text-white transition-colors">Contacto</a>
 </nav>

 <!-- Redes Sociales -->
 <div class="flex gap-6">
  <a class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center hover:bg-white hover:text-primary transition-all text-white" href="https://wa.me/523322207506" target="_blank" rel="noopener noreferrer" title="WhatsApp">
    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-7 h-7">
      <path fill-rule="evenodd" clip-rule="evenodd" d="M3.50002 12C3.50002 7.30558 7.3056 3.5 12 3.5C16.6944 3.5 20.5 7.30558 20.5 12C20.5 16.6944 16.6944 20.5 12 20.5C10.3278 20.5 8.77127 20.0182 7.45798 19.1861C7.21357 19.0313 6.91408 18.9899 6.63684 19.0726L3.75769 19.9319L4.84173 17.3953C4.96986 17.0955 4.94379 16.7521 4.77187 16.4751C3.9657 15.176 3.50002 13.6439 3.50002 12ZM12 1.5C6.20103 1.5 1.50002 6.20101 1.50002 12C1.50002 13.8381 1.97316 15.5683 2.80465 17.0727L1.08047 21.107C0.928048 21.4637 0.99561 21.8763 1.25382 22.1657C1.51203 22.4552 1.91432 22.5692 2.28599 22.4582L6.78541 21.1155C8.32245 21.9965 10.1037 22.5 12 22.5C17.799 22.5 22.5 17.799 22.5 12C22.5 6.20101 17.799 1.5 12 1.5ZM14.2925 14.1824L12.9783 15.1081C12.3628 14.7575 11.6823 14.2681 10.9997 13.5855C10.2901 12.8759 9.76402 12.1433 9.37612 11.4713L10.2113 10.7624C10.5697 10.4582 10.6678 9.94533 10.447 9.53028L9.38284 7.53028C9.23954 7.26097 8.98116 7.0718 8.68115 7.01654C8.38113 6.96129 8.07231 7.046 7.84247 7.24659L7.52696 7.52195C6.76823 8.18414 6.3195 9.2723 6.69141 10.3741C7.07698 11.5163 7.89983 13.314 9.58552 14.9997C11.3991 16.8133 13.2413 17.5275 14.3186 17.8049C15.1866 18.0283 16.008 17.7288 16.5868 17.2572L17.1783 16.7752C17.4313 16.5691 17.5678 16.2524 17.544 15.9269C17.5201 15.6014 17.3389 15.308 17.0585 15.1409L15.3802 14.1409C15.0412 13.939 14.6152 13.9552 14.2925 14.1824Z" fill="currentColor"/>
    </svg>
  </a>
 <a class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center hover:bg-white hover:text-primary transition-all text-white" href="mailto:atencionclientes@mmpharma.mx" target="_blank" rel="noopener noreferrer">
 <span class="material-symbols-outlined text-xl">mail</span>
 </a>
 </div>

  <!-- Copyright -->
 <div class="w-full text-[10px] text-white/60 tracking-[0.3em] uppercase transition-colors text-center flex flex-col items-center">
 <p class="mb-6 cursor-default hover:text-white">&copy; <span id="copyright-year">2026</span> MMPharma. Todos los derechos reservados.</p>
  <div class="flex flex-col items-center gap-3 border-t border-white/10 pt-6 w-full max-w-md">
  <p class="normal-case tracking-wide text-white/80 text-sm md:text-base">Sistema realizado por <a href="https://www.atomic.zone/" target="_blank" rel="noopener noreferrer" class="text-white hover:text-tertiary hover:underline transition-colors font-bold decoration-white/20 underline-offset-4">Atomic Advertising</a></p>
  <a href="https://www.atomic.zone/" target="_blank" rel="noopener noreferrer" class="hover:scale-105 transition-transform duration-300">
  <img src="<?= $base ?? '' ?>Img/atomic%20logo.png" alt="Atomic Advertising" class="h-10 w-auto object-contain" style="filter: brightness(0) invert(1);">
  </a>
  </div>
 <script>document.getElementById('copyright-year').textContent = new Date().getFullYear();</script>
 </div>

</footer>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
 AOS.init({
 duration: 800,
 once: true,
 offset: 50,
 });
</script>
</body>
</html>
