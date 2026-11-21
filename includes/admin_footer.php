</main>
</div>
<?php include __DIR__.'/site_footer.php'; ?>
<?php render_toasts(); ?>
<script>document.querySelectorAll('.toast').forEach(function (t) { new bootstrap.Toast(t).show(); });</script>
</body>

</html>