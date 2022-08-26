<?php

		namespace App\EntityListener;

		use App\Entity\Users;
		use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

		class UserListener {

		    private UserPasswordHasherInterface $hasher;

		    public function __construct(UserPasswordHasherInterface $hasher)
		    {
			$this->hasher = $hasher;
		    }

		    public function prePersist(Users $user) {
			$this->encodePassword($user);
		    }
		    
		    public function preUpdate(Users $user) {
			$this->encodePassword($user);
		    }

		    public function encodePassword(Users $user) {
			if ($user->getPlainPassword() === null) {
			    return;
			}

			$user->setPassword(
			    $this->hasher->hashPassword(
				$user,
				$user->getPlainPassword()
			    )
			);

			$user->setPlainPassword('');
		    }

		}