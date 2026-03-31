    </div>
</main>

<footer class="site-footer">
    <div class="container">
        <div class="site-footer__box">
            <div class="site-footer__left">
                <div class="site-footer__brand">Корочки.есть</div>
                <div class="site-footer__text">Портал подачи заявок на онлайн-обучение</div>
            </div>
            <div class="site-footer__right">© <?php echo date('Y'); ?> Все права защищены</div>
        </div>
    </div>
</footer>

<button type="button" class="scroll-top" id="scrollTopBtn" aria-label="Наверх">↑</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    var slider = document.querySelector('[data-simple-slider]');
    if (slider) {
        var items = slider.querySelectorAll('.simple-slide');
        var prev = slider.querySelector('[data-slider-prev]');
        var next = slider.querySelector('[data-slider-next]');
        var index = 0;
        var timer = null;

        function showSlide(i) {
            for (var k = 0; k < items.length; k++) {
                items[k].classList.remove('active');
            }
            items[i].classList.add('active');
        }

        function goNext() {
            index++;
            if (index >= items.length) {
                index = 0;
            }
            showSlide(index);
        }

        function goPrev() {
            index--;
            if (index < 0) {
                index = items.length - 1;
            }
            showSlide(index);
        }

        function startAuto() {
            timer = setInterval(goNext, 3000);
        }

        function resetAuto() {
            clearInterval(timer);
            startAuto();
        }

        if (next) {
            next.addEventListener('click', function () {
                goNext();
                resetAuto();
            });
        }

        if (prev) {
            prev.addEventListener('click', function () {
                goPrev();
                resetAuto();
            });
        }

        showSlide(index);
        startAuto();
    }

    var revealElements = document.querySelectorAll('.reveal-on-scroll');

    function revealOnScroll() {
        for (var i = 0; i < revealElements.length; i++) {
            var rect = revealElements[i].getBoundingClientRect();
            if (rect.top < window.innerHeight - 60) {
                revealElements[i].classList.add('revealed');
            }
        }
    }

    revealOnScroll();
    window.addEventListener('scroll', revealOnScroll);

    var scrollTopBtn = document.getElementById('scrollTopBtn');

    if (scrollTopBtn) {
        window.addEventListener('scroll', function () {
            if (window.pageYOffset > 250) {
                scrollTopBtn.classList.add('is-visible');
            } else {
                scrollTopBtn.classList.remove('is-visible');
            }
        });

        scrollTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
})();
</script>
</body>
</html>