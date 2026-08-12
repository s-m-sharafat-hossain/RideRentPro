<?php if (isset($show_sidebar) && $show_sidebar): ?>
    </div>
<?php endif; ?>

<div class="footer">
    &copy; <?php echo date('Y'); ?> <?php echo APP_NAME; ?>. All rights reserved.
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php if (isset($extra_js)): ?>
    <?php foreach ($extra_js as $js): ?>
        <script src="<?php echo $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
</body>
</html>