<?php 

$this->view()->prt();

return;



if($this->env->request->is_action){

    $this->item->update($this->xui->request['--form']);
    
} else {
    
    $this->o->xui->form->datasource($this->get_form_data());
    $this->o->xui->form->template('std_template');
    $this->o->xui->form->field_prefix('--form');
    $this->o->xui->form->open();
    $this->o->xui->form['name']->field('text','Name')->prt();
    $this->o->xui->form['associate']->field('select','Name')->options([])->prt();
    $this->o->xui->form['associate']->field('checkbox','Name')->prt();
    $this->o->xui->form->submit('Submit');
    $this->o->xui->form->close();
}




if($this->env->request->is_action){

    $this->item->update($this->xui->request['--form']);
    
} else {
    
    $this->o->xui->form->layout([
        fn($form) => $form->datasource($this->get_form_data()),
        fn($form) => $form->template('std_tempalte'),
        fn($form) => $form->field_prefix('--form'),
        [
            'row',
            [
                fn($form) => $form['[name]']->field('text','Name')->prt(),
            ]
        ],
        [
            [
                fn($form) => $form['[associate]']->field('select','Name')->options([])->prt(),
                fn($form) => $form['[associate]']->field('checkbox','Name')->prt(),
            ]
        ],
        [
            [],
            [
                'col-auto',
                fn($form) => $form->submit_button('Submit'),
            ]
        ]
    ])->prt();
    
}


$this->xui->view('xui')(function(){
    
});



if($this->env->request->is_action){

    $this->item->update($this->o->xui->request['--form']);
    
} else {
    
    $this->layout([
        fn() => $this->context(fn($xui) => $xui->form->new()),
        fn($form) => $form->datasource($this->context->model->form_data()),
        fn($form) => $form->template('std_tempalte'),
        fn($form) => $form->field_prefix('--form'),
        [
            'row',
            [
                fn($form) => $form['[name]']->field('text','Name')->prt(),
            ]
        ],
        [
            'row',
            [
                'col',
                [
                    'row',
                    fn($form) => $form['[associate]']->field('select','Name')->options([])->prt(),
                    fn($form) => $form['[associate]']->field('checkbox','Name')->prt(),
                ]
            ]
        ],
        [
            'row',
            ['col'],
            [
                'col-auto',
                fn($form) => $form->submit_button('Submit'),
            ]
        ]
    ])->prt();
    
}


if($this->env->request->is_action){

    $this->item->update($this->o->xui->request['--form']);
    
} else {
    $this->
    $this->o->xui->context(fn($xui) => $xui->form->new())->layout([
        fn($form) => $form->datasource($this->get_form_data()),
        fn($form) => $form->template('std_tempalte'),
        fn($form) => $form->field_prefix('--form'),
        [
            'row',
            [
                fn($form) => $form['[name]']->field('text','Name')->prt(),
            ]
        ],
        [
            'row',
            [
                fn($form) => $form['[associate]']->field('select','Name')->options([])->prt(),
                fn($form) => $form['[associate]']->field('checkbox','Name')->prt(),
            ]
        ],
        [
            'row',
            ['col'],
            [
                'col-auto',
                fn($form) => $form->submit_button('Submit'),
            ]
        ]
    ])->prt();
    
}
