<?php
/**
 * @var Pollen\Form\FormTemplateInterface $this
 * @var Pollen\Form\FormInterface $form
 */
if ($messages = $this->get('notices.ERROR', [])) :
    echo $this->partial('tag', [
        'attrs'   => [
           'class' => 'FormNotice FormNotice--error'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-error', compact('form', 'messages'))
    ]);
elseif (($messages = $this->get('notices.SUCCESS', [])) || ($messages = $this->get('notices.notice', []))) :
    echo $this->partial('tag', [
        'attrs'   => [
            'class' => 'FormNotice FormNotice--success'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-success', compact('form', 'messages'))
    ]);
elseif ($messages = $this->get('notices.INFO', [])) :
    echo $this->partial('tag', [
        'attrs'   => [
            'class' => 'FormNotice FormNotice--info'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-info', compact('form', 'messages'))
    ]);
elseif ($messages = $this->get('notices.WARNING', [])) :
    echo $this->partial('tag', [
        'attrs'   => [
            'class' => 'FormNotice FormNotice--warning'
        ],
        'tag'    => 'div',
        'content' => $this->fetch('notices-warning', compact('form', 'messages'))
    ]);
endif;