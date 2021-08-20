<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FormFieldDriverInterface $field
 * @var Pollen\Form\FormInterface $form
 */
echo $this->partial('tag', array_merge($field->params('label.wrapper', []), [
    'content' => $this->section('content')
]));