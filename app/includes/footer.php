                <footer class="footer-bar">
                    <p>&copy; 2025 City Hospital Management System. All rights reserved.</p>
                    <p>Version 1.0.0</p>
                </footer>
            </main>
        </div>
    </div>

    <script src="<?= e(versioned_asset_url('js/main.js')); ?>"></script>
    <?php if (isset($pageScripts) && is_array($pageScripts)): ?>
        <?php foreach ($pageScripts as $pageScript): ?>
            <script src="<?= e((string) $pageScript); ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
