<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FormFieldDriverInterface $field
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($required = $field->params('required.tagged')) : ?>
    <?php echo $this->field('required', $required); ?>
<?php endif; ?>