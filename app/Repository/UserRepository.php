<?php

namespace App\Repository;
use App\Models\User;
class UserRepository{
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function create(array $dados): object {
        return  $this->user::create($dados);
    }

}
?>
