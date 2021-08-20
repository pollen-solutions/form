<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FormFieldDriverInterface $field
 * @var Pollen\Form\FormInterface $form
 */
?>
<?php if ($field->hasWrapper()) : $this->layout('wrapper-field', $this->all()); endif; ?>
<?php echo $field->before(); ?>
<?php $this->insert('field-content', compact('field', 'form')); ?>
<?php echo $field->after();