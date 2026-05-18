<footer class="bg-black text-white py-16">
 <div class="max-w-[1369px] mx-auto px-8 flex flex-col items-center text-center space-y-8">
 <!-- Logo -->
 <img src="<?= $base ?? '' ?>logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-10 w-auto">
 
 <!-- Navegación -->
 <nav class="flex flex-wrap justify-center gap-x-12 gap-y-4 text-xs font-bold uppercase tracking-[0.2em] text-white/60">
 <a href="<?= $base ?? '' ?>INDEX/index.php" class="hover:text-white transition-colors">Inicio</a>
 <a href="<?= $base ?? '' ?>QUIENES_SOMOS/quienes_somos.php" class="hover:text-white transition-colors">Nosotros</a>
 <a href="<?= $base ?? '' ?>CATALOGO/catalogo.php" class="hover:text-white transition-colors">Catálogo</a>
 <a href="<?= $base ?? '' ?>CONTACTO/contacto.php" class="hover:text-white transition-colors">Contacto</a>
 </nav>

 <!-- Redes Sociales -->
 <div class="flex gap-6">
 <a class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center hover:bg-white hover:text-black transition-all text-white" href="tel:3322207506">
 <span class="material-symbols-outlined text-xl">phone</span>
 </a>
 <a class="w-12 h-12 bg-white/5 rounded-full flex items-center justify-center hover:bg-white hover:text-black transition-all text-white" href="mailto:atencionclientes@mmpharma.mx">
 <span class="material-symbols-outlined text-xl">mail</span>
 </a>
 </div>

 <!-- Copyright -->
 <div class="w-full text-[10px] text-white/60 tracking-[0.3em] uppercase hover:text-white transition-colors cursor-default">
 <p>&copy; <script>document.write(new Date().getFullYear());</script> MMPharma. Todos los derechos reservados.</p>
 </div>
 </div>
</footer>
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
