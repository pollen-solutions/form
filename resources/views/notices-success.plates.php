<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var string[] $messages
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($messages = $this->get('messages')) : ?>
<ol class="Notice-items FormNotice-items FormNotice-items--success">
    <?php foreach ($messages as $message) : ?>
        <li class="Notice-item FormNotice-item FormNotice-item--success"><?php echo $message; ?></li>
    <?php endforeach; ?>
</ol>
<?php endif;