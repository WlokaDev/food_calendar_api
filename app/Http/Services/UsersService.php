<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UsersService
{
    /**
     * @var User
     */

    private User $user;

    /**
     * @param User|null $user
     */

    public function __construct(User $user = null)
    {
        $this->user = $user ?: new User();
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param string $email
     * @param string|null $name
     * @param string|null $password
     * @param bool $setVerified
     * @return $this
     */

    public function assignData(
        string $email,
        ?string $name = null,
        ?string $password = null,
        bool $setVerified = false
    ): self
    {
        $this->user->name = $name;
        $this->user->email = $email;
        $this->user->password = Hash::make($password);

        if($setVerified) {
            $this->user->markEmailAsVerified();
        }

        $this->user->save();

        if (!$this->user->email_verified_at) {
            event(new Registered($this->user));
        }

        return $this;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $image
     * @return string
     */

    public function storeImage(
        UploadedFile $image
    ) : string
    {
        $extension = $image->getClientOriginalExtension();
        $content = file_get_contents($image);

        if(!$content) {
            throw new \RuntimeException('Content of image is empty.');
        }

        $fileName = Str::uuid() . '.' . $extension;
        $path = "/users/images/" . $fileName;

        if(Storage::disk('local')->put($path, $content)) {
            return $path;
        }

        throw new \RuntimeException('Unable to save file.');
    }
}
