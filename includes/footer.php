  <footer class="footer">
    <div class="container grid-3">
      <div>
        <h4>powergirls.az</h4>
        <p><?php echo e(t('message_delivery_post')); ?></p>
        <p><?php echo e(t('message_order_channels')); ?></p>
      </div>
      <div>
        <h4><?php echo e(t('footer_delivery')); ?></h4>
        <p><?php echo e(t('delivery_text_fallback')); ?></p>
        <h4><?php echo e(t('footer_returns')); ?></h4>
        <p><?php echo e(t('returns_text')); ?></p>
      </div>
      <div>
        <h4><?php echo e(t('footer_follow')); ?></h4>
        <a data-ig-link href="<?php echo e($settings['instagram_url'] ?? '#'); ?>" target="_blank" data-track="instagram_click">Instagram</a><br />
        <a href="https://wa.me/<?php echo e(clean_phone($settings['whatsapp_number'] ?? '')); ?>" target="_blank" data-track="whatsapp_click">WhatsApp</a>
      </div>
    </div>
  </footer>
</body>
</html>
