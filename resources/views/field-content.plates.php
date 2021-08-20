<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FormFieldDriverInterface $field
 * @var Pollen\Form\FormInterface $form
 */
echo ($field->params('label.position') === 'before')
    ? $this->fetch('field-label', compact('field', 'form')) . $field
    : $field. $this->fetch('field-label', compact('field', 'form'));