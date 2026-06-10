<footer class="site-footer">
    <div class="container footer-grid">

        <div class="logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/logo.png" alt="<?php bloginfo('name'); ?> Logo">
            <p>Redefining the Van Life.</p>
            <div class="footer-socials">
                <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/instagram.png" alt="Instagram"></a>
                <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/facebook.png" alt="Facebook"></a>
                <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/youtube.png" alt="YouTube"></a>
                <a href="#" target="_blank" rel="noopener"><img src="<?php echo get_template_directory_uri(); ?>/assets/linkedin.png" alt="LinkedIn"></a>
            </div>
        </div>
        <div class="footer-menus-wrapper">

        <div class="footer-menus">
            <div class="footer-column">
                <h4>SHOWROOM</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'showroom',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

            <div class="footer-column">
                <h4>PARTS & SERVICES</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'services',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
                <h4>MERCH</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'merch',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

            <div class="footer-column">
                <h4>ABOUT</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'about',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

            <div class="footer-column">
                <h4>MORE</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'more',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

        </div>
        <div class="footer-menus">
            <div class="footer-column">
                <h4>OWNERSHIP</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'ownership',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

            <div class="footer-column">
                <h4>LEGAL</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'legal',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

            <div class="footer-column">
                <h4>COMPLIANCE</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'compliance',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>

            <div class="footer-column">
                <h4>COMPANY INFO</h4>
                <?php
                wp_nav_menu([
                    'theme_location' => 'company_info',
                    'container'      => false,
                    'menu_class'     => 'menu',
                ]);
                ?>
            </div>
            </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
            <div class="footer-copyright">
                © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.
            </div>
            <div class="footer-contents">
                <span>Van Haven LTD are a credit broker and not a lender. We are Authorised and Regulated by the Financial Conduct Authority. FCA No: 785140 Finance is Subject to status. Other offers may be available but cannot be used in conjunction with this offer. We work with a number of carefully selected credit providers who may be able to offer you finance for your purchase. Registered in England & Wales: 10345749</span>
            </div>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>