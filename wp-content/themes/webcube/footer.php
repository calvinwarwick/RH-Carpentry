<footer class="custom-footer">
    <div class="footer-top py-4" style="background-color:rgb(241, 246, 247);">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
                    <div class="d-flex align-items-center justify-content-center justify-content-md-start">
                        <a href="https://palmerpartners.com" target="_blank" rel="noopener" class="d-flex align-items-center" style="">
                            <img src="/wp-content/uploads/2025/05/image-1.png" alt="Palmer & Partners Logo" class="footer-logo me-3" style=" height: 45px;">
                        </a>
                    </div>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                        <a href="https://webcube.uk" target="_blank" class="d-flex align-items-center text-decoration-none ms-2 text-dark">
                            <span class="footer-logo-text fw-bold">made by</span>
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-webcube.svg" alt="Webcube Logo" class="mx-2 footer-logo-webcube" style="height: 40px;">
                            <span class="footer-logo-text fw-bold">webcube</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom py-3">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <p class="mb-0">Copyright &copy; 2025 Palmer & Partners</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="<?php echo esc_url(home_url('/privacy-policy')); ?>" class="text-dark footer-link me-4">Privacy policy</a>
                    <a href="<?php echo esc_url(home_url('/terms-and-conditions')); ?>" class="text-dark footer-link">Terms and conditions</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>

</html>