    <footer class="bg-dark fg-white text-center p-4 mt-auto">
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITENAME; ?>. <?php echo $data['txt']['SYS_COPYRIGHT'] ?? 'SYS_COPYRIGHT'; ?></p>
    </footer>
</div> <!-- /navview-content -->
</div> <!-- /navview -->

    <!-- Metro UI v5 JS -->
    <script src="https://cdn.metroui.org.ua/current/metro.js"></script>

    <!-- Contrôles de défilement horizontal permanent pour tableaux responsive -->
    <script>
    (function() {
        function setupTableScrollControls() {
            var wraps = document.querySelectorAll('.table-container, .horizontal-scroll, .table-wrap');
            wraps.forEach(function(wrap) {
                if (wrap.nextElementSibling && wrap.nextElementSibling.classList.contains('table-scroll-controls')) {
                    var existing = wrap.nextElementSibling;
                    if (existing._updateThumb) existing._updateThumb();
                    return;
                }

                var controls = document.createElement('div');
                controls.className = 'table-scroll-controls d-flex flex-align-center';
                controls.innerHTML = 
                    '<button type="button" class="scroll-btn-left" title="Défiler à gauche">' +
                        '<span class="mif-chevron-left"></span>' +
                    '</button>' +
                    '<div class="custom-table-scrollbar flex-grow-1 mx-2" title="Glisser ou cliquer pour faire défiler">' +
                        '<div class="custom-table-thumb"></div>' +
                    '</div>' +
                    '<button type="button" class="scroll-btn-right" title="Défiler à droite">' +
                        '<span class="mif-chevron-right"></span>' +
                    '</button>';

                wrap.parentNode.insertBefore(controls, wrap.nextSibling);

                var bar = controls.querySelector('.custom-table-scrollbar');
                var thumb = controls.querySelector('.custom-table-thumb');
                var btnLeft = controls.querySelector('.scroll-btn-left');
                var btnRight = controls.querySelector('.scroll-btn-right');

                function update() {
                    var maxScroll = wrap.scrollWidth - wrap.clientWidth;
                    if (maxScroll <= 2) {
                        controls.style.display = 'none';
                        return;
                    }
                    controls.style.display = 'flex';
                    var barWidth = bar.clientWidth;
                    if (barWidth <= 0) return;
                    var ratio = wrap.clientWidth / wrap.scrollWidth;
                    var thumbWidth = Math.max(36, ratio * barWidth);
                    thumb.style.width = thumbWidth + 'px';
                    var maxThumb = barWidth - thumbWidth;
                    var left = maxScroll > 0 ? (wrap.scrollLeft / maxScroll) * maxThumb : 0;
                    thumb.style.left = Math.min(Math.max(0, left), maxThumb) + 'px';
                }

                controls._updateThumb = update;

                wrap.addEventListener('scroll', update, { passive: true });
                window.addEventListener('resize', update);

                // Flèche Gauche
                btnLeft.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrap.scrollBy({ left: -140, behavior: 'smooth' });
                });

                // Flèche Droite
                btnRight.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrap.scrollBy({ left: 140, behavior: 'smooth' });
                });

                // Glisser-déposer sur le curseur bleu
                var isDragging = false;
                var startX = 0;
                var startScroll = 0;

                thumb.addEventListener('pointerdown', function(e) {
                    isDragging = true;
                    startX = e.clientX;
                    startScroll = wrap.scrollLeft;
                    thumb.setPointerCapture(e.pointerId);
                    e.preventDefault();
                    e.stopPropagation();
                });

                window.addEventListener('pointermove', function(e) {
                    if (!isDragging) return;
                    var dx = e.clientX - startX;
                    var barWidth = bar.clientWidth;
                    var thumbWidth = thumb.clientWidth;
                    var maxThumb = barWidth - thumbWidth;
                    var maxScroll = wrap.scrollWidth - wrap.clientWidth;
                    if (maxThumb > 0) {
                        wrap.scrollLeft = startScroll + (dx / maxThumb) * maxScroll;
                    }
                });

                window.addEventListener('pointerup', function() {
                    isDragging = false;
                });

                // Clic direct sur la piste
                bar.addEventListener('click', function(e) {
                    if (e.target === thumb) return;
                    var rect = bar.getBoundingClientRect();
                    var clickRatio = (e.clientX - rect.left) / rect.width;
                    var maxScroll = wrap.scrollWidth - wrap.clientWidth;
                    wrap.scrollTo({ left: clickRatio * maxScroll, behavior: 'smooth' });
                });

                setTimeout(update, 50);
                setTimeout(update, 300);
            });
        }

        // Surveiller en continu pour détecter quand Metro UI génère .table-container
        var attempts = 0;
        var checkTimer = setInterval(function() {
            attempts++;
            if (document.querySelector('.table-container, .horizontal-scroll, .table-wrap')) {
                setupTableScrollControls();
            }
            if (attempts > 30) {
                clearInterval(checkTimer);
            }
        }, 150);

        if (document.readyState === 'complete') {
            setupTableScrollControls();
        } else {
            window.addEventListener('load', function() {
                setTimeout(setupTableScrollControls, 100);
            });
        }
    })();
    </script>
</body>
</html>

