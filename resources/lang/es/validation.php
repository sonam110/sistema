<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | El following language lines contain El default error messages used by
    | El validator class. Some of Else rules have multiple versions such
    | as El size rules. Feel free to tweak each of Else messages here.
    |
    */

    'aceptado' => 'El :attribute debe ser aceptado.',
    'active_url' => 'El :attribute no es un valido URL.',
    'after' => 'El :attribute debe ser una fecha posterior a :date.',
    'after_or_equal' => 'El :attribute debe ser una fecha posterior o igual a :date.',
    'alpha' => 'El :attribute solo puede tener letras.',
    'alpha_dash' => 'El :attribute solo puede tener letras, numeros, guiones y underscores.',
    'alpha_num' => 'El :attribute solo puede tener letras y numeros.',
    'array' => 'El :attribute debe ser an array.',
    'before' => 'El :attribute debe ser una fecha anterior a :date.',
    'before_or_equal' => 'El :attribute debe ser una fecha anterior o igual a :date.',
    'between' => [
        'numeric' => 'El :attribute debe estar entre :min and :max.',
        'file' => 'El :attribute debe estar entre :min and :max kilobytes.',
        'string' => 'El :attribute debe estar entre :min and :max characters.',
        'array' => 'El :attribute debe tener entre :min and :max items.',
    ],
    'boolean' => 'El :attribute field debe ser verdadero or falso.',
    'confirmed' => 'El :attribute confirmacion no coincide.',
    'date' => 'El :attribute no es una fecha valida.',
    'date_equals' => 'El :attribute debe ser una fecha igaual a :date.',
    'date_format' => 'El :attribute does not match El format :format.',
    'different' => 'El :attribute y :oElr deben ser diferentes.',
    'digits' => 'El :attribute debe ser de :digits digitos.',
    'digits_between' => 'El :attribute debe estar entre :min and :max digits.',
    'dimensions' => 'El :attribute tine dimensiones de imagenes invalidas.',
    'distinct' => 'El :attribute field tiene un valor duplicado.',
    'email' => 'El :attribute debe ser una direccion email valida.',
    'ends_with' => 'El :attribute debe terminar con uno de los siguientes valores: :values.',
    'exists' => 'El :attribute seleccionado no es valido.',
    'file' => 'El :attribute debe ser un archivo.',
    'filled' => 'El :attribute field debe tener un valor.',
    'gt' => [
        'numeric' => 'El :attribute debe ser mayor a :value.',
        'file' => 'El :attribute debe ser mayor a :value kilobytes.',
        'string' => 'El :attribute debe ser mayor que :value caracteres.',
        'array' => 'El :attribute debe tener mas de :value items.',
    ],
    'gte' => [
        'numeric' => 'El :attribute debe ser mayor o igual a :value.',
        'file' => 'El :attribute debe ser mayor o igual a :value kilobytes.',
        'string' => 'El :attribute debe ser mayor o igual a :value caracteres.',
        'array' => 'El :attribute debe tener :value items or mas.',
    ],
    'image' => 'El :attribute debe ser una imagen.',
    'in' => 'El :attribute seleccionado no es valido.',
    'in_array' => 'El :attribute field does not exist in :oElr.',
    'integer' => 'El :attribute debe ser un integer.',
    'ip' => 'El :attribute debe ser un valido IP .',
    'ipv4' => 'El :attribute debe ser un validator IPv4 .',
    'ipv6' => 'El :attribute debe ser un valido IPv6 .',
    'json' => 'El :attribute debe ser un valido JSON .',
    'lt' => [
        'numeric' => 'El :attribute debe ser menor que :value.',
        'file' => 'El :attribute debe ser menor que :value kilobytes.',
        'string' => 'El :attribute debe ser menor que :value characters.',
        'array' => 'El :attribute debe ser menor que :value items.',
    ],
    'lte' => [
        'numeric' => 'El :attribute debe ser menor que o igual :value.',
        'file' => 'El :attribute debe ser menor que o igual :value kilobytes.',
        'string' => 'El :attribute debe ser menor que o igual :value characters.',
        'array' => 'El :attribute no debe tener mas de :value items.',
    ],
    'max' => [
        'numeric' => 'El :attribute no debe ser mayor a :max.',
        'file' => 'El :attribute no debe ser mayor a :max kilobytes.',
        'string' => 'El :attribute no debe ser mayor a :max characters.',
        'array' => 'El :attribute no debe ser tener mas de :max items.',
    ],
    'mimes' => 'El :attribute debe ser un archivo del tipo: :values.',
    'mimetypes' => 'El :attribute debe ser un archivo del tipo: :values.',
    'min' => [
        'numeric' => 'El :attribute debe ser al menos :min.',
        'file' => 'El :attribute debe ser al menos :min kilobytes.',
        'string' => 'El :attribute debe ser al menos :min characters.',
        'array' => 'El :attribute debe ser al menos :min items.',
    ],
    'not_in' => 'El selected :attribute no es valido.',
    'not_regex' => 'El :attribute tiene un formato invalido.',
    'numeric' => 'El :attribute debe ser un numero.',
    'password' => 'El password no es correcto.',
    'present' => 'El :attribute tiene que estar presente.',
    'regex' => 'El :attribute tiene un formato invalido.',
    'required' => 'El :attribute campo es requerido.',
    'required_if' => 'El :attribute campo es requerido cuando :oElr es :value.',
    'required_unless' => 'El :attribute campo es requerido excepto :oElr esta en :values.',
    'required_with' => 'El :attribute campo es requerido cuando :values esta presente.',
    'required_with_all' => 'El :attribute campo es requerido cuando :values estan presentes.',
    'required_without' => 'El :attribute campo es requerido cuando :values no esta presente.',
    'required_without_all' => 'El :attribute campo es requerido cuando ninguno de :values estan presente.',
    'same' => 'El :attribute y :oElr deben coincidir.',
    'size' => [
        'numeric' => 'El :attribute debe ser :size.',
        'file' => 'El :attribute debe ser :size kilobytes.',
        'string' => 'El :attribute debe ser :size characters.',
        'array' => 'El :attribute debe contener :size items.',
    ],
    'starts_with' => 'El :attribute debe comenzar con uno de los siguientes: :values.',
    'string' => 'El :attribute debe ser una cadena.',
    'timezone' => 'El :attribute debe ser una zona valida.',
    'unique' => 'El :attribute ya fue tomado.',
    'uploaded' => 'El :attribute fallo al subir.',
    'url' => 'El :attribute tiene un formato invalido.',
    'uuid' => 'El :attribute debe ser un UUID valido.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using El
    | convention "attribute.rule" to name El lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | El following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
