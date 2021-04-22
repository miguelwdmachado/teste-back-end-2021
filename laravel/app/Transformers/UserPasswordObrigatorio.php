<?php

namespace App\Transformers;

use App\Models\User;
use Flugg\Responder\Transformers\Transformer;

class UserPasswordObrigatorio extends Transformer
{
    /**
     * List of available relations.
     *
     * @var string[]
     */
    protected $relations = [];

    /**
     * List of autoloaded default relations.
     *
     * @var array
     */
    protected $load = [];

    /**
     * Transform the model.
     *
     * @param  \App\Models\User $user
     * @return array
     */
    public static function transform(User $user)
    {
        return [
            'filename' => 'Password',
            'message' => 'O campo Senha é obrigatório.'
        ];
    }
}
