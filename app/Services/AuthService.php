<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Http\UploadedFile;

class AuthService
{
    private AuthRepository $authRepository;

    public function __construct(AuthRepository $authRepository)
    {
        $this->authRepository = $authRepository;
    }


    public function register(array $data)
    {
        // Pastikan pengecekan instance menggunakan 'UploadedFile' yang benar
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            $data['photo'] = $this->uploadPhoto($data['photo']);
        }

        return $this->authRepository->register($data);
    }


    public function login(array $data)
    {
        return $this->authRepository->login($data);
    }

    public function tokenLogin(array $data)
    {
        return $this->authRepository->tokenLogin($data);
    }

    private function uploadPhoto(UploadedFile $photo): string
    {
        // Contoh sederhana: simpan di folder 'avatars' dalam disk 'public'
        return $photo->store('users', 'public');
    }
}
