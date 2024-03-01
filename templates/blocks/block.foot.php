<script src="<?= $system['address'] ?>assets/js/scripts.js" defer></script>
<?php foreach ($jsScripts as $jsScript) : ?>
    <script src="<?= $jsScript['src'] ?>" defer></script>
<?php endforeach; ?>
<script src="<?= $system['address'] ?>assets/js/toast.js" defer></script>
</body>

</html>