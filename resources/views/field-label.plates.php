<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FormFieldDriverInterface $field
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($field->hasLabel()) : ?>
    <?php if ($field->params('label.wrapper')) : $this->layout('wrapper-label', $this->all()); endif; ?>
    <?php echo $this->field('label', $field->params('label', [])); ?>
    <?php $this->insert('field-required', compact('field', 'form')); ?>
<?php endif;