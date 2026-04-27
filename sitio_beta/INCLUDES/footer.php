<footer class="bg-[#003e79] text-white py-16">
    <div class="max-w-[1600px] mx-auto px-12 text-center flex flex-col items-center">
        <!-- Branding & Navegación -->
        <div class="flex flex-col items-center gap-8 mb-12">
            <img src="logos/MMPharma-Logotipo-Horizontal-Blanco.png" alt="MMPharma" class="h-8 w-auto">
            
            <div class="flex justify-center gap-4">
                <a class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-all hover:scale-110" href="tel:3322207506">
                    <span class="material-symbols-outlined text-xl">phone</span>
                </a>
                <a class="w-10 h-10 bg-white/5 rounded-full flex items-center justify-center hover:bg-white/10 transition-all hover:scale-110" href="mailto:atencionclientes@mmpharma.mx">
                    <span class="material-symbols-outlined text-xl">mail</span>
                </a>
            </div>

            <ul class="flex flex-wrap justify-center gap-x-10 gap-y-4 text-blue-100/60 text-sm font-medium">
                <li><a href="index.php" class="hover:text-white transition-colors">Inicio</a></li>
                <li><a href="quienes_somos.php" class="hover:text-white transition-colors">¿Quiénes somos?</a></li>
                <li><a href="contacto.php" class="hover:text-white transition-colors">Contacto</a></li>
            </ul>
        </div>

        <div class="pt-8 border-t border-white/5 w-full">
            <p class="text-[10px] text-blue-100/30 font-black uppercase tracking-[0.2em]">
                © <?= date('Y') ?> MMPharma. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
    duration: 800,
    once: true,
    offset: 50
  });
</script>
</body>
</html>
